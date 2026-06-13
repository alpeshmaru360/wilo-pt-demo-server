@extends('layouts.admin')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ trans('Select Part to mange tooltips.') }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            @include('layouts.breadcrumbs')
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
    
<!-- Main content -->
<section class="content">
@include('layouts.message')

<a href="{{ route('admin.booster_set') }}">Booster Set</a><br>
<a href="{{ route('admin.control_panel') }}">Control Panel</a><br>
<a href="{{ route('admin.scp_pumps') }}">Scp Pump Assembly</a><br>
<!-- A Code: 06-11-2026 Start -->
<a href="{{ route('admin.scpv_pumps') }}">Scpv Pump Assembly</a><br>
<!-- A Code: 06-11-2026 End -->
<a href="{{ route('admin.atmos_giga') }}">Atmos Giga</a><br>


</section>

@endsection