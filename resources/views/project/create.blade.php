@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('project.index') }}" class="text-muted fw-light">Project</a> / Add Project
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Project</h5>
            <a class="btn btn-primary" href="{{ route('project.index') }}">Back</a>
        </div>

        <form action="{{ route('project.store') }}" method="POST">
            @csrf
            <div class="card-body">

                {{-- Project Basic Info --}}
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Project Name *</label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required />
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Developer *</label>
                        <select name="developer_id" class="form-control" required>
                            <option value="">Select Developer</option>
                            @foreach($developers as $developer)
                                <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                {{-- Ladder Section --}}
                <h5>Project Ladder</h5>

                <table class="table table-bordered" id="ladderTable">
                    <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Min Deals</th>
                            <th>Max Deals</th>
                            <th>Brokerage %</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="date" name="ladders[0][project_s_date]" class="form-control" required></td>
                            <td><input type="date" name="ladders[0][project_e_date]" class="form-control" required></td>
                            <td><input type="number" name="ladders[0][s_booking]" class="form-control" required></td>
                            <td><input type="number" name="ladders[0][e_booking]" class="form-control" required></td>
                            <td><input type="text" name="ladders[0][ladder]" class="form-control" required></td>
                            <td>
                                <button type="button" class="btn btn-success addRow">+</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Submit</button>

            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 1;

document.addEventListener('click', function(e) {

    if(e.target.classList.contains('addRow')) {
        let table = document.querySelector('#ladderTable tbody');

        let newRow = `
            <tr>
                <td><input type="date" name="ladders[${rowIndex}][project_s_date]" class="form-control" required></td>
                <td><input type="date" name="ladders[${rowIndex}][project_e_date]" class="form-control" required></td>
                <td><input type="number" name="ladders[${rowIndex}][s_booking]" class="form-control" required></td>
                <td><input type="number" name="ladders[${rowIndex}][e_booking]" class="form-control" required></td>
                <td><input type="text" name="ladders[${rowIndex}][ladder]" class="form-control" required></td>
                <td>
                    <button type="button" class="btn btn-danger removeRow">-</button>
                </td>
            </tr>
        `;
        table.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    }

    if(e.target.classList.contains('removeRow')) {
        e.target.closest('tr').remove();
    }

});
</script>
@endsection