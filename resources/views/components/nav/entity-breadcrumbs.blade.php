@foreach($breadcrumbs as $breadcrumb)
    @if(isset($breadcrumb['routeName']))
        <a href="{{ route($breadcrumb['routeName'], $breadcrumb['routeParameter']) }}">{{ $breadcrumb['label'] }}</a><i class="fa-light fa-angle-right mx-2"></i>
    @else
        {{ $breadcrumb['label'] }}
    @endif
@endforeach
