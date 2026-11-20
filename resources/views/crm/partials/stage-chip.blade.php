@php($color = $lead->stage?->color ?? '#64748b')
<span class="chip" style="background: {{ $color }}1f; color: {{ $color }}">
    {{ $lead->stage?->name }}@if($lead->subStage) · {{ $lead->subStage->name }}@endif
</span>
