@extends('frontend.layout.app')
@section('content')

<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="addQuotationMidSection">
                <h2>Quotation</h2>
                <div class="quotationTopSection">
                    <div class="tableResponsive">
                        <table>
                            <thead>
                                <tr>
                                    <th align="left">Quotation No</th>
                                    <th align="right">{{$quotation->quotation_number}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="left">Revision</td>
                                    <td align="right">{{$customer->revision_number}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Date</td>
                                    <td align="right">{{ \Carbon\Carbon::parse($customer->created_at)->format('m/d/Y')   }}</td>
                                </tr>
                                <tr>
                                    <td align="left">Customer Name</td>
                                    <td align="right">{{$customer->name}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Project Name</td>
                                    <td align="right">{{$customer->revision_number}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Project Location</td>
                                    <td align="right">{{$customer->project_location}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Country</td>
                                    <td align="right">{{$customer->country}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Segment category </td>
                                    <td align="right">{{$customer->segment_category}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Customer Email</td>
                                    <td align="right">{{$customer->email_id}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Customer Phone no</td>
                                    <td align="right">{{$customer->phone_no}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Customer Address</td>
                                    <td align="right">{{$customer->address}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Customer Enquiry Form Number</td>
                                    <td align="right">{{$customer->enquiry_form_number}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Consultant</td>
                                    <td align="right">{{$customer->consultant}}</td>
                                </tr>
                                <tr>
                                    <td align="left">Contractor</td>
                                    <td align="right">{{$customer->contractor}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php $totalPrice = 0.00; ?>
                <div class="quotationBottomSection">
                    <div class="tableResponsive">
                        <table>
                            <thead>
                                <tr>
                                    <th width="15%">Item Description</th>
                                    <th width="15%">Article Number</th>
                                    <th width="10%">Component</th>
                                    <th width="5%">Unit Price</th>
                                    <th width="5%">Qty</th>
                                    <th width="10%">Total Price</th>
                                    <th width="15%">Selection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($controlPanelCartData->isNotEmpty())
                                @foreach($controlPanelCartData as $key=> $val)
                                <tr>                                   
                                    <!-- A Code: 01-04-2026 Start - Model not Open -->
                                    <td>
                                        <a class="detail-modal-cp" href="javascript:void(0)">
                                            Control Panel {{$val->noofpumps['value'] }} x {{ $val->powers['value'] }}KW {{$val->starter_code}}/AE
                                        </a>
                                        <input type="hidden" class="cp-cart-id" value="{{$val['id']}}">
                                    </td>
                                    <!-- A Code: 01-04-2026 Start - Model not Open -->

                                    <td>{{$val['full_article_number']}}</td>
                                    <td>Control Panel </td>
                                    <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                    <td>
                                        {{$val->qty}}
                                    </td>
                                    <td class="total-price">{{App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty)}}</td>
                                    <td>
                                        <a href="{{ URL::to('controlpanel/cart-item/'.$val['id'] )}}" target="_blank">
                                            <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                        </a>
                                    </td>
                                </tr>
                                <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif

                                @if($atmosCartData->isNotEmpty())
                                @foreach($atmosCartData as $key=> $val)
                                @php
                                    $short_code = DB::table('atmos_materials')->where('id',$val->material_id)->pluck("short_code")->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a class="detail-modal" href="javascript:void(0)">
                                        {{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                                        </a>
                                    </td>
                                    <td>
                                        <a class="detail-modal" href="javascript:void(0)">
										{{--{{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}--}}
										@if(auth()->user() && auth()->user()->country_id == "6")
                                                @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                                                    {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                                                    @else
                                                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                                    @endif 
                                            @else
                                                {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                            @endif
                                        @if($val->bare_shaft_article_number != null)
                                            <br>
                                            [Bare Shaft Article Number: {{$val->bare_shaft_article_number}}]
                                        @endif
                                        </a>
                                    </td>
                                    <td>Atmos Giga</td>
                                    <td>
                                        {{--{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}--}}
                                        @if($val['is_bareshaft_selection'] != "1")
                                            {{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}
                                        @else
                                            {{ App\Helpers\CurrencyHelper::withCurrency($val['bare_pump_price'])}}
                                        @endif
                                    </td>
                                    <td>{{$val->qty}}</td>
                                    <td class="total-price">
                                        @if($val['is_bareshaft_selection'] != "1")
                                            {{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}
                                        @else
                                            {{ App\Helpers\CurrencyHelper::withCurrency($val->bare_pump_price*$val->qty) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($val['is_accesories_manual'])
                                            <a href="javascript:void(0)">
                                        @else
                                            <a href="{{ URL::to('atmos/cart-item/'.$val['id'] )}}" target="_blank">
                                        @endif
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />                                            
                                            </a>
                                    </td>
                                </tr>
                                @if($val['is_bareshaft_selection'] != "1")
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @else
                                    <?php $totalPrice += round($val->bare_pump_price * $val->qty); ?>
                                @endif
                                @endforeach
                                @endif

                                @if($scpCartData->isNotEmpty())
                                @foreach($scpCartData as $key=> $val)
                                @php
                                    $short_code = DB::table('scp_materials')->where('id',$val->material_id)->pluck("short_code")->first();
                                @endphp
                                <tr>
                                    <!-- A Code: 27-02-2026 Start - Model not Open -->
                                    <td>
                                        <a class="detail-modal-scp" href="javascript:void(0)">
                                            {{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                                        </a>
                                    </td>
                                    <td>
                                        <a class="detail-modal-scp" href="javascript:void(0)">
										@if(auth()->user() && auth()->user()->country_id == "6")
                                                @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                                                    {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                                                @else
                                                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                                @endif 
                                        @else
                                            {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                        @endif
                                        </a>
                                    </td>
                                    <!-- A Code: 27-02-2026 End - Model not Open -->
                                     
                                    <td>Scp Pump</td>
                                    <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                    <td>{{$val->qty}}</td>
                                    <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                    <td>
                                        <!-- @if($val['is_accesories_manual'])
                                            <a href="javascript:void(0)" >
                                        @else
                                            <a href="{{ URL::to('scp/cart-item/'.$val['id'] )}}" target="_blank">
                                        @endif
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />                                                
                                            </a>  -->
                                            
                                        <!-- A Code: 27-02-2026 Start - Model not Open -->
                                            <a href="{{ URL::to('scp/cart-item/'.$val['id'] )}}" target="_blank">
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />                                                
                                            </a> 
                                            <input type="hidden" class="scp-cart-id" value="{{$val['id']}}">
                                        <!-- A Code: 27-02-2026 End - Model not Open -->
                                                                           
                                    </td>
                                </tr>
                                    @if($val['is_bareshaft_selection'] != "1")
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @else
                                        <?php $totalPrice += round($val->bare_pump_price * $val->qty); ?>
                                    @endif
                                @endforeach
                                @endif

                                <!-- A Code: 24-02-2026 Start -->
                                @if($scpvCartData->isNotEmpty())
                                @foreach($scpvCartData as $key=> $val)
                                @php
                                    $short_code = DB::table('scpv_materials')->where('id',$val->material_id)->pluck("short_code")->first();
                                @endphp
                                <tr>
                                    <!-- A Code: 27-02-2026 Start - Model not Open -->
                                    <td>                                        
                                        <a class="detail-modal-scpv" href="javascript:void(0)">                                        
                                            {{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                                        </a>                                        
                                    </td>
                                    <td>
                                        <a class="detail-modal-scpv" href="javascript:void(0)">
										@if(auth()->user() && auth()->user()->country_id == "6")
                                            @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                                                {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                                            @else
                                                {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                            @endif 
                                        @else
                                            {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                        @endif
                                        </a>
                                    </td>
                                    <!-- A Code: 27-02-2026 End - Model not Open -->
                                    <td>Scpv Pump</td>
                                    <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                    <td>{{$val->qty}}</td>
                                    <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                    <td>      
                                        <!-- @if($val['is_accesories_manual'])
                                            <a href="javascript:void(0)">
                                        @else
                                            <a href="{{ URL::to('scpv/cart-item/'.$val['id'] )}}" target="_blank">
                                        @endif -->

                                       <!-- A Code: 27-02-2026 Start - Model not Open -->
                                            <a href="{{ URL::to('scpv/cart-item/'.$val['id'] )}}" target="_blank">
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />                                                
                                            </a>                                      
                                     
                                            <input type="hidden" class="scpv-cart-id" value="{{$val['id']}}">
                                        <!-- A Code: 27-02-2026 End - Model not Open --> 
                                    </td>
                                </tr>
                                    @if($val['is_bareshaft_selection'] != "1")
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @else
                                        <?php $totalPrice += round($val->bare_pump_price * $val->qty); ?>
                                    @endif
                                @endforeach
                                @endif
                                <!-- A Code: 24-02-2026 End -->

                                {{--booster cart starts--}}
                                @if($boosterCartData->isNotEmpty())
                                @foreach($boosterCartData as $key=> $val)
                                <tr>
                                    <td>
                                        <!-- A Code: 17-06-2026 Start -->
                                        <a class="detail-modal-booster" href="javascript:void(0)">
                                            @php     
                                                $request_data = DB::table('control_panels')->where('id', $val->cp_id)->first();
                                                $cpNo_of_pump = DB::table('number_of_pumps')->where('id', $request_data->no_of_pump_id)->value('value');
                                                $cpDetails = optional($val->cpDetails); // NEW booster_carts_cp_details 
                                                if (!optional($cpDetails)->no_of_pump)
                                                {
                                                    $boosterData = $val->boosterCpDataOld[0]; // Old control_panels Data                                                    
                                                }else{
                                                    $boosterData = $val->boosterCpData[0]; // NEW control_panels_master Data                                                    
                                                }
                                                // Display New booster_carts_cp_details 
                                                // But When it's Empty Then Display NEW control_panels_master Data
                                                // if NEW control_panels_master Data with Multiple Values Then Display According to OLD control_panels Data

                                                $getMatchingValue = function ($masterValue, $selectedValue) {
                                                    if (str_contains($masterValue, ',')) {
                                                        $options = array_map('trim', explode(',', $masterValue));
                                                        return in_array($selectedValue, $options) ? $selectedValue : null;
                                                    }
                                                    return $masterValue;
                                                };
                                                $noOfPump = $cpDetails->no_of_pump 
                                                    ?? $getMatchingValue($boosterData->no_of_pumps, $cpNo_of_pump);
                                                    
                                                $const =null;
                                                if(str_starts_with($boosterData->table_name, 'basic_')  == true){
                                                    $const = "COE";
                                                }else{
                                                    $const = 'CO';
                                                    $array_check = array(3,4,7);
                                                    
                                                    $stater_type_id = DB::table('starter_types')->where('value', trim($cpDetails->stater_type))->value('id');
                                                    if(in_array($stater_type_id,$array_check) ){
                                                        $const = 'COR';
                                                    }
                                                }
                                            @endphp
                                            {{$const}} {{ $noOfPump }} {{$val->model_no }}/{{$boosterData->code}}/AE
											<br>
                                            @if(!empty($val['mechanical_article_number']))
                                            [{{$val['mechanical_article_number']}} - Mechnical Assembly]
                                            @endif
                                            <br>
                                            @if(!empty($val['electrical_article_number']))
                                            [{{$val['electrical_article_number']}} - Control panel]
										@endif
                                        </a>
                                        <!-- A Code: 17-06-2026 End -->

                                        <!-- A Code: 15-06-2026 Start -->
                                        <input type="hidden" class="booster-cart-id" value="{{$val['id']}}">
                                        <!-- A Code: 15-06-2026 End -->
                                    </td>
                                    <td>
                                        <a class="detail-modal" href="javascript:void(0)">
										{{--{{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}--}}
										@if(auth()->user() && auth()->user()->country_id == "6")
                                                @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                                                    {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                                                @else
                                                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                                    @endif 
                                            @else
                                                {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                            @endif
                                        </a>
                                    </td>
                                    <td>Booster</td>
                                    <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}
                                        <span style = "">
                                            <br>[Electrical : {{App\Helpers\CurrencyHelper::withCurrency($val['electrical_items_price'])}} ]
                                            <br>[Mechanical : {{App\Helpers\CurrencyHelper::withCurrency($val['mechanical_items_price'])}} ]
                                        </span>
                                    </td>
                                    <td class="">{{$val->qty }}</td>
                                    <td class="total-price">{{  App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}
                                    </td>
                                    <td>  
                                        <a href="{{ URL::to('booster-set/cart-item/'.$val['id'] )}}" target="_blank"> <img src="{{asset('fassets/images/viewIcon.png')}}" /></a>                                        
                                    </td>
                                </tr>
                                <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif
                                {{-- booster cart ends--}}

								{{-- Fire Fighting Pump Start --}}
                                @if($firefightingCartData->isNotEmpty())
                                    @foreach($firefightingCartData as $key=> $val)
                                        <tr>
                                            <td>
                                                <a class="detail-modal" href="javascript:void(0)">
                                                {{ ucwords(str_replace('-pump', '', $val->category)) }} - {{ $val->pump_models }}/AE
                                                </a>
                                            </td>
                                            <td>
                                                <a class="detail-modal" href="javascript:void(0)">
                                                    {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                </a>
                                            </td>
                                            <td>{{ ucwords(str_replace('-pump', '', $val->category)) }} Pump</td>
                                            <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                            <td>{{$val->qty}}</td>
                                            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                            <td>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'].'/excel' )}}" target="_blank">
                                                    Excel
                                                </a>
                                                <!-- start - 20250106 edit model not open -->
                                                <input type="hidden" class="cp-id" value="{{$val['id']}}">
                                                <!-- end - 20250106 edit model not open -->
                                            </td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                        <div class="text-left" id=""><h4>Total Price: </h4><span class="" id="total-price-updated"> {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex cusPagination">
            <!--            <div class="">
            <?php $cpId = Request::get('cp_id'); ?>
                           <a href="{{URL::to('controlpanel/customer-information/' . $customer->id ) }}"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
                        </div>-->
            <div class="">
                <a  onclick="window.history.back()" href="javascript:void(0)"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
            <!--            <div class="">
                            <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
                        </div>-->
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
            </div>
            <div class="right">
                <ul>
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('/')}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- //start - 20250106 edit model not open -->
<!-- mid section end -->
<div id="detail-control-panel-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <!-- <span class="close">&times;</span> -->
        <div class="modal-body" id="detail-control-panel-modal-body">
            <!--Table-->
        </div>
        <div class="modalBtns">
            <span class="close-detail-control-panel-modal" >Close</span>
        </div>
    </div>

</div>
<!-- //end - 20250106 edit model not open -->
<script>
    //start - 20250106 edit model not open
    $(".detail-modal").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        $.ajax({
            type: "get",
            url: "{{ url('fire-fighting') }}" + '/' + cp_id + '/edit',//20250106 edit model not open
            data: {cp_id: cp_id},
            success: function (response) {
                if (response.data.html) {

                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {
            }
        });
    });
    $(document).on("click", '.close-detail-control-panel-modal', function (event) {
        $("#detail-control-panel-modal").hide();
    });
    //end - 20250106 edit model not open

    // A Code: 27-02-2026 Start - Model not Open
    $(".detail-modal-scpv").on("click", function () {
        var scpv_id = $(this).closest('tr').find('.scpv-cart-id').val();
        $.ajax({
            type: "get",
            url: "{{url('scpv/ajax-detail-modal-scpv')}}",
            data: {scpv_id: scpv_id},
            success: function (response) {
                if (response.data.html) {
                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {
            }
        });
    });
    // A Code: 27-02-2026 End - Model not Open

    // A Code: 27-02-2026 Start - Model not Open
    $(".detail-modal-scp").on("click", function () {
        var scp_id = $(this).closest('tr').find('.scp-cart-id').val();
        $.ajax({
            type: "get",
            url: "{{url('scp/ajax-detail-modal-scp')}}",
            data: {scp_id: scp_id},
            success: function (response) {
                if (response.data.html) {
                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {
            }
        });
    });
    // A Code: 27-02-2026 End - Model not Open

    // A Code: 01-04-2026 Start - Model not Open
    $(".detail-modal-cp").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-cart-id').val();
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/ajax-detail-modal-cp')}}",
            data: {cp_id: cp_id},
            success: function (response) {
                if (response.data.html) {
                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {
            }
        });
    });
    // A Code: 01-04-2026 End - Model not Open

    $(".detail-modal-booster").on("click", function () {
        var booster_id = $(this).closest('tr').find('.booster-cart-id').val();
        $.ajax({
            type: "get",
            url: "{{url('booster/ajax-detail-modal-booster')}}",
            data: {booster_id: booster_id},
            success: function (response) {
                if (response.data.html) {

                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {

            }

        });


    });

</script>
@endsection
