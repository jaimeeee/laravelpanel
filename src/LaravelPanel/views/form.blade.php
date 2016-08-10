@extends('panel::layout')

@section('content')
@if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Error!</strong> Some of the fields have missing or wrong data.
    </div>
@endif
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{{ $record ? trans('panel::global.edit_entity', ['entity' => $entity->name()]) : trans('panel::global.save_entity', ['entity' => $entity->name()]) }}</h3>
      </div>
      <div class="panel-body">
{!! $formCode !!}
      </div>
    </div>
@endsection
