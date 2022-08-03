@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))
@section('content')
    Sorry, you're looking for something that isn't here.
@endsection
