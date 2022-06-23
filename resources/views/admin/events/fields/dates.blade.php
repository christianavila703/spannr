@if($event->end_date)
    <span>{{ Carbon\Carbon::parse($event->start_date)->format('D M j') }}</span>
    <span class="mx-1">&ndash;</span>
    <span>{{ Carbon\Carbon::parse($event->end_date)->format('M j, Y') }}</span>
@else
    <span>{{ Carbon\Carbon::parse($event->start_date)->format('D M j, Y') }}</span>
@endif
