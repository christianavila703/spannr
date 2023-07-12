@props(
    [
        'records',
        'routeName',
        'routeParameter' => 'slug',
        'contentParameter' => 'name',
    ]
)

@foreach ($records as $record)
    <a href="{{ route($routeName, $record->{$routeParameter}) }}">{{ $record->{$contentParameter} }}</a>
    @if (!$loop->last)
        @if ($loop->iteration == 3)
            and {{ $records->count() - $loop->iteration }} more
            @break
        @else
            /
        @endif
    @endif
@endforeach

