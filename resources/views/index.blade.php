@extends('excon::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('excon.name') !!}</p>
@endsection
