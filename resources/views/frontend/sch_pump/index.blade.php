@php
$tool_tip = DB::table('tool_tip')->where('part_id',3)->get();
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
</style>
@section('content')
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="formsMidSection">
                <h2>SCH Pumps</h2>
                <div class="formWidget">

                    <ul id='errors'>
                    </ul>
                    <form action="" id="scp_form">
					<div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide"></div>
                            </div>
                            <input type="text" class="formInput" name="full_article_number" id="full_article_number" placeholder="Full Article Number">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[0]->pump_model ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[1]->impeller_material ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="impeller_material" id="impeller_material" class="formInput" disabled>
                                <option value="">Impeller Material</option>
                                @foreach($atmos_materials as $am)
                                <option value={{$am->id}}>{{$am->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">{{$tool_tip[2]->seal_gland_pack ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[3]->motor_power ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[4]->power_supply ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[5]->frequency ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[6]->no_of_poles ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[7]->efficiency ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[8]->motor_brand ?? ""}}</div>
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
                                <div class="popper-content hide">{{$tool_tip[9]->application ?? ""}}</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="application" id="application" class="formInput" disabled>
                                <option value="">Application</option>
                                @foreach(Config::get('constants.atmos_giga_application_options') as $key=>$v)
                                <option value={{$v}}>{{$key}}</option>
                                @endforeach
                            </select>
                        </div>
						
                        <input type="hidden" name='bare_shaft_price' id="price_hidden" class="price_hidden">
                        <input type="hidden" name='is_bare_shaft_price_manual' id="is_bare_shaft_price_manual" value="0">
                        <input type="hidden" name='acessories_price' id="acessories_price_hidden" class="acessories_price_hidden">
                        <input type="hidden" name='is_acessories_price_manual' id="is_acessories_price_manual" value="0">
                        <input type="hidden" name='motor_price' id="motor_price_hidden" class="motor_price_hidden">
                        <input type="hidden" name='frame_size' id="frame_size" class="frame_size">
                        <input type="hidden" name='master_price_id' id="master_price_id" class="master_price_id">
                        <input type="hidden" name='master_last_price_id' id="master_last_price_id" class="master_last_price_id">
                        <input type="hidden" name='code_price' id="code-price" >
                        <input type="hidden" name='total_price' id="total-price" >
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
                    <!-- <li><a href="#" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li> -->                    
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>

<!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
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
      <!-- <span class="close">&times;</span> -->
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
        {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
        {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
      </div>
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
// pump_model

    $("#pump_model").on('change', function (e) {

        $(this).val() == "" ? $("#impeller_material").attr('disabled', 'disabled')
                : $("#impeller_material").removeAttr('disabled');

    });


    $("#s_g_pack").on('change', function (e) {

        $("#loader").show();
        ajax_data = {
            'impeller_id': $("#impeller_material").val(),
            'pump_model': $("#pump_model").val(),
            'sg_pack': $(this).val()
        };
        var url = "{{url('scp_price')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,

            success: function (res) {

                if (res != "price not found") {
                    // alert(res);
                    // $('#manual_price').show();
                    // $("#poles").removeAttr('disabled');
                    // $("#motor_brand").removeAttr('disabled');
                    // $("#frequency").removeAttr('disabled');
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#motor_power").removeAttr('disabled');
                    // $("#efficiency").removeAttr('disabled');
                    // $("#power_supply").removeAttr('disabled');
                    // $("#application").removeAttr('disabled');

                    $("#price_hidden").val(res);
                    $("#is_bare_shaft_price_manual").val(0);

                } else {

                    $('#manual_price').show();

                }

            }


        });

    });
    // price_hidden
    // add_price

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
        var url = "{{url('get_scp_frame')}}";
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
                    get_accessories(res);

                } else {

                    var pole = $('#poles option:selected').val();
                    var voltage = $('#power_supply option:selected').val();

                    var motor_power = $('#motor_power option:selected').val();


                    // alert();

                    // location.reload();
                    error = pole + "pole - " + motor_power + "kW product is not avilable .";
                    jQuery.noConflict();
                    $("#error_statement").text(error);
                    // $('#alert_data').text("#error_statement");
                    $('#alert_data').show();
                    
                    $( "#refresh_close" ).click(function() {
                        location.reload();
                    });
                    // $('#manual_price').show();

                }

            }


        });

    });

    function refresh()
    {
        location.reload();
    }

    function get_accessories(res) {
        // alert("there");
        $("#loader").show();

        ajax_data = {
            'frame': res.frame_size,
            'pump_id': $("#pump_model").val()
        };
        var url = "{{url('get_scp_accessories')}}";
        $.ajax({
            type: "post",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data,

            success: function (res) {

                if (res != 0) {
                    // alert("where");
                    // $("#application").removeAttr('disabled');
                    // acessories_price_hidden     
                    $(document).ajaxComplete(function () {
                        $("#loader").hide();
                    });
                    $("#acessories_price_hidden").val(res);
                    $("#is_acessories_price_manual").val(0);

                } else {

                    $('#manual_acessories_price_modal').show();
                    // alert("resposnse is 0");
                    // $('#manual_price').show();

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
        var url = "{{url('get_scp_motor_price')}}";
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
                    // return res;
                    $("#motor_price_hidden").val(res);


                } else {

                    // alert("resposnse is 0");
                    // $('#manual_price').show();
                    // error = pole + "pole - " + motor_power + "kW product is not avilable .";
                    // jQuery.noConflict();
                    $("#error_statement").text("No Product Found.");
                    // $('#alert_data').text("#error_statement");
                    $('#alert_data').show();
                    
                    $( "#refresh_close" ).click(function() {
                        location.reload();
                    });
                    // alert("No Product Found.");
                    // location.reload();
                }

            }


        });

    });

    $("#calculate").on('click', function () {

        var isEmpty = true;
        $('#scp_form  select').each(
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
            url: "{{url('scp/ajaxCalculate')}}",
            data: $('#scp_form').serialize() + "&code_price=" + adder_code_price,
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

//                $("#record-temp").html(controlPanel);

            },
            error: function (data) {

            }

        });
    });

    $("#optional-button").on('click', function () {
        var isEmpty = true;
        $('#scp_form  select').each(
                function (index) {
                    var input = $(this);
                    if (input.val() == "") {
                        isEmpty = false;

//             console.log('Type: ' + input.attr('type') + 'Name: ' + input.attr('name') + 'Value: ' + input.val());
                    }


                }
        );
//        isEmpty = true;
        if (isEmpty) {
            var master_id = $("#master_price_id").val();
            var last_master_id = $("#master_last_price_id").val();
            if (last_master_id == '' || last_master_id != master_id) {

                $("#master_last_price_id").val(master_id);
                $.ajax({
                    type: "post",
                    url: "{{url('scp/ajax-optional-modal')}}",

                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

                    data: '',
//             contentType: false,

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


   // $(document).on("click", '#addtocart', function (event) {
        //var adderIds = [];



        //$('input[name="adder_id"]:checked').each(function () {
           // adderIds.push($(this).val());

        //});
       // $.ajax({
          //  type: "post",
         //   url: "{{url('scp/addtocart')}}",

         //   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

           // data: $('#scp_form').serialize() + "&adder_ids=" + adderIds,
//             contentType: false,

          //  success: function (response) {

           //     if (response.url) {

          //          location.reload();
//                    window.location = response.url;
            //    }
           //     if (response.msg) {
           //         alert(response.msg);
            //    }

//                $("#record-temp").html(controlPanel);

           // },
            //error: function (data) {

            //}

        //});


    //});

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
                                url: "{{url('scp/addtocart')}}",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: $('#scp_form').serialize() + "&adder_ids=" + adderIds + "&country=" + selectedCountry,
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
                        url: "{{url('scp/addtocart')}}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: $('#scp_form').serialize() + "&adder_ids=" + adderIds + "&country=" + selectedCountry,
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

            var url = "{{url('atmos/ajax-optional-selected-adder')}}";
            $.ajax({
                type: "post",
                url: url,

                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

                data: $('#scp_form').serialize() + "&adder_ids=" + adderIds + "&master_price_id=" + master_id,
//             contentType: false,

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
$("#full_article_number").bind('keypress',function(e){
        if(e.which == 13){
            if($(this).val().length >1)
            {
                e.preventDefault();
                var full_article_number = $("#full_article_number").val();
                $.ajax({
                type: "get",
                url: "{{route('scp.searchbyarticle')}}",
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
                    }
                    else {
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
