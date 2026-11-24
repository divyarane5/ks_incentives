@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<style>
    .sig-drop { border: 2px dashed var(--kp-line); border-radius: 12px; background: #fff; min-height: 120px; display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer; padding: 10px; text-align: center; color: var(--kp-muted); font-size: .85rem; }
    .sig-drop img { max-width: 100%; max-height: 110px; }
    .sig-drop input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .sig-paper { background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; padding: 18px 20px; font-size: .9rem; color: #1f2a3d; }
    .sig-paper .area { position: relative; height: 90px; margin-top: 6px; }
    .sig-paper .area .s { position: absolute; left: 0; top: 14px; height: 66px; max-width: 220px; }
    .sig-paper .area .t { position: absolute; left: 130px; top: -4px; height: 90px; max-width: 130px; opacity: .9; }
    .sig-paper .who { border-top: 1px solid #1f2a3d; width: 220px; padding-top: 4px; font-size: .82rem; }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:900px">
    <div class="kp-crumb"><a href="{{ route('letters.index') }}">Letters</a> <i class="bx bx-chevron-right"></i> <span>Signature and stamp</span></div>
    <h4 class="mb-3">{{ $signatory->exists ? 'Edit signature' : 'Add a signature' }}</h4>
    @if($errors->any())<div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    <form method="POST" action="{{ route('letters.signatory.save', $signatory->exists ? $signatory : null) }}" enctype="multipart/form-data" class="hr-form">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="kp-panel"><div class="kp-panel-b" style="padding:14px">
                    <div class="mb-3"><label class="form-label">Signed by (name)</label><input name="name" class="form-control" value="{{ old('name', $signatory->name) }}" required id="f-name"></div>
                    <div class="mb-3"><label class="form-label">Designation</label><input name="designation" class="form-control" value="{{ old('designation', $signatory->designation) }}" placeholder="e.g. Director" id="f-desig"></div>
                    <div class="mb-3">
                        <label class="form-label">For company</label>
                        <input name="company" class="form-control" list="companies" value="{{ old('company', $signatory->company) }}" placeholder="Leave empty for all companies">
                        <datalist id="companies">@foreach($companies as $co)<option value="{{ $co }}">@endforeach</datalist>
                        <div class="form-text">Letters for people of this company use this signature. Leave empty to use it for everyone.</div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Signature</label>
                            <label class="sig-drop" id="d-sig">
                                @if($signatory->url('signature'))<img src="{{ $signatory->url('signature') }}" alt="">@else<span><i class="bx bx-pen fs-3 d-block"></i>Photo or scan of the signature</span>@endif
                                <input type="file" name="signature" accept="image/*" {{ $signatory->exists ? '' : 'required' }}>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Company stamp (optional)</label>
                            <label class="sig-drop" id="d-stamp">
                                @if($signatory->url('stamp'))<img src="{{ $signatory->url('stamp') }}" alt="">@else<span><i class="bx bx-badge-check fs-3 d-block"></i>Photo or scan of the stamp</span>@endif
                                <input type="file" name="stamp" accept="image/*">
                            </label>
                            @if($signatory->stamp)<label class="small mt-1"><input type="checkbox" name="remove_stamp" value="1"> Remove stamp</label>@endif
                        </div>
                    </div>
                    <div class="form-text mb-3">Sign on plain white paper and take a clear photo, or use a scan. The white background is removed automatically. A PNG with a see-through background works too.</div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_default" value="1" id="f-def" @checked(old('is_default', $signatory->is_default || ! $signatory->exists))><label class="form-check-label" for="f-def">Default signature (used when no company matches)</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="active" value="1" id="f-act" @checked(old('active', $signatory->active ?? true))><label class="form-check-label" for="f-act">In use</label></div>
                </div></div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-label">How it looks on the letter</div>
                <div class="sig-paper">
                    <div>For <b>{{ $signatory->company ?: 'Keystone Real Estate Advisory Pvt. Ltd.' }}</b></div>
                    <div class="area">
                        <img class="s" id="p-sig" src="{{ $signatory->url('signature') }}" alt="" style="{{ $signatory->url('signature') ? '' : 'display:none' }}">
                        <img class="t" id="p-stamp" src="{{ $signatory->url('stamp') }}" alt="" style="{{ $signatory->url('stamp') ? '' : 'display:none' }}">
                    </div>
                    <div class="who"><b id="p-name">{{ $signatory->name ?: 'Name' }}</b><br><span id="p-desig">{{ $signatory->designation ?: 'Designation' }}</span><br>Authorised signatory</div>
                </div>
                <div class="alert alert-info small mt-3 mb-0">
                    <b>Have a USB signing token (DSC)?</b> The app can't use the token directly. Issue the letter with "No signature", download the unsigned PDF, sign it on your computer with the token (Adobe Reader or emSigner), and upload the signed PDF on the Letters page. The person then gets the token-signed copy.
                </div>
            </div>
        </div>
        <div class="kp-savebar">
            @if($signatory->exists)
                <button type="submit" form="del-sig" class="btn btn-outline-danger me-auto">Turn off</button>
            @endif
            <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary"><i class="bx bx-check"></i> Save</button>
        </div>
    </form>
    @if($signatory->exists)
        <form id="del-sig" method="POST" action="{{ route('letters.signatory.delete', $signatory) }}" onsubmit="return confirm('Stop using this signature on new letters?')">@csrf @method('DELETE')</form>
    @endif
</div>
<script>
(function () {
    [['d-sig', 'p-sig'], ['d-stamp', 'p-stamp']].forEach(function (pair) {
        var box = document.getElementById(pair[0]), input = box.querySelector('input'), prev = document.getElementById(pair[1]);
        input.addEventListener('change', function () {
            if (!input.files[0]) return;
            var url = URL.createObjectURL(input.files[0]);
            var img = box.querySelector('img') || box.insertBefore(document.createElement('img'), input);
            var span = box.querySelector('span'); if (span) span.remove();
            img.src = url; prev.src = url; prev.style.display = '';
        });
    });
    var bind = function (from, to, empty) { var f = document.getElementById(from); f.addEventListener('input', function () { document.getElementById(to).textContent = f.value || empty; }); };
    bind('f-name', 'p-name', 'Name'); bind('f-desig', 'p-desig', 'Designation');
})();
</script>
@endsection
