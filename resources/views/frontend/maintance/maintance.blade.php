@extends('frontend.layout.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="formsMidSection">
                <h1>{{$title}}</h1>
                <h2>Under Maintance</h2>
                <h2 style="margin-bottom: 150px !important;">We'll Be Back Soon</h2>
                <div class="formWidget">
                    <ul id='errors'>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection






