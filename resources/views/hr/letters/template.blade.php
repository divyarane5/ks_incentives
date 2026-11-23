@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1200px">
    <div class="kp-crumb"><a href="{{ route('letters.index') }}">Letters</a> <i class="bx bx-chevron-right"></i> <span>{{ $template->exists ? 'Edit template' : 'New template' }}</span></div>
    <h4 class="mb-3">{{ $template->exists ? $template->name : 'New template' }}</h4>
    @if($errors->any())<div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    <form method="POST" action="{{ route('letters.template.save', $template->exists ? $template : null) }}" class="hr-form">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="kp-panel"><div class="kp-panel-b row g-3" style="padding:14px">
                    <div class="col-12 col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $template->name) }}" required></div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Kind</label>
                        <select name="type" class="form-select raw-select">
                            @foreach(\App\Models\LetterTemplate::TYPES as $k => $v)<option value="{{ $k }}" {{ old('type', $template->type) === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex align-items-end">
                        <label class="d-flex align-items-center gap-2 mb-2"><input type="hidden" name="active" value="0"><input type="checkbox" class="form-check-input m-0" name="active" value="1" {{ old('active', $template->active ?? true) ? 'checked' : '' }}> In use</label>
                    </div>
                    <div class="col-12"><label class="form-label">Subject</label><input name="subject" id="subject" class="form-control" value="{{ old('subject', $template->subject) }}"></div>
                    <div class="col-12">
                        <label class="form-label">Letter text</label>
                        <textarea name="body" id="body" class="form-control" rows="18" style="font-family:inherit;line-height:1.5" required>{{ old('body', $template->body) }}</textarea>
                        <div class="form-text">Leave an empty line between paragraphs. Start a line with "- " for a bullet. Put **two stars** around words for bold. Date, address and signature are added automatically.</div>
                    </div>
                </div></div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="kp-panel">
                    <div class="kp-panel-h"><h2><i class="bx bx-code-curly"></i>Fill-ins</h2><span class="kp-note">tap to insert</span></div>
                    <div class="kp-panel-b">
                        <ul class="kp-list">
                            @foreach($fields as $code => $label)
                                <li style="padding:6px 0;cursor:pointer" onclick="insertField('{{ $code }}')"><div class="bd"><div class="t" style="font-family:monospace;font-size:.85rem">{{ $code }}</div><div class="m">{{ $label }}</div></div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="kp-savebar">
            <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary"><i class="bx bx-save"></i> Save template</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
var lastField = document.getElementById('body');
['subject', 'body'].forEach(function (id) { document.getElementById(id).addEventListener('focus', function () { lastField = this; }); });
function insertField(code) {
    var el = lastField, s = el.selectionStart || el.value.length, e = el.selectionEnd || s;
    el.value = el.value.slice(0, s) + code + el.value.slice(e);
    el.focus(); el.selectionStart = el.selectionEnd = s + code.length;
}
</script>
@endsection
