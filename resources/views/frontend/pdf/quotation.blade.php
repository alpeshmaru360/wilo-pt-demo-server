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
                <h2>Control Panel</h2>
                <div class="formWidget">

                    <ul id='errors'>
                    </ul>
                    <form id ='controlpanel_form' action="">
                        @csrf
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">My first popover content goes here My first popover content goes hereMy first popover content goes here.</div>
                            </div>
                            <input type="text" class="formInput" name="article_number" id="" placeholder="Article Number">                            
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="no_of_pump" id="no_of_pump" class="select-control-panel formInput" >
                                <option value="">Number of Pumps*</option>
                                @foreach($numberOfPumps as $numberOfPump)
                                <option value="{{$numberOfPump->id}}">{{$numberOfPump->value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="power_rating" id="power_rating" class="formInput" disabled>
                                <option value="">Motor Power*</option>



                            </select>
                        </div> 
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="voltage" id="voltage" class="formInput" disabled>
                                <option value="">Supply Voltage*</option>


                            </select>
                        </div> 

                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="application" id="application" class="formInput" disabled>
                                <option value="">Application*</option>


                            </select>
                        </div>

                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
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
                                <div class="popper-content hide">popover content goes here.</div>
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
                                <div class="popper-content hide">popover content goes here.</div>
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
                                <div class="popper-content hide">popover content goes here.</div>
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
                                <div class="popper-content hide">popover content goes here.</div>
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
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="enclosure" id="enclosure" class="formInput" disabled>
                                <option value="">Enclosure*</option>
                                <!--                                <option value="metal">metal</option>
                                                                <option value="GRP">GRP</option>
                                                                <option value="Stainless-steel">Stainless steel</option>-->
                            </select>
                        </div>   


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
    <input type="hidden" id="cp-id" value=""/>
    <input type="hidden" id="last-cp-id" value=""/>
    <input type="hidden" id="table-name" value=""/>
    <input type="hidden" id="column-name" value=""/>
    <input type="hidden" id="code-price" value=""/>
    <input type="hidden" id="adder-enclousre-area-item" value=""/>
    <input type="hidden" id="total-price" value=""/>
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

    function refresh()
    {
        location.reload();
    }

    $("#no_of_pump").on('change', function (e) {
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



                for (var i = 0; i < powers.length; i++) {
                    var id = powers[i]['id'];
                    var value = powers[i]['value'];
                    $("#power_rating").append("<option value='" + id + "'>" + value + "</option>");
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
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {

                var voltages = response.data.voltages;


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



    $("#enclosure").on('change', function () {

        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize(),
            success: function (response) {
                $("#cp-id").val(response.data.cp_id);
                $("#table-name").val(response.data.table_name);
                $("#column-name").val(response.data.column_name);


//                if (response.data.cp_records_html) {
//                    $("#price").html('');
//                    $("#price").html(response.data.cp_price + '$');
//                    $("#master-price-record").html('');
//                    $("#master-price-record").html(response.data.cp_records_html);
//                    $("#myModal").show();
//                }

//                $("#record-temp").html(controlPanel);

            },
            error: function (data) {

            }

        });


    });
    $("#calculate").on('click', function () {

        var adder_code_price = $('#code-price').val();
        var enclousreItem = $('#adder-enclousre-area-item').val();
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajaxFilter')}}",
            data: $('#controlpanel_form').serialize() + "&code_price=" + adder_code_price + "&enclousreItem=" + enclousreItem,
            success: function (response) {

                if (response.data.cp_records_html) {
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
        $('#controlpanel_form  select').each(
                function (index) {
                    var input = $(this);
                    if (input.val() == "") {
                        isEmpty = false;

//             console.log('Type: ' + input.attr('type') + 'Name: ' + input.attr('name') + 'Value: ' + input.val());
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
                    url: "{{url('controlpanel/ajax-optional-modal')}}",

                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

                    data: $('#controlpanel_form').serialize() + "&cp_id=" + cp_id,
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
            $("#error-modal-body").html('<h4>Please Select all conrtol panel fields without article number. </h4>');
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


    $(document).on("click", '#addtocart', function (event) {
        var adderIds = [];
        var cp_id = $("#cp-id").val();
        var tableName = $("#table-name").val();
        var columnName = $("#column-name").val();
        var codePrice = $("#code-price").val();
        var totalPrice = $("#total-price").val();

        $('input[name="adder_id"]:checked').each(function () {
            adderIds.push($(this).val());

        });



        if (adderIds.length >= 1) {

        }

        $.ajax({
            type: "post",
            url: "{{url('controlpanel/addtocart')}}",

            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

            data: $('#controlpanel_form').serialize() + "&control_panel_id=" + $('#cp-record-data').val() +
                    "&adder_ids=" + adderIds + "&cp_id=" + cp_id
                    + "&table_name=" + tableName + "&column_name=" + columnName + "&code_price=" + codePrice + "&total_price=" + totalPrice,
//             contentType: false,

            success: function (response) {

                if (response.url) {

                    location.reload();
//                    window.location = response.url;
                }
                if (response.msg) {
                    alert(response.msg);
                }

//                $("#record-temp").html(controlPanel);

            },
            error: function (data) {

            }

        });


    });

    $(document).on("click", '#optional-button-add', function (event) {

        var adderIds = [];

        $('input[name="adder_id"]:checked').each(function () {
            adderIds.push($(this).val());

        });



        if (adderIds.length >= 1) {
            var cp_id = $("#cp-id").val();
            var tableName = $("#table-name").val();
            var columnName = $("#column-name").val();

            var url = "{{url('controlpanel/ajax-optional-selected-adder')}}";
            $.ajax({
                type: "post",
                url: url,

                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             'dataType': 'json',

                data: $('#controlpanel_form').serialize() + "&adder_ids=" + adderIds + "&cp_id=" + cp_id
                        + "&table_name=" + tableName + "&column_name=" + columnName,
//             contentType: false,

                success: function (response) {

                    if (response.code_price && response.starter_code == 'other') {
                        if (response.enclousreItem) {
                            $("#code-price").val(response.code_price);
                            $("#adder-enclousre-area-item").val(JSON.stringify(response.enclousreItem));
                            $("#adder-optional-modal").hide();
                        } else {
                            $("#code-price").val(0);
                            alert('We have no available enclusre box. Please remove and select and another optional code.');
                        }
                    }
                    if (response.code_price && response.starter_code == 'xtreme') {
                        $("#code-price").val(response.code_price);
                    }

//                $("#record-temp").html(controlPanel);

                },
                error: function (data) {

                }

            });
        }
    });

</script>
<script>

    // modal box scripts
    var modal = document.getElementById("myModal");

    var btn = document.getElementById("myBtn");

    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function () {
        modal.style.display = "block";
    }

    span.onclick = function (event) {
        alert('ok');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

@stop






