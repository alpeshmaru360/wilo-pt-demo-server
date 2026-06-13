@extends('layouts.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">  
            <h1 class="m-0 text-dark">{{$title}}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        </div>
        </div>
    </div>
</div>
    
<section class="content">
@include('layouts.message')

<div class="card-body">
        <form action="{{ route('admin.maintance_mode_post') }}" method="POST">
              @csrf
                    <input type="hidden" name="lable" value="{{$lable}}">
               <div class="form-group">
                   <label for="name">Current Status :
                    @if($maintance_mode == "0.0" || $maintance_mode == "0")
                    <input class="btn btn-danger" value="Off" style="width: 15% !important;">
                    @else
                    <input class="btn btn-success" value="On"  style="width: 15% !important;">
                    @endif
                   </label>
                </div>
            <div>
               <input type="radio" id = "maintance_mode_on" value="1" name="maintance_mode" {{$maintance_mode == '1'  ?  'checked' : ''  }} ">
               <label for="maintance_mode_off">On Maintenance Mode</label>
            <br>
               <input type="radio" id = "maintance_mode_on" value="0" name="maintance_mode" {{$maintance_mode != '1'  ?  'checked' : ''  }} ">
               <label for="maintance_mode_off">Off Maintenance Mode</label>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</section>
@endsection