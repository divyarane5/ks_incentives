@extends('crm.layouts.app', ['title' => 'WhatsApp messages', 'back' => route('crm.team.index')])
@section('content')
<div class="mb-3 flex items-start justify-between gap-3">
    <p class="text-sm text-slate-500">Ready-made messages your team can send from any lead. They open in WhatsApp with the lead's name and project filled in.</p>
    <a href="{{ route('crm.message-templates.create') }}" class="btn shrink-0">+ Message</a>
</div>
<div class="space-y-2">
    @foreach($templates as $t)
        <a href="{{ route('crm.message-templates.edit', $t) }}" class="card block p-3 {{ $t->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-center justify-between gap-2">
                <div class="font-semibold">{{ $t->name }}</div>
                @unless($t->is_active)<span class="chip bg-slate-200 text-slate-600">Off</span>@endunless
            </div>
            <div class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $t->body }}</div>
        </a>
    @endforeach
</div>
@endsection
