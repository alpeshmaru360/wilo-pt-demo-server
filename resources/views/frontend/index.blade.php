@extends('frontend.layout.app')
@section('content')

@if(Auth::user())
<script>window.location = "{{route('main.home')}}";</script>
@endif
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center homeMidSection">
            <div class="left" data-aos="fade-right">
                <h2>WILO BRINGS <span>THE FUTURE.</span></h2>
                <p>All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. </p>
                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
            </div>
            <div class="right" data-aos="zoom-in">
                <img src="{{asset('fassets/images/home_product_img.png')}}" alt="">
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->
@endsection