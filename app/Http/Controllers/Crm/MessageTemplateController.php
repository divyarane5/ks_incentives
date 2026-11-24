<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\MessageTemplate;
use App\Services\LeadService;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    /** Open WhatsApp with the message filled in, and note it on the lead's timeline. */
    public function send(Request $request, Lead $lead, LeadService $leads)
    {
        $this->authorize('view', $lead);
        $template = $request->query('template') ? MessageTemplate::where('is_active', true)->findOrFail($request->query('template')) : null;
        $lead->loadMissing('projects');
        $text = $template?->textFor($lead, $request->user());

        $leads->log($lead, $request->user(), 'whatsapp', null, $template?->name ?? 'WhatsApp opened', $text);

        return redirect()->away('https://wa.me/'.$lead->country_code.$lead->phone.($text ? '?text='.rawurlencode($text) : ''));
    }

    // Admin screens

    public function index()
    {
        return view('crm.message-templates.index', ['templates' => MessageTemplate::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create()
    {
        return view('crm.message-templates.form', ['template' => new MessageTemplate(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        MessageTemplate::create($this->validated($request));

        return redirect()->route('crm.message-templates.index')->with('status', 'Message added.');
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('crm.message-templates.form', ['template' => $messageTemplate]);
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $messageTemplate->update($this->validated($request));

        return redirect()->route('crm.message-templates.index')->with('status', 'Message saved.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        return redirect()->route('crm.message-templates.index')->with('status', 'Message deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:1500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
