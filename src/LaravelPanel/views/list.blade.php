@extends('panel::layout')

@section('content')
@if (Request::get('deleted'))
    <div class="alert alert-info alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      The {{ strtolower($entity->name) }} has been <strong>deleted</strong>.
    </div>
@endif
@if (Request::get('created'))
    <div class="alert alert-success alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      The {{ strtolower($entity->name) }} has been <strong>created</strong>!
    </div>
@endif
@if (Request::get('updated'))
    <div class="alert alert-success alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      The {{ strtolower($entity->name) }} has been <strong>updated</strong>!
    </div>
@endif
    <ul class="nav nav-pills">
@if (!isset($hideCreateRecord) || !$hideCreateRecord)
      <li>
        <a href="{{ $entity->url('create') }}">New {{ $entity->name }}</a>
      </li>
@endif
    </ul>
    <table class="table table-striped">
      <thead>
        <tr>
@foreach ($rows as $property => $name)
          <th>{{ $name }}</th>
@endforeach
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
@if (in_array('edit', $actions))
          <td style="text-align: right;"><a href="{{ $entity->url($record->id) }}">Edit <i class="fa fa-edit fa-fw" aria-hidden="true"></i></a></td>
@endif
@if (in_array('delete', $actions))
          <td style="text-align: right;"><a href="#" data-toggle="modal" data-target="#delete-modal" data-id="{{ $record->id }}">Delete <i class="fa fa-trash fa-fw" aria-hidden="true"></i></a></td>
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
            <h4 class="modal-title" id="delete-modal-label">Delete {{ $entity->name }}</h4>
          </div>
          <div class="modal-body">
            Are you sure you want to delete the <strong>{{ strtolower($entity->name) }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a href="#" class="btn btn-danger modal-action">Yes, delete {{ strtolower($entity->name) }}</a>
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
      modal.find('.modal-action').attr('href', '{{ $entity->url() }}/' + recipient + '/delete');
    });
  </script>
@endif
@endsection
