<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Wilo</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="{{asset('fassets/css/css2.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('fassets/css/main.css')}}">
<link rel="stylesheet" href="{{asset('fassets/css/aos.css')}}">
<script src="{{asset('fassets/js/jquery.min.js')}}"></script>
<link href="{{asset('fassets/css/css3.css')}}" rel="stylesheet" />
<script src="{{asset('fassets/js/select2.min.js')}}"></script>
@yield('style')

</head>
<body class="homePage">

    <div class="canvas_top_right">
        <span class="size size1"></span>
        <span class="size size2 span1"></span>
        <span class="size size2 span2"></span>
        <span class="size size2 span3"></span>
        <span class="size size2 span4"></span>
        <span class="size size2 span5"></span>
        <span class="size size3 span6"></span>
        <span class="size size3 span7"></span>
        <span class="size size3 span8"></span>
    </div>
    <!-- mainWrapper start -->
    <div class="mainWrapper">
        <!-- header start -->
         @include('includes.header')
        <!-- header end -->

<!-- mid section start-->
@yield('content')
<!-- mid section end -->


        <!-- footer start -->
           @include('includes.footer')
        <!-- footer end -->




    </div>
    <!-- mainWrapper end -->
    <div class="canvas_left_bottom">
        <span class="size fspan1"></span>
        <span class="size fspan2"></span>
        <span class="size fspan3"></span>
        <span class="size fspan4"></span>
        <span class="size fspan5"></span>
        <span class="size fspan6"></span>
        <span class="size fspan7"></span>
        <span class="size fspan8"></span>
        <span class="size fspan9"></span>
        <span class="size fspan10small"></span>
    </div>

    <!-- JS files -->
    <script src="{{asset('fassets/js/jquery-3.6.0.min.js')}}" ></script>
    <script src="{{asset('fassets/js/aos.js')}}"></script>
    <script src="{{asset('fassets/js/main.js')}}"></script>
    @yield('script')
</body>
</html>
