@extends('layouts.public')

@section('title','Client Enquiry - Source')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.form-card { border-radius:10px; padding:25px; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px;}
.step-title{font-weight:600;border-left:4px solid #0d6efd;padding-left:10px;margin-bottom:12px}
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5 bg-light">
                    <h2 class="text-center mb-4 fw-bold">Client Enquiry — Source of Visit</h2>

                    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

                    <form method="POST" action="{{ route('client-enquiry.public.storeSource') }}">

                        @csrf

                        <div class="form-card">
                            <div class="step-title">Source of Visit</div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Source of Visit *</label>
                                    <select name="source_of_visit" id="source_of_visit" class="form-select @error('source_of_visit') is-invalid @enderror" required>
                                        <option value="">Select Source</option>
                                        <option value="Reference" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='Reference' ? 'selected':'' }}>Reference</option>
                                        <option value="Channel Partner" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='Channel Partner' ? 'selected':'' }}>Channel Partner</option>
                                        <option value="Website" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='Website' ? 'selected':'' }}>Website</option>
                                        <option value="News" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='News' ? 'selected':'' }}>News</option>
                                        <option value="Walk in" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='Walk in' ? 'selected':'' }}>Walk in</option>
                                        <option value="Other" {{ old('source_of_visit', $step2['source_of_visit'] ?? '')=='Other' ? 'selected':'' }}>Other</option>
                                    </select>
                                    @error('source_of_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Reference --}}
                                <div id="reference_section" style="display:none" class="col-md-6">
                                    <label class="form-label">Reference Name</label>
                                    <input name="reference_name" value="{{ old('reference_name', $step2['reference_name'] ?? '') }}" class="form-control">
                                </div>
                                <div id="reference_contact_section" style="display:none" class="col-md-6">
                                    <label class="form-label">Reference Contact</label>
                                    <input name="reference_contact" value="{{ old('reference_contact', $step2['reference_contact'] ?? '') }}" class="form-control">
                                </div>

                                {{-- Channel Partner --}}
                                <div id="channel_section" style="display:none" class="col-md-6">
                                    <label class="form-label">Channel Partner</label>
                                    <select name="channel_partner_id" class="form-select" id="channel_partner_id">
                                        <option value="">Select</option>

                                        @foreach($channelPartners as $partner)
                                            <option value="{{ $partner->id }}"
                                                {{ old('channel_partner_id', $step2['channel_partner_id'] ?? '') == $partner->id ? 'selected':'' }}>
                                                {{ $partner->firm_name }}
                                            </option>
                                        @endforeach

                                        {{-- ADD NEW --}}
                                        <option value="add_new">+ Add New Channel Partner</option>
                                    </select>

                                </div>

                                <div id="sourcing_manager_section" style="display:none" class="col-md-6">
                                    <label class="form-label">Sourcing Manager</label>
                                    <select name="sourcing_manager_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $m)
                                            <option value="{{ $m->id }}" {{ old('sourcing_manager_id', $step2['sourcing_manager_id'] ?? '') == $m->id ? 'selected':'' }}>{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Remarks --}}
                                <div id="remarks_section" style="display:none" class="col-md-12 mt-3">
                                    <label class="form-label">Remarks / Note</label>
                                    <textarea name="remarks" class="form-control">{{ old('remarks', $step2['remarks'] ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('client-enquiry.public.create') }}" class="btn btn-secondary">Previous</a>
                                <button type="submit" class="btn btn-primary">Submit Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Channel Partner Modal -->
<div class="modal fade" id="addChannelPartnerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="quickAddCPForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Channel Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Firm Name *</label>
                        <input type="text" name="firm_name" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const cpSelect = document.getElementById('channel_partner_id');

    cpSelect.addEventListener('change', function () {
        if (this.value === 'add_new') {
            this.value = '';
            new bootstrap.Modal(document.getElementById('addChannelPartnerModal')).show();
        }
    });

    document.getElementById('quickAddCPForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        fetch("{{ route('channel-partners.quick-store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Append new option
                const opt = document.createElement('option');
                opt.value = data.id;
                opt.textContent = data.firm_name;
                opt.selected = true;

                cpSelect.insertBefore(opt, cpSelect.lastElementChild);
                bootstrap.Modal.getInstance(
                    document.getElementById('addChannelPartnerModal')
                ).hide();

                form.reset();
            }
        })
        .catch(err => {
            alert('Failed to add Channel Partner');
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const source = document.getElementById('source_of_visit');
    const reference = document.getElementById('reference_section');
    const referenceContact = document.getElementById('reference_contact_section');
    const channel = document.getElementById('channel_section');
    const sourcing = document.getElementById('sourcing_manager_section');
    const remarks = document.getElementById('remarks_section');

    function toggle() {
        const v = source.value;
        reference.style.display = (v === 'Reference') ? 'block' : 'none';
        referenceContact.style.display = (v === 'Reference') ? 'block' : 'none';
        channel.style.display = (v === 'Channel Partner') ? 'block' : 'none';
        sourcing.style.display = (v === 'Channel Partner') ? 'block' : 'none';

        const remarkFor = ['Website','News','Walk in','Other'];
        remarks.style.display = remarkFor.includes(v) ? 'block' : 'none';
    }

    source.addEventListener('change', toggle);
    toggle(); // initial
});
</script>
@endpush

@endsection
