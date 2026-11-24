@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-1"><span class="text-muted fw-light">KeyPeople /</span> Module access</h4>
    <p class="text-muted mb-4">
        Tick which RealtySuite modules each role can open. Inside a module, the usual permissions on
        <a href="{{ route('role.index') }}">Roles</a> (view, edit, approve ...) still decide what they can do.
        Superadmin always has everything.
    </p>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card mb-4">
        <h5 class="card-header">By role</h5>
        <form method="POST" action="{{ route('module-access.roles') }}">
            @csrf
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role</th>
                            @foreach ($modules as $key => $m)
                                <th class="text-center" title="{{ $m['about'] }}">{{ $m['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            @php($has = $role->permissions->pluck('name')->all())
                            @php($isSuper = $role->name === config('roles.SUPER_ADMIN'))
                            <tr>
                                <td>{{ $role->name }} <span class="text-muted small">({{ $role->users_count }})</span></td>
                                @foreach ($modules as $key => $m)
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" name="access[{{ $role->id }}][{{ $key }}]" value="1"
                                               @checked($isSuper || in_array('module-'.$key, $has, true)) @disabled($isSuper)>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body"><button class="btn btn-primary">Save role access</button></div>
        </form>
    </div>

    <div class="card">
        <h5 class="card-header">Extra modules for one person</h5>
        <div class="card-body">
            <p class="text-muted small">Adds modules on top of what the person's role gives. To take a module away from one person, change their role.</p>
            <form method="POST" action="{{ route('module-access.user') }}" class="row g-3 align-items-end mb-4">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Person</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Choose</option>
                        @foreach ($people as $p)<option value="{{ $p->id }}">{{ $p->name }}{{ $p->employee_code ? ' ('.$p->employee_code.')' : '' }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    @foreach ($modules as $key => $m)
                        <label class="form-check form-check-inline"><input type="checkbox" class="form-check-input" name="modules[]" value="{{ $key }}"> {{ $m['name'] }}</label>
                    @endforeach
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Save</button></div>
            </form>

            @if ($extras->isNotEmpty())
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Person</th><th>Role</th><th>Extra modules</th></tr></thead>
                    <tbody>
                        @foreach ($extras as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                                <td>{{ $u->permissions->filter(fn ($p) => str_starts_with($p->name, 'module-'))->map(fn ($p) => $modules[substr($p->name, 7)]['name'] ?? $p->name)->join(', ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-muted small mt-2">To remove someone's extras, choose them above with no boxes ticked and save.</p>
            @endif
        </div>
    </div>
</div>
@endsection
