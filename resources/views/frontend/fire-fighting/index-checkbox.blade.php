@php
    $tool_tip = DB::table('tool_tip')->where('part_id',1)->get();
    foreach($tool_tip as $t){
        $key = $t->component_name;
        $t->$key = $t->tool_tip;
    }
@endphp

@extends('frontend.layout.app')

@section('content')
<section class="midContent" id="midContent">
    <div class="container">
        <div class="flex-center">
            <div class="pumpInfoMidSection">
                <div class="pumpInfoList">
                    <form id="firefightingForm">
                        <div class="accSec1">
                           <div class="panel formWidget mh-100">
                                <div class="panelBody">
                                    <div class="tabNav px-2">
                                        <button type="button" class="tabLinks" id="tabDefaultOpen" onclick="openTab(event, 'main_pump_panel')" value="main_pump_panel">Main Pump</button>
                                        <button type="button" class="tabLinks" onclick="openTab(event, 'jockey_pump_panel')" value="jockey_pump_panel">Jockey Pump</button>
                                    </div>
                                    <div class="tabContentWrapper">
                                        {{-- Main Pump Panel --}}
                                        <div class="tabContent" id="main_pump_panel">
                                            <div class="row">
                                                <div class="col-12 px-3 mx-1 main_panel_selection_hide_show">
                                                    <h6>Main Pump</h6>
                                                </div>
                                                <div class="col-12 main_panel_selection_hide_show">
                                                    <div class="formFields px-2">
                                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                                        <select name="main_panel_selection" id="main_panel_selection" class="formInput main_panel_selection electrical-formInput">
                                                            <option value="">Select Pump Models*</option>
                                                            <option value="electrical">Electrical</option>
                                                            <option value="diesel">Diesel</option>
                                                            <option value="electrical-diesel">Electrical & Diesel</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-12 d-flex justify-content-around">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input main_panel_selection" id="electricalCheck" name="main_panel_selection" value="electrical">
                                                        <label class="custom-control-label" for="electricalCheck">Electrical</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input main_panel_selection" id="dieselCheck" name="main_panel_selection" value="diesel">
                                                        <label class="custom-control-label" for="dieselCheck">Diesel</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input main_panel_selection" id="electricalDieselCheck" name="main_panel_selection" value="electrical-diesel">
                                                        <label class="custom-control-label" for="electricalDieselCheck">Electrical & Diesel</label>
                                                    </div>
                                                </div> --}}
                                            </div>

<div class="row mt-3">
    <div class="col-12 d-flex justify-content-around flex-nowrap">
        {{-- Main -> Electrical --}}
        <div class="px-2 w-100 main_panel_section-hide electrical-section-show electrical-diesel-section-show">
            <div class="row">
                <div class="col-12">
                    <div class="formFields">
                        <input type="text" name="electrical_article_number" id="electrical_article_number" class="formInput" placeholder="Pump article number*">
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_pumpmodels" id="electrical_pumpmodels" class="formInput electrical-formInput">
                            <option value="">Select Pump Models*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_pumptype" id="electrical_pumptype" class="formInput electrical-formInput">
                            <option value="">Select Pump type*</option>
                            <option>End suction</option>
                            <option>Splitcase</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_frequency" id="electrical_frequency" class="formInput electrical-formInput">
                            <option value="">Select Frequency*</option>
                            <option>50</option>
                            <option>60</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_pump_approval" id="electrical_pump_approval" class="formInput electrical-formInput">
                            <option value="">Select Pump approval*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_flow" id="electrical_flow" class="formInput electrical-formInput">
                            <option value="">Select Flow*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_head" id="electrical_head" class="formInput electrical-formInput">
                            <option value="">Select Head*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="electrical_speed" id="electrical_speed" class="formInput electrical-formInput">
                            <option value="">Select Speed*</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main -> Diesel --}}
        <div class="px-2 w-100 main_panel_section-hide diesel-section-show electrical-diesel-section-show">
            <div class="row">
                <div class="col-12">
                    <div class="formFields">
                        <input type="text" name="diesel_article_number" id="diesel_article_number" class="formInput" placeholder="Pump article number*">
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_pumpmodels" id="diesel_pumpmodels" class="formInput diesel-formInput">
                            <option value="">Select Pump Models*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_pumptype" id="diesel_pumptype" class="formInput diesel-formInput">
                            <option value="">Select Pump type*</option>
                            <option>End suction</option>
                            <option>Splitcase</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_frequency" id="diesel_frequency" class="formInput diesel-formInput">
                            <option value="">Select Frequency*</option>
                            <option>50</option>
                            <option>60</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_pump_approval" id="diesel_pump_approval" class="formInput diesel-formInput">
                            <option value="">Select Pump approval*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_engine_approval" id="diesel_engine_approval" class="formInput diesel-formInput">
                            <option value="">Select Engine approval*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_flow" id="diesel_flow" class="formInput diesel-formInput">
                            <option value="">Select Flow*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_head" id="diesel_head" class="formInput diesel-formInput">
                            <option value="">Select Head*</option>
                        </select>
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="diesel_speed" id="diesel_speed" class="formInput diesel-formInput">
                            <option value="">Select Speed*</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="optBtn m-0 d-flex justify-content-center"><a href="javascript:void(0)" id="optional-button" class="main_panel_section-hide diesel-section-show electrical-section-show electrical-diesel-section-show">Optional</a></div>
    </div>
</div>

                                        </div>
                                        <div class="tabContent" id="jockey_pump_panel">
<div class="row mt-3">
    <div class="col-12 px-3 mx-1">
        <h6>Jockey Pump</h6>
    </div>
    <div class="col-12 d-flex justify-content-around flex-nowrap">
        {{-- Jockey --}}
        <div class="px-2 w-100 jockey_panel_section">
            <div class="row">
                <div class="col-12">
                    <div class="formFields">
                        <input type="text" name="jockey_article_number" id="jockey_article_number" class="formInput" placeholder="Pump article number*">
                    </div>
                    <div class="formFields">
                        <input type="text" name="jockey_pumppower" id="jockey_pumppower" class="formInput jockey-formInput" placeholder="Pump Power*">
                    </div>
                    <div class="formFields">
                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                        <select name="jockey_frequency" id="jockey_frequency" class="formInput jockey-formInput">
                            <option value="">Select Frequency*</option>
                            <option>50</option>
                            <option>60</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="optBtn m-0 d-flex justify-content-center"><a href="javascript:void(0)" id="jockey-optional-button" class="">Optional</a></div>
                </div>
            </div>
        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
                Unit Price:
                <button id="calculate" class="clcBtn">Calculate</button>  <span id="price"></span>
            </div>
            <div class="right">
                <ul>
                    <li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
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


@section('script')
<script>

    $('.main_panel_section-hide').hide();

    $('.tabLinks').each(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        }
    });
    $('.main_panel_selection_hide_show').hide();
    $(document).on('click', '.tabLinks', function () {
        if ($(this).val() == 'main_pump_panel') {
            $('.main_panel_selection_hide_show').show();
            $('.tabNav').hide();
        } else if ($(this).val() == 'jockey_pump_panel') {
            $('.tabNav').hide();
        }
    });

    mainPanelShow($('.main_panel_selection:checked'));
    $(document).on('change', '.main_panel_selection', function () {
         mainPanelShow($(this));
         $('.main_panel_selection_hide_show').hide();
    });


    disableInput('electrical-formInput');
    $(document).on('change', '.electrical-formInput', function () {
        disableInput('electrical-formInput');
    });

    disableInput('diesel-formInput');
    $(document).on('change', '.diesel-formInput', function () {
        disableInput('diesel-formInput');
    });


    function mainPanelShow(thisv) {
        var main_panel_section = thisv.val();
        
        $('.main_panel_section-hide').each(function () {
            $(this).hide();
        });

        $('.'+main_panel_section+'-section-show').each(function () {
            $(this).show();
        });

        $('.main_panel_selection').each(function () {
            $(this).prop('checked', false);
        });
        thisv.prop('checked', true);
    }

    function disableInput(inputClass) {
        var disable_inp = false;
        $('.'+inputClass).each(function () {
            if (!disable_inp) {
                $(this).removeAttr('disabled');
                if ($(this).val() == '') {
                    disable_inp = true;
                }
            } else {
                $(this).attr('disabled', 'disabled');
            }
        });
    }
</script>
@stop

@section('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
<style>
    .pumpInfoMidSection .pumpInfoList .panel {
        max-height: 100%;
    }

    .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
        background-color: #169e88;
    }
    .custom-control-label::after {
        border: 1px solid #169e88;
        border-radius: 5px;
    }

    .formWidget .formFields .formArrowIcon {
        top: 20%;
    }

    .optBtn a {
/*        display: none;*/
    }
    a:hover {
        color: #169e88;
        text-decoration: none;
    }

    .pumpInfoMidSection .pumpInfoList .panelBody .tabNav button.tabLinks {
        border: 0.1px solid #169e88;
    }

    .pumpInfoMidSection .pumpInfoList .panelBody .tabNav button.tabLinks:nth-child(1) {
        border-right: none;
    }


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
@stop