@if($enquiry->updates->isEmpty())
    <p class="text-muted text-center">No history available.</p>
@else
    @foreach($enquiry->updates as $update)
        <div class="border rounded p-2 mb-2 bg-light">
            <strong>Status:</strong>
            {{ ucfirst(str_replace('_',' ',$update->status)) }} <br>

            <strong>Feedback:</strong>
            {{ $update->feedback }} <br>

            <small class="text-muted">
                {{ $update->created_at->format('d M Y, h:i A') }}
            </small>
        </div>
    @endforeach
@endif
