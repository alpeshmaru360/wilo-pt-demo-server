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
        <div class="flex-center" style="min-height:400px;">
            <div class="pumpInfoMidSection">
                <div class="pumpInfoList">
                    <form id="firefightingForm">
                        <div class="accSec1">
                           <div class="panel formWidget mh-100">
                                <div class="panelBody">
                                    {{-- Main & Jockey Tab Nav --}}
                                    <div class="tabNav px-2 border-0 d-flex justify-content-between m-0 p-0 row">
                                        <button type="button" class="tabLinks col-5" id="tabDefaultOpen" onclick="openTab(event, 'main_pump_panel')" value="main_pump_panel">Main Pump</button>
                                        <button type="button" class="tabLinks col-5" onclick="openTab(event, 'jockey_pump_panel')" value="jockey_pump_panel">Jockey Pump</button>
                                    </div>
                                    <div class="tabContentWrapper">
                                        {{-- Main Pump Panel --}}
                                        <div class="tabContent" id="main_pump_panel">
                                            <div class="row">
                                                <div class="main_panel_selection_hide_show main_panel_selection_set">
                                                    <div class="formFields px-2">
                                                        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                                        <select name="main_panel_selection" id="main_panel_selection" class="formInput main_panel_selection">
                                                            <option value="">Select Pump Models*</option>
                                                            <option value="electrical">Electrical</option>
                                                            <option value="diesel">Diesel</option>
                                                            <option value="electrical-diesel">Electrical & Diesel</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Main Pump Wise --}}
                                            <div class="row mt-3">
                                                <div class="col-12 d-flex justify-content-around flex-nowrap">
                                                    {{-- Main -> Electrical --}}
                                                    <div class="px-2 w-100 main_panel_section-hide electrical-section-show electrical-diesel-section-show">
                                                        <div class="row">
<div class="col-12">
    <h6 class="main_panel_selection_text">Electrical</h6>
</div>
<div class="col-12">
    <div class="formFields main_panel_section-hide electrical-section-show">
        <input type="text" name="electrical_article_number" id="electrical_article_number" class="formInput" placeholder="Pump article number*">
    </div>
    <div class="formFields">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="electrical_pumptype" id="electrical_pumptype" class="formInput electrical-formInput">
            <option value="">Select Pump type*</option>
            @foreach($electrical_pump_type as $ek_pump_type => $e_pump_type)
            <option>{{ $e_pump_type }}</option>
            @endforeach
        </select>
    </div>
    <div class="formFields">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="electrical_frequency" id="electrical_frequency" class="formInput electrical-formInput">
            <option value="">Select Frequency*</option>
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
    <div class="formFields">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="electrical_control_panel_type" id="electrical_control_panel_type" class="formInput electrical_control_panel_type-formInput">
            <option value="">Select Control Panel Type*</option>
        </select>
    </div>
</div>
                                                        </div>
                                                    </div>

                                                    {{-- Main -> Diesel --}}
                                                    <div class="px-2 w-100 main_panel_section-hide diesel-section-show electrical-diesel-section-show">
                                                        <div class="row">
<div class="col-12">
    <h6 class="main_panel_selection_text">Diesel</h6>
</div>
<div class="col-12">
    <div class="formFields main_panel_section-hide diesel-section-show">
        <input type="text" name="diesel_article_number" id="diesel_article_number" class="formInput" placeholder="Pump article number*">
    </div>
    <div class="formFields">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="diesel_pumptype" id="diesel_pumptype" class="formInput diesel-formInput">
            <option value="">Select Pump type*</option>
            @foreach($diesel_pump_type as $dk_pump_type => $d_pump_type)
            <option>{{ $d_pump_type }}</option>
            @endforeach
        </select>
    </div>
    <div class="formFields">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="diesel_frequency" id="diesel_frequency" class="formInput diesel-formInput">
            <option value="">Select Frequency*</option>
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
    <div class="formFields" style="display: none;visibility: hidden;">
        <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
        <select name="diesel_control_panel_type" id="diesel_control_panel_type" class="formInput diesel_control_panel_type-formInput">
            <option value="">Select Control Panel Type*</option>
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
                                                                <!-- /** start 20241231 for jockey pump form auto fill***********/ -->
                                                                <div class="formFields">
                                                                    <input type="text" name="jockey_full_article_number" id="jockey_full_article_number" class="formInput jockeypump-formInput" placeholder="Pump Full article number*">
                                                                </div>
                                                                <div class="formFields">
                                                                    <input type="text" name="jockey_article_number" id="jockey_article_number" class="formInput jockeypump-formInput" placeholder="Pump article number*">
                                                                </div>
                                                                <!-- /** start 20241231 for jockey pump form auto fill***********/ -->
                                                                <div class="formFields">
                                                                    <input type="text" name="jockey_pumppower" id="jockey_pumppower" class="formInput jockeypump-formInput" placeholder="Pump Power*">
                                                                </div>
                                                                <div class="formFields">
                                                                    <span class="formArrowIcon"><img src="{{url('fassets/images/arrowDownIcon.png')}}" /></span>
                                                                    <select name="jockey_frequency" id="jockey_frequency" class="formInput jockeypump-formInput">
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
    <div class="modal-content">
        <div class="modal-body" id="master-price-record">
        </div>
        <div class="modalBtns">
            <button id="addtocart">Add to Cart</button>
            <span class="close" onclick="refresh()">Cancel</span>
            <span class="close-cart-modal" >Close</span>
        </div>
    </div>
</div>

<div id="adder-optional-modal" class="modal">
    <div class="modal-content modal-backdrop">
        <div class="modal-body p-0" id="adder-optional-modal-table">
        </div>
        <div class="modalBtns">
            <span class="close" id="optional-button-add">Add</span>
            <span class="close" id="optional-button-close">Close</span>
        </div>
    </div>
</div>

<div id="error-modal" class="modal">
    <div class="modal-content">
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

<div id="other-pump-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="other-pump-modal-body">
        </div>
        <div class="modalBtns mt-0 pt-0">
            <span class="close" id="error-close">Close</span>
        </div>
    </div>
</div>

<div id="other-pump-success-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="">
            <h4></h4>
        </div>
        <div class="modalBtns">
            <span class="close" id="error-close">Close</span>
        </div>
    </div>
</div>
@endsection


@section('script')
{{-- Full Article Flow --}}
<script>
    var article_modal_show = false;
    $(document).on('blur keyup', '#electrical_article_number', function (e) {
        if (e.keyCode === 13) {
            $(this).blur();
            $('#calculate').click();
            article_modal_show = true;
        }
        if (e.type === 'focusout') {
            if ($(this).val() != '') {
                adderIds = [];
                var articlenumber = $(this).val();
                var findarticlenumber = jQuery.grep(electricalpumparticle, function (filter) {
                    return filter.full_article_number == articlenumber;
                });

                if (findarticlenumber.length > 0) {
                    var field_val = findarticlenumber[0].field_val;
                    field_val.forEach(function (val) {
                        for (var vkey in val) {
                            if ($('#'+vkey).length) {
                                $('#'+vkey).val(val[vkey]).change();
                            }
                            if (vkey == 'id') {
                                other_pump_modal_id = val[vkey];
                            }
                            if (vkey == 'electrical_pumpmodels') {
                                other_pump_modal = val[vkey];
                            }
                        }
                    });
                    var adderIdsfind = findarticlenumber[0].all_prices.adderpricelist;
                    if (adderIdsfind.length > 0) {
                        adderIdsfind.forEach(function (val) {
                            adderIds.push('' + val.code);
                        });
                    }
                } else {
                    $("#error-modal-body").html('');
                    $("#error-modal-body").html('<h4>Pump Article Data not found.</h4>');
                    $("#error-modal").show();
                    return;
                }
            }
        }
    });

    $(document).on('blur keyup', '#diesel_article_number', function (e) {
        if (e.keyCode === 13) {
            $(this).blur();
            $('#calculate').click();
            article_modal_show = true;
        }
        if (e.type === 'focusout') {
            if ($(this).val() != '') {
                adderIds = [];
                var articlenumber = $(this).val();
                var findarticlenumber = jQuery.grep(dieselpumparticle, function (filter) {
                    return filter.full_article_number == articlenumber;
                });

                if (findarticlenumber.length > 0) {
                    var field_val = findarticlenumber[0].field_val;
                    field_val.forEach(function (val) {
                        for (var vkey in val) {
                            if ($('#'+vkey).length) {
                                $('#'+vkey).val(val[vkey]).change();
                            }
                            if (vkey == 'id') {
                                other_pump_modal_id = val[vkey];
                            }
                            if (vkey == 'diesel_pumpmodels') {
                                other_pump_modal = val[vkey];
                            }
                        }
                    });
                    var adderIdsfind = findarticlenumber[0].all_prices.adderpricelist;
                    if (adderIdsfind.length > 0) {
                        adderIdsfind.forEach(function (val) {
                            adderIds.push('' + val.code);
                        });
                    }
                } else {
                    $("#error-modal-body").html('');
                    $("#error-modal-body").html('<h4>Pump Article Data not found.</h4>');
                    $("#error-modal").show();
                    return;
                }
            }
        }
    });
</script>

