@php
    $tool_tip = DB::table('tool_tip')->where('part_id',6)->get();    
    foreach($tool_tip as $t){
        $key = $t->component_name; 
        $t->$key = $t->tool_tip;
    }    
@endphp
@extends('frontend.layout.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .error{outline: 1px solid red;}
    .pump_model_manual_add {
        width: 30px;height: 30px;background: #169e88;display: block;text-align: center;color: white;font-weight: bold;position: absolute;
        right: -2.5rem;top: 0.5rem;border-radius: 100%;cursor: pointer;
    }
    .pump_model_manual_add:hover{color: white !important;}
    .modalBtnsManual{margin-top: 15px !important;}
    .manual_modal_btn{width: 25% !important;}
    .col-4 {flex: 0 0 40%;max-width: 40%;box-sizing: border-box;padding: 5px;}
    .col-6 input {width: 100%;padding: 5px;box-sizing: border-box;}
    .row {display: flex;flex-wrap: wrap;margin-bottom: 10px;}#impeller_material_manual{appearance: auto !important;width: 100%;padding: 6px;}
    /* Change styles for cancel button and delete button on extra small screens */
    @media screen and (max-width: 300px) {
        .cancelbtn, .deletebtn {width: 100%;}
    }
</style>
@section('content')
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="formsMidSection">
                <h2>SCPV Pumps</h2>
                <div class="formWidget">
                    <ul id='errors'>
                    </ul>
                    <form action="" id="scpv_form">
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide"></div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="bareshaft_pump_full_pump" id="bareshaft_pump_full_pump" class="formInput">
                                <option value="both_pump">Select Full pump Or Manual pump</option>
                                <option value="full_pump">Full pump</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>   
					    <div class="formFields" id="full_article_number_section">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[0]->full_article_number ?? ""}}</div>
                            </div>
                            <input type="text" class="formInput" name="full_article_number" id="full_article_number" placeholder="Full Article Number">
                        </div>
                        <div class="formFields" id="bareshaft_pump_article_number_section"  style="display: none;">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide"></div>
                            </div>
                            <input type="text" class="formInput" name="bareshaft_pump_article_number" id="bareshaft_pump_article_number" placeholder="Bareshaft Pump Article Number">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>   
                                {{-- A Code: 20-02-2026 Start --}}                             
                                <div class="popper-content hide">{{$tool_tip[1]->pump_model ?? ""}}</div>
                                <a class="pump_model_manual_add d-none" id="pump_model_manual_add" style="left:110% !important;">+</a>
                                {{-- A Code: 20-02-2026 End --}}   
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="pump_model" id="pump_model" class="formInput">
                                <option value="">Pump Model</option>
                                @foreach($pump_types as $pt)
                                <option value={{$pt->id}}>{{$pt->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[2]->impeller_material ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="impeller_material" id="impeller_material" class="formInput" disabled>
                                <option value="">Impeller Material</option>
                                @foreach($atmos_materials as $am)
                                <option value={{$am->id}}>{{$am->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- A Code: 05-03-2026 Start -->
                        <div class="formFields" style="display:none;" id="pump_model_flow_head">
                            <input type="text" name="pump_model_flow" id="pump_model_flow" class="formInput" placeholder="Flow*" style="margin-right: 10px;">
                            <input type="text" class="formInput" placeholder="-m³/h" style="margin-right: 10px; width:30%;" readonly>
                            <input type="text" name="pump_model_head" id="pump_model_head" class="formInput" placeholder="Head*" style="margin-right: 10px;">
                            <input type="text" class="formInput" placeholder="- mm" style="width:30%;" readonly>
                        </div>

                        <div class="formFields" style="display:none;" id="pump_model_impeller_required">
                            <input type="text" name="pump_model_impeller_size" id="pump_model_impeller_size" 
                            class="formInput" placeholder="Impeller standard size*" style="margin-right: 10px;" readonly>
                            <input type="text" class="formInput" placeholder="- mm" style="margin-right: 10px; width:30%;" readonly>
                            <input type="text" name="pump_model_required_size" id="pump_model_required_size" 
                            class="formInput" placeholder="Required impeller size*" style="margin-right: 10px;">
                            <input type="text" class="formInput" placeholder="- mm" style="width:30%;" readonly>
                        </div>
                        <!-- A Code: 05-03-2026 End -->

                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[3]->seal_gland_pack ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="s_g_pack" id="s_g_pack" class="formInput" >
                                <option value="">Seal/gland pack</option>
                                @foreach(Config::get('constants.seal_gland_pack') as $key=>$v)
                                <option value={{$v}}>{{$key}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[4]->motor_power ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="motor_power" id="motor_power" class="formInput" disabled>
                                <option value="">Motor Power</option>
                                @foreach($power as $p)
                                <option value={{$p}}>{{$p}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[5]->power_supply ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="power_supply" id="power_supply" class="formInput" disabled>
                                <option value="">Power Supply</option>
                                @foreach($voltage as $v)
                                <option value={{$v}}>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[6]->frequency ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="frequency" id="frequency" class="formInput" disabled>
                                <option value="">Frequency</option>
                                @foreach($frequency as $f)
                                <option id={{$f}} value={{$f}}>{{$f}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[7]->no_of_poles ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="poles" id="poles" class="formInput" disabled>
                                <option value="">Number of Poles</option>
                                @foreach($poles as $p)
                                <option value={{$p}}>{{$p}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[8]->efficiency ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="efficiency" id="efficiency" class="formInput" disabled>
                                <option value="">Efficiency</option>
                                @foreach($efficiency as $e)
                                <option value={{$e}}>{{$e}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[9]->motor_brand ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="motor_brand" id="motor_brand" class="formInput" disabled>
                                <option value="">Motor Brand</option>
                                @foreach($brand as $b)
                                <option value={{$b}}>{{$b}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[10]->application ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="application" id="application" class="formInput" disabled>
                                <option value="">Application</option>
                                @foreach(Config::get('constants.atmos_giga_application_options') as $key=>$v)
                                <option value={{$v}}>{{$key}}</option>
                                @endforeach
                            </select>
                        </div>                        

                        <!-- A Code: 05-03-2026 Start -->
                        <input type="hidden" name='origin_country' id="origin_country" class="origin_country">  
                        <!-- A Code: 05-03-2026 End -->
						
                        <input type="hidden" name='bare_shaft_price' id="price_hidden" class="price_hidden">                        
                        <input type="hidden" name='is_bare_shaft_price_manual' id="is_bare_shaft_price_manual" value="0">

                        <!-- A Code: 05-03-2026 Start -->
                        <input type="hidden" name='is_shipping_charge_manual' id="is_shipping_charge_manual" value="0">
                        <input type="hidden" name='shipping_price_manual' id="shipping_price_manual_hidden" class="shipping_price_manual_hidden">    
                        <!-- A Code: 05-03-2026 End -->

                        <input type="hidden" name='acessories_price' id="acessories_price_hidden" class="acessories_price_hidden">

                        <!-- A Code: 05-03-2026 Start -->
                        <input type="hidden" name='manual_acessories_price' id="manual_acessories_price_hidden" class="manual_acessories_price_hidden">
                        <!-- A Code: 05-03-2026 End --> 

                        <input type="hidden" name='is_acessories_price_manual' id="is_acessories_price_manual" value="0">

                        <!-- A Code: 05-03-2026 Start -->
                        <input type="hidden" name='is_acessories_price_manual1' id="is_acessories_price_manual1" value="0">
                        <!-- A Code: 05-03-2026 End --> 

                        <input type="hidden" name='motor_price' id="motor_price_hidden" class="motor_price_hidden">
                        <input type="hidden" name='frame_size' id="frame_size" class="frame_size">
                        <input type="hidden" name='master_price_id' id="master_price_id" class="master_price_id">
                        <input type="hidden" name='master_last_price_id' id="master_last_price_id" class="master_last_price_id">
                        <input type="hidden" name='code_price' id="code-price">
                        <input type="hidden" name='total_price' id="total-price">

                        <!-- A Code: 05-03-2026 Start -->
                        <input type="hidden" name='impeller_minimum_size' id="impeller_minimum_size">
                        <input type="hidden" name='impeller_maximum_size' id="impeller_maximum_size">
                        <input type="hidden" name='is_bare_shaft_article_number_method' id="is_bare_shaft_article_number_method" value="manual">
                        <!-- A Code: 05-03-2026 End --> 

                    </form>
                </div>
                <div class="optBtn"><a href="javascript:void(0)" id="optional-button">Optional</a></div>
            </div>
        </div>
        <div class="d-flex cusPagination d-none">
            <div class="">
                <a href=""><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
            <div class="">
                <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
            </div>
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
                Unit Price:
                <!--Unit Price: -->
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
<!-- mid section end -->

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

<div id="manual_price" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="">
            <!--Table-->
            <input type="number" class="form-control" id="manual_price_val" name="manual_price_val">
        </div>
        <div class="modalBtns">
            <button id="insert_price">Add Price</button>
            <span class="close" onclick="refresh()">Cancel</span>
            <span class="close-cart-modal" >Close</span>
        </div>
    </div>
</div>

<!-- Pump model manual price -->
<div id="pump_model_manual_price" class="modal">
    <div class="modal-content">
        <form id="manual_pump_form">
            <div class="modal-body" id="">
                <div class="row">
                    <div class="col-4">
                        <span>Pump model</span>
                    </div>
                    <div class="col-6">
                        <input type="text" name="manual_pump_model_name" class="manual_pump_model_name" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Impeller type</span>
                    </div>
                    <div class="col-6">
                        <select name="manual_pump_model_impeller_material" id="impeller_material_manual" class="formInput manual_pump_model_impeller_material" required>
                            <option value="">Impeller Material</option>
                            @foreach($atmos_materials as $am)
                            <option value={{$am->id}}>{{$am->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                         <span>Bare shaft price</span>
                    </div>
                    <div class="col-6">
                        <input type="text" name="manual_pump_model_bare_shaft_price" class="manual_pump_model_bare_shaft_price" required>
                    </div>            
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Accessories price</span>
                    </div>
                    <div class="col-6">
                        <input type="text" name="manual_pump_model_acessories_price" class="manual_pump_model_acessories_price" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Shipping price</span>
                    </div>
                    <div class="col-6">
                        <input type="text" name="manual_pump_model_shipping_price" class="manual_pump_model_shipping_price" required>
                    </div>
                </div>
            </div>

            <div class="modalBtnsManual modalBtns">
                <button id="manual_pump_model_add_btn" class="manual_add_pump_model_btn">Add Manual</button>
                <span class="close manual_modal_btn" onclick="refresh()">Cancel</span>
            </div>
        </form>

    </div>
</div>

<div id="manual_acessories_price_modal" class="modal">
    <div class="modal-content" id="">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="">
            <!--Table-->
            <input type="number" class="form-control" id="manual_acessories_price" name="manual_acessories_price">
            <span>Product not available enter manually</span>
        </div>
        <div class="modalBtns">
            <button id="insert_price_accessories">Add Accesories Price</button>
            <span class="close" onclick="refresh()">Cancel</span>

        </div>
    </div>
</div>

<div id="adder-optional-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-body" id="adder-optional-modal-table">
        </div>
        <div class="modalBtns">
            <span class="close" id="optional-button-add">Add</span>
            <span class="close" id="optional-button-close">Close</span>
        </div>
    </div>
</div>

<div class="loader" style="display:none" id="loader"></div>
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

<div id="optional-add-success-modal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="">
            <h4>Optional added Successful!</h4>
        </div>
        <div class="modalBtns">
            <span class="close" id="error-close">Close</span>
        </div>
    </div>
</div>

<div class="modal alert_data" id="alert_data" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Not Available</h5>
        <button type="button" class="close" id="refresh_close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="error_statement"></p>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
@endsection

<style>
    /* Float cancel and delete buttons and add an equal width */
    .cancelbtn, .deletebtn {float: left; width: 50%;}

    /* Add a color to the cancel button */
    .cancelbtn {background-color: #ccc;color: black;}

    /* Add a color to the delete button */
    .deletebtn {background-color: #f44336;}

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;top: 0;width: 100%;height: 100%;
        overflow: auto; /* Enable scroll if needed */
        background-color: #474e5d;padding-top: 50px;
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    /* Style the horizontal ruler */
    hr {border: 1px solid #f1f1f1;margin-bottom: 25px;}

    /* The Modal Close Button (x) */
    .close {position: absolute;right: 35px;top: 15px;font-size: 40px;font-weight: bold;color: #f1f1f1;}

    .close:hover,
    .close:focus {color: #f44336;cursor: pointer;}

    /* Clear floats */
    .clearfix::after {content: "";clear: both;display: table;}
    li.error {color: red;}

    /* Change styles for cancel button and delete button on extra small screens */
    @media screen and (max-width: 300px) {
        .cancelbtn, .deletebtn {width: 100%;}
    }
</style>
@section('script')
<script>
    // pump_model
    $("#pump_model").on('change', function (e) {
        $(this).val() == "" ? $("#impeller_material").attr('disabled', 'disabled')
                : $("#impeller_material").removeAttr('disabled');
    });

    $("#pump_model_manual_add").on('click',function(event){
        event.preventDefault();
        $("#pump_model_manual_price").show();
    });

    $("#manual_pump_form").on('submit', function(event) {
        event.preventDefault();
        var pump_model_val = $(".manual_pump_model_name").val();
        var impeller_material_val = $("#impeller_material_manual").val();
        var bare_shaft_price = $(".manual_pump_model_bare_shaft_price").val();
        var accessories_price = $(".manual_pump_model_acessories_price").val();
        var shipping_price = $(".manual_pump_model_shipping_price").val();
        var newOption = new Option(pump_model_val, pump_model_val, true, true);
        $("#pump_model").append(newOption).val(pump_model_val);
        $("#pump_model_manual_price").hide();
        if ($("#impeller_material option[value='" + impeller_material_val + "']").length > 0) {
            $("#impeller_material").val(impeller_material_val).find("option[value='" + impeller_material_val + "']").prop("selected", true);
        }
        $("#impeller_material").prop("disabled", true);
        $("#pump_model").prop("disabled", true); 
        // Add hidden fields to capture the values for submission
        if (!$("#hidden_impeller_material").length) {
            $('<input>').attr({
                type: 'hidden',
                id: 'hidden_impeller_material',
                name: 'impeller_material'
            }).appendTo('#scpv_form'); // A Code: 06-03-2026
        }
        $("#hidden_impeller_material").val(impeller_material_val);

        if (!$("#hidden_pump_model").length) {
            $('<input>').attr({
                type: 'hidden',
                id: 'hidden_pump_model',
                name: 'pump_model'
            }).appendTo('#scpv_form'); // A Code: 06-03-2026
        }
        $("#hidden_pump_model").val(pump_model_val);
        $("#motor_power").removeAttr('disabled');
        $("#is_bare_shaft_price_manual").val(1);
        $("#is_acessories_price_manual").val(1);
        $("#is_shipping_charge_manual").val(1);
        $("#is_acessories_price_manual1").val(1);
        $("#price_hidden").val(bare_shaft_price);
        $("#manual_acessories_price_hidden").val(accessories_price);
        $("#acessories_price_hidden").val(accessories_price);
        $("#shipping_price_manual_hidden").val(shipping_price);
        $("#pump_model_flow_head").css('display','none');
        $("#pump_model_impeller_required").css('display','none');
    });

    $("#pump_model_required_size").focusout(function(){
        var required_size_price = $(this).val();
        var minimum_price = $("#impeller_minimum_size").val();
        var maximum_price = $("#impeller_maximum_size").val();
        if(required_size_price < minimum_price || required_size_price > maximum_price){
            alert("Required size should be between" + minimum_price + ' & ' + maximum_price);
            $("#motor_power").attr('disabled', 'disabled');
            return false;
        }
        else{
            var bareshaft_pump_full_pump = $("#bareshaft_pump_full_pump").val();
            if(bareshaft_pump_full_pump != "bareshaft_pump"){
                $("#motor_power").removeAttr('disabled');
            }
        }
    }); 

    $("#s_g_pack").on('change', function (e) {
        $("#loader").show();
        ajax_data = {
            'impeller_id': $("#impeller_material").val(),
            'pump_model': $("#pump_model").val(),
            'sg_pack': $(this).val()
        };
        var url = "{{url('scpv_price')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,
            success: function (res) {
                if (res != "price not found") {
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#motor_power").removeAttr('disabled');
                    $("#price_hidden").val(res);
                    $("#is_bare_shaft_price_manual").val(0);
                } else {
                    $('#manual_price').show();
                }
            }
        });
    });

    $("#insert_price").click(function () {
        $("#price_hidden").val($("#manual_price_val").val());
        $("#manual_price").hide();
         $("#motor_power").removeAttr('disabled');
        $("#is_bare_shaft_price_manual").val(1);
    });

    $("#insert_price_accessories").click(function () {
        $("#acessories_price_hidden").val($("#manual_acessories_price").val());
        $("#manual_acessories_price_modal").hide();
        $("#is_acessories_price_manual").val(1);
    });

    $("#motor_power").on('change', function (e) {
        $(this).val() == "" ? $("#power_supply").attr('disabled', 'disabled')
                : $("#power_supply").removeAttr('disabled');
    });

    $("#power_supply").on('change', function (e) {
        var power_supply = $(this).children("option:selected").val();
        if(power_supply == 400){
            $("#60").attr('disabled', 'disabled');
            $("#50").removeAttr('disabled');        
        }else{
            $("#50").attr('disabled', 'disabled');
            $("#60").removeAttr('disabled');
        }
        $(this).val() == "" ? $("#frequency").attr('disabled', 'disabled')
                : $("#frequency").removeAttr('disabled');
    });

    $("#frequency").on('change', function (e) {
        $(this).val() == "" ? $("#poles").attr('disabled', 'disabled')
                : $("#poles").removeAttr('disabled');
    });

    $("#poles").on('change', function (e) {
        $(this).val() == "" ? $("#efficiency").attr('disabled', 'disabled')
                : $("#efficiency").removeAttr('disabled');
    });

    $("#efficiency").on('change', function (e) {
        $(this).val() == "" ? $("#motor_brand").attr('disabled', 'disabled')
                : $("#motor_brand").removeAttr('disabled');
    });

    $("#motor_brand").on('change', function (e) {
        $("#loader").show();
        ajax_data = {
            'poles': $("#poles").val(),
            'motor_brand': $(this).val(),
            'frequency': $("#frequency").val(),
            'motor_power': $("#motor_power").val(),
            'effieciency': $("#efficiency").val(),
            'power_supply': $("#power_supply").val()
        };
        var url = "{{url('get_scpv_frame')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,

            success: function (res) {

                if (res != 0) {
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#application").removeAttr('disabled');
                    $("#frame_size").val(res.frame_size);
                    $("#master_price_id").val(res.id);
                    //get_accessories(res);
                    // A Code Test: 18-03-2026 Start
                    if($("#is_acessories_price_manual1").val() == "0"){
                        get_accessories(res);
                    }
                    // A Code Test: 18-03-2026 End

                } else {
                    var pole = $('#poles option:selected').val();
                    var voltage = $('#power_supply option:selected').val();
                    var motor_power = $('#motor_power option:selected').val();
                    error = pole + "pole - " + motor_power + "kW product is not avilable .";
                    jQuery.noConflict();
                    $("#error_statement").text(error);
                    $('#alert_data').show();
                    
                    $( "#refresh_close" ).click(function() {
                        location.reload();
                    });
                }
            }

        });
    });

    function refresh()
    {
        location.reload();
    }

    function get_accessories(res) {
        $("#loader").show();
        ajax_data = {
            'frame': res.frame_size,
            'pump_id': $("#pump_model").val()
        };
        var url = "{{url('get_scpv_accessories')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,
            success: function (res) {
                console.log(res);
                if (res != 0) {
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#acessories_price_hidden").val(res);
                    $("#is_acessories_price_manual").val(0);

                } else {
                    //$('#manual_acessories_price_modal').show();

                    // A Code: 06-03-2026 Start
                    var bareshaft_pump_full_pump = $('#bareshaft_pump_full_pump').val();
                    //alert(bareshaft_pump_full_pump);
                    if(bareshaft_pump_full_pump != 'manual'){
                        $('#manual_acessories_price_modal').show();
                    }
                    // A Code: 06-03-2026 End
                    
                }
            }
        });
    }

    $("#application").on('change', function (e) {
        $("#loader").show();
        ajax_data = {
            "val": $(this).val(),
            "master_price_id": $("#master_price_id").val()
        };
        var url = "{{url('get_scpv_motor_price')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,
            success: function (res) {
                if (res != 0) {
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#motor_price_hidden").val(res);
                } else {
                    $("#error_statement").text("No Product Found.");
                    $('#alert_data').show();
                    
                    $( "#refresh_close" ).click(function() {
                        location.reload();
                    });
                }
            }
        });
    });

    $("#calculate").on('click', function () {
        var isEmpty = true;
        $('#scpv_form  select').each(
                function (index) {
                    var input = $(this);
                    if (input.val() == "") {
                        isEmpty = false;
                    }
                }
        );
        if (!isEmpty) {
            $("#error-modal-body").html('');
            $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
            $("#error-modal").show();
            return;
        }
        $("#loader").show();
        var adder_code_price = $('#code-price').val();
        $.ajax({
            type: "get",
            url: "{{url('scpv/ajaxCalculate')}}",
            data: $('#scpv_form').serialize() + "&code_price=" + adder_code_price,
            success: function (response) {
                if (response.data.cp_records_html) {
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#price").html('');
                    $("#price").html(response.data.cp_price + '$');
                    $("#total-price").val(response.data.total_price);
                    $("#master-price-record").html('');
                    $("#master-price-record").html(response.data.cp_records_html);
                    $("#myModal").show();
                }
            },
            error: function (data) {
            }
        });
    });

    $("#optional-button").on('click', function () {
        var isEmpty = true;
        $('#scpv_form  select').each(
            function (index) {
                var input = $(this);
                if (input.val() == "") {
                    isEmpty = false;
                }
            }
        );
        if (isEmpty) {
            var master_id = $("#master_price_id").val();
            var last_master_id = $("#master_last_price_id").val();
            if (last_master_id == '' || last_master_id != master_id) {
                $("#master_last_price_id").val(master_id);
                $.ajax({
                    type: "post",
                    url: "{{url('scpv/ajax-optional-modal')}}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: '',
                    success: function (response) {
                        $("#adder-optional-modal-table").html('');
                        $("#adder-optional-modal-table").html(response.data);
                        $("#adder-optional-modal").show();
                    },
                    error: function (data) {
                    }
                });
            } else {
                $("#adder-optional-modal").show();
            }
        } else {
            $("#error-modal-body").html('');
            $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
            $("#error-modal").show();
        }
    });

    $("#optional-button-close").on('click', function () {
        $("#adder-optional-modal").hide();
    });

    $("#error-close").on('click', function () {
        $("#error-modal").hide();
    });

    $(document).on("click", '.close-cart-modal', function (event) {
        $("#myModal").hide();
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
        var adderIds = [];
        var countryOrigin = true;
        var selectedCountry = '';
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
                        if(!countryOrigin){
                            $('input[name="adder_id"]:checked').each(function () {
                                adderIds.push($(this).val());
                            });
                            $.ajax({
                                type: "post",
                                url: "{{url('scpv/addtocart')}}",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: $('#scpv_form').serialize() + "&adder_ids=" + adderIds + "&country=" + selectedCountry,
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
                    $('input[name="adder_id"]:checked').each(function(){
                        adderIds.push($(this).val());
                    });
                    $.ajax({
                        type: "post",
                        url: "{{url('scpv/addtocart')}}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: $('#scpv_form').serialize() + "&adder_ids=" + adderIds + "&country=" + selectedCountry,
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
            }
        });
    });
    
    $(document).on("click", '#optional-button-add', function (event) {
        var adderIds = [];
        $('input[name="adder_id"]:checked').each(function () {
            adderIds.push($(this).val());
        });

        $("#adder-optional-modal").hide();
        if (adderIds.length >= 1) {
            var master_id = $("#master_price_id").val();
            var url = "{{url('scpv/ajax-optional-selected-adder')}}"; // A Code: 19-03-2026
            $.ajax({
                type: "post",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $('#scpv_form').serialize() + "&adder_ids=" + adderIds + "&master_price_id=" + master_id,
                success: function (response) {
                    if (response.code_price) {
                        $("#code-price").val(response.code_price);
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

    // Code for search by full article number 
    // $("#full_article_number").bind('keypress',function(e){
    //         if(e.which == 13){
    //             if($(this).val().length >1)
    //             {
    //                 e.preventDefault();
    //                 var full_article_number = $("#full_article_number").val();
    //                 $.ajax({
    //                 type: "get",
    //                 url: "{{route('scpv.searchbyarticle')}}",
    //                 data: {full_article_number: full_article_number},
    //                 success: function (response){
    //                     if(response.data.cp_records_html) {
    //                     $(document).ajaxComplete(function () {
    //                         $("#loader").hide();
    //                     });
    //                     $("#price").html('');
    //                     $("#price").html(response.data.cp_price + '$');
    //                     $("#total-price").val(response.data.total_price);
    //                     $("#master-price-record").html('');
    //                     $("#master-price-record").html(response.data.cp_records_html);
    //                     $("#myModal").show();
    //                     }
    //                     else {
    //                         $("#error-modal").show();
    //                         $("#error-modal-body").html(response.data.cp_records_html_error);
    //                     }
    //                     },
    //                     error:function(data){

    //                     }
    //                 });
    //             }
    //         }
    // });

    // $(document).on("change","#bareshaft_pump_full_pump",function(){
    //     var value = $(this).val();
    //     if(value == "full_pump" || value == "both_pump"){
    //     }
    //     else if(value == "bareshaft_pump"){
    //         $("#full_article_number_section").css('display','none');
    //         $("#bareshaft_pump_article_number_section").css('display','block');
    //         $("#pump_model_manual_add").css('display','none ');
    //     }
    //     else if(value == "manual"){
    //         $("#full_article_number_section").css('display','block');
    //         $("#bareshaft_pump_article_number_section").css('display','none');
    //         $("#pump_model_manual_add").css('display','block');
    //         $("#pump_model, #impeller_material").attr('disabled', 'disabled');
    //     }
    //     else{
    //         $("#full_article_number_section").css('display','block');
    //         $("#bareshaft_pump_article_number_section").css('display','none');
    //         $("#pump_model_manual_add").css('display','block');
    //     }
    // });

    // A Code: 19-03-2026 Start
    $(document).on("change", "#bareshaft_pump_full_pump", function () {
        var value = $(this).val();
        if (value === "full_pump" || value === "both_pump") {
            $("#pump_model").val("").trigger("change");
            $("#full_article_number_section").show();
            $("#pump_model_manual_add").hide();
            $("#pump_model").prop('disabled', false);
        } 
        else if (value === "bareshaft_pump") {
            $("#full_article_number_section").hide();
            $("#bareshaft_pump_article_number_section").show();
            $("#pump_model_manual_add").hide();
        } 
        else if (value === "manual") {
            $("#full_article_number_section").show();
            $("#bareshaft_pump_article_number_section").hide();
            $("#pump_model_manual_add").show();
            $("#pump_model, #impeller_material").prop('disabled', true);
        } 
        else {
            $("#full_article_number_section").show();
            $("#bareshaft_pump_article_number_section").hide();
            $("#pump_model_manual_add").show();
        }
    });
    // A Code: 19-03-2026 End
    

    $("#full_article_number").bind('keypress',function(e){
        if(e.which == 13){
            if($(this).val().length >1)
            {
                e.preventDefault();
                var full_article_number = $("#full_article_number").val();
                $.ajax({
                    type: "get",
                    url: "{{route('scpv.searchbyarticle')}}",
                    data: {full_article_number: full_article_number},
                    success: function (response){
                        if(response.data.cp_records_html) {
                            $(document).ajaxComplete(function () {
                                $("#loader").hide();
                            });
                            $("#price").html('');
                            $("#price").html(response.data.cp_price + '$');
                            $("#total-price").val(response.data.total_price);
                            $("#master-price-record").html('');
                            $("#master-price-record").html(response.data.cp_records_html);
                            $("#myModal").show();
                        }else {
                            
                            $("#error-modal").show();
                            $("#error-modal-body").html(response.data.cp_records_html_error);
                        }
                    },
                    error:function(data){

                    }
                });
            }
        }
    });
</script>
@stop
