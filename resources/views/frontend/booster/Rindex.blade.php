@php
    $tool_tip = DB::table('tool_tip')->where('part_id',1)->get();
    foreach($tool_tip as $t){
    $key = $t->component_name;
    $t->$key = $t->tool_tip;
    }
@endphp
@extends('frontend.layout.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .error{
        outline: 1px solid red;
    }
    .panel.formWidget {
        max-height: none !important;
    }
	th{
        text-align: left;   
    }
</style>
@section('content')
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">    
            <div class="pumpInfoMidSection">
                <div class="pumpInfoList">
                    <form id="boosterForm">
                        <input type="hidden" value="{{$qoutation}}" id="qoutation_value">
                        <div class="accSec1">
						<input type="hidden" name='origin_country' id="origin_country" class="origin_country">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[0]->pump_info ?? ""}}</div>
                            </div>
                            <button class="accordion acTitle" id="accDefaultOpen">Pump Info</button>
                            <div class="panel formWidget">
                                <div class="panelBody">

                                <div class="formFields">
                                    <div class="helpBtnWrap" style="position:relative;">
                                        <a href="" class="helpBtn">?</a>
                                        <div class="popper-content hide"></div>
                                    </div>
                                    <input type="text" class="formInput" name="full_article_number" id="full_article_number" placeholder="Full Article Number">
                                </div>

                                    <div class="formFields">
                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="pumpType" id="pumpType" class="formInput">
                                            <option value="">Select Type*</option>
                                            <option value="full_pump">Full Pump</option>
                                            <option value="bareshaft_pump">Bare Shaft Pump</option>
                                            <option value="manually">Manually Entered by user</option>
                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <input type="text" name="article_number_pi" id="article_number_pi" class="formInput" placeholder="Pump Article Number">
                                    </div>
                                    <div class="formFields" id="model_list">
                                        <input type="text" name="pump_model_pi" id="pump_model_pi" class="formInput" placeholder="Pump Model Number">
                                    </div>
                                    <div class="formFields left">
                                        <input type="text" name="motor_power_pi" id="motor_power_pi" class="formInput" placeholder="Motor Power*">

                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">Motor Power*</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields right">
                                        <input type="text" name="supply_voltage_pi" id="supply_voltage_pi" class="formInput" placeholder="Supply Voltage*">

                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">Supply Voltage*</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields left" id="motor_brand_pi_div">
                                        <input type="text" name="motor_brand_pi" id="motor_brand_pi" class="formInput" placeholder="Motor Brand">

                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">Motor Brand</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields right">
                                        <input type="text" name="frequency_pi" id="frequency_pi" class="formInput" placeholder="Frequency*">

                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">Frequency*</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields left">
                                        <input type="text" name="efficiency_pi" id="efficiency_pi" class="formInput" placeholder="Efficiency">

                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">Efficiency</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields right">
                                        <span class="formArrowIcon"><img src=="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="no_of_pumps_pi" id="no_of_pumps_pi" class="formInput" disabled>
                                            <option value="">Number of Pumps*</option>

                                            @for($i=1; $i<=8; $i++){
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="formFields left">
                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="system_pressure_pi" id="system_pressure_pi" class="formInput">
                                            <option value="">System Pressure*</option>
                                            <option value="PN16">PN16</option>
                                            <option value="PN25">PN25</option>
                                        </select>
                                        {{--                            <span class="formArrowIcon"><img src="assets/images/arrowDownIcon.png" /></span>--}}
                                        {{--                            <select name="" id="" class="formInput">--}}
                                            {{--                                <option value="">System Pressure*</option>--}}
                                            {{--                                <option value="">option A</option>--}}
                                            {{--                                <option value="">option B</option>--}}
                                            {{--                                <option value="">option C</option>--}}
                                            {{--                            </select>--}}
                                    </div>
                                    <div class="formFields right">
                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="manifold_material_pi" id="manifold_material_pi" class="formInput">
                                            <option value="">Manifold Material*</option>
                                            <option value="SS316">SS316</option>
                                            <option value="SS304">SS304</option>
                                        </select>
                                    </div>

                                    <!-- <div class="formFields left DpUnit">
                                        <input type="text" placeholder="DP Flow" name="dp_flow_pi" id="dp_flow_pi" class="formInput">
                                    </div> -->

                                    <!-- <div class="formFields right DpUnit">
                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select  name="dp_flow_units_pi" id="dp_flow_units_pi" class="formInput">
                                            <option value="">Units*</option>
                                            <option value="L/S">L/S</option>
                                            <option value="US g.p.m">US g.p.m</option>
                                            <option value="m3/h">m3/h</option>
                                        </select>
                                    </div>
                                    <div class="formFields left DpUnit">
                                        <input type="text" name="dp_head_pi" id="dp_head_pi" placeholder="DP Head" class="formInput">
                                    </div>
                                    <div class="formFields right DpUnit">
                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="dp_head_units_pi" id="dp_head_units_pi" class="formInput">
                                            <option value="">Units*</option>
                                            <option value="m">m</option>
                                            <option value="ft">ft</option>
                                        </select>
                                    </div> -->
                                    
                                    <div class="formFields" hidden>
                                        <input type="text" hidden name="unit_price_pi" id="unit_price_pi" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text" hidden name="pump_height_pi" id="pump_height_pi" value="">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="cp-id" value="" name="cp-id"/>
                            <input type="hidden" id="last-cp-id" value=""/>
                            <input type="hidden" id="code-price-mechanical" value="0" name="code-price-mechanical"/>
                            <input type="hidden" id="table-name" value=""/>
                            <input type="hidden" id="column-name" value=""/>
                            <input type="hidden" id="adder-enclousre-area-item" value=""/>
                            <input type="hidden" id="code-price" name="code-price-electrical" value="0"/>
                            <input type="hidden" id="ptp-distance-id" name="ptp-distance-id" value="0"/>
                        </div>
                    </form>

                    <div class="accSec3">
                        <div class="helpBtnWrap" style="position: relative;">
                            <a href="" class="helpBtn">?</a>
                            <div class="popper-content hide">{{$tool_tip[1]->control_panel ?? ""}}</div>
                        </div>
                        <button class="accordion acTitle">Control Panel</button>
                        <div class="panel formWidget">
                            <div class="panelBody">
                                <form id ='controlpanel_form' action="">
                                    @csrf
                                    <div class="formFields" hidden>
                                        <input type="text" name="cp_price" id="cp_price" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text" name="control_panel_price_for_booster" id="control_panel_price_for_booster" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text" name="cp_range" id="cp_range" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text" name="cp_starter_type" id="cp_starter_type" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text"  name="cp_height" id="cp_height" value="">
                                    </div>
                                    <div class="formFields" hidden>
                                        <input type="text"  name="cp_width" id="cp_width" value="">
                                    </div>
                                    {{--                                    <div class="formFields">--}}
                                        {{--                                        <input type="text" class="formInput" name="article_number" id="" placeholder="Article Number">--}}
                                        {{--                                    </div>--}}
                                    <div class="formFields">
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="no_of_pump" id="no_of_pump" class="select-control-panel formInput" >
                                            <option value="">Number of Pumps*</option>
                                            @foreach($numberOfPumps as $numberOfPump)
                                            <option value="{{$numberOfPump->id}}">{{$numberOfPump->value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="formFields" id="power_rating_div">
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="power_rating" id="power_rating" class="formInput" disabled>
                                            <option value="">Motor Power*</option>



                                        </select>
                                    </div>
                                    <div class="formFields" id="voltage_div">
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="voltage" id="voltage" class="formInput" disabled>
                                            <option value="">Supply Voltage*</option>


                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[2]->application ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="application" id="application" class="formInput" disabled>
                                            <option value="">Application*</option>


                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[3]->ambient_type ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="ambient_temp" id="ambient_temp" class="formInput" disabled>
                                            <option value="">Ambient Temp*</option>
                                            <!--                                <option value="40">40</option>
                                                                            <option value="50">50</option>-->

                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[4]->stater_type ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="stater_type" id="stater_type" class="formInput" disabled>
                                            <option value="">Stater Type*</option>
                                            <!--                                <option value="xtreme">Xtreme</option>
                                                                            <option value="constant-speed-DOL">Constant speed-DOL</option>
                                                                            <option value="constant-speed-SD">constant speed - SD</option>
                                                                            <option value="multi-VFD">Multi VFD</option>
                                                                            <option value="single-VFD">Single VFD</option>
                                                                            <option value="softstarter">softstarter</option>
                                                                            <option value="multi-VFD-Bypass">Multi VFD+ Bypass</option>-->
                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[5]->communication_protocol ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="communication_protocol" id="communication_protocol" class="formInput" disabled>
                                            <option value="">Communication Protocol*</option>
                                            <!--                                <option value="vFC">VFC</option>
                                                                            <option value="modbus-RTU">Modbus RTU</option>
                                                                            <option value="modbus-TCP">Modbus TCP/IP</option>
                                                                            <option value="bacnet">Bacnet</option>-->

                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[6]->ip_rating ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="ip_rating" id="ip_rating" class="formInput" disabled>
                                            <option value="">IP Rating*</option>
                                            <!--                                <option value="IP54">IP54</option>
                                                                            <option value="IP65">IP65</option>
                                                                            <option value="IP66">IP66</option>-->
                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[7]->components ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="component" id="component" class="formInput" disabled>
                                            <option value="">Components*</option>
                                            <!--                                <option value="standard">standard</option>
                                                                            <option value="Economic">Economic</option>-->
                                        </select>
                                    </div>
                                    <div class="formFields">
                                        <div class="helpBtnWrap" style="position: relative;">
                                            <a href="" class="helpBtn">?</a>
                                            <div class="popper-content hide">{{$tool_tip[8]->enclosure ?? ""}}</div>
                                        </div>
                                        <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                                        <select name="enclosure" id="enclosure" class="formInput" disabled>
                                            <option value="">Enclosure*</option>
                                            <!--                                <option value="metal">metal</option>
                                                                            <option value="GRP">GRP</option>
                                                                            <option value="Stainless-steel">Stainless steel</option>-->
                                        </select>
                                    </div>
                                    <input type="hidden" id="cp-id" value="" name="cp-id"/>
                                    <input type="hidden" id="last-cp-id" value=""/>

                                    <input type="hidden" id="code-price-mechanical" value="0" name="code-price-mechanical"/>
                                    <input type="hidden" id="code-price-electrical" value="0" name="code-price-electrical"/>
                                    <input type="hidden" id="total-price" value="0" name="total-price"/>
                                    <input type="hidden" id="ptp-distance-id" value="0" name="ptp-distance-id"/>
                                    <input type="hidden" id="standard_component_price" value="0" name="standard_component_price"/>
                                    <input type="hidden" id="mechanical_system_price" value="0" name="mechanical_system_price"/>

                                    <input type="hidden" id="electrical_items_price" value="0" name="electrical_items_price"/>
                                    <input type="hidden" id="mechanical_items_price" value="0" name="mechanical_items_price"/>

                                    <input type="hidden" id="cablePrice" value="0" name="cablePrice"/>

                                    <input type="hidden" id="mechanicalcomponent_billofmaterial" value="" name="mechanicalcomponent_billofmaterial"/>

                                    {{--                                    <input type="hidden" id="ptp_distance_id" value="0" name="ptp_distance_id"/>--}}

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="accSec4">
                        <div class="helpBtnWrap" style="position: relative;">
                            <a href="" class="helpBtn">?</a>
                            <div class="popper-content hide">{{$tool_tip[9]->optional ?? ""}}</div>
                        </div>
                        <button class="accordion acTitle" id="adder-optional">Optional</button>
                        <div class="panel formWidget">
                            <div class="panelBody">
                                <div class="tabNav" >
                                    <button class="tabLinks mechanical-adder-tab" id="tabDefaultOpen" onclick="openTab(event, 'tab1')">Mechanical</button>
                                    <button class="tabLinks electrical-adder-tab" onclick="openTab(event, 'tab2')">Electrical</button>
                                </div>
                                <div class="tabContentWrapper">
                                    <div class="tabContent" id="tab1">

                                    </div>
                                    <div class="tabContent" id="tab2">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="d-flex cusPagination">
            <div class="">
                <a href=""><img src="assets/images/arrowLefticon.png" /> Back</a>
            </div>
            <div class="">
                <button>Next <img src="assets/images/arrowLefticon.png" /></button>
            </div>
        </div>-->
        <div class="d-flex formPageFooter">
            <div class="left">
                Unit Price:
                <button id="calculate" class="clcBtn">Calculate</button>  <span id="price"></span>
            </div>
            <div class="right">
                <ul>
                    <li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>
                <!-- <li><a href="#" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li> -->
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>

                <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="master-price-record">
            <!--Table-->
        </div>
        <div class="modalBtns">
            <button id="addtocart">Add to Cart</button>
            <span class="close" onclick="refresh()">Cancel</span>
            <span class="close-cart-modal" >Close</span>
        </div>
    </div>
</div>

<div id="countryOriginModal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="country_selction" id="dubai_selection" value="dubai" />
              <label class="form-check-label" for="dubai_selection"> Dubai </label>
            </div>

            <!-- Default checked radio -->
            <div class="form-check">
              <input class="form-check-input" type="radio" name="country_selction" id="ksa_selection" value="ksa"/>
              <label class="form-check-label" for="ksa_selection"> KSA </label>
            </div>
        </div>
        <div class="modalBtns">
            <button id="Select_country_origin">Select</button>
        </div>
    </div>
</div>

<div id="error-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="error-modal-body">
        </div>
        <div class="modalBtns">
            <span class="close" id="error-close">Close</span>
        </div>
    </div>
</div>

<div id="price-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="price-modal-body">
            <div class="formFields">
                <input type="text" name="unit_price_pi_modal" id="unit_price_pi_modal" class="formInput" placeholder="Pump Unit Price">
            </div>
        </div>
        <div class="modalBtns">
            <span class="close" id="price-close">Enter</span>
        </div>
    </div>
</div>

<div id="optional-add-success-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="">
            <h4>Optional added Successful!</h4>
        </div>
        <div class="modalBtns">
            <span class="close" id="error-close">Close</span>
        </div>
    </div>
</div>
@endsection
<style>
    /* Float cancel and delete buttons and add an equal width */
    .cancelbtn, .deletebtn {
        float: left;
        width: 50%;
    }

    /* Add a color to the cancel button */
    .cancelbtn {
        background-color: #ccc;
        color: black;
    }

    /* Add a color to the delete button */
    .deletebtn {
        background-color: #f44336;
    }

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: #474e5d;
        padding-top: 50px;
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    /* Style the horizontal ruler */
    hr {
        border: 1px solid #f1f1f1;
        margin-bottom: 25px;
    }

    /* The Modal Close Button (x) */
    .close {
        position: absolute;
        right: 35px;
        top: 15px;
        font-size: 40px;
        font-weight: bold;
        color: #f1f1f1;
    }

    .close:hover,
    .close:focus {
        color: #f44336;
        cursor: pointer;
    }

    /* Clear floats */
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
    li.error {
        color: red;
    }

    /* Change styles for cancel button and delete button on extra small screens */
    @media screen and (max-width: 300px) {
        .cancelbtn, .deletebtn {
            width: 100%;
        }
    }
</style>
@section('script')
<script>
    //PUMP INFO STARTS
    $("#price-close").on('click', function () {
        $('#unit_price_pi').val($('#unit_price_pi_modal').val());
        $("#price-modal").hide();
    });

    $(document).on("click", '.close-cart-modal', function (event) {
        $("#myModal").hide();
    });

    //error modal
    $("#error-close").on('click', function () {
        $("#error-modal").hide();
    });

    $('#no_of_pumps').on('change', function (e) {
        var no_of_pump = $('#no_of_pumps option:selected').val();
        $('#no_of_pumps_pi').val(no_of_pump);
    });

    $("#pumpType").on('change', function (e) {
        $('#pump_model_pi').val("");
        $('#motor_power_pi').val("");
        $('#supply_voltage_pi').val("");
        $('#frequency_pi').val("");
        $('#article_number_pi').val("");
        $('#motor_brand_pi').val("");
        $('#efficiency_pi').val("");
        $('#system_pressure_pi').val("");
        $('#manifold_material_pi').val("");
        $('#dp_flow_pi').val("");
        $('#dp_head_pi').val("");
        // $('#no_of_pumps_pi').val("");
        $('#dp_flow_units_pi').val("");
        $('#dp_head_units_pi').val("");
        $('#unit_price_pi').val("");
        $('#pump_hegiht_pi').val("");
        $('#no_of_pump').val('');
        $('#no_of_pumps_pi').val('');

        // $("#no_of_pumps_pi").val("");
        // $("#voltage").val("");
        // $("#power_rating").val("");
        // $("#no_of_pumps").val("");

        $("#power_rating").removeAttr('disabled');
        $('#controlpanel_form select[id="no_of_pumps"] option:gt(0)').remove().end();
        $('#no_of_pumps_pi select[id="no_of_pumps_pi"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="power_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $("#voltage").attr('disabled', 'disabled');
        $("#application").attr('disabled', 'disabled');
        $("#ambient_temp").attr('disabled', 'disabled');
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');

        pumpType = $('#pumpType').val();
        if (pumpType == 'full_pump') {
            $('#model_list').html('');
            $('#model_list').append('<input type="text" name="pump_model_pi" id="pump_model_pi" class="formInput" placeholder="Pump Model Number">');
            $('#pump_model_pi').attr('disabled', 'disabled');
            $('#motor_power_pi').attr('disabled', 'disabled');
            $('#supply_voltage_pi').attr('disabled', 'disabled');
            $('#motor_brand_pi_div').html('');
            $('#motor_brand_pi_div').append('<input type="text" name="motor_brand_pi" value ="WSM" id="motor_brand_pi" class="formInput" placeholder="Motor Brand">');
            $('#motor_brand_pi').attr('disabled', 'disabled');
            $('#frequency_pi').attr('disabled', 'disabled');
            $('#unit_price_pi').val("");
            $('#pump_hegiht_pi').val("");
            $('#power_rating').attr('disabled', 'disabled');
            $('#voltage').attr('disabled', 'disabled');

        } else if (pumpType == 'bareshaft_pump') {
            // $('#pump_model_pi_select').hide();
            // $('#pump_model_pi').show();
            $('#model_list').html('');
            $('#model_list').append('<input type="text" name="pump_model_pi" id="pump_model_pi" class="formInput" placeholder="Pump Model Number">');

            $('#pump_model_pi').attr('disabled', 'disabled');
            $('#motor_power_pi').attr('disabled', 'disabled');
            $('#supply_voltage_pi').attr('disabled', 'disabled');
            $('#motor_brand_pi').attr('disabled', 'disabled');
            $('#power_rating').attr('disabled', 'disabled');
            $('#voltage').attr('disabled', 'disabled');
            $('#motor_brand_pi_div').html('');
            $('#motor_brand_pi_div').append('<select onchange="getPumpDetail()" name="motor_brand_pi" id="motor_brand_pi" class="formInput"><option value="">Motor Brand*</option> <option value="TEE">TEE</option> <option value="ABB">ABB</option> </select>');
            
            $('#frequency_pi').attr('disabled', 'disabled');
            $('#efficiency_pi').attr('disabled', 'disabled');
            $('#unit_price_pi').val("");
            $('#pump_hegiht_pi').val("");
        } else if (pumpType == 'manually') {
            $("#price-modal").show();
            $('#pump_model_pi').hide();
            $('#motor_power_pi').attr('disabled', 'disabled');
            $('#supply_voltage_pi').attr('disabled', 'disabled');
            $('#motor_brand_pi').attr('disabled', 'disabled');
            $('#frequency_pi').removeAttr('disabled');
            $('#efficiency_pi').attr('disabled', 'disabled');
            $('#unit_price_pi').val("");
            $('#pump_height_pi').val("");
            $.ajax({
                type: "get",
                data: null,
                url: "{{route('boosterset.getPumpAllModelNo')}}",
                success: function (response) {
                    console.log(response.data)
                    if (response.data != null) {
                        $('#model_list').html('');
                        var myParent = document.getElementById('model_list');
                        const ModelList = response.data;
                        //Create array of options to be added
                        var array = response.data;
                        //Create and append select list
                        var selectList = document.createElement("select");
                        selectList.id = "pump_model_pi";
                        selectList.name = "pump_model_pi";
                        selectList.className = "formInput selectpicker";
                        myParent.appendChild(selectList);
                        var option = document.createElement("option");
                        option.value = "";
                        option.text = "Pump Model*";
                        selectList.appendChild(option);
                        //Create and append the options
                        // for (var i = 0; i < array.length; i++) {
                        //     var option = document.createElement("option");
                        //     option.value = array[i].model_no;
                        //     option.attr("height", array[i].height);
                        //     option.attr("price", array[i].height);
                        //
                        //     option.text = array[i].model_no;
                        //     selectList.appendChild(option);
                        // }

                        $.each(array, function (key, value) {
                            $('#pump_model_pi')
                                    .append($("<option></option>")
                                            .attr("height", value.height)
                                            .text(value.model_no));
                        });
                        $("#pump_model_pi").change(function () {
                            var height = $('#pump_model_pi option:selected').attr('height');
                            var pump_model_pi = $('#pump_model_pi option:selected').val();
                            $('#pump_height_pi').val(height);
                            $('#pump_model_pi').val(pump_model_pi);
                            calculateMechanicalComponent();
                        });
                    }


                },
                error: function (data) {

                }

            });
        }
    });

    $('#article_number_pi').blur(function () {
        if ($(this).val().length > 0) {
            pumpType = $('#pumpType').val();
            article_number = $('#article_number_pi').val();
            motor_brand = $('#motor_brand_pi option:selected').val();
            $.ajax({
                type: "get",
                url: "{{route('boosterset.pumpDetailByType')}}",
                data: {pumpType: pumpType, article_number: article_number, motor_brand: motor_brand},
                success: function (response) {
                    console.log(response.data)
                    if (response.data != null) {
                        $('#pump_model_pi').val(response.data.model_number);
                        $('#motor_power_pi').val(response.data.motor_power);
                        $('#supply_voltage_pi').val(response.data.supply_voltage);
                        $('#frequency_pi').val(response.data.frequency);
                        $('#unit_price_pi').val(response.data.price);
                        $('#pump_height_pi').val(response.data.pump_height);
                        // $('#power_rating_div').html('<input type="text" name="power_rating" id="power_rating" class="formInput" placeholder="Motor Power*" value="'+response.data.motor_power+'" disabled="disabled">');
                        // $('#voltage_div').html('<input type="text" name="voltage" id="voltage" class="formInput" placeholder="Supply Voltage*" value="'+response.data.supply_voltage+'" disabled="disabled">');

                        if (pumpType == 'bareshaft_pump') {
                            // $('#motor_brand_pi').val(response.data.motor_brand);
                            // $("#motor_brand_pi").append("<option value='" + id + "'>" + value + "</option>");
                            if (response.data.required != null) {
                                console.log('sdfsdfsf')
                                $("#error-modal-body").html('');
                                $("#error-modal-body").html('<h4>' + response.data.required + ' </h4>');
                                $("#error-modal").show();
                            } else {
                                $('#efficiency_pi').val(response.data.efficiency);
                                calculateMechanicalComponent();
                                $("#no_of_pump").prop("selectedIndex", 0);

                                //  $('#controlpanel_form select[id="no_of_pump"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="power_rating"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
                                $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
                                $('#cp_height').val("");
                                $('#cp_width').val("");
                                $('#cp_price').val("");
                                $('#cp_range').val("");
                                $('#cp_starter_type').val("");
                                $("#voltage").attr('disabled', 'disabled');
                                $("#application").attr('disabled', 'disabled');
                                $("#ambient_temp").attr('disabled', 'disabled');
                                $("#stater_type").attr('disabled', 'disabled');
                                $("#communication_protocol").attr('disabled', 'disabled');
                                $("#enclosure").attr('disabled', 'disabled');
                                $("#ip_rating").attr('disabled', 'disabled');
                                $("#component").attr('disabled', 'disabled');

                            }
                        }
                        if (pumpType == 'full_pump') {
                            calculateMechanicalComponent();
                            $("#no_of_pump").prop("selectedIndex", 0);

                            //$('#controlpanel_form select[id="no_of_pump"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="power_rating"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
                            $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
                            $('#cp_height').val("");
                            $('#cp_width').val("");
                            $('#cp_price').val("");
                            $('#cp_range').val("");
                            $('#cp_starter_type').val("");
                            $("#voltage").attr('disabled', 'disabled');
                            $("#application").attr('disabled', 'disabled');
                            $("#ambient_temp").attr('disabled', 'disabled');
                            $("#stater_type").attr('disabled', 'disabled');
                            $("#communication_protocol").attr('disabled', 'disabled');
                            $("#enclosure").attr('disabled', 'disabled');
                            $("#ip_rating").attr('disabled', 'disabled');
                            $("#component").attr('disabled', 'disabled');                        }
                    } else {
                        $("#error-modal-body").html('');
                        $("#error-modal-body").html('<h4>Pump price is not available. Enter prices manually. </h4>');
                        $("#error-modal").show();
                        $('#pump_model_pi').val("");
                        $('#motor_power_pi').val("");
                        $('#supply_voltage_pi').val("");
                        $('#frequency_pi').val("");
                        $('#article_number_pi').val("");
                        $('#motor_brand_pi').val("");
                        $('#efficiency_pi').val("");
                        $('#system_pressure_pi').val("");
                        $('#manifold_material_pi').val("");
                        $('#dp_flow_pi').val("");
                        $('#dp_head_pi').val("");
                        // $('#no_of_pumps_pi').val("");
                        $('#dp_flow_units_pi').val("");
                        $('#dp_head_units_pi').val("");
                        $('#unit_price_pi').val("");
                        $('#pump_height_pi').val("");
                        $("#no_of_pump").prop("selectedIndex", 0);

                        // $('#controlpanel_form select[id="no_of_pump"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="power_rating"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
                        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
                        $('#cp_height').val("");
                        $('#cp_width').val("");
                        $('#cp_price').val("");
                        $('#cp_range').val("");
                        $('#cp_starter_type').val("");
                        $("#voltage").attr('disabled', 'disabled');
                        $("#application").attr('disabled', 'disabled');
                        $("#ambient_temp").attr('disabled', 'disabled');
                        $("#stater_type").attr('disabled', 'disabled');
                        $("#communication_protocol").attr('disabled', 'disabled');
                        $("#enclosure").attr('disabled', 'disabled');
                        $("#ip_rating").attr('disabled', 'disabled');
                        $("#component").attr('disabled', 'disabled');
                    }


                },
                error: function (data) {

                }

            });
        }
    });

    function getPumpDetail() {
        pumpType = $('#pumpType').val();
        article_number = $('#article_number_pi').val();
        motor_brand = $('#motor_brand_pi option:selected').val();
        $.ajax({
            type: "get",
            url: "{{route('boosterset.pumpDetailByType')}}",
            data: {pumpType: pumpType, article_number: article_number, motor_brand: motor_brand},
            success: function (response) {
                console.log(response.data)
                if (response.data != null) {
                    $('#pump_model_pi').val(response.data.model_number);
                    $('#motor_power_pi').val(response.data.motor_power);
                    $('#supply_voltage_pi').val(response.data.supply_voltage);
                    $('#frequency_pi').val(response.data.frequency);
                    $('#unit_price_pi').val(response.data.price);
                    $('#pump_height_pi').val(response.data.pump_height);
                    if (pumpType == 'bareshaft_pump') {
                        calculateMechanicalComponent();
                        // $('#motor_brand_pi').val(response.data.motor_brand);
                        if (response.data.required != null) {
                            console.log('sdfsdfsf')
                            $("#error-modal-body").html('');
                            $("#error-modal-body").html('<h4>' + response.data.required + ' </h4>');
                            $("#error-modal").show();
                        } else {
                            $('#efficiency_pi').val(response.data.efficiency);
                            calculateMechanicalComponent();

                        }
                    }
                    if (pumpType == 'full_pump') {
                        calculateMechanicalComponent();
                    }
                } else {
                    $("#error-modal-body").html('');
                    $("#error-modal-body").html('<h4>Pump price is not available. Enter prices manually. </h4>');
                    $("#error-modal").show();
                    $('#pump_model_pi').val("");
                    $('#motor_power_pi').val("");
                    $('#supply_voltage_pi').val("");
                    $('#frequency_pi').val("");
                    $('#article_number_pi').val("");
                    $('#motor_brand_pi').val("");
                    $('#efficiency_pi').val("");
                    $('#system_pressure_pi').val("");
                    $('#manifold_material_pi').val("");
                    $('#dp_flow_pi').val("");
                    $('#dp_head_pi').val("");
                    // $('#no_of_pumps_pi').val("");
                    $('#dp_flow_units_pi').val("");
                    $('#dp_head_units_pi').val("");
                    $('#unit_price_pi').val("");
                    $('#pump_height_pi').val("");
                }
            },
            error: function (data) {
            }
        });
    }
    //PUMP INFO ENDS
    //CP STARTS
    function refresh()
    {
        location.reload();
    }

    function callCpanel() {
        $("#application").removeAttr('disabled');
        $("#motor_power select").val($('#motor_power_pi').val());
        $("#voltage select").val($('#supply_voltage_pi').val());

        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#ambient_temp").attr('disabled', 'disabled');
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var applications = response.data.applications;
                for (var i = 0; i < applications.length; i++) {
                    var id = applications[i]['id'];
                    var value = applications[i]['value'];
                    $("#application").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    }
    $("#no_of_pump").on('change', function (e) {

        var no_of_pump = $('#no_of_pump option:selected').val();
        $('#no_of_pumps_pi').val(no_of_pump);
        $("#power_rating").removeAttr('disabled');
        $('#controlpanel_form select[id="power_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $("#voltage").attr('disabled', 'disabled');
        $("#application").attr('disabled', 'disabled');
        $("#ambient_temp").attr('disabled', 'disabled');
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');



        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var powers = response.data.powers;
                if ($('#pumpType').val() == 'full_pump' || $('#pumpType').val() == 'bareshaft_pump') {

                    for (var i = 0; i < powers.length; i++) {
                        var id = powers[i]['id'];
                        var value = powers[i]['value'];
                        motor_power = $('#motor_power_pi').val();
                        console.log(parseFloat(value).toFixed(2) + '   ' + parseFloat(motor_power).toFixed(2));
                        if (parseFloat(value).toFixed(2) === parseFloat(motor_power).toFixed(2)) {
                            is_power = true;
                            $("#power_rating").append("<option value='" + id + "'>" + value + "</option>");

                        }
                        // else{

                        // }
                    }
                    if (is_power == false) {
                        $("#error-modal-body").html('');
                        $("#error-modal-body").html('<h4>Product does not exist with these values.</h4>');
                        $("#error-modal").show();
                        $("#power_rating").attr('readonly', 'readonly');
                    }

                } else {
                    for (var i = 0; i < powers.length; i++) {
                        var id = powers[i]['id'];
                        var value = powers[i]['value'];
                        $("#power_rating").append("<option value='" + id + "'>" + value + "</option>");

                        console.log()
                    }
                }


            },
            error: function (data) {

            }

        });
    });

    $("#power_rating").on('change', function () {
        $("#voltage").removeAttr('disabled');
        $('#controlpanel_form select[id="voltage"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#application").attr('disabled', 'disabled');
        $("#ambient_temp").attr('disabled', 'disabled');
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var voltages = response.data.voltages;
                console.log(voltages);

                    for (var i = 0; i < voltages.length; i++) {
                        var id = voltages[i]['id'];
                        var value = voltages[i]['value'];
                            $("#voltage").append("<option value='" + id + "'>" + value + "</option>");
                }


            },
            error: function (data) {

            }

        });
    });

    //Voltage
    $("#voltage").on('change', function () {
        $("#application").removeAttr('disabled');
        $('#controlpanel_form select[id="application"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#ambient_temp").attr('disabled', 'disabled');
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var applications = response.data.applications;
                console.log(applications)
                // for (var i = 0; i < applications.length; i++) {
                var id = applications[0]['id'];
                var value = applications[0]['value'];
                $("#application").append("<option value='" + id + "'>" + value + "</option>");
                // }

            },
            error: function (data) {

            }

        });
    });

    $("#application").on('change', function () {
        $("#ambient_temp").removeAttr('disabled');
        $('#controlpanel_form select[id="ambient_temp"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#stater_type").attr('disabled', 'disabled');
        $("#communication_protocol").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var ambienttemps = response.data.ambienttemps;
                for (var i = 0; i < ambienttemps.length; i++) {
                    var id = ambienttemps[i]['id'];
                    var value = ambienttemps[i]['value'];
                    $("#ambient_temp").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    });

    $("#ambient_temp").on('change', function () {
        $("#stater_type").removeAttr('disabled');
        $('#controlpanel_form select[id="stater_type"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $("#enclosure").attr('disabled', 'disabled');
        $("#ip_rating").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var startertypes = response.data.startertypes;
                for (var i = 0; i < startertypes.length; i++) {
                    var id = startertypes[i]['id'];
                    var value = startertypes[i]['value'];
                    $("#stater_type").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    });

    $("#stater_type").on('change', function () {
        $("#communication_protocol").removeAttr('disabled');
        $('#controlpanel_form select[id="communication_protocol"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $("#ip_rating").attr('disabled', 'disabled');
        $("#enclosure").attr('disabled', 'disabled');
        $("#component").attr('disabled', 'disabled');
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var comunicationprotocols = response.data.comunicationprotocols;
                for (var i = 0; i < comunicationprotocols.length; i++) {
                    var id = comunicationprotocols[i]['id'];
                    var value = comunicationprotocols[i]['value'];
                    $("#communication_protocol").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    });

    $("#communication_protocol").on('change', function () {
        $("#ip_rating").removeAttr('disabled');
        $('#controlpanel_form select[id="ip_rating"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#component").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var ipratings = response.data.ipratings;
                for (var i = 0; i < ipratings.length; i++) {
                    var id = ipratings[i]['id'];
                    var value = ipratings[i]['value'];
                    $("#ip_rating").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    });

    $("#ip_rating").on('change', function () {
        $("#component").removeAttr('disabled');
        $('#controlpanel_form select[id="component"] option:gt(0)').remove().end();
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#enclosure").attr('disabled', 'disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var components = response.data.components;
                for (var i = 0; i < components.length; i++) {
                    var id = components[i]['id'];
                    var value = components[i]['value'];
                    $("#component").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {

            }

        });
    });

    $("#component").on('change', function () {
        $('#controlpanel_form select[id="enclosure"] option:gt(0)').remove().end();
        $("#enclosure").removeAttr('disabled');
        $('#cp_height').val("");
        $('#cp_width').val("");
        $('#cp_price').val("");
        $('#cp_range').val("");
        $('#cp_starter_type').val("");
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var enclosures = response.data.enclousres;
                for (var i = 0; i < enclosures.length; i++) {
                    var id = enclosures[i]['id'];
                    var value = enclosures[i]['value'];
                    $("#enclosure").append("<option value='" + id + "'>" + value + "</option>");
                }

            },
            error: function (data) {
            }
        });
    });

    $(".electrical-adder-tab").on('click', function () {

            var adder_code_price = $('#code-price').val();
            var enclousreItem = $('#adder-enclousre-area-item').val();
            $.ajax({
                    type: "get",
                    url: "{{url('controlpanel/ajaxFilter')}}",
                    data: $('#controlpanel_form').serialize() + "&code_price=" + adder_code_price + "&enclousreItem=" + enclousreItem,
                    success: function (response){
                        if(response.data.enclourse_exist == null)
                        {
                            alert("Product not available, Enclosure item is not available..!!");
                        }
                            //$("#optional-button").disable();
                        else
                        {
                            var isEmpty = true;
                            $('#controlpanel_form  select').each(
                                    function (index) {
                                        var input = $(this);
                                        if (input.val() == "") {
                                            isEmpty = false;
                                        }
                                    }
                            );
                            if (isEmpty) {
                                var cp_id = $("#cp-id").val();
                                var last_cp_id = $("#last-cp-id").val();
                                if (last_cp_id == '' || last_cp_id != cp_id) {
                                    $("#last-cp-id").val(cp_id);
                                    $.ajax({
                                        type: "post",
                                        url: "{{url('booster/ajax-optional-modal-adder')}}",
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    //             'dataType': 'json',

                                        data: $('#controlpanel_form').serialize() + "&cp_id=" + cp_id,
                                //             contentType: false,

                                        success: function (response) {
                                            // alert("here");
                                            $("#tab2").html('');
                                            $("#tab2").html(response.data);
                                            // $("#adder-optional-modal").show();

                                        },
                                        error: function (data) {

                                        }

                                    });
                                } else {
                                    $("#adder-optional-modal").show();
                                }

                            } else {

                                $("#error-modal-body").html('');
                                $("#error-modal-body").html('<h4>Please Select all conrtol panel fields without article number. </h4>');
                                $("#error-modal").show();
                            }
                        }
                    }
        });
    });

    $("#enclosure").on('change', function () {
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {
                $("#cp-id").val(response.data.cp_id);
                $("#table-name").val(response.data.table_name);
                $("#column-name").val(response.data.column_name);
                $("#cp_height").val(response.data.cp_height);
                $("#cp_width").val(response.data.cp_width);
                $("#cp_price").val(response.data.cp_price_booster);
                $("#cp_range").val(response.data.ranges[0].id);
                $("#cp_starter_type").val(response.data.startertypes[0].id);
                calculateMechanicalComponent();

                var cp_id = $("#cp-id").val();
                var delayInMilliseconds = 1000;
                setTimeout(function () {
                    mechanicalAdder(cp_id);
                }, delayInMilliseconds);
                electrical_adder(cp_id);
            },
            error: function (data) {
            }
        });
    });

    function calculateMechanicalComponent() {
        var adder_code_price = $('#code-price').val();
        var enclousreItem = $('#adder-enclousre-area-item').val();
        var pump_model = $('#pump_model_pi').val();
        var no_of_pumps = $('#no_of_pump option:selected').val();
        var pump_height_pi = $('#pump_height_pi').val();
        var cp_height = $('#cp_height').val();
        var cp_width = $('#cp_width').val();
        var system_pressure = $('#system_pressure_pi option:selected').val();
        var starter_type = $('#cp_starter_type').val();
        var range = $('#cp_range').val();
        var power = $('#power_rating option:selected').text();
        var voltage = $('#voltage option:selected').text();
        var manifold = $('#manifold_material_pi').val();
        var cp_price = $('#cp_price').val();
        var unit_price_pi = $('#unit_price_pi').val();
        var code_price = $("#code-price").val(); //Electrical adder code price
        var article_number = $('#article_number_pi').val();

        var mechanical_code_price = $("#code-price-mechanical").val(); //Mechanical adder code price

        $.ajax({
            type: "get",
            url: "{{route('boosterset.calculateMechanicalComponent')}}",
            data: {pump_model: pump_model, no_of_pumps: no_of_pumps,
                pump_height: pump_height_pi, panel_height: cp_height, panel_width: cp_width,
                system_pressure: system_pressure,
                starter_type: starter_type, range: range,
                voltage: voltage, power: power, manifold: manifold,
                cp_price: cp_price, pump_unit_price: unit_price_pi, code_price: code_price,
                mechanical_code_price: mechanical_code_price,article_number:article_number
            },
            success: function (response) {
                
                if (response.data.error_html) {
                    $("#error-modal-body").html('');
                    $("#error-modal-body").html('<h4>' + response.data.error_html + '</h4>');
                    $("#error-modal").show();
                }
                var mechanical_items_price = $("#mechanical_items_price").val(response.data.mechanical_items_price);
                $("#electrical_items_price").val(response.data.electrical_items_price);
                var electrical_price_val = $("#electrical_items_price").val();
                console.log("calculateAjax");
                console.log("Electrical Price:", electrical_price_val);
                console.log(response.data.electrical_items_price);
                $("#ptp-distance-id").val(response.data.ptp_distance_id);
                $("#cp_price").val(response.data.cp_price);
                var cp_price = response.data.cp_price;
                },
            error: function (data) {

            }

        });
    }

    // MECHANICAL SYSTEM
    $("#system_pressure_pi").on('change', function () {
        calculateMechanicalComponent();
    });

    function mechanicalAdder(cp_id) {
        $.ajax({
            type: "get",
            url: "{{url('booster-set/mechanical-ajax-optional-html')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //  'dataType': 'json',

            data: $('#controlpanel_form').serialize() + "&cp_id=" + cp_id + "&ptp_distance_id=" + $("#ptp-distance-id").val(),
            //  contentType: false,

            success: function (response) {

                $("#tabDefaultOpen").addClass('active');
                $("#tab1").html('');
                $("#tab1").html(response.data);
            // $("#tab1").show();
            },
            error: function (data) {
            }
        });
    }

    function electrical_adder(cp_id) {
        $.ajax({
            type: "post",
            url: "{{url('booster/ajax-optional-modal-adder')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                //'dataType': 'json',

            data: $('#controlpanel_form').serialize() + "&cp_id=" + cp_id,
                // contentType: false,

            success: function (response) {
                $("#tab2").html('');
                $("#tab2").html(response.data);
                // $("#adder-optional-modal").show();
            },
            error: function (data) {
            }
        });
    }

    $(document).on("click", '#add-mechanical-adder', function (event) {
        var mechanicaAdderIds = [];
        var code60 = '';
        var code61 = '';
        var code65 = '';
        var code66 = '';
        var code67 = '';
        var manifold_material_pi = $("#manifold_material_pi").val();
        $('input[name="adder_mechnical_id"]:checked').each(function () {
            mechanicaAdderIds.push($(this).val());
        });
        if ($("#code60").val()) {

            code60 = $("#code60").val();
            mechanicaAdderIds.push(60);
        }
        if ($("#code61").val()) {

            code61 = $("#code61").val();
            mechanicaAdderIds.push(61);
        }

        if ($("#code65").val()) {

            code65 = $("#code65").val();
            mechanicaAdderIds.push(65);
        }
        if ($("#code66").val()) {

            code66 = $("#code66").val();
            mechanicaAdderIds.push(66);
        }
        if ($("#code67").val()) {

            code67 = $("#code67").val();
            mechanicaAdderIds.push(67);
        }

        if (mechanicaAdderIds.length >= 1) {
            var url = "{{url('booster-set/mechanical-ajax-adder-calculate')}}";
            $.ajax({
                type: "get",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                // 'dataType': 'json',

                data: $('#controlpanel_form').serialize() + "&mechanical_adder_ids=" + mechanicaAdderIds
                        + "&code60=" + code60 + "&code61=" + code61 + "&code65=" + code65 + "&code66=" + code66 + "&code67=" + code67
                        + "&manifold_material_pi=" + manifold_material_pi + "&no_of_pumps=" + $('#no_of_pump option:selected').val() +
                        "&manifold=" + $('#manifold_material_pi').val() + "&ptp_distance_id=" + $("#ptp-distance-id").val()
                        + "&system_pressure=" + $('#system_pressure_pi option:selected').val(),

                //             contentType: false,

                success: function (response) {

                    if (response.mechanical_adder_price) {
                        $("#code-price-mechanical").val(response.mechanical_adder_price);
                         $("#optional-add-success-modal").show();
                            setTimeout(function () {
                                $("#optional-add-success-modal").hide();
                            }, 2000);
                    }



                },
                error: function (data) {

                }

            });
        }
    });

    $("#system_pressure_pi").on('change', function () {
        calculateMechanicalComponent();
    });

    $("#motor_power_pi").on('change', function () {
        calculateMechanicalComponent();
    });

    $("#manifold_material_pi").on('change', function () {
        calculateMechanicalComponent();
    });

    $("#supply_voltage_pi").on('change', function () {
        calculateMechanicalComponent();
    });

    $("#calculate").on('click', function () {
        var adder_code_price = $('#code-price').val();
        var enclousreItem = $('#adder-enclousre-area-item').val();
        $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajaxFilter')}}",
                data: $('#controlpanel_form').serialize() + "&code_price=" + adder_code_price + "&enclousreItem=" + enclousreItem,
                success: function (response){
                    if(response.data.enclourse_exist == null)
                    {
                        alert("Product not available, Enclosure item is not available..!!");
                    }
                    else
                    {
                        var control_panel_price_for_booster = response.data.control_panel_price_for_booster;
                        var adder_code_price = $('#code-price').val();
                        var enclousreItem = $('#adder-enclousre-area-item').val();
                        var pump_model = $('#pump_model_pi').val();
                        var no_of_pumps = $('#no_of_pump option:selected').val();
                        var pump_height_pi = $('#pump_height_pi').val();
                        var cp_height = $('#cp_height').val();
                        var cp_width = $('#cp_width').val();
                        var system_pressure = $('#system_pressure_pi option:selected').val();
                        var starter_type = $('#cp_starter_type').val();
                        var range = $('#cp_range').val();
                        var power = $('#power_rating option:selected').text();
                        var voltage = $('#voltage option:selected').text();
                        var manifold = $('#manifold_material_pi').val();
                        var cp_price = $('#cp_price').val();
                        var unit_price_pi = $('#unit_price_pi').val();
                        var code_price = $("#code-price").val(); //Electrical adder code price
                        var article_number = $('#article_number_pi').val();
                        var mechanical_code_price = $("#code-price-mechanical").val(); //Mechanical adder code price
                        $.ajax({
                            type: "get",
                            url: "{{route('boosterset.calculateMechanicalComponent')}}",
                            data: {pump_model: pump_model, no_of_pumps: no_of_pumps,
                                pump_height: pump_height_pi, panel_height: cp_height, panel_width: cp_width,
                                system_pressure: system_pressure,
                                starter_type: starter_type, range: range,
                                voltage: voltage, power: power, manifold: manifold,
                                cp_price: control_panel_price_for_booster, pump_unit_price: unit_price_pi, code_price: code_price,
                                mechanical_code_price: mechanical_code_price,article_number:article_number
                            },
                            success: function (response) {
                                if (response.data.booster_price){
                                    var adder_code_price = $('#code-price').val();
                                    var enclousreItem = $('#adder-enclousre-area-item').val();
                                    var totalPrice = $("#total-price").val(response.data.booster_price);
                                    var standard_component_price = $("#standard_component_price").val(response.data.standard_component_price);
                                    var mechanical_system_price = $("#mechanical_system_price").val(response.data.mechanical_system_price);
                                    var cablePrice = $("#cablePrice").val(response.data.cablePrice);

                                    $('#control_panel_price_for_booster').val(control_panel_price_for_booster);

                                    var ptp_distance_id = $("#ptp-distance-id").val(response.data.ptp_distance_id);
                                    var mechanicalcomponent_billofmaterial = response.data.bill_of_material_booster; //retrieve array
                                    //alert(mechanicalcomponent_billofmaterial);
                                    mechanicalcomponent_billofmaterial = JSON.stringify(mechanicalcomponent_billofmaterial);
                                    $('#mechanicalcomponent_billofmaterial').val(mechanicalcomponent_billofmaterial);
                                    $("#master-price-record").html('');
                                    $("#master-price-record").html(response.data.html);
                                    $("#myModal").show();
                                } else {
                                    console.log('not valid');
                                    // $("#master-price-record").html('');
                                    // $("#master-price-record").html(response.data.html);
                                    // $('#addtocart').attr('disabled');
                                    // $("#myModal").show();
                                    if (response.data.html) {
                                        $("#error-modal-body").html('');
                                        $("#error-modal-body").html('<h4>' + response.data.html + '</h4>');
                                        $("#error-modal").show();
                                    }
                                    if (response.data.validation_messages) {
                                        let text = "";
                                        document.getElementById("error-modal-body").innerHTML = text;
                                        message = response.data.validation_messages;
                                        Object.keys(message).forEach(function (key) {
                                            $("#error-modal-body").html('<h4>' + message[key] + '</h4><br>');

                                        });

                                        $("#error-modal").show();

                                    }
                                }
                            },
                            error: function (data) {
                            }
                        });
                            }
                                },
                                    error: function (data) {
                                }
                        });
    });

    let text = "";
    document.getElementById("error-modal-body").innerHTML = text;

    function printValidationMessages() {
        text += (index + 1) + ": " + item + "<br>";
    }

    $(document).on("click", '#optional-button-add', function (event) {
        var adderIds = [];
        $('input[name="adder_id"]:checked').each(function () {
            adderIds.push($(this).val());
        });

        if (adderIds.length >= 1) {
            var cp_id = $("#cp-id").val();
            // alert(cp_id + )
            var tableName = encodeURIComponent($("#table-name").val());
            
            var columnName = $("#column-name").val();
            var data = $('#controlpanel_form').serialize();
            var url = "{{url('controlpanel/ajax-optional-selected-adder')}}";
            
            $.ajax({
                type: "post",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                data: $('#controlpanel_form').serialize() + "&adder_ids=" + adderIds + "&cp_id=" + cp_id
                        + "&table_name=" + tableName + "&column_name=" + columnName,

                success: function (response) {

                    if (response.code_price && response.starter_code == 'other') {
                        if (response.enclousreItem) {
                            $("#code-price").val(response.code_price);
                            $("#adder-enclousre-area-item").val(JSON.stringify(response.enclousreItem));
                            $("#optional-add-success-modal").show();
                            setTimeout(function () {
                                $("#optional-add-success-modal").hide();
                            }, 2000);
                            // $("#adder-optional-modal").hide();
                        } else {
                            $("#code-price").val(0);
                            alert('We have no available enclusre box. Please remove and select and another optional code.');
                        }
                    }
                    if (response.code_price && response.starter_code == 'xtreme') {
                        $("#code-price").val(response.code_price);
                    }

                //  $("#record-temp").html(controlPanel);

                },
                error: function (data) {

                }

            });
        }
    });
	
	function country_name(){
    $.ajax({
            type: "get",
            url: "{{url('country_name')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                $("#origin_country").val(response.country_name);
            },
        });
    }
   
	$(document).on("click", '#addtocart', function (event) {
        console.log("cp_price before add to cart:", $('#cp_price').val());
        var adderIds = [];
        var mechanicaAdderIds = [];
        var code60 = '';
        var code61 = '';
        var code65 = '';
        var code66 = '';
        var code67 = '';
        var full_article_number = $("#full_article_number").val();
        var cp_id = $("#cp-id").val();
        var tableName = $("#table-name").val();
        var columnName = $("#column-name").val();
        var codePrice = $("#code-price").val();
        var totalPrice = $("#total-price").val();
        var adder_code_price = $('#code-price').val();
        var enclousreItem = $('#adder-enclousre-area-item').val();
        var pump_model = $('#pump_model_pi').val();
        var article_number = $('#article_number_pi').val();
        var no_of_pumps = $('#no_of_pumps option:selected').val();
        var pump_height_pi = $('#pump_height_pi').val();
        var cp_height = $('#cp_height').val();
        var cp_width = $('#cp_width').val();
        var system_pressure = $('#system_pressure_pi option:selected').val();
        var starter_type = $('#cp_starter_type').val();
        var range = $('#cp_range').val();
        var power = $('#power_rating option:selected').val();
        var voltage = $('#voltage option:selected').val();
        var manifold = $('#manifold_material_pi').val();
        var cp_price = $('#cp_price').val();
        var unit_price_pi = $('#unit_price_pi').val();
        var code_price = $("#code-price").val(); //Electrical adder code price
        var pump_type = $("#pumpType option:selected").val();
        var mechanical_code_price = $("#code-price-mechanical").val();
        var standard_component_price = $("#standard_component_price").val();
        var mechanical_system_price = $("#mechanical_system_price").val();
        var cablePrice = $("#cablePrice").val();
        var ptp_distance_id = $("#ptp-distance-id").val();
        var no_of_pump = $('#no_of_pump option:selected').val();
        var application = $('#application option:selected').val();
        var ambient_temp = $('#ambient_temp option:selected').val();
        var stater_type = $('#stater_type option:selected').val();
        var communication_protocol = $('#communication_protocol option:selected').val();
        var ip_rating = $('#ip_rating option:selected').val();
        var component = $('#component option:selected').val();
        var enclosure = $('#enclosure option:selected').val();
        var qoutation_value = $("#qoutation_value").val();
        var mechanical_items_price = $("#mechanical_items_price").val();
        var electrical_items_price = $("#electrical_items_price").val();
        console.log("add to cart");
        console.log(electrical_items_price);
        var countryOrigin = true;
        var selectedCountry = '';
        $('input[name="adder_mechnical_id"]:checked').each(function () {
            mechanicaAdderIds.push($(this).val());
        });
        if ($("#code60").val()) {
            code60 = $("#code60").val();
            mechanicaAdderIds.push(60);
        }
        if ($("#code61").val()) {
            code61 = $("#code61").val();
            mechanicaAdderIds.push(61);
        }
        if ($("#code65").val()) {
            code65 = $("#code65").val();
            mechanicaAdderIds.push(65);
        }
        if ($("#code66").val()) {
            code66 = $("#code66").val();
            mechanicaAdderIds.push(66);
        }
        if ($("#code67").val()) {
            code67 = $("#code67").val();
            mechanicaAdderIds.push(67);
        }
        
        $('input[name="adder_id"]:checked').each(function () {
            adderIds.push($(this).val());
        });

        var mechanicalcomponent_billofmaterial = $('#mechanicalcomponent_billofmaterial').val(); //retrieve array
        mechanicalcomponent_billofmaterial = JSON.parse(mechanicalcomponent_billofmaterial);
        $.ajax({
            type: "get",
            url: "{{url('country_name')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                var origin_country = response.country_name;
                var valueCountry =  $("input[name='country_selction']:checked").val();
                if(origin_country == "ksa" && countryOrigin && valueCountry == undefined){
                     $("#countryOriginModal").show();
                     $('#Select_country_origin').on('click', function() {
                        selectedCountry = $("input[name='country_selction']:checked").val();
                        $("#countryOriginModal").hide();
                        var countryOrigin = false;
                        control_panel_price_for_booster = $('#control_panel_price_for_booster').val();
                        if(!countryOrigin){
                            // $('input[name="adder_id"]:checked').each(function () {
                            //     adderIds.push($(this).val());
                            // });
                            $.ajax({
                                type: "post",
                                url: "{{route('boosterset.addtocart')}}",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: {

                                    control_panel_price_for_booster:control_panel_price_for_booster,
                                    full_article_number:full_article_number,
                                    pump_model: pump_model,article_number:article_number, no_of_pumps: no_of_pumps,
                                    pump_height: pump_height_pi, panel_height: cp_height, panel_width: cp_width,
                                    system_pressure: system_pressure,
                                    starter_type: starter_type, range: range,
                                    voltage: voltage, power: power, manifold: manifold,
                                    cp_price: cp_price, pump_unit_price: unit_price_pi,
                                    code_price: code_price,
                                    mechanical_code_price: mechanical_code_price,
                                    adder_ids: adderIds,
                                    cp_id: cp_id,
                                    mechanical_adder_ids: mechanicaAdderIds,
                                    total_price: totalPrice,
                                    pump_type: pump_type, standard_component_price: standard_component_price,
                                    mechanical_system_price: mechanical_system_price, ptp_distance_id: ptp_distance_id,
                                    cablePrice: cablePrice,
                                    no_of_pump: no_of_pump,
                                    mechanicalcomponent_billofmaterial: mechanicalcomponent_billofmaterial,
                                    application: application,
                                    ambient_temp: ambient_temp,
                                    stater_type: stater_type,
                                    communication_protocol: communication_protocol,
                                    ip_rating: ip_rating,
                                    component: component,
                                    enclosure: enclosure,
                                    code60: code60,
                                    code61: code61,
                                    code65: code65,
                                    code66: code66,
                                    code67: code67,
                                    enclousreItem: enclousreItem,
                                    qoutation_value:qoutation_value,
                                    country:selectedCountry,
                                    mechanical_items_price:mechanical_items_price,
                                    electrical_items_price:electrical_items_price
                                },
                                success: function (response) {
                                    if (response.url) {
                                        location.reload();
                                    }
                                    if (response.msg) {
                                        alert(response.msg);
                                    }
                                },
                                error: function (data) {
                                }
                            });
                        }
                    });
                }
                else{
                    control_panel_price_for_booster = $('#control_panel_price_for_booster').val();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: "post",
                        url: "{{route('boosterset.addtocart')}}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {
                            control_panel_price_for_booster:control_panel_price_for_booster,
                            full_article_number:full_article_number,
                            pump_model: pump_model,article_number:article_number, no_of_pumps: no_of_pumps,
                            pump_height: pump_height_pi, panel_height: cp_height, panel_width: cp_width,
                            system_pressure: system_pressure,
                            starter_type: starter_type, range: range,
                            voltage: voltage, power: power, manifold: manifold,
                            cp_price: cp_price, pump_unit_price: unit_price_pi,
                            code_price: code_price,
                            mechanical_code_price: mechanical_code_price,
                            adder_ids: adderIds,
                            cp_id: cp_id,
                            mechanical_adder_ids: mechanicaAdderIds,
                            total_price: totalPrice,
                            pump_type: pump_type, standard_component_price: standard_component_price,
                            mechanical_system_price: mechanical_system_price, ptp_distance_id: ptp_distance_id,
                            cablePrice: cablePrice,
                            no_of_pump: no_of_pump,
                            mechanicalcomponent_billofmaterial: mechanicalcomponent_billofmaterial,
                            application: application,
                            ambient_temp: ambient_temp,
                            stater_type: stater_type,
                            communication_protocol: communication_protocol,
                            ip_rating: ip_rating,
                            component: component,
                            enclosure: enclosure,
                            code60: code60, code61: code61, code65: code65, code66: code66, code67: code67,
                            enclousreItem: enclousreItem,
                            qoutation_value:qoutation_value,
                            mechanical_items_price:mechanical_items_price,
                            electrical_items_price:electrical_items_price
                        },
                        success: function (response) {
                            if (response.url) {

                                location.reload();
                            //window.location = response.url;
                            }
                            if (response.msg) {
                                alert(response.msg);
                            }

                            // $("#record-temp").html(controlPanel);

                        },
                        error: function (data) {
                        }
                    });
                }
            }
        });
    });

    //Booster form validate
    function boosterValidate() {
        $("#boosterForm").validate({
            rules: {
                pump_model: "required",
                no_of_pumps: "required",
                pump_height: "required",
                panel_height: "required",
                panel_width: "required",
                system_pressure: "required",
                manifold: "required",
                cp_price: "required",
                mechanical_code_price: "required",
                pump_unit_price: "required"

            },
            messages: {
                firstname: "Pump Model Field is required",
                no_of_pumps: "No of Pumps Field is required",
                pump_height: "Pump Height is missing",
                panel_height: "Panel Height is missing.",
                panel_width: "Panel Width is missing.",
                system_pressure: "System Pressure is required",
                manifold: "Manifold is missing",
                cp_price: "Code Price is missing",
                mechanical_code_price: "Mechanical Code Price is missing",
                pump_unit_price: "Pump Unit Price is missing"
            }
        })
    }

    $("#full_article_number").bind('keypress', function (e) {
        if(e.which == 13) {
            if ($(this).val().length > 0) {
            e.preventDefault();
            var full_article_number = $('#full_article_number').val();
            var adder_code_price = $('#code-price').val();
            var enclousreItem = $('#adder-enclousre-area-item').val();
            var code_price = $("#code-price").val();
            $.ajax({
                type: "get",
                url: "{{url('/controlpanel/searchAjaxFilter')}}",
                data: "full_article_number=" + full_article_number + "&code_price=" + adder_code_price + "&enclousreItem=" + enclousreItem,
                success: function (response){
                    if (response.data.cp_records_html){
                        var range =response.data['ranges'][0]['id'];
                        var starter_type =response.data['startertypes'][0]['id'];
                        var cp_price = response.data.cp_price;
                        var cp_id = response.data.cp_id;
                        var full_article_number = response.data.full_article_number;
                        var cp_height = response.data.cp_height;
                        var control_panel_price_for_booster = response.data.control_panel_price_for_booster;
                        var cp_width = response.data.cp_width;
                        var pump_model =  response.data.pump_model;
                        var no_of_pumps = response.data['controlPanel'][0]['no_of_pump_id'];
                        var power =  response.data['powers'][0]['value'];
                        var voltage =  response.data['voltages'][0]['value'];
                        var adder_code_price = $('#code-price').val();
                        // var enclousreItem = $('#adder-enclousre-area-item').val();
                        var enclousreItem = $('#adder-enclousre-area-item').val();
                        if(enclousreItem==''){
                            var enclousreItem = JSON.stringify(response.data.enclousreItem);
                            $('#adder-enclousre-area-item').val(enclousreItem);
                        }
                        var pump_height_pi = $('#pump_height_pi').val();
                        var system_pressure = $('#system_pressure_pi option:selected').val();
                        var unit_price_pi = $('#unit_price_pi').val();
                        var code_price = $("#code-price").val(); //Electrical adder code price
                        var article_number = $('#article_number_pi').val();
                        var mechanical_code_price = $("#code-price-mechanical").val(); //Mechanical adder code price
                        $.ajax({
                            type: "get",
                            url: "{{route('boosterset.searchCalculateMechanicalComponent')}}",
                            data: {
                                full_article_number:full_article_number,
                                pump_model: pump_model, 
                                no_of_pumps: no_of_pumps,
                                pump_height: pump_height_pi,
                                panel_height: cp_height, 
                                panel_width: cp_width,
                                system_pressure: system_pressure,
                                starter_type: starter_type, 
                                range: range,
                                voltage: voltage, 
                                power: power, 
                                //manifold: manifold,
                                cp_price: control_panel_price_for_booster, 
                                pump_unit_price: unit_price_pi, 
                                code_price: code_price,
                                enclousreItem:enclousreItem,
                                //mechanical_code_price: mechanical_code_price,
                                //article_number:article_number
                            },
                            //var mechanical_code_price = $("#code-price-mechanical").val();for mechanical aders price
                            success: function (response){
                                if (response.data.booster_price){
                                    $("#code-price-mechanical").val(response.data.mechanical_code_price);
                                    var adder_code_price = $('#code-price').val();
                                    var enclousreItem = $('#adder-enclousre-area-item').val();
                                    var totalPrice = $("#total-price").val(response.data.booster_price);
                                    var standard_component_price = $("#standard_component_price").val(response.data.standard_component_price);
                                    var mechanical_system_price = $("#mechanical_system_price").val(response.data.mechanical_system_price);
                                    var cablePrice = $("#cablePrice").val(response.data.cablePrice);
                                    var ptp_distance_id = $("#ptp-distance-id").val(response.data.ptp_distance_id);
                                    var mechanicalcomponent_billofmaterial = response.data.bill_of_material_booster; //retrieve array

                                    var mechanical_items_price = $("#mechanical_items_price").val(response.data.mechanical_items_price);
                                    var electrical_items_price = $("#electrical_items_price").val(response.data.electrical_items_price);

                                    mechanicalcomponent_billofmaterial = JSON.stringify(mechanicalcomponent_billofmaterial);
                                    $('#mechanicalcomponent_billofmaterial').val(mechanicalcomponent_billofmaterial);
                                    $("#master-price-record").html('');
                                    $("#master-price-record").html(response.data.html);
                                    $("#myModal").show();
                                } else {
                                    console.log('not valid');
                                    // $("#master-price-record").html('');
                                    // $("#master-price-record").html(response.data.html);
                                    // $('#addtocart').attr('disabled');
                                    // $("#myModal").show();
                                    if (response.data.html) {
                                        $("#error-modal-body").html('');
                                        $("#error-modal-body").html('<h4>' + response.data.html + '</h4>');
                                        $("#error-modal").show();
                                    }
                                    if (response.data.validation_messages) {
                                        let text = "";
                                        document.getElementById("error-modal-body").innerHTML = text;
                                        message = response.data.validation_messages;
                                        Object.keys(message).forEach(function (key) {
                                            $("#error-modal-body").html('<h4>' + message[key] + '</h4><br>');
                                        });
                                        $("#error-modal").show();
                                    }
                                }
                            },
                            error:function(data){
                            }
                        });
                    }
                    else
                    {
                        $("#error-modal").show();
                        $("#error-modal-body").html(response.data.cp_records_html_error);
                    }
                },
                    error: function(data){
                }
            });
            }
        }
    });
</script>
@stop