{{-- Disable Flow --}}
<script>
    var adderIds = [];
    var adderprice = 0;
    var pump_type = '';
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
        } else if ($(this).val() == 'jockey_pump_panel') {
            $('.tabNav').hide();
            $('.tabNav').removeClass('d-flex');
            jockeyPumpAjax();
        }
    });

    mainPanelShow($('.main_panel_selection:checked'));
    $(document).on('change', '.main_panel_selection', function () {
        mainPanelShow($(this));
        $('.main_panel_selection_hide_show').hide();
        $('.tabNav').hide();
        $('.tabNav').removeClass('d-flex');
        mainPumpAjax();
    });

    disableElectricalInput();
    $(document).on('change', '.electrical-formInput', function () {
        disableElectricalInput($(this).attr('name'));
    });

    disableDieselInput();
    $(document).on('change', '.diesel-formInput', function () {
        disableDieselInput($(this).attr('name'));
    });

    disableInput('jockeypump-formInput');
    $(document).on('change', '.jockeypump-formInput', function () {
        disableInput('jockeypump-formInput');
    });

    
    $(document).on('change', '#diesel_engine_approval', function () {
        if ($('#main_panel_selection').val() == 'electrical-diesel') {
            disableElectricalInput('electrical_flow');
        }
    });

    $(document).on('change', '#diesel_head', function () {
        if ($('#main_panel_selection').val() == 'electrical-diesel') {
            if ($(this).val() != '') {
                // Add options in diesel pump data
                dieselpumptemp = dieselpump;
                var dataSetForTemp = [];
                var disableDieselSelection = {
                    'diesel_pumptype': 'pump_type',
                    'diesel_frequency': 'frequency',
                    'diesel_pump_approval': 'pump_approval',
                    'diesel_engine_approval': 'engine_approval',
                    'diesel_flow': 'flow',
                    'diesel_head': 'head',
                    'diesel_speed': 'speed_rpm'
                };
                var db_original_key = '';
                var original_key_val = '';

                $('.diesel-formInput').each(function() {
                    if ($(this).attr('id') != 'diesel_speed') {

                        db_original_key = disableDieselSelection[$(this).attr('id')];
                        original_key_val = $(this).val();
                        dieselpumptemp = jQuery.grep(dieselpumptemp, function(filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == original_key_val;
                            }
                        });
                    }
                });
                dieselpumptemp.forEach(function(element) {
                    dataSetForTemp.push(element.speed_rpm);
                });

                dataSetForTemp = dataSetForTemp.filter(function(el, index, arr) {
                    return index === arr.indexOf(el);
                });
                dataSetForTemp.sort();
                $('#diesel_speed').find('option').not(':first').remove();
                if (dataSetForTemp.length > 0) {
                    dataSetForTemp.forEach(function(element) {
                        $('#diesel_speed').append($("<option></option>").text(element));
                    });
                } else {
                    alert('Diesel Speed RPM Data not found..!!');
                }
            }
        }
    });


        function disableElectricalInput(changed = '') {
            var disableElectricalList = ['electrical_pumptype', 'electrical_frequency', 'electrical_pump_approval',
                'electrical_flow', 'electrical_head', 'electrical_speed'
            ];

            var disableDieselList = ['diesel_pumptype', 'diesel_frequency', 'diesel_pump_approval',
                'diesel_engine_approval', 'diesel_flow', 'diesel_head', 'diesel_speed'
            ];

            var disableElectricalSelection = {
                'electrical_pumpmodels': 'wilo_pump_models',
                'electrical_pumptype': 'pump_type',
                'electrical_frequency': 'frequency',
                'electrical_pump_approval': 'pump_approval',
                'electrical_flow': 'flow',
                'electrical_head': 'head',
                'electrical_speed': 'speed_rpm'
            };

            var disableElectricalSelectionOptionsText = {
                'electrical_pumpmodels': 'Select Pump Models*',
                'electrical_pumptype': 'Select Pump type*',
                'electrical_frequency': 'Select Frequency*',
                'electrical_pump_approval': 'Select Pump approval*',
                'electrical_flow': 'Select Flow*',
                'electrical_head': 'Select Head*',
                'electrical_speed': 'Select Speed*',
                'electrical_control_panel_type': 'Select Control Panel Type*',
            };

            var changeElectricalwithDiesel = {
                'electrical_pumpmodels': 'diesel_pumpmodels',
                'electrical_pumptype': 'diesel_pumptype',
                'electrical_frequency': 'diesel_frequency',
                'electrical_pump_approval': 'diesel_pump_approval',
                'electrical_flow': 'diesel_flow',
                'electrical_head': 'diesel_head',
                'electrical_speed': 'diesel_speed'
            };

            var disableDieselSelection = {
                // 'diesel_pumpmodels':'pump_models',
                'diesel_pumptype': 'pump_type',
                'diesel_frequency': 'frequency',
                'diesel_pump_approval': 'pump_approval',
                'diesel_engine_approval': 'engine_approval',
                'diesel_flow': 'flow',
                'diesel_head': 'head',
                'diesel_speed': 'speed_rpm'
            };
            var dataSetForTemp = [];
            var main_panel_selection = $('#main_panel_selection').val();
            $('#electrical_control_panel_type').prop('disabled', true);
            $('#diesel_control_panel_type').prop('disabled', true);

            if (changed == '') {
                $.each(disableElectricalList, function(key, value) {
                    if (key != 0) {
                        $('#' + value).prop('disabled', true);
                    }
                });
            } else {
                var selected_options = origin_selected_options = 0;
                $.each(disableElectricalList, function(key, value) {
                    if (value == changed) {
                        selected_options = origin_selected_options = key;
                    }
                });

                var electricalSelectedData = $('#' + disableElectricalList[selected_options]).val();
                if (electricalSelectedData != '') {

                    // If Both Select
                    if (main_panel_selection == 'electrical-diesel') {
                        if (changed == 'electrical_frequency') {
                            electricalSelectedData = '50/60';
                        }
                        var electrical_with_diesel_change = [];
                        electrical_with_diesel_change.push('<option value="">' + disableElectricalSelectionOptionsText[
                            changed] + '</option>');
                        electrical_with_diesel_change.push('<option selected>' + electricalSelectedData + '</option>');

                        // If Head or Speed from Diesel
                        if (changed == 'electrical_head' || changed == 'electrical_speed') {

                        } else {
                            $('#' + changeElectricalwithDiesel[changed]).html(electrical_with_diesel_change.join(''));
                        }
                    }

                    // Filter from array
                    var electricalFilterData = [];
                    electricalpumptemp = electricalpump;
                    for (var i = 0; i <= selected_options; i++) {
                        var original_key = disableElectricalList[i];
                        var db_original_key = disableElectricalSelection[original_key];
                        var original_key_val = $('#' + original_key).val();

                        electricalpumptemp = electricalFilterData = jQuery.grep(electricalpumptemp, function(filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == original_key_val;
                            }
                        });
                    }

                    // Select Next
                    selected_options += 1;
                    var new_changed = disableElectricalList[selected_options];
                    var new_changed_selection = disableElectricalSelection[new_changed];
                    var new_changed_selection_text = disableElectricalSelectionOptionsText[new_changed];

                    if (main_panel_selection == 'electrical-diesel') {
                        // change custom diesel engine approval
                        if (new_changed == 'electrical_flow') {
                            var electricalDieselFilterData = [];
                            dieselpumptemp = dieselpump;
                            for (var i = 0; i <= selected_options - 1; i++) {
                                var original_key = disableElectricalList[i];
                                var db_original_key = disableDieselSelection[changeElectricalwithDiesel[original_key]];
                                var original_key_val = $('#' + original_key).val();
                                if (original_key == 'electrical_frequency' && (original_key_val == '50' ||
                                        original_key_val == '60')) {
                                    original_key_val = '50/60';
                                }
                                // console.log(original_key, original_key_val);
                                dieselpumptemp = electricalDieselFilterData = jQuery.grep(dieselpumptemp, function(filter) {
                                    if (db_original_key in filter) {
                                        return filter[db_original_key] == original_key_val;
                                    }
                                });
                            }
                            // console.log(electricalDieselFilterData);

                            var select_val_electricalDieselFilterData = [];
                            electricalDieselFilterData = $.each(electricalDieselFilterData, function(key, value) {
                                select_val_electricalDieselFilterData.push(value.engine_approval);
                            });

                            electricalDieselFilterData = select_val_electricalDieselFilterData;
                            electricalDieselFilterData = groupSimilar(electricalDieselFilterData);
                            if (electricalDieselFilterData.length > 0) {
                                var diesel_engine_approval_option = [];
                                diesel_engine_approval_option.push('<option value="">Select Engine approval*</option>');
                                $.each(electricalDieselFilterData, function(key, value) {
                                    if (value != '' && value != null) {
                                        diesel_engine_approval_option.push('<option>' + value + '</option>');
                                    }
                                });
                                $('#diesel_engine_approval').html(diesel_engine_approval_option.join(''));
                            } else {
                                alert('Engine approval data not found.');
                            }
                        }
                    }

                    $('#' + new_changed).prop('disabled', false);
                    $('#' + new_changed).prop('disabled', false);

                    if (main_panel_selection == 'electrical' || main_panel_selection == 'electrical-diesel') {
                        
                        if (changed == 'electrical_speed') {
                            // On Head Change recreate options 
                            var dataSetForControlPanelType = true;
                            electrical_control_panel_type_temp = electrical_control_panel_type;
                            //for (var i = 0; i <= selected_options; i++) {
                            
                                var original_key1 = 'electrical_frequency';
                                var db_original_key1 = 'frequency';
                                var original_key_val1 = $('#' + original_key1).val();

                                var original_key2 = 'electrical_pump_approval';
                                var db_original_key2 = 'approval';
                                var original_key_val2 = $('#' + original_key2).val();

                                
                                if (electrical_control_panel_type_temp.length > 0) {
                                    var original_key3 = 'motor_power';
                                    var motor_power = electricalpumptemp[0].motor_power;
                                    //var motor_power = electrical_control_panel_type_temp[0].motor_power;
                                    var db_original_key3 = 'motor_power';
                                    var original_key_val3 = motor_power;
                                }

                                var main_pump_selection = $('.main_panel_selection').find(":selected").val();

                                if (main_pump_selection == 'electrical' || main_pump_selection == 'electrical-diesel') {
                                    var original_key4 = 'category';
                                    var db_original_key4 = 'category';
                                    var original_key_val4 = 'Electrical';
                                }else{
                                    var original_key4 = 'category';
                                    var db_original_key4 = 'category';
                                    var original_key_val4 = 'Diesel';
                                }


                                if (original_key_val1 == undefined || original_key_val1 == '' || original_key_val2 == undefined || original_key_val2 == '' || original_key_val3 == undefined || original_key_val3 == '' || original_key_val4 == undefined || original_key_val4 == '') {
                                    dataSetForControlPanelType = false;
                                } else {


                                    electrical_control_panel_type_temp = jQuery.grep(electrical_control_panel_type_temp, function(filter) {
                  
                                        if (db_original_key1 in filter) {
                                            return filter[db_original_key1] == original_key_val1;
                                        }
                                    });

                                    electrical_control_panel_type_temp = jQuery.grep(electrical_control_panel_type_temp, function(filter) {

                                        if (db_original_key2 in filter) {
                                            return filter[db_original_key2] == original_key_val2;
                                        }
                                    });

                                    electrical_control_panel_type_temp = jQuery.grep(electrical_control_panel_type_temp, function(filter) {

                                        if (db_original_key3 in filter) {
                                            return filter[db_original_key3] == original_key_val3;
                                        }

                                    });

                                    dataSetForTempVar = [];
                                    electrical_control_panel_type_temp.forEach(function(element) {
                                        dataSetForTempVar.push(element.type);
                                    });

                                    dataSetForTempVar = dataSetForTempVar.filter(function(el, index, arr) {
                                        return index === arr.indexOf(el);
                                    });
                                    dataSetForTempVar.sort();
                                    $('#electrical_control_panel_type').prop('disabled', false);
                                    $('#electrical_control_panel_type').find('option').not(':first').remove();
                                    dataSetForTempVar.forEach(function(element) {
                                        $('#electrical_control_panel_type').append($("<option></option>").text(element));
                                    });
                                }
                            //}
                            if (electrical_control_panel_type_temp.length <= 0) {
                                //alert('Electrical Control Type Data not found..!!');
                                $("#error-modal-body").html('');
                                $("#error-modal-body").html('<h4>Electrical Control Type Data not found..!!</h4>');
                                $("#error-modal").show();
                                return;
                            }
                        }
                    }

                    // Custom Option for Electrical & Diesel Options
                    if (main_panel_selection == 'electrical-diesel') {
                        // change custom diesel head
                        if (new_changed == 'electrical_head') {
                            var dataSetForHead = true;
                            dieselpumptemp = dieselpump;
                            for (var i = 0; i <= selected_options; i++) {

                                var original_key = disableDieselList[i];
                                var db_original_key = disableDieselSelection[original_key];
                                var original_key_val = $('#' + original_key).val();

                                if (original_key_val == undefined || original_key_val == '') {
                                    dataSetForHead = false;
                                } else {
                                    dieselpumptemp = jQuery.grep(dieselpumptemp, function(filter) {
                                        if (db_original_key in filter) {
                                            return filter[db_original_key] == original_key_val;
                                        }
                                    });
                                    dataSetForTemp = [];
                                    dieselpumptemp.forEach(function(element) {
                                        dataSetForTemp.push(element.head);
                                    });

                                    dataSetForTemp = dataSetForTemp.filter(function(el, index, arr) {
                                        return index === arr.indexOf(el);
                                    });
                                    dataSetForTemp.sort();

                                    // console.log(dataSetForTemp);

                                    $('#diesel_head').find('option').not(':first').remove();
                                    dataSetForTemp.forEach(function(element) {
                                        $('#diesel_head').append($("<option></option>").text(element));
                                    });
                                }
                            }
                            if (!dataSetForHead) {
                                // Disable Head Field for not getting all value of diesel
                                $('#' + new_changed).prop('disabled', true);

                            }
                        }


                        if (new_changed == 'electrical_speed') {

                            // On Head Change recreate options 
                            var dataSetForHead = true;
                            dieselpumptemp = dieselpump;
                            for (var i = 0; i <= selected_options; i++) {

                                var original_key = disableDieselList[i];
                                var db_original_key = disableDieselSelection[original_key];
                                var original_key_val = $('#' + original_key).val();

                                if (original_key_val == undefined || original_key_val == '') {
                                    dataSetForHead = false;
                                } else {
                                    dieselpumptemp = jQuery.grep(dieselpumptemp, function(filter) {
                                        if (db_original_key in filter) {
                                            return filter[db_original_key] == original_key_val;
                                        }
                                    });
                                    dataSetForTemp = [];
                                    dieselpumptemp.forEach(function(element) {
                                        dataSetForTemp.push(element.head);
                                    });

                                    dataSetForTemp = dataSetForTemp.filter(function(el, index, arr) {
                                        return index === arr.indexOf(el);
                                    });
                                    dataSetForTemp.sort();

                                    $('#diesel_head').find('option').not(':first').remove();
                                    dataSetForTemp.forEach(function(element) {
                                        $('#diesel_head').append($("<option></option>").text(element));
                                    });
                                }
                            }


                            // Change value of head of selected electircal head in beetween -5 to +1
                            $('#diesel_head').val('').change();
                            var min = electricalSelectedData - 0.5;
                            var max = parseFloat(electricalSelectedData) + parseFloat(1);
                            var deisel_heade_count = 0;
                            $('#diesel_head').find('option').each(function() {
                                if (!isNaN($(this).text())) {
                                    if (min <= $(this).text() && $(this).text() <= max) {
                                        deisel_heade_count += 1;
                                    } else {
                                        $(this).remove();
                                    }
                                }
                            });
                            if (deisel_heade_count == 0) {
                                alert('Diesel Head Data not found..!!');
                            }
                        }
                    }

                    // Set options in Next Select
                    if (electricalFilterData.length > 0) {
                        var electrical_selction_data = [];
                        var electrical_selction_option = [];
                        $.each(electricalFilterData, function(key, value) {
                            if (new_changed_selection in value) {
                                electrical_selction_data.push(value[new_changed_selection]);
                            }
                        });

                        electrical_selction_data.sort(function(a, b) {
                            return parseFloat(a) - parseFloat(b);
                        });
                        
                        // Group by values
                        electrical_selction_data = groupSimilar(electrical_selction_data);


                        electrical_selction_option.push('<option value="">' + new_changed_selection_text + '</option>');
                        $.each(electrical_selction_data, function(key, value) {
                            if (value != '' && value != null) {
                                electrical_selction_option.push('<option>' + value + '</option>');
                            }
                        });

                        // Change New Select options
                        $('#' + new_changed).html(electrical_selction_option.join(''));
                    } else {
                        alert('Data not found.');
                    }
                }
                // Other Selection Disable
                $.each(disableElectricalList, function(key, value) {
                    other_pump_modal = '';
                    other_pump_modal_id = '';
                    if (selected_options < key) {
                        $('#' + value).prop('disabled', true);
                        $('#' + value).val('').change();
                        if (main_panel_selection == 'electrical-diesel') {
                            $('#' + changeElectricalwithDiesel[value]).find('option').not(':first').remove();
                            $('#' + changeElectricalwithDiesel[value]).val('').change();
                        }
                    }
                });
            }
        }

        function disableDieselInput(changed = '') {
            var disableDieselList = ['diesel_pumptype', 'diesel_frequency', 'diesel_pump_approval',
                'diesel_engine_approval', 'diesel_flow', 'diesel_head', 'diesel_speed'
            ];

            var disableDieselSelection = {
                'diesel_pumpmodels': 'pump_models',
                'diesel_pumptype': 'pump_type',
                'diesel_frequency': 'frequency',
                'diesel_pump_approval': 'pump_approval',
                'diesel_engine_approval': 'engine_approval',
                'diesel_flow': 'flow',
                'diesel_head': 'head',
                'diesel_speed': 'speed_rpm'
            };

            var disableDieselSelectionOptionsText = {
                'diesel_pumpmodels': 'Select Pump Models*',
                'diesel_pumptype': 'Select Pump type*',
                'diesel_frequency': 'Select Frequency*',
                'diesel_pump_approval': 'Select Pump approval*',
                'diesel_engine_approval': 'Select Engine approval*',
                'diesel_flow': 'Select Flow*',
                'diesel_head': 'Select Head*',
                'diesel_speed': 'Select Speed*',
                'diesel_control_panel_type': 'Select Control Panel Type*',
            };
            var main_panel_selection = $('#main_panel_selection').val();
            $('#diesel_control_panel_type').prop('disabled', true);

            if (changed == 'diesel_speed') {
                // On Head Change recreate options 
                var dataSetForControlPanelType = true;
                diesel_control_panel_type_temp = diesel_control_panel_type;
                //for (var i = 0; i <= selected_options; i++) {
                
                    var original_key1 = 'diesel_frequency';
                    var db_original_key1 = 'frequency';
                    var original_key_val1 = $('#' + original_key1).val();

                    var original_key2 = 'diesel_pump_approval';
                    var db_original_key2 = 'approval';
                    var original_key_val2 = $('#' + original_key2).val();

                    /*var original_key3 = 'diesel_flow';
                    var db_original_key3 = 'motor_power';
                    var original_key_val3 = $('#' + original_key3).val();
                    if (diesel_control_panel_type_temp.length > 0) {
                        var original_key3 = 'motor_power';
                        //var motor_power = diesel_control_panel_type_temp[0].motor_power;
                        var motor_power = dieselpumptemp[0].motor_power;
                        var db_original_key3 = 'motor_power';
                        var original_key_val3 = motor_power;
                    }
                    original_key_val3 == undefined || original_key_val3 == '' ||
                    */

                    var original_key4 = 'category';
                    var db_original_key4 = 'category';
                    var original_key_val4 = 'Diesel';


                    if (original_key_val1 == undefined || original_key_val1 == '' || original_key_val2 == undefined || original_key_val2 == '' ||  original_key_val4 == undefined || original_key_val4 == '') {
                        dataSetForControlPanelType = false;
                    } else {


                        diesel_control_panel_type_temp = jQuery.grep(diesel_control_panel_type_temp, function(filter) {
      
                            if (db_original_key1 in filter) {
                                return filter[db_original_key1] == original_key_val1;
                            }
                        });

                        diesel_control_panel_type_temp = jQuery.grep(diesel_control_panel_type_temp, function(filter) {

                            if (db_original_key2 in filter) {
                                return filter[db_original_key2] == original_key_val2;
                            }
                        });

                        /*diesel_control_panel_type_temp = jQuery.grep(diesel_control_panel_type_temp, function(filter) {

                            if (db_original_key3 in filter) {
                                return filter[db_original_key3] == original_key_val3;
                            }

                        });*/

                        dataSetForTempVar = [];
                        diesel_control_panel_type_temp.forEach(function(element) {
                            dataSetForTempVar.push(element.type);
                        });

                        dataSetForTempVar = dataSetForTempVar.filter(function(el, index, arr) {
                            return index === arr.indexOf(el);
                        });
                        dataSetForTempVar.sort();
                        $('#diesel_control_panel_type').prop('disabled', false);
                        $('#diesel_control_panel_type').find('option').not(':first').remove();
                        dataSetForTempVar.forEach(function(element) {
                            $('#diesel_control_panel_type').append($("<option></option>").text(element));
                        });
                    }
                //}

                if (diesel_control_panel_type_temp.length <= 0) {
                    //alert('Diesel Control Type Data not found..!!');
                    $("#error-modal-body").html('');
                    $("#error-modal-body").html('<h4>Diesel Control Type Data not found..!!</h4>');
                    $("#error-modal").show();
                    return;
                }
            }

            // If Both Option Select
            if (main_panel_selection == 'electrical-diesel') {
                $.each(disableDieselList, function(key, value) {
                    if (value == 'diesel_engine_approval' || value == 'diesel_head' || value == 'diesel_speed') {
                        $('#' + value).prop('disabled', false);
                    } else {
                        $('#' + value).prop('disabled', true);
                    }
                });
            } else {

                // If Single Option Select
                if (changed == '') {
                    $.each(disableDieselList, function(key, value) {
                        if (key != 0) {
                            $('#' + value).prop('disabled', true);
                        }
                    });
                } else {
                    var selected_options = 0;
                    $.each(disableDieselList, function(key, value) {
                        if (value == changed) {
                            selected_options = key;
                        }
                    });

                    var dieselSelectedData = $('#' + disableDieselList[selected_options]).val();
                    if (dieselSelectedData != '') {

                        // Filter from array
                        var dieselFilterData = [];
                        dieselpumptemp = dieselpump;
                        for (var i = 0; i <= selected_options; i++) {
                            var original_key = disableDieselList[i];
                            var db_original_key = disableDieselSelection[original_key];
                            var original_key_val = $('#' + original_key).val();

                            dieselpumptemp = dieselFilterData = jQuery.grep(dieselpumptemp, function(filter) {
                                if (db_original_key in filter) {
                                    return filter[db_original_key] == original_key_val;
                                }
                            });
                        }

                        // Select Next
                        selected_options += 1;
                        var new_changed = disableDieselList[selected_options];
                        var new_changed_selection = disableDieselSelection[new_changed];
                        var new_changed_selection_text = disableDieselSelectionOptionsText[new_changed];


                        $('#' + new_changed).prop('disabled', false);
                        $('#' + new_changed).prop('disabled', false);

                        // Set options in Next Select
                        if (dieselFilterData.length > 0) {
                            var diesel_selction_data = [];
                            var diesel_selction_option = [];
                            $.each(dieselFilterData, function(key, value) {
                                if (new_changed_selection in value) {
                                    diesel_selction_data.push(value[new_changed_selection]);
                                }
                            });

                            // Group by values
                            diesel_selction_data = groupSimilar(diesel_selction_data);


                            diesel_selction_option.push('<option value="">' + new_changed_selection_text + '</option>');
                            $.each(diesel_selction_data, function(key, value) {
                                if (value != '' && value != null) {
                                    diesel_selction_option.push('<option>' + value + '</option>');
                                }
                            });

                            // Change New Select options
                            $('#' + new_changed).html(diesel_selction_option.join(''));
                        } else {
                            alert('Data not found.');
                        }
                    }

                    // Other Selection Disable
                    $.each(disableDieselList, function(key, value) {
                        other_pump_modal = '';
                        other_pump_modal_id = '';
                        if (selected_options < key) {
                            $('#' + value).prop('disabled', true);
                            $('#' + value).val('').change();
                        }
                    });
                }
            }
        }

        function mainPanelShow(thisv) {
            var main_panel_section = thisv.val();

            $('.main_panel_section-hide').each(function() {
                $(this).hide();
            });

            $('.' + main_panel_section + '-section-show').each(function() {
                $(this).show();
            });

            $('.main_panel_selection').each(function() {
                $(this).prop('checked', false);
            });
            thisv.prop('checked', true);
        }

        function disableInput(inputClass) {
            // if (inputClass == 'electrical-formInput') {
            var disable_inp = false;
            $('.' + inputClass).each(function() {
                if (!disable_inp) {
                    // console.log($(this).attr('name'));
                    $(this).removeAttr('disabled');
                    if ($(this).val() == '') {
                        disable_inp = true;
                    }
                } else {
                    $(this).prop('disabled', true);
                }
            });
            // }
        }

        const groupSimilar = arr => {
            return arr.reduce((acc, val) => {
                const {
                    data,
                    map
                } = acc;
                const ind = map.get(val);
                if (map.has(val)) {
                    // data[ind][1]++;
                } else {
                    map.set(val, data.push(val) - 1);
                }
                return {
                    data,
                    map
                };
            }, {
                data: [],
                map: new Map()
            }).data;
        };
    </script>

    {{-- Price Calcution --}}
    <script>
        $(document).on('click', '#calculate', function() {
            if (pump_type == '') {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select Pump. </h4>');
                $("#error-modal").show();
                return;
            } else {
                switch (pump_type) {
                    case 'jockey-pump':
                        jockeyPumpPriceCalculate();
                        break;

                    case 'electrical':
                        electricalPumpPriceCalculate();
                        break;

                    case 'diesel':
                        dieselPumpPriceCalculate();
                        break;

                    case 'electrical-diesel':
                        electricalDieselPumpPriceCalculate();
                        break;
                }
            }
        });

        $(document).on('click', '#optional-button', function() {
            // console.log(adderIds);
            let optional_check = true;
            let modal_heading = '';
            let electrical_control_panel_type_val = $('.electrical_control_panel_type-formInput').val();
            let electrical_option_show = true;

            switch (pump_type) {
                case 'electrical':
                    $('.electrical-formInput').each(function() {
                        if (optional_check && $(this).val() == '') {
                            optional_check = false;
                        }
                    });
                    if (electrical_control_panel_type_val == '') {
                        optional_check = false;
                    }
                    break;

                case 'diesel':
                    $('.diesel-formInput').each(function() {
                        if (optional_check && $(this).val() == '') {
                            optional_check = false;
                        }
                    });
                    break;

                case 'electrical-diesel':
                    $('.electrical-formInput').each(function() {
                        if (optional_check && $(this).val() == '') {
                            optional_check = false;
                        }
                    });
                    $('.diesel-formInput').each(function() {
                        if (optional_check && $(this).val() == '') {
                            optional_check = false;
                        }
                    });
                    break;
            }

            // If Data is Blank
            if (optional_check) {
                let option_tr = '';
                $("#adder-optional-modal-table").html('');
                let checked_adder = '';

                if (pump_type == 'electrical' || pump_type == 'electrical-diesel') {
                    option_tr = '';
                    $.each(adder_ids, function(key, value) {
                        checked_adder = '';
                        if (value.version == 'FireFighting/Electrical') {
                            if (jQuery.inArray('' + value.id, adderIds) !== -1) {
                                checked_adder = 'checked';
                            }
                            if (electrical_control_panel_type_val == 'SD' || electrical_control_panel_type_val == 'DOL') {
                                electrical_option_show = true;
                            } else {
                                if (value.id < 9) {
                                    electrical_option_show = false;
                                } else {
                                    electrical_option_show = true;
                                }
                            }

                            if (electrical_option_show) {
                                option_tr += `<tr>
                                    <td><label class="mb-0" for="${pump_type}-optional-${value.id}">${value.adder_list}</label></td>
                                    <td><label class="mb-0 pl-2" for="${pump_type}-optional-${value.id}">${value.code}</label></td>
                                    <td><input type="checkbox" name="adder_id" id="${pump_type}-optional-${value.id}" class="adder-checkbox" data-id="${value.id}" value="${value.id}" ${checked_adder}></td>
                                </tr>`;
                            }
                        }
                    });

                    if (pump_type == 'electrical-diesel') {
                        modal_heading =
                            `<h6 data-type="electrical" class="modal-heading-change active">Electrical</h6>`;
                        var electrical_diesel_modal_heading =
                            `<div class="main-table"><h6 data-type="diesel" class="modal-heading-change">Diesel</h6></div>`;
                    } else {
                        modal_heading = `<h5>Electrical</h5>`;
                        var electrical_diesel_modal_heading = '';
                    }

                    $("#adder-optional-modal-table").append(`<div class="main-table">${modal_heading}</div>${electrical_diesel_modal_heading}<table class="table electrical-main-table"><thead><tr><th>Title</th><th>Code</th><th>Select</th></tr></thead><tbody>
                    ${option_tr}
                    </tbody></table>`);
                }

                if (pump_type == 'diesel' || pump_type == 'electrical-diesel') {
                    option_tr = '';
                    $.each(adder_ids, function(key, value) {
                        checked_adder = '';
                        if (value.version == 'FireFighting/Diesel') {
                            if (jQuery.inArray('' + value.id, adderIds) !== -1) {
                                checked_adder = 'checked';
                            }
                            option_tr += `<tr>
                            <td><label class="mb-0" for="${pump_type}-optional-${value.id}">${value.adder_list}</label></td>
                            <td><label class="mb-0 pl-2" for="${pump_type}-optional-${value.id}">${value.code}</label></td>
                            <td><input type="checkbox" name="adder_id" id="${pump_type}-optional-${value.id}" class="adder-checkbox" data-id="${value.id}" value="${value.id}" ${checked_adder}></td>
                        </tr>`;
                        }
                    });
                    if (pump_type == 'electrical-diesel') {
                        modal_heading = ``;
                    } else {
                        modal_heading = `<div class="main-table"><h5>Diesel</h5></div>`;
                    }
                    $("#adder-optional-modal-table").append(`${modal_heading}<table class="table diesel-main-table"><thead><tr><th>Title</th><th>Code</th><th>Select</th></tr></thead><tbody>
                    ${option_tr}
                    </tbody></table>`);
                }

                if (pump_type == 'electrical-diesel') {
                    changeElectricalDieselAdders();
                    $("#adder-optional-modal .modal-content").css('width', '50%');
                    $("#adder-optional-modal .main-table").addClass('col-6 p-0 m-0');
                    $("#adder-optional-modal-table").addClass('d-flex');
                }
                $("#adder-optional-modal").show();
                return;
            } else {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            }
        });

        $(document).on('click', '#jockey-optional-button', function() {
            let jockey_optional_check = true;
            $('.jockeypump-formInput').each(function() {
                if ($(this).val() == '') {
                    jockey_optional_check = false;
                }
            });
            if (jockey_optional_check) {
                let option_tr = '';
                $.each(adder_ids, function(key, value) {
                    let checked_adder = '';
                    if (jQuery.inArray('' + value.id, adderIds) !== -1) {
                        checked_adder = 'checked';
                    }
                    option_tr += `<tr>
                    <td><label class="mb-0" for="jockey-optional-${value.id}">${value.adder_list}</label></td>
                    <td><label class="mb-0 pl-2" for="jockey-optional-${value.id}">${value.code}</label></td>
                    <td><input type="checkbox" name="adder_id" id="jockey-optional-${value.id}" class="adder-checkbox" data-id="${value.id}" value="${value.id}" ${checked_adder}></td>
                </tr>`;
                });
                $("#adder-optional-modal-table").html('');
                $("#adder-optional-modal-table").html(`<table class="table"><thead><tr><th>Title</th><th>Code</th><th>Select</th></tr></thead><tbody>
                ${option_tr}
                </tbody></table>`);
                $("#adder-optional-modal").show();
                return;
            } else {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            }
        });

        $(document).on('click', '#optional-button-add', function() {
            adderIds = [];
            $('input[name="adder_id"]:checked').each(function() {
                adderIds.push($(this).val());
            });
            $("#adder-optional-modal").hide();
            if (adderIds.length > 0) {
                $("#optional-add-success-modal").show();
                setTimeout(function() {
                    $("#optional-add-success-modal").hide();
                }, 2000);
            }
        });

        $(document).on('click', '.modal-heading-change', function() {
            $('.modal-heading-change').each(function() {
                $(this).removeClass('active');
            })
            $(this).addClass('active');
            changeElectricalDieselAdders();
        });

        function changeElectricalDieselAdders() {
            $('.modal-heading-change').each(function() {
                if ($(this).hasClass('active')) {
                    $('.' + $(this).data('type') + '-main-table').show();
                } else {
                    $('.' + $(this).data('type') + '-main-table').hide();
                }
            });
        }

        function electricalDieselPumpPriceCalculate() {
            let checkvalblank = false;
            $('.diesel-formInput').each(function() {
                if (!checkvalblank && $(this).val() == '') {
                    checkvalblank = true;
                }
            });

            $('.electrical-formInput').each(function() {
                if (!checkvalblank && $(this).val() == '') {
                    checkvalblank = true;
                }
            });

            // If is Blank
            if (checkvalblank) {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            } else {
                var dieselSelection = {
                    'diesel_pumpmodels': 'pump_models',
                    'diesel_pumptype': 'pump_type',
                    'diesel_frequency': 'frequency',
                    'diesel_pump_approval': 'pump_approval',
                    'diesel_engine_approval': 'engine_approval',
                    'diesel_flow': 'flow',
                    'diesel_head': 'head',
                    'diesel_speed': 'speed_rpm'
                };
                var dieselSelectionData = [];

                dieselpumptemp = dieselpump;
                $('.diesel-formInput').each(function() {
                    var inputName = $(this).attr('name');
                    var inputVal = $(this).val();
                    if (inputName in dieselSelection) {
                        var db_original_key = dieselSelection[inputName];
                        dieselpumptemp = dieselSelectionData = jQuery.grep(dieselpumptemp, function(filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == inputVal;
                            }
                        });
                    }
                });

                var electricalSelection = {
                    'electrical_pumpmodels': 'wilo_pump_models',
                    'electrical_pumptype': 'pump_type',
                    'electrical_frequency': 'frequency',
                    'electrical_pump_approval': 'pump_approval',
                    'electrical_flow': 'flow',
                    'electrical_head': 'head',
                    'electrical_speed': 'speed_rpm'
                };
                var electricalSelectionData = [];

                electricalpumptemp = electricalpump;
                $('.electrical-formInput').each(function() {
                    var inputName = $(this).attr('name');
                    var inputVal = $(this).val();
                    if (inputName in electricalSelection) {
                        var db_original_key = electricalSelection[inputName];
                        electricalpumptemp = electricalSelectionData = jQuery.grep(electricalpumptemp, function(
                            filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == inputVal;
                            }
                        });
                    }
                });

                $('.diesel-formInput').each(function() {
                    $(this).prop('disabled', false);
                });

                var electrical_data = $('.electrical-formInput').serializeArray();
                var diesel_data = $('.diesel-formInput').serializeArray();
                $('.diesel-formInput').each(function() {
                    $(this).prop('disabled', true);
                });

                if (other_pump_modal != '') {
                    electricalpumptemp = electricalSelectionData = jQuery.grep(electricalpumptemp, function(filter) {
                        return filter['wilo_pump_models'] == other_pump_modal;
                    });
                    electrical_data.push({
                        name: 'electrical_pumpmodels',
                        value: other_pump_modal
                    });
                }

                if (other_pump_modal != '') {
                    dieselpumptemp = dieselSelectionData = jQuery.grep(dieselpumptemp, function(filter1) {
                        return filter1['pump_models'] == other_pump_modal;
                    });
                    diesel_data.push({
                        name: 'diesel_pumpmodels',
                        value: other_pump_modal
                    });
                }

                var electrical_control_panel_type = $('#electrical_control_panel_type').val();
                var diesel_control_panel_type = $('#diesel_control_panel_type').val();
                if (electrical_control_panel_type != '') {
                    electrical_data.push({
                        name: 'electrical_control_panel_type',
                        value: electrical_control_panel_type
                    });
                }
                if (diesel_control_panel_type != '') {
                    diesel_data.push({
                        name: 'diesel_control_panel_type',
                        value: diesel_control_panel_type
                    });
                }

                if (electricalSelectionData.length == 1 && (dieselSelectionData.length > electricalSelectionData.length)) {

                    other_pump_modal = electricalSelectionData[0].wilo_pump_models;
                    dieselpumptemp = dieselSelectionData = jQuery.grep(dieselpumptemp, function(filter1) {
                        return filter1['pump_models'] == other_pump_modal;
                    });
                    diesel_data.push({
                        name: 'diesel_pumpmodels',
                        value: other_pump_modal
                    });
                }

                if (dieselSelectionData.length > 0) {
                    if (electricalSelectionData.length > 0) {

                        if (dieselSelectionData.length == 1 && electricalSelectionData.length == 1) {

                            $.ajax({
                                type: "post",
                                url: "{{ route('fire-fighting.store') }}",
                                'dataType': 'json',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    'pump_type': pump_type,
                                    'adder_ids': adderIds,
                                    'post_type': 'price-calculate',
                                    'diesel_data': diesel_data,
                                    'electrical_data': electrical_data
                                },
                                success: function(response) {
                                    if (response['success']) {
                                        $("#master-price-record").html('');
                                        $("#master-price-record").html(response['html']);
                                        $("#myModal").find('.modal-content').css('width', '60%');
                                        $("#myModal").show();
                                    } else {
                                        alert(response['msg']);
                                    }
                                },
                                error: function(data) {}
                            });
                        } else {
                            $("#other-pump-modal-body").html('');
                            var other_pump_modal_html = '';
                            electricalSelectionData.forEach(function(item) {
                                other_pump_modal_html += `<tr>
                                    <td class="border-0"><label class="mb-0" for="electrical-optional-1">${item.wilo_pump_models}</label></td>
                                    <td class="border-0"><label class="mb-0 font-weight-bold" for="electrical-optional-1">${item.unit_price}$</label></td>
                                    <td class="border-0"><span class="btn select-other-pump-modal" data-pump="${item.wilo_pump_models}" data-id="${item.id}">Select</span></td>
                                </tr>`;
                            });
                            $("#other-pump-modal-body").html(`<div class="main-table"><h6>Select Pump Modal</h6><table class="table"><tbody>
                                ${other_pump_modal_html}
                            </tbody></table></div>`);
                            $("#other-pump-modal").show();
                        }
                    } else {
                        alert('Electrical Pump Data not found');
                    }
                } else {
                    alert('Diesel Pump Data not found');
                }
            }
        }

        function dieselPumpPriceCalculate() {
            let checkvalblank = false;
            $('.diesel-formInput').each(function() {
                if (!checkvalblank && $(this).val() == '') {
                    checkvalblank = true;
                }
            });

            // If is Blank
            if (checkvalblank) {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            } else {
                var dieselSelection = {
                    'diesel_pumpmodels': 'pump_models',
                    'diesel_pumptype': 'pump_type',
                    'diesel_frequency': 'frequency',
                    'diesel_pump_approval': 'pump_approval',
                    'diesel_engine_approval': 'engine_approval',
                    'diesel_flow': 'flow',
                    'diesel_head': 'head',
                    'diesel_speed': 'speed_rpm'
                };
                var dieselSelectionData = [];

                dieselpumptemp = dieselpump;
                $('.diesel-formInput').each(function() {
                    var inputName = $(this).attr('name');
                    var inputVal = $(this).val();
                    if (inputName in dieselSelection) {
                        var db_original_key = dieselSelection[inputName];
                        dieselpumptemp = dieselSelectionData = jQuery.grep(dieselpumptemp, function(filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == inputVal;
                            }
                        });
                    }
                });

                var dieselserialize = $('.diesel-formInput').serializeArray();
                if (other_pump_modal != '') {
                    dieselpumptemp = dieselSelectionData = jQuery.grep(dieselpumptemp, function(filter) {
                        return filter['id'] == other_pump_modal_id;
                    });
                    dieselserialize.push({
                        name: 'id',
                        value: other_pump_modal_id
                    });
                    dieselserialize.push({
                        name: 'diesel_pumpmodels',
                        value: other_pump_modal
                    });
                }

                var diesel_control_panel_type = $('#diesel_control_panel_type').val();
                if (diesel_control_panel_type != '') {
                    dieselserialize.push({
                        name: 'diesel_control_panel_type',
                        value: diesel_control_panel_type
                    });
                }

                if (dieselSelectionData.length > 0) {
                    if (dieselSelectionData.length == 1) {
                        var diesel_append_str = '';
                        $.ajax({
                            type: "post",
                            url: "{{ route('fire-fighting.store') }}",
                            'dataType': 'json',
                            data: {
                                _token: "{{ csrf_token() }}",
                                'pump_type': pump_type,
                                'adder_ids': adderIds,
                                'post_type': 'price-calculate',
                                'data': dieselserialize
                            },
                            success: function(response) {
                                if (response['success']) {
                                    if ('data' in response) {
                                        if ('Pressure relief valve' in response['data']) {
                                            diesel_append_str += `<li>Pressure relief valve: ${response['data']['Pressure relief valve']}$</li>`;
                                        }
                                        if ('Flow meter' in response['data']) {
                                            diesel_append_str += `<li>Flow meter: ${response['data']['Flow meter']}$</li>`;
                                        }
                                        if ('Waste cone' in response['data']) {
                                            diesel_append_str += `<li>Waste cone: ${response['data']['Waste cone']}$</li>`;
                                        }
                                    }
                                    $("#price").html('');
                                    $("#price").html(getPrice(response['price']) + '$');
                                    $("#master-price-record").html('');
                                    $("#master-price-record").html(`<div class="columns">
                                    <ul class="price" style="list-style: none;">
                                        <li class="header">${response['data'].wilo_pump_models}</li>
                                        <li class="grey">${response['data'].pump_type}</li>
                                        <li class="grey">${response['data'].frequency} </li>${diesel_append_str}
                                        <li>Total Price: <b>${getPrice(response['price'])}</b><span>$</span> </li>  
                                    </ul>
                                </div>`);
                                    if (!article_modal_show) {
                                        $("#myModal").show();
                                    }
                                    article_modal_show = false;
                                } else {
                                    alert(response['msg']);
                                }
                            },
                            error: function(data) {}
                        });
                    } else {
                        $("#other-pump-modal-body").html('');
                        var other_pump_modal_html = '';
                        dieselSelectionData.forEach(function(item) {
                            other_pump_modal_html += `<tr>
                                <td class="border-0"><label class="mb-0" for="electrical-optional-1">${item.pump_models}</label></td>
                                <td class="border-0"><label class="mb-0 font-weight-bold" for="electrical-optional-1">${item.unit_price}$</label></td>
                                <td class="border-0"><span class="btn select-other-pump-modal" data-pump="${item.pump_models}" data-id="${item.id}">Select</span></td>
                            </tr>`;
                        });
                        $("#other-pump-modal-body").html(`<div class="main-table"><h6>Select Pump Modal</h6><table class="table"><tbody>
                            ${other_pump_modal_html}
                        </tbody></table></div>`);
                        $("#other-pump-modal").show();
                    }
                } else {
                    alert('Diesel Pump Data not found');
                }
            }
        }

        function electricalPumpPriceCalculate() {
            let checkvalblank = false;
            $('.electrical-formInput').each(function() {
                if (!checkvalblank && $(this).val() == '') {
                    checkvalblank = true;
                }
            });

            // If is Blank
            if (checkvalblank) {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            } else {
                var electricalSelection = {
                    'electrical_pumpmodels': 'wilo_pump_models',
                    'electrical_pumptype': 'pump_type',
                    'electrical_frequency': 'frequency',
                    'electrical_pump_approval': 'pump_approval',
                    'electrical_flow': 'flow',
                    'electrical_head': 'head',
                    'electrical_speed': 'speed_rpm'
                };
                var electricalSelectionData = [];

                electricalpumptemp = electricalpump;
                $('.electrical-formInput').each(function() {
                    var inputName = $(this).attr('name');
                    var inputVal = $(this).val();
                    if (inputName in electricalSelection) {
                        var db_original_key = electricalSelection[inputName];
                        electricalpumptemp = electricalSelectionData = jQuery.grep(electricalpumptemp, function(
                            filter) {
                            if (db_original_key in filter) {
                                return filter[db_original_key] == inputVal;
                            }
                        });
                    }
                });
                var electricalserialize = $('.electrical-formInput').serializeArray();
                // console.log(electricalSelectionData, electricalserialize);
                if (other_pump_modal != '') {
                    electricalpumptemp = electricalSelectionData = jQuery.grep(electricalpumptemp, function(filter) {
                        return filter['id'] == other_pump_modal_id;
                    });
                    electricalserialize.push({
                        name: 'id',
                        value: other_pump_modal_id
                    });
                    electricalserialize.push({
                        name: 'electrical_pumpmodels',
                        value: other_pump_modal
                    });
                }
                var electrical_control_panel_type = $('#electrical_control_panel_type').val();
                if (electrical_control_panel_type != '') {
                    electricalserialize.push({
                        name: 'electrical_control_panel_type',
                        value: electrical_control_panel_type
                    });
                }
                if (electricalpumptemp.length > 0) {
                    var motor_power = electricalpumptemp[0].motor_power;
                    if (motor_power.length > 0) {
                        electricalserialize.push({
                            name: 'motor_power',
                            value: motor_power
                        });
                    }
                }
                
                // console.log(electricalSelectionData);
                if (electricalSelectionData.length > 0) {
                    if (electricalSelectionData.length == 1) {

                        $.ajax({
                            type: "post",
                            url: "{{ route('fire-fighting.store') }}",
                            'dataType': 'json',
                            data: {
                                _token: "{{ csrf_token() }}",
                                'pump_type': pump_type,
                                'adder_ids': adderIds,
                                'post_type': 'price-calculate',
                                'data': electricalserialize
                            },
                            success: function(response) {
                                if (response['success']) {
                                    $("#price").html('');
                                    $("#price").html(getPrice(response['price']) + '$');
                                    $("#master-price-record").html('');
                                    $("#master-price-record").html(`<div class="columns">
                                    <ul class="price" style="list-style: none;">
                                        <li class="header">${response['data'].wilo_pump_models}</li>
                                        <li class="grey">${response['data'].pump_type}</li>
                                        <li class="grey">${response['data'].frequency} </li>
                                        <li>Total Price: <b>${getPrice(response['price'])}</b><span>$</span> </li>  
                                    </ul>
                                </div>`);
                                    if (!article_modal_show) {
                                        $("#myModal").show();
                                    }
                                    article_modal_show = false;
                                } else {
                                    alert(response['msg']);
                                }
                            },
                            error: function(data) {}
                        });
                    } else {
                        $("#other-pump-modal-body").html('');
                        var other_pump_modal_html = '';
                        electricalSelectionData.forEach(function(item) {
                            other_pump_modal_html += `<tr>
                                <td class="border-0"><label class="mb-0" for="electrical-optional-1">${item.wilo_pump_models}</label></td>
                                <td class="border-0"><label class="mb-0 font-weight-bold" for="electrical-optional-1">${item.unit_price}$</label></td>
                                <td class="border-0"><span class="btn select-other-pump-modal" data-pump="${item.wilo_pump_models}" data-id="${item.id}">Select</span></td>
                            </tr>`;
                        });
                        $("#other-pump-modal-body").html(`<div class="main-table"><h6>Select Pump Modal</h6><table class="table"><tbody>
                            ${other_pump_modal_html}
                        </tbody></table></div>`);
                        $("#other-pump-modal").show();
                        // alert('Multiple Electrical Pump Data found');
                    }
                } else {
                    alert('Electrical Pump Data not found');
                }
            }
        }

        function jockeyPumpPriceCalculate() {
            let checkvalblank = false;
            $('.jockeypump-formInput').each(function() {
                if (!checkvalblank && $(this).val() == '') {
                    checkvalblank = true;
                }
            });

            // If is Blank
            if (checkvalblank) {
                $("#error-modal-body").html('');
                $("#error-modal-body").html('<h4>Please Select all fields. </h4>');
                $("#error-modal").show();
                return;
            } else {
                /** start 20241231 for jockey pump form auto fill***********/
                var jockeypump_article_no = $('input[name="jockey_full_article_number"]').val();
                
                var jockeypump_article_data = jQuery.grep(jockeypumparticle, function(articless) {
                    return articless.full_article_number == jockeypump_article_no;
                });

                /** end 20241231 for jockey pump form auto fill*************/
                if (jockeypump_article_data.length > 0) {
                    if (jockeypump_article_data.length == 1) {
                        if ($('#jockey_pumppower').val() == jockeypump_article_data[0].power) {
                            if ($('#jockey_frequency').val() == jockeypump_article_data[0].frequency) {
                                $.ajax({
                                    type: "post",
                                    url: "{{ route('fire-fighting.store') }}",
                                    'dataType': 'json',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        'pump_type': pump_type,
                                        'adder_ids': adderIds,
                                        'post_type': 'price-calculate',
                                        'data': {
                                            'article_no': jockeypump_article_data[0].pump_article_no,
                                            'power': jockeypump_article_data[0].power,
                                            'frequency': jockeypump_article_data[0].frequency,
                                            'unit_price': jockeypump_article_data[0].unit_price
                                        }
                                    },
                                    success: function(response) {
                                        
                                        if (response['success']) {
                                            /** start 20241231 for jockey pump form auto fill*************/
                                            $("#price").html('');
                                            $("#price").html(getPrice(response['price']) + '$');
                                            $("#master-price-record").html('');
                                            $("#master-price-record").html(`<div class="columns">
                                            <ul class="price" style="list-style: none;">
                                                <li class="header">${jockeypump_article_data[0].pump_models}</li>
                                                <li class="grey">${jockeypump_article_data[0].pump_type}</li>
                                                <li class="grey">${jockeypump_article_data[0].power}</li>
                                                <li class="grey">${jockeypump_article_data[0].frequency} </li>
                                                <li>Total Price: <b>${getPrice(response['price'])}</b><span>$</span> </li>  
                                            </ul>
                                        </div>`);
                                            $("#myModal").show();
                                            /** end 20241231 for jockey pump form auto fill*************/
                                        } else {
                                            alert(response['msg']);
                                        }
                                    },
                                    error: function(data) {}
                                });
                            } else {
                                $("#error-modal-body").html('');
                                $("#error-modal-body").html('<h4>Pump frequency not match with Pump article number. </h4>');
                                $("#error-modal").show();
                                return;
                            }
                        } else {
                            $("#error-modal-body").html('');
                            $("#error-modal-body").html('<h4>Pump Power not match with Pump article number. </h4>');
                            $("#error-modal").show();
                            return;
                        }
                    } else {
                        alert('Multiple Jockey Pump Data found');
                    }
                } else {
                    alert('Jockey Pump Data not found');
                }
            }
        }
    </script>

    {{-- Initialize Data Call --}}
    <script>
        var jockeypump;
        var electricalpump, electricalpumptemp, electricalpumparticle;
        var dieselpump, dieselpumptemp, dieselpumparticle;
        var adder_ids;
        var other_pump_modal = '';
        var other_pump_modal_id = '';
        // Main Pump Data Call

        $(document).on('click', '.select-other-pump-modal', function() {
            other_pump_modal = $(this).data('pump');
            other_pump_modal_id = $(this).data('id');
            $("#other-pump-modal").hide();

            $('#other-pump-success-modal').find('.modal-body').find('h4').text('').text(
                `${other_pump_modal} Pump Modal select Successful!`);
            $('#other-pump-success-modal').show();
        });

        // Jockey Pump Data Call
        $('input[name="jockey_full_article_number"]').prop('disabled', true);///** start 20241231 for jockey pump form auto fill***********/
        $(document).on('blur', 'input[name="jockey_full_article_number"]', function() {///** start 20241231 for jockey pump form auto fill***********/
            var jockeypump_article_no = $(this).val();
            if (jockeypump_article_no != '') {
                /** start 20241231 for jockey pump form auto fill***********/
                var jockeypump_article_data = jQuery.grep(jockeypumparticle, function(articless) {
                    return articless.full_article_number == jockeypump_article_no;
                });
                /** end 20241231 for jockey pump form auto fill*************/
              
                if ($.isArray(jockeypump_article_data) && jockeypump_article_data.length > 0) {
                    /** start 20241231 for jockey pump form auto fill***********/
                    $('#jockey_article_number').val(jockeypump_article_data[0]['article_number'])
                    /** end 20241231 for jockey pump form auto fill*************/
                    $('#jockey_pumppower').val(jockeypump_article_data[0]['power']);
                    $('#jockey_frequency').prop('disabled', false);

                    $('#jockey_frequency').find('option').remove().end().append('<option>' +
                        jockeypump_article_data[0]['frequency'] + '</option>').val(jockeypump_article_data[0][
                        'frequency'
                    ]).change();
                } else {
                    alert('Jockey Pump data not found..!!');
                }
            }
        });

        $("#optional-button-close").on('click', function() {
            $("#adder-optional-modal").hide();
        });

        $(document).on("click", '#optional-add-success-modal #error-close', function(event) {
            $("#optional-add-success-modal").hide();
        });

        $(document).on("click", '#other-pump-modal #error-close', function(event) {
            $("#other-pump-modal").hide();
        });

        $(document).on("click", '#error-modal #error-close', function(event) {
            $("#error-modal").hide();
        });

        $(document).on("click", '#other-pump-success-modal #error-close', function(event) {
            $("#other-pump-success-modal").hide();
        });

        $(document).on("click", '.close-cart-modal', function(event) {
            $("#myModal").hide();
        });

        function refresh() {
            location.reload();
        }

        function mainPumpAjax() {
            var main_pump_selection = $('.main_panel_selection').find(":selected").val();
           
            if (main_pump_selection == 'electrical' || main_pump_selection == 'electrical-diesel') {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'electrical-pump') }}",
                    beforeSend: function() {
                        $('#electrical_pumptype').prop('disabled', true);
                        $('#electrical_article_number').prop('disabled', true);
                    },
                    success: function(response) {
                        electricalpump = electricalpumptemp = response;
                        $('#electrical_pumptype').prop('disabled', false);
                        $('#electrical_article_number').prop('disabled', false);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'electrical-pump-articles') }}",
                    beforeSend: function() {
                        $('#electrical_article_number').prop('disabled', true);
                    },
                    success: function(response) {
                        electricalpumparticle = response; // firefighting_electrical_pump table
                        $('#electrical_article_number').prop('disabled', false);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'electrical-control-panel-type') }}",
                    beforeSend: function() {
                        $('#control_panel_type').prop('disabled', true);
                    },
                    success: function(response) {
                        electrical_control_panel_type = response;
                        $('#control_panel_type').prop('disabled', false);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            }

            if (main_pump_selection == 'diesel' || main_pump_selection == 'electrical-diesel') {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'diesel-pump') }}",
                    beforeSend: function() {
                        $('#diesel_pumptype').prop('disabled', true);
                    },
                    success: function(response) {
                        dieselpump = dieselpumptemp = response;
                        if (main_pump_selection != 'electrical-diesel') {
                            $('#diesel_pumptype').prop('disabled', false);
                        }
                    },
                    error: function(data) {
                        
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'diesel-pump-articles') }}",
                    beforeSend: function() {
                        $('#diesel_article_number').prop('disabled', true);
                    },
                    success: function(response) {
                        dieselpumparticle = response;
                        $('#diesel_article_number').prop('disabled', false);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'diesel-control-panel-type') }}",
                    beforeSend: function() {
                        $('#control_panel_type').prop('disabled', true);
                    },
                    success: function(response) {
                        diesel_control_panel_type = response;
                        $('#control_panel_type').prop('disabled', false);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            }

            if (main_pump_selection == 'electrical-diesel') {
                disableDieselInput();
            }

            // Adder Id Fetch
            if (main_pump_selection != '') {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fire-fighting.show', 'adder-') }}" + main_pump_selection,
                    success: function(response) {
                        adder_ids = response;
                        pump_type = main_pump_selection;
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        }

        function jockeyPumpAjax() {

            pump_type = 'jockey-pump';
            $.ajax({
                type: "GET",
                url: "{{ route('fire-fighting.show', 'jockey-pump') }}",
                success: function(response) {
                    jockeypump = response;
                    $('input[name="jockey_full_article_number"]').prop('disabled', false);///** start 20241231 for jockey pump form auto fill***********/
                },
                error: function(data) {
                    console.log(data);
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ route('fire-fighting.show', 'adder-jockey-pump') }}",
                success: function(response) {
                    adder_ids = response;
                },
                error: function(data) {
                    console.log(data);
                }
            });
            /** start 20241231 for jockey pump form auto fill***********/
            $.ajax({
                type: "GET",
                url: "{{ route('fire-fighting.show', 'jockey-pump-articles') }}",
                beforeSend: function() {
                    $('#jockey_full_article_number').prop('disabled', true);/** start 20241231 for jockey pump form auto fill***********/
                },
                success: function(response) {
                    jockeypumparticle = response;
                    $('#jockey_full_article_number').prop('disabled', false);/** start 20241231 for jockey pump form auto fill***********/
                },
                error: function(data) {
                    console.log(data);
                }
            });
            /** end 20241231 for jockey pump form auto fill***********/
        }

        function getPrice(price) {
            let powerOften = Math.pow(10, 2);
            let result = Math.round(price * powerOften) / powerOften;
            return result;
        }
    </script>

    {{-- cart flow --}}
    <script>
        $(document).on('click', '#addtocart', function() {
            let cart_pump_data;
            let cart_pump_extra_data;
            switch (pump_type) {
                case 'jockey-pump':
                    cart_pump_data = $('.jockeypump-formInput').serializeArray();
                    break;

                case 'electrical':
                    cart_pump_data = $('.electrical-formInput').serializeArray();
                    if (other_pump_modal != '') {
                        cart_pump_data.push({
                            name: 'id',
                            value: other_pump_modal_id
                        });
                        cart_pump_data.push({
                            name: 'electrical_pumpmodels',
                            value: other_pump_modal
                        });
                    }
                    var electrical_control_panel_type = jQuery('#electrical_control_panel_type').val();
                    if (electrical_control_panel_type != '') {
                        cart_pump_data.push({
                            name: 'electrical_control_panel_type',
                            value: electrical_control_panel_type
                        });
                    }                    
                    break;

                case 'diesel':
                    cart_pump_data = $('.diesel-formInput').serializeArray();
                    if (other_pump_modal != '') {
                        cart_pump_data.push({
                            name: 'id',
                            value: other_pump_modal_id
                        });
                        cart_pump_data.push({
                            name: 'diesel_pumpmodels',
                            value: other_pump_modal
                        });
                    }

                    var diesel_control_panel_type = jQuery('#diesel_control_panel_type').val();
                    if (diesel_control_panel_type != '') {
                        cart_pump_data.push({
                            name: 'diesel_control_panel_type',
                            value: diesel_control_panel_type
                        });
                    }

                    break;

                case 'electrical-diesel':
                    $('.diesel-formInput').each(function() {
                        $(this).prop('disabled', false);
                    });

                    var electrical_data = $('.electrical-formInput').serializeArray();
                    var diesel_data = $('.diesel-formInput').serializeArray();
                    $('.diesel-formInput').each(function() {
                        $(this).prop('disabled', true);
                    });
                    cart_pump_data = electrical_data;
                    cart_pump_extra_data = diesel_data;
                    if (other_pump_modal != '') {
                        // cart_pump_data.push({name: 'id', value: other_pump_modal_id});
                        cart_pump_data.push({
                            name: 'electrical_pumpmodels',
                            value: other_pump_modal
                        });
                    }
                    if (other_pump_modal != '') {
                        // cart_pump_extra_data.push({name: 'id', value: other_pump_modal_id});
                        cart_pump_extra_data.push({
                            name: 'diesel_pumpmodels',
                            value: other_pump_modal
                        });
                    }
                    var electrical_control_panel_type = jQuery('#electrical_control_panel_type').val();
                    if (electrical_control_panel_type != '') {
                        cart_pump_data.push({
                            name: 'electrical_control_panel_type',
                            value: electrical_control_panel_type
                        });
                    }
                    var diesel_control_panel_type = jQuery('#diesel_control_panel_type').val();
                    if (diesel_control_panel_type != '') {
                        cart_pump_data.push({
                            name: 'diesel_control_panel_type',
                            value: diesel_control_panel_type
                        });
                    }                    
                    break;
            }
            $.ajax({
                type: "post",
                url: "{{ route('fire-fighting.update', auth()->id()) }}",
                'dataType': 'json',
                data: {
                    _token: "{{ csrf_token() }}",
					_method:"put",
                    'pump_type': pump_type,
                    'adder_ids': adderIds,
                    'data': cart_pump_data,
                    'extra_data': cart_pump_extra_data
                },
                success: function(response) {
                    if (response['success']) {
                        location.reload();
                    } else {
                        alert(response['msg']);
                    }
                },
                error: function(data) {}
            });
        });
    </script>
@stop

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('fassets/css/bootstrap.min.css') }}">
    <style>
        .modal-heading-change {
            border: 0;
            font-size: 0.9rem;
            font-family: "WiloPlusGlobalBold";
            outline: none;
            background: #fff;
            cursor: pointer;
            flex: 0 0 50%;
            padding: 0.6rem;
            border: 0.1px solid #169e88;
            border-radius: 3px;
        }

        .modal-heading-change.active {
            background-color: #169e88;
            color: white;
        }

        .select-other-pump-modal {
            border: 0;
            background: #169e88;
            color: white !important;
            border-radius: 5px;
            padding: 7px 20px;
            font-size: 15px;
            font-weight: 600;
        }

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

        div#adder-optional-modal div#adder-optional-modal-table {
            border: 0;
        }

        div#adder-optional-modal div#adder-optional-modal-table tr td {
            font-size: 16px;
            line-height: 30px;
            border: 0;
            padding: 0;
            font-weight: 500;
        }

        div#adder-optional-modal div#adder-optional-modal-table tr th {
            font-size: 18px;
            line-height: 30px;
            border: 0;
            padding: 0;
        }

        div#master-price-record ul.price li {
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .modalBtns span.close {
            height: 40px;
        }

        .main_panel_selection_set {
            width: 44.5%;
            padding: 0 14px;
        }

        /* Float cancel and delete buttons and add an equal width */
        .cancelbtn,
        .deletebtn {
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
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 50px;
        }

        .modal .modal-content .modalBtns button,
        .modal .modal-content .modalBtns span {
            opacity: 1;
        }

        .modal .modal-content .modalBtns span:hover {
            color: white;
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto;
            /* 5% from the top, 15% from the bottom and centered */
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
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

            .cancelbtn,
            .deletebtn {
                width: 100%;
            }
        }
    </style>
@stop
