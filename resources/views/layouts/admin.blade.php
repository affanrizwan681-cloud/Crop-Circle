@extends('layouts.app')

@section('content')
<div class="container-fluid portal-shell">
  <div class="row">
    @include('layouts.partials.admin-sidebar')
    <div class="col-12 col-md-9 col-lg-10 portal-main px-md-4">
      @yield('admin-content')
    </div>
  </div>
</div>
@endsection
