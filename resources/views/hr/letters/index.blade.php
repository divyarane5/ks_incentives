@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<style>
    .hr-signed { background: #f6f8fb; border-radius: 10px; padding: 10px 12px; margin: -4px 0 10px; }
    .hr-sig-thumb { width: 86px; height: 46px; background: #fff; border: 1px solid var(--kp-line); border-radius: 8px; position: relative; flex-shrink: 0; display: flex; align-items: center; padding: 3px; }
    .hr-sig-thumb img { max-width: 60px; max-height: 38px; }
    .hr-sig-thumb img.st { position: absolute; right: 3px; top: 3px; max-width: 34px; max-height: 38px; opacity: .85; }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1300px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <span>Letters</span></div>
    <div class="hr-head">
        <div><h4>Letters</h4><div class="sub">Offer, appointment, confirmation, increment, relieving and experience letters.</div></div>
        <a href="{{ route('letters.create', request()->only('user_id')) }}" class="btn btn-primary"><i class="bx bx-file-blank"></i> Issue a letter</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-envelope"></i>Issued letters</h2>
                    <form method="GET" style="min-width:200px">
                        <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()" data-live-search="true">
                            <option value="">Everyone</option>
                            @foreach($people as $p)<option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->status !== 'Active' ? ' (left)' : '' }}</option>@endforeach
                        </select>
                    </form>
                </div>
                <div class="kp-panel-b">
                    @forelse($letters as $l)
                        <ul class="kp-list"><li>
                            <div class="ic"><i class="bx bx-file"></i></div>
                            <div class="bd">
                                <div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div>
                                <div class="m"><a href="{{ route('users.show', $l->user_id) }}">{{ $l->user?->name }}</a> · {{ $l->letter_date->format('d M Y') }} · {{ $l->reference }}</div>
                                <div class="m">Issued by {{ $l->issuer?->name ?? '—' }}</div>
                            </div>
                            <div class="r d-flex gap-1 flex-wrap justify-content-end">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('letters.pdf', $l) }}">PDF</a>
                                <button type="button" class="btn btn-sm {{ $l->signed_pdf ? 'btn-success' : 'btn-outline-secondary' }}" data-bs-toggle="collapse" data-bs-target="#signed{{ $l->id }}" title="Signed with the USB token (DSC)">
                                    <i class="bx {{ $l->signed_pdf ? 'bx-check-shield' : 'bx-shield' }}"></i>
                                </button>
                                <form method="POST" action="{{ route('letters.destroy', $l) }}" onsubmit="return confirm('Remove this letter? The person will no longer see it.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
                            </div>
                        </li></ul>
                        <div class="collapse" id="signed{{ $l->id }}">
                            <div class="hr-signed">
                                @if($l->signed_pdf)
                                    <div class="small mb-2"><i class="bx bx-check-shield text-success"></i> The person sees the copy signed with the token.
                                        <a target="_blank" href="{{ route('letters.pdf', ['letter' => $l, 'unsigned' => 1]) }}">App copy</a></div>
                                @else
                                    <div class="small mb-2">Signing with the USB token (DSC)? <a href="{{ route('letters.pdf', ['letter' => $l, 'download' => 1, 'unsigned' => 1]) }}">Download the unsigned PDF</a>, sign it on your computer (Adobe Reader or emSigner), then upload it here.</div>
                                @endif
                                <form method="POST" action="{{ route('letters.signed', $l) }}" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap align-items-center">
                                    @csrf
                                    <input type="file" name="signed_pdf" accept="application/pdf" class="form-control form-control-sm" style="max-width:260px" required>
                                    <button class="btn btn-sm btn-primary">{{ $l->signed_pdf ? 'Replace' : 'Upload signed PDF' }}</button>
                                </form>
                                @if($l->signed_pdf)
                                    <form method="POST" action="{{ route('letters.signed', $l) }}" class="mt-1">@csrf<input type="hidden" name="remove" value="1"><button class="btn btn-link btn-sm p-0 text-danger">Remove signed copy</button></form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="kp-empty">No letters issued yet.</div>
                    @endforelse
                    <div class="mt-2">{{ $letters->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-shield-quarter"></i>Signing with the DSC token</h2></div>
                <div class="kp-panel-b" style="font-size:.85rem">
                    <ol class="mb-0 ps-3">
                        <li>Issue the letter and download its PDF.</li>
                        <li>Open it in Adobe Reader with the token plugged in: All tools &gt; Use a certificate &gt; Digitally sign, and draw the box in the empty space under "For …".</li>
                        <li>Upload the signed copy on that letter here. Employees then see the signed version.</li>
                    </ol>
                </div>
            </div>
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-layout"></i>Templates</h2><a href="{{ route('letters.template.edit') }}" class="btn btn-sm btn-outline-primary">New</a></div>
                <div class="kp-panel-b">
                    <ul class="kp-list">
                        @foreach($templates as $t)
                            <li>
                                <div class="bd">
                                    <div class="t">{{ $t->name }} @unless($t->active)<span class="hr-status cancelled">Not in use</span>@endunless</div>
                                    <div class="m">{{ \App\Models\LetterTemplate::TYPES[$t->type] ?? $t->type }} · {{ $t->subject }}</div>
                                </div>
                                <div class="r"><a href="{{ route('letters.template.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Edit</a></div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
