<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\CelebrationPost;
use App\Models\User;
use App\Services\CelebrationService;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** HR makes birthday / work anniversary posts (3 designs each) that show on everyone's Home page. */
class CelebrationController extends Controller
{
    public function index(Request $request)
    {
        $this->hrOnly($request);
        $type = in_array($request->get('type'), ['birthday', 'anniversary'], true) ? $request->get('type') : null;
        $items = CelebrationService::upcoming(30)->when($type, fn ($c) => $c->where('type', $type))->values();
        $posts = CelebrationPost::with('user')->where('post_date', '>=', now()->subDays(30)->toDateString())
            ->orderByDesc('post_date')->orderByDesc('id')->get();
        $posted = $posts->keyBy(fn ($p) => $p->user_id.'|'.$p->type.'|'.$p->post_date->toDateString());

        return view('hr.celebrations.index', compact('items', 'posts', 'posted', 'type'));
    }

    /** Pick a design, edit the message, then post or download. */
    public function make(Request $request)
    {
        $this->hrOnly($request);
        $type = $request->get('type') === 'anniversary' ? 'anniversary' : 'birthday';
        $person = User::findOrFail($request->get('user_id'));
        $date = $request->filled('date') ? Carbon::parse($request->get('date')) : null;
        if (! $date) {
            $raw = $type === 'birthday' ? $person->dob : $person->joining_date;
            $date = $raw ? CelebrationService::nextOccurrence(Carbon::parse($raw), now()->startOfDay()) : now()->startOfDay();
        }
        $years = null;
        if ($type === 'anniversary' && $person->joining_date) {
            $years = max(1, $date->year - Carbon::parse($person->joining_date)->year);
        }
        $existing = CelebrationPost::where('user_id', $person->id)->where('type', $type)->whereDate('post_date', $date)->first();
        $design = (int) ($existing->design ?? $request->get('design', 4));
        $message = $existing->message ?? null;
        $cards = [];
        foreach (array_keys(CelebrationService::DESIGNS[$type]) as $d) {
            $cards[$d] = CelebrationService::card($type, $d, $person, $years, $message, $date, $existing->photo ?? null, $existing->photo_pos ?? null);
        }

        return view('hr.celebrations.make', compact('type', 'person', 'date', 'years', 'design', 'cards', 'existing'));
    }

    public function store(Request $request)
    {
        $me = $this->hrOnly($request);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:birthday,anniversary',
            'design' => 'required|integer|between:1,4',
            'years' => 'nullable|integer|min:1|max:80',
            'message' => 'nullable|string|max:400',
            'post_date' => 'required|date',
            'photo' => 'nullable|image|max:8192',
            'photo_pos' => ['nullable', 'regex:/^[0-9.]+,[0-9.]+,[0-9.]+$/'],
        ]);
        $post = CelebrationPost::firstOrNew(['user_id' => $data['user_id'], 'type' => $data['type'], 'post_date' => Carbon::parse($data['post_date'])->toDateString()]);
        $post->fill(['design' => $data['design'], 'years' => $data['years'] ?? null, 'message' => $data['message'] ?? null, 'posted_by' => $me->id]);
        $hasColumn = Schema::hasColumn('celebration_posts', 'photo');
        if ($hasColumn && ($request->hasFile('photo') || $request->boolean('remove_photo'))) {
            if ($post->photo) {
                Storage::disk('public')->delete($post->photo);
            }
            $post->photo = $request->hasFile('photo') ? $request->file('photo')->store('celebrations', 'public') : null;
        }
        if (Schema::hasColumn('celebration_posts', 'photo_pos')) {
            $post->photo_pos = $data['photo_pos'] ?? null;
        }
        $post->save();
        $when = Carbon::parse($data['post_date'])->isToday() ? 'It is on the Home page now.' : 'It will show on the Home page on '.Carbon::parse($data['post_date'])->format('d M').'.';

        return redirect()->route('celebrations.index')->with('success', 'Post saved. '.$when);
    }

    public function destroy(Request $request, CelebrationPost $post)
    {
        $this->hrOnly($request);
        if ($post->photo) {
            Storage::disk('public')->delete($post->photo);
        }
        $post->delete();

        return back()->with('success', 'Post removed from the Home page.');
    }

    private function hrOnly(Request $request): User
    {
        $me = $request->user();
        abort_unless(LeaveService::isHr($me), 403);

        return $me;
    }
}
