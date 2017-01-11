@extends('panel::layout')

@section('content')
@if (Request::get('deleted'))
    <div class="alert alert-info alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      {!! trans('panel::global.deleted_msg', ['entity' => strtolower($entity->name())]) !!}
    </div>
@endif
@if (Request::get('created'))
    <div class="alert alert-success alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      {!! trans('panel::global.created_msg', ['entity' => strtolower($entity->name())]) !!}
    </div>
@endif
@if (Request::get('updated'))
    <div class="alert alert-success alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      {!! trans('panel::global.updated_msg', ['entity' => strtolower($entity->name())]) !!}
    </div>
@endif
    <ul class="nav nav-pills">
@if (!isset($hideCreateRecord) || !$hideCreateRecord)
      <li>
@if (isset($parentEntity) && $parentRecord)
        <a href="{{ $parentEntity->url($parentRecord->id.'/'.$entity->url.'/create') }}">{{ trans('panel::global.new_entity', ['entity' => $entity->name()]) }}</a>
@else
        <a href="{{ $entity->url('create') }}">{{ trans('panel::global.new_entity', ['entity' => $entity->name()]) }}</a>
@endif
      </li>
@endif
    </ul>
    <table class="table table-striped">
      <thead>
        <tr>
@foreach ($rows as $property => $name)
          <th>{{ $name }}</th>
@endforeach
@if ($entity->children)
@foreach ($entity->children as $child)
          <th></th>
@endforeach
@endif
@if (in_array('edit', $actions))
          <th></th>
@endif
@if (in_array('delete', $actions))
          <th></th>
@endif
        </tr>
      </thead>
      <tbody>
@foreach ($records as $record)
        <tr>
@foreach ($rows as $property => $name)
<?php /* This part needs to fixed, obviously, it is not optimized */ ?>
<?php /* It needs to support inifinite parts, and each gets parsed individually */ ?>
@if ($parts = explode(':', $property))
@if (count($parts) > 1)
          <td>{{ $record->{$parts[0]}->{$parts[1]} }}</td>
@else
@if (substr($property, -1) == '!')
<?php $escapedProperty = rtrim($property, '!'); ?>
          <td>{!! $record->$escapedProperty !!}</td>
@else
          <td>{{ $record->$property }}</td>
@endif
@endif
@endif
<?php /* Ending of to be optimized part */ ?>
@endforeach
@if ($entity->children)
@foreach ($entity->children as $child)
          <td><a href="{{ $entity->url($record->id.'/'.$child->url) }}">{!! $child->icon ? '<i class="'.$child->icon.' fa-fw" aria-hidden="true"></i> ': '' !!}{{ $child->name(true) }}</a></td>
@endforeach
@endif
@if (in_array('edit', $actions))
@if (isset($parentEntity) && $parentRecord)
          <td style="text-align: right;"><a href="{{ $parentEntity->url($parentRecord->id.'/'.$entity->url.'/edit/'.$record->id) }}">{{ trans('panel::global.edit') }} <i class="fa fa-edit fa-fw" aria-hidden="true"></i></a></td>
@else
          <td style="text-align: right;"><a href="{{ $entity->url('edit/'.$record->id) }}">{{ trans('panel::global.edit') }} <i class="fa fa-edit fa-fw" aria-hidden="true"></i></a></td>
@endif
@endif
@if (in_array('delete', $actions))
          <td style="text-align: right;"><a href="#" data-toggle="modal" data-target="#delete-modal" data-id="{{ $record->id }}">{{ trans('panel::global.delete') }} <i class="fa fa-trash fa-fw" aria-hidden="true"></i></a></td>
@endif
        </tr>
@endforeach
      </tbody>
    </table>
@if (get_class($records) == 'Illuminate\Pagination\LengthAwarePaginator')
    {!! $records->render() !!}
@endif
@if (in_array('delete', $actions))
    <!-- Modal -->
    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="delete-modal-label">{{ trans('panel::global.delete_entity', ['entity' => $entity->name()]) }}</h4>
          </div>
          <div class="modal-body">
            {!! trans('panel::global.delete_entity_msg', ['entity' => strtolower($entity->name())]) !!}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('panel::global.cancel') }}</button>
            <a href="#" class="btn btn-danger modal-action">{{ trans('panel::global.confirm_delete', ['entity' => strtolower($entity->name())]) }}</a>
          </div>
        </div>
      </div>
    </div>
@endif
@endsection

@section('footer')
@if (in_array('delete', $actions))
  <script>
    $('#delete-modal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var recipient = button.data('id'); // Extract info from data-* attributes
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this);
@if (isset($parentEntity) && $parentRecord)
      modal.find('.modal-action').attr('href', '{{ $parentEntity->url() . '/' . $parentRecord->id . '/' . $entity->url }}/delete/' + recipient);
@else
      modal.find('.modal-action').attr('href', '{{ $entity->url() }}/delete/' + recipient);
@endif
    });
  </script>
@endif
@endsection
