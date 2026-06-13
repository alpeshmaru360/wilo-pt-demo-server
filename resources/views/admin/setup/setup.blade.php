@extends('layouts.admin')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ trans('Setup') }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
    
<!-- Main content -->
<section class="content">
@include('layouts.message')
<div class="card-body">
        <form action="{{ route("admin.setup_post") }}" method="POST">
              @csrf

              @foreach($current_data as $cd)
              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{$cd->label}}</label>
                <input type="text" id="{{$cd->name}}" name="{{$cd->name}}" class="form-control" value="{{$cd->value}}">
              </div>
              @endforeach
            
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>


</section>

@endsection