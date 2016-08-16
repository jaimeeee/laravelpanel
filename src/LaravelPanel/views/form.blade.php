@extends('panel::layout')

@section('header')
{!! $header !!}
@endsection

@section('content')
@if (count($errors) > 0)
    <div class="alert alert-danger">
      {!! trans('panel::global.missing_error') !!}
    </div>
@endif
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{{ $panel }}</h3>
      </div>
      <div class="panel-body">
{!! $formCode !!}
      </div>
    </div>
@endsection

@section('footer')
{!! $footer !!}
@endsection
