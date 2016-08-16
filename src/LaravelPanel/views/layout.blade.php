<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>{{ isset($title) ? $title . ' | ' : '' }}{{ config('panel.title') }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('/css/panel.css') }}">
@yield('header')
</head>
<body>
  <div class="navbar navbar-default navbar-fixed-top" role="menu">
    <div class="navbar-header">
      <a class="navbar-brand" href="{{ url('/') }}">{!! config('panel.title_prepend') !!} {{ config('panel.title') }}</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="{{ url('/logout') }}"><i class="fa fa-sign-out" aria-hidden="true"></i> {{ trans('panel::global.logout') }}</a></li>
      </ul>
    </div>
  </div>
  <div id="wrapper">
    <div class="sidebar" role="navigation">
      <ul class="nav nav-sidebar primary">
        <li{!! Request::is(ltrim(config('panel.url'), '/')) ? ' class="active"' : null !!}><a href="{{ url(config('panel.url')) }}"><i class="fa fa-tachometer fa-fw" aria-hidden="true"></i> {{ trans('panel::global.dashboard') }}</a></li>
      </ul>
      <ul class="nav nav-sidebar">
@foreach ($list as $entity)
@if (!$entity->hidden)
        <li{!! Request::is(ltrim(config('panel.url'), '/') . '/' . $entity->url . '*') ? ' class="active"' : null !!}><a href="{{ $entity->url() }}"><i class="{{ $entity->icon }} fa-fw" aria-hidden="true"></i> {{ $entity->name(true) }}</a></li>
@endif
@endforeach
      </ul>
    </div>
    <div class="main">
@if(isset($title))
      <h2>{{ $title }}{{ isset($subtitle) ? ': ' . $subtitle : '' }}</h2>
@endif
      <div class="content">
        <div class="row">
@yield('content')
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.4.0/tinymce.min.js"></script>
  <script>
  tinymce.init({
    selector: 'textarea.rich',
    menubar: false,
    statusbar: false,
    plugins: "code,paste,image,media,link,visualblocks,textcolor,colorpicker",
    toolbar: "undo redo | forecolor removeformat | bold italic | alignleft aligncenter alignright alignjustify | image media link | bullist numlist outdent indent | visualblocks code",
    relative_urls: false,
    image_advtab: true
  });
  </script>
@yield('footer')
</body>
</html>
