@extends('crm.layouts.app', ['title' => $template->exists ? $template->name : 'New message', 'back' => route('crm.message-templates.index')])
@section('content')
<form method="POST" action="{{ $template->exists ? route('crm.message-templates.update', $template) : route('crm.message-templates.store') }}" class="card space-y-3">
    @csrf
    @if($template->exists) @method('PUT') @endif
    <div>
        <label class="label" for="name">Name</label>
        <input id="name" name="name" class="input" required value="{{ old('name', $template->name) }}" placeholder="e.g. Brochure">
    </div>
    <div>
        <label class="label" for="body">Message</label>
        <textarea id="body" name="body" rows="5" class="input" required>{{ old('body', $template->body) }}</textarea>
        <div class="mt-2 text-xs text-slate-500">
            These words are filled in automatically:
            <ul class="mt-1 grid gap-0.5 sm:grid-cols-2">
                @foreach(\App\Models\MessageTemplate::PLACEHOLDERS as $code => $label)
                    <li><code class="font-mono text-brand-700">{{ $code }}</code> {{ strtolower($label) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="label" for="sort_order">Order in the list</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="input" value="{{ old('sort_order', $template->sort_order ?? 0) }}">
        </div>
        <label class="flex items-center gap-3 self-end pb-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300" @checked(old('is_active', $template->is_active))>
            <span>Show this message to the team</span>
        </label>
    </div>
    <button class="btn w-full sm:w-auto">{{ $template->exists ? 'Save' : 'Add message' }}</button>
</form>
@if($template->exists)
    <form method="POST" action="{{ route('crm.message-templates.destroy', $template) }}" class="mt-3" onsubmit="return confirm('Delete this message?')">
        @csrf @method('DELETE')
        <button class="text-sm font-medium text-red-600">Delete message</button>
    </form>
@endif
@endsection
