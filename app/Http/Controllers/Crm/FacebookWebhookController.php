<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Jobs\FetchFacebookLead;
use Illuminate\Http\Request;

/**
 * Facebook Lead Ads webhook. Facebook first checks the address (GET), then sends a
 * notice for each new lead (POST). The lead's details are fetched in the background.
 */
class FacebookWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $token = config('services.facebook.verify_token');
        abort_unless($token && $request->query('hub_mode') === 'subscribe' && hash_equals($token, (string) $request->query('hub_verify_token')), 403);

        return response((string) $request->query('hub_challenge'), 200)->header('Content-Type', 'text/plain');
    }

    public function receive(Request $request)
    {
        $secret = config('services.facebook.app_secret');
        abort_unless($secret, 503, 'Facebook is not set up.');

        // Only accept messages signed by Facebook with our app secret.
        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);
        abort_unless(hash_equals($expected, (string) $request->header('X-Hub-Signature-256')), 403);

        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) === 'leadgen' && ! empty($change['value']['leadgen_id'])) {
                    FetchFacebookLead::dispatch((string) $change['value']['leadgen_id']);
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
