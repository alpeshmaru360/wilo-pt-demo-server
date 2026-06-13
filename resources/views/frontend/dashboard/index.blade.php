@extends('frontend.layout.app')
@section('content')
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="componentMidSection">
                <h3>Select Component for Quotation</h3>
                <div class="componentList">
                    @if(auth()->user()->booster_access == "1")
                        <div class="componentBox" data-aos="flip-left" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                        {{--@dd(isset($maintance_mode_booster), $maintance_mode_booster == "0.0" , $maintance_mode_booster == "0")--}}
                            @if(isset($maintance_mode_booster))
                                @if($maintance_mode_booster == "0.0" || $maintance_mode_booster == "0")
                                    <a href="{{route('boosterset')}}">
                                @else
                                    <a href="{{route('is_maintance_mode',['label'=>'maintance_mode_booster'])}}">
                                @endif
                            @endif
                            <label for="">
                                <img src="{{asset('fassets/images/1.png')}}" alt="Component image">
                                <h4>Booster Set</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->control_panel_access == "1")
                        <div class="componentBox" data-aos="flip-right" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                            @if(isset($control_panel_maintance_mode))
                                @if($control_panel_maintance_mode == "0.0" || $control_panel_maintance_mode == "0")
                                    <a href="{{route('cp.controlpanel')}}">
                                @else
                                    <a href="{{route('is_maintance_mode',['label'=>'control_panel_maintance_mode'])}}">
                                @endif
                                @endif
                            <label for="">
                                <img src="{{asset('fassets/images/control_panel.png')}}" alt="Component image" style="width:47%;">
                                <h4>Control Panel</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->fire_fighting_access == "1")
                        <div class="componentBox" data-aos="flip-left" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                        @if(isset($maintance_mode_fire_fighting))
                                @if($maintance_mode_fire_fighting == "0.0" || $maintance_mode_fire_fighting == "0")
                                    <a href="{{route('fire-fighting.index')}}">
                                @else
                                    <a href="{{route('is_maintance_mode',['label'=>'fire-fighting_maintance_mode'])}}">
                                @endif
                                @endif
                            <label for="">
                                <img src="{{asset('fassets/images/fire-fighting.png')}}" alt="Component image">
                                <h4>Fire Fighting Pump</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->scp_access == "1")
                        <div class="componentBox" data-aos="flip-left" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                        @if(isset($maintance_mode_scp))
                                @if($maintance_mode_scp == "0.0" || $maintance_mode_scp == "0")
                                <a href="{{route('scp.pump')}}">
                                    @else
                                    <a href="{{route('is_maintance_mode',['label'=>'scp_maintance_mode'])}}">
                                    @endif
                                @endif
                            <label for="">
                                <img src="{{asset('fassets/images/scp_photo.JPG')}}" alt="Component image">
                                <h4>SCP Pump Assembly</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->atmos_access == "1")
                        <div class="componentBox" data-aos="flip-right" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                            @if(isset($maintance_mode_atmos))
                                @if($maintance_mode_atmos == "0.0" || $maintance_mode_atmos == "0")
                                    <a href="{{route('ag.atmos_giga')}}">
                                @else
                                    <a href="{{route('is_maintance_mode',['label'=>'atmos_maintance_mode'])}}">
                                @endif
                            @endif
                            <label for="">
                                <img src="{{asset('fassets/images/atmosgiga.png')}}" alt="Component image">
                                <h4>Atmos GIGA</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->sch_access == "1")
                        <div class="componentBox" data-aos="flip-left" data-aos-offset="300"
                        data-aos-easing="ease-in-sine">
                            @if(isset($maintance_mode_sch))
                                @if($maintance_mode_sch == "0.0" || $maintance_mode_sch == "0")
                                    <a href="{{route('sch.pump')}}">
                                @else
                                    <a href="{{route('is_maintance_mode',['label'=>'sch_maintance_mode'])}}">
                                @endif
                            @endif
                            <label for="">
                                <img src="{{asset('fassets/images/atmosgiga.png')}}" alt="No image found">
                                <h4>SCH Pump</h4>
                            </label>
                            </a>
                        </div>
                    @endif

                    <!-- A Code: 05-03-2026 Start -->
                    @if(auth()->user()->scpv_access == "1")
                        <div class="componentBox" data-aos="flip-left" data-aos-offset="300"
                            data-aos-easing="ease-in-sine">
                            @php
                                $scpvRoute = route('scpv.index');
                                if(isset($maintance_mode_scpv) && ($maintance_mode_scpv != "0.0" && $maintance_mode_scpv != "0")) {
                                    $scpvRoute = route('is_maintance_mode', ['label' => 'scpv_maintance_mode']);
                                }
                            @endphp
                            <a href="{{ $scpvRoute }}">
                                <label>
                                    <img src="{{ asset('fassets/images/scp_photo.JPG') }}" alt="Component image">
                                    <h4>SCPV Pump Assembly</h4>
                                </label>
                            </a>
                        </div>
                    @endif
                    <!-- A Code: 05-03-2026 End -->

                </div>
            </div>
        </div>

        <div class="d-flex cusPagination">
            <div class="">
                <a href=""><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
            <div class="">
                <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->
@endsection
