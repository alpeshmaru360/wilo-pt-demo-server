
@extends('layouts.admin')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
        @if($part_id == 4)
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Atmos Giga"}}</h1>
        @elseif($part_id == 3)
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Scp Pump Assembly"}}</h1>
            
        {{-- A Code: 31-10-2025 Start --}}
        @elseif($part_id == 6)            
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Scpv Pump Assembly"}}</h1>
        {{-- A Code: 31-10-2025 End --}}

		@elseif($part_id == 5)
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Fire fighting"}}</h1>
        @elseif($part_id == 2)
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Control Panel"}}</h1>
        @else
            <h1 class="m-0 text-dark">{{ trans('OTP Margin For ') }}{{"Booster Set"}}</h1>
        @endif
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
        <form action="{{ route('admin.otp_margin_post') }}" method="POST">
              @csrf
              <input type="hidden" name="part_id" value="{{$part_id}}">
              @foreach($current_data as $cd)
              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
              <label for="name">{{ucfirst($cd->country)}}</label>
              <input type="text" id="{{$cd->country}}" name="{{$cd->country}}" class="form-control" value="{{$cd->value}}">
              </div>
              @endforeach   
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</section>
@endsection