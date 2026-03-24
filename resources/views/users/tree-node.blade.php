<li>
    <div class="org-card" onclick="toggleNode({{ $user->id }})">

        <div class="card-inner">
            <div class="avatar"></div>
            <div>
                <div class="emp-name">{{ $user->name }}</div>
                <div class="emp-role">
                    {{ optional($user->designation)->name }}
                </div>
                <div class="emp-role">
                    Emp ID: {{ $user->employee_code }}
                </div>
            </div>
        </div>

        @if($user->subordinates->count())
            <div class="team-count">
                👥 {{ $user->subordinates->count() }}
            </div>
        @endif

    </div>

    @if($user->subordinates->count())
        <ul id="child-{{ $user->id }}" class="hidden">
            @foreach($user->subordinates as $child)
                @include('users.tree-node', ['user' => $child])
            @endforeach
        </ul>
    @endif
</li>