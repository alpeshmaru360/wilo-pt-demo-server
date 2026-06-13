@extends('layouts.admin')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ trans('Add tooltips for Control Panel.') }}</h1>
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
        <form action="{{ route("admin.save_control_panel_tool_tip") }}" method="POST">
              @csrf
              @foreach($current_data as $cd)

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ucfirst(str_replace("_"," ",$cd->component_name))}}</label>
                <input type="text" id="{{$cd->component_name}}" name="{{$cd->component_name}}" class="form-control" value="{{$cd->tool_tip}}">
                </div>

              @endforeach
              
            
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>


</section>

@endsection