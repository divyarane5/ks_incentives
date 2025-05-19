@extends('layouts.app')

@section('content')
<!-- Content -->
<script  type="text/javascript">
    	function onlyNumbers(evt) {
    var e = event || evt; // For trans-browser compatibility
    var charCode = e.which || e.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function NumToWord(inputNumber, outputControl) {
    var str = new String(inputNumber)
    var splt = str.split("");
    var rev = splt.reverse();
    var once = ['Zero', ' One', ' Two', ' Three', ' Four', ' Five', ' Six', ' Seven', ' Eight', ' Nine'];
    var twos = ['Ten', ' Eleven', ' Twelve', ' Thirteen', ' Fourteen', ' Fifteen', ' Sixteen', ' Seventeen', ' Eighteen', ' Nineteen'];
    var tens = ['', 'Ten', ' Twenty', ' Thirty', ' Forty', ' Fifty', ' Sixty', ' Seventy', ' Eighty', ' Ninety'];

    numLength = rev.length;
    var word = new Array();
    var j = 0;

    for (i = 0; i < numLength; i++) {
        switch (i) {

            case 0:
                if ((rev[i] == 0) || (rev[i + 1] == 1)) {
                    word[j] = '';
                }
                else {
                    word[j] = '' + once[rev[i]];
                }
                word[j] = word[j];
                break;

            case 1:
                aboveTens();
                break;

            case 2:
                if (rev[i] == 0) {
                    word[j] = '';
                }
                else if ((rev[i - 1] == 0) || (rev[i - 2] == 0)) {
                    word[j] = once[rev[i]] + " Hundred ";
                }
                else {
                    word[j] = once[rev[i]] + " Hundred and";
                }
                break;

            case 3:
                if (rev[i] == 0 || rev[i + 1] == 1) {
                    word[j] = '';
                }
                else {
                    word[j] = once[rev[i]];
                }
                if ((rev[i + 1] != 0) || (rev[i] > 0)) {
                    word[j] = word[j] + " Thousand";
                }
                break;

                
            case 4:
                aboveTens();
                break;

            case 5:
                if ((rev[i] == 0) || (rev[i + 1] == 1)) {
                    word[j] = '';
                }
                else {
                    word[j] = once[rev[i]];
                }
                if (rev[i + 1] !== '0' || rev[i] > '0') {
                    word[j] = word[j] + " Lakh";
                }
                 
                break;

            case 6:
                aboveTens();
                break;

            case 7:
                if ((rev[i] == 0) || (rev[i + 1] == 1)) {
                    word[j] = '';
                }
                else {
                    word[j] = once[rev[i]];
                }
                if (rev[i + 1] !== '0' || rev[i] > '0') {
                    word[j] = word[j] + " Crore";
                }                
                break;

            case 8:
                aboveTens();
                break;

            //            This is optional. 

            //            case 9:
            //                if ((rev[i] == 0) || (rev[i + 1] == 1)) {
            //                    word[j] = '';
            //                }
            //                else {
            //                    word[j] = once[rev[i]];
            //                }
            //                if (rev[i + 1] !== '0' || rev[i] > '0') {
            //                    word[j] = word[j] + " Arab";
            //                }
            //                break;

            //            case 10:
            //                aboveTens();
            //                break;

            default: break;
        }
        j++;
    }

    function aboveTens() {
        if (rev[i] == 0) { word[j] = ''; }
        else if (rev[i] == 1) { word[j] = twos[rev[i - 1]]; }
        else { word[j] = tens[rev[i]]; }
    }

    word.reverse();
    var finalOutput = '';
    for (i = 0; i < numLength; i++) {
        finalOutput = finalOutput + word[i];
    }
    document.getElementById(outputControl).innerHTML = finalOutput;
}
    </script>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('developer_ladder.index') }}" class="text-muted fw-light">AOP Ladder</a>/ Add AOP Ladder</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add AOP Ladder</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('developer_ladder.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('developer_ladder.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="developer_id">Developer<span class="start-mark">*</span></label>
                        <select id="developer_id" name="developer_id" class="" @error('developer_id') autofocus @enderror required>
                            <option value="">Select Developer</option>
                            @if (!empty($developers))
                                @foreach ($developers as $key => $developer)
                                    <option value="{{ $developer->id }}" {{ ($developer->id == old('developer_id') ? 'selected' : '') }}>{{ $developer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('developer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">AOP<span class="start-mark">*</span></label>
                        <!-- <input name="aop" class="form-control" id="aop" value="{{ old('aop') }}" required/> -->
                        <input id="aop" name="aop" class="form-control" type="text" onkeypress="return onlyNumbers(this.value);" onkeyup="NumToWord(this.value,'divDisplayWords');" maxlength="12"  />
                    <div id="divDisplayWords" style="font-size: 13; color: Teal; font-family: Arial;"></div>
                        @error('aop')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="ladder"> Ladder  (in %)<span class="start-mark">*</span></label>
                        <input name="ladder" class="form-control" id="ladder" value="{{ old('ladder') }}" required/>
                        @error('ladder')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="aop_s_date" class="form-label">AOP Start Date<span class="start-mark">*</span></label>
                        <input class="form-control" name="aop_s_date" type="date" value="{{ old('aop_s_date') }}" id="aop_s_date" name="aop_s_date" @error('aop_s_date') autofocus @enderror required />
                        @error('aop_s_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="aop_e_date" class="form-label">AOP End Date<span class="start-mark">*</span></label>
                        <input class="form-control" name="aop_e_date" type="date" value="{{ old('aop_e_date') }}" id="aop_e_date" name="aop_e_date" @error('aop_e_date') autofocus @enderror required />
                        @error('aop_e_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- / Content -->
@endsection
