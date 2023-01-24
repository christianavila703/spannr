@props(['parentUrl', 'parentLabel', 'currentLabel'])
<div class="breadcrumbs">
    <a href="{{ route('home') }}" class="home">Spannr</a>
    <span class="connector">/</span>
    <a href="{{ $parentUrl }}" class="parent">{{ $parentLabel }}</a>
    <span class="connector">/</span>
    <span class="current">{!! $currentLabel !!}</span>
</div>
