@if(! empty($signatory) && $signImg)
    <div class="signed">
        <img class="sig" src="{{ $signImg }}" alt="">
        @if($stampImg)<img class="stamp" src="{{ $stampImg }}" alt="">@endif
    </div>
@else
    {{-- Left blank: sign here with the DSC token (Adobe Reader > Use a certificate > Digitally sign). --}}
    <div class="dsc-space"></div>
@endif
