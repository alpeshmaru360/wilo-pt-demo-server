@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<style>
  .button {
      font-size:17px;
      margin-top:20px;
      display: block;
      width: 228px;
      height: 49px;
      background: #169e88;
      padding: 10px;
      text-align: center;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      line-height: 25px;
  }
  .button:hover{
    text-decoration:none;
    color:white;
  }
  .text-primary {
    color: #169e88 !important;
  }
  .bg-primary {
    background: #169e88 !important;
  }
  a.text-primary:focus, a.text-primary:hover {
    color: #0baa91 !important;
    text-decoration: none;
  }
  .blink_me {
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }
</style>

<section class="content">
@include('layouts.message')
</section>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">All Quotation Logs</h1>
            </div>
            <div class="col-sm-6">
            </div>
        </div>
    </div>
</div>

<div class="row p-0 m-0">
    {{-- @dd($files, $path, $url) --}}
    @foreach($files as $file)
        <div class="col-12 py-2 px-5 d-flex justify-content-between">
                <a href="{{ $url . '/' . $file }}" class="text-primary"><i class="fa fa-file-excel"></i>&nbsp;{{ $file }}</a>
                <a href="{{ url('admin/all-quotation-list') }}?quotation={{ base64_encode($path . '/' . $file) }}" class="text-danger"><i class="fa fa-trash"></i>&nbsp;</a>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')    
@endsection