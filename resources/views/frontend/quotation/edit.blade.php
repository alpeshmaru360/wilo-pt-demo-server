@extends('frontend.layout.app')
@section('content')
@php
    if(empty($quotation)){
        header("Location: " . route('controlpanel.quotations.userlist'));
        exit;
    }
@endphp
<style>
    #add_quotation{background: green;padding: 5px 6px 6px 5px;border-radius: 14px;color: white;}
    .qty_input .qty .scpv-minus {
        cursor: pointer;display: inline-block;vertical-align: top;color: #169e88;width: 30px;height: 30px;
        font: 30px / 0.9 "Noto Sans", sans-serif, sans-serif;text-align: center;background-clip: padding-box;
    }
    .qty_input .qty .scpv-plus {
        cursor: pointer;display: inline-block;vertical-align: top;color: #169e88;width: 30px;height: 30px;
        font: 30px / 0.9 "Noto Sans", sans-serif, sans-serif;text-align: center;
    }
</style>
<!-- A Code: 26-05-2026 Start -->
<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<!-- A Code: 26-05-2026 End -->
 
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
								<!--
                                <tr>
                                    <th align="left">Add Quotation</th>
                                    <th align="right">
                                        <a href = "{{url('/'.$quotation->quotation_number)}}" id="add_quotation" target="_blank"> Add </a> 
                                    </th>
                                </tr>
								-->
                                <tr>
							
                                    <th align="left">Revision Number</th>
                                    <th align="right">
                                        <a href = "#" id="revision_number"> {{$quotations_revision_counter->total_revision_number}} </a> 
                                    </th>
                                </tr>
                            </thead>

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
                                    <th width="10%">Article Number</th>
                                    <th width="10%">Component</th>
                                    <th width="5%">Unit Price</th>
                                    <th width="15%">Qty</th>
                                    <th width="10%">Total Price</th>
                                    <th width="05%">Selection</th>
                                </tr>
                            </thead>
                            @if($quotation != "")
                            <tbody>                                
                                @if($controlPanelCartData->isNotEmpty())
                                @foreach($controlPanelCartData as $key=> $val)
                                    <tr id="control_panel_data-{{$val['id']}}">                                       
                                        <!-- A Code: 01-04-2026 End - Model not Open -->
                                        <td>
                                            <a class="detail-modal-cp" href="javascript:void(0)">
                                                {{$val->applications['value'] }} {{--$val->noofpumps['value'] --}} x {{ $val->powers['value'] }} {{$val->starter_code}}
                                            </a>
                                        </td>                                        
                                        <td><a class="detail-modal-cp" href="javascript:void(0)">
                                                {{$val['full_article_number']}}
                                            </a>
                                        </td>                                        
                                        <!-- A Code: 01-04-2026 End - Model not Open -->

                                        <td>Control Panel </td>

                                        <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                        <td>
                                            <div class="qty_input">
                                                <div class="qty">
                                                    <span class="minus qtyBtn">-</span>
                                                    <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                    <span class="plus qtyBtn">+</span>
                                                </div>
                                            </div>
                                        </td>
                                        <input type="hidden" class="cp-id" value="{{$val['id']}}">
                                        <input type="hidden"  id="cp-{{$val['id']}}" >                                        
                                        <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                        <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                        <td>
                                            <a href="{{ URL::to('controlpanel/cart-item/'.$val['id'] )}}" target="_blank">
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>
                                            <button class="delete_controlpanel_item">
                                                <img src="{{asset('fassets/images/delIcon.png')}}" /> 
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif                                

                                @if($atmosCartData->isNotEmpty())
                                @foreach($atmosCartData as $key=> $val)
                                    <tr id = "atmos_cart_item-{{$val['id']}}">
                                        <td style="display: none;"><input type="checkbox" checked name="atmos_checked_id" value="{{$val['id']}}"></td>
                                        <td>

                                            <a class="detail-modal" href="javascript:void(0)">
                                                {{$val->pump_name }} x {{ $val->brand }} x {{$val->power}}
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
                                            @if($val['is_bareshaft_selection'] != "1")
                                                {{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}
                                            @else
                                                {{ App\Helpers\CurrencyHelper::withCurrency($val['bare_pump_price'])}}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="qty_input">
                                                <div class="qty">
                                                    <span class="atmos-minus qtyBtn">-</span>
                                                    <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                    <span class="atmos-plus qtyBtn">+</span>
                                                </div>
                                            </div>
                                        </td>

                                        <input type="hidden" class="atmos-cart-id" value="{{$val['id']}}">
                                        <input type="hidden"  id="at-{{$val['id']}}" >
                                        <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
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
                                            <button class="delete_atmos_item">
                                                <img src="{{asset('fassets/images/delIcon.png')}}" />
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif

                                @if($scpCartData->isNotEmpty())
                                @foreach($scpCartData as $key=> $val)
                                    <tr id = "scp_cart_item-{{$val['id']}}">
                                        <td style="display: none;"><input type="checkbox" checked name="scp_checked_id" value="{{$val['id']}}"></td>
                                        <!-- A Code: 27-02-2026 Start - Model not Open -->
                                        <td>
                                            <a class="detail-modal-scp" href="javascript:void(0)">
                                                {{$val->pump_name }} x {{ $val->brand }} x {{$val->power}}
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
                                        <td>Scp</td>
                                        <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                        <td>
                                            <div class="qty_input">
                                                <div class="qty">
                                                    <span class="scp-minus qtyBtn">-</span>
                                                    <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                    <span class="scp-plus qtyBtn">+</span>
                                                </div>
                                            </div>
                                        </td>
                                        <input type="hidden" class="cp-id" value="{{$val['id']}}">
                                        <input type="hidden" class="scp-cart-id" value="{{$val['id']}}">
                                        <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                        <input type="hidden"  id="scp-{{$val['id']}}" >
                                        <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                        <td>
                                            {{--
                                            @if($val['is_accesories_manual'])
                                            <a href="javascript:void(0)">
                                            @else
                                            <a href="{{ URL::to('scp/cart-item/'.$val['id'] )}}" target="_blank">
                                            @endif
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>
                                            --}}
                                           
                                            <a href="{{ URL::to('scp/cart-item/'.$val['id'] )}}" target="_blank">                                         
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>

                                            <button class="delete_scp_item">
                                                <img src="{{asset('fassets/images/delIcon.png')}}" />
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif

                                <!-- A Code: 25-02-2026 Start -->
                                @if($scpvCartData->isNotEmpty())
                                @foreach($scpvCartData as $key=> $val)
                                    <tr id = "scpv_cart_item-{{$val['id']}}">
                                        <td style="display: none;"><input type="checkbox" checked name="scpv_checked_id" value="{{$val['id']}}"></td>
                                        <!-- A Code: 27-02-2026 Start - Model not Open -->
                                        <td>
                                            <a class="detail-modal-scpv" href="javascript:void(0)">
                                                {{$val->pump_name }} x {{ $val->brand }} x {{$val->power}}
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
                                        <td>Scpv</td>
                                        <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
                                        <td>
                                            <div class="qty_input">
                                                <div class="qty">
                                                    <span class="scpv-minus qtyBtn">-</span>
                                                    <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                    <span class="scpv-plus qtyBtn">+</span>
                                                </div>
                                            </div>
                                        </td>
                                        <input type="hidden" class="cp-id" value="{{$val['id']}}">
                                        <input type="hidden" class="scpv-cart-id" value="{{$val['id']}}">
                                        <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                        <input type="hidden"  id="scpv-{{$val['id']}}">
                                        <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                        <td>
                                            {{--
                                            @if($val['is_accesories_manual'])
                                            <a href="javascript:void(0)">
                                            @else
                                            <a href="{{ URL::to('scpv/cart-item/'.$val['id'] )}}" target="_blank">
                                            @endif
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>
                                            --}}
                                            
                                            <a href="{{ URL::to('scpv/cart-item/'.$val['id'] )}}" target="_blank">                                          
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>
                                            <button class="delete_scpv_item">
                                                <img src="{{asset('fassets/images/delIcon.png')}}" />
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif
                                <!-- A Code: 25-02-2026 End -->

                                {{--  booster cart starts--}}
                                @if($boosterCartData->isNotEmpty())
                                @foreach($boosterCartData as $key=> $val)
                                    <tr id = "booster_cart_data-{{$val['id']}}">
                                        <td style="display: none;"><input type="checkbox" checked name="booster_checked_id" value="{{$val['id']}}"></td>
                                        <td>
                                            <!-- A Code: 17-06-2026 Start -->
                                            <a class="detail-modal-booster" href="javascript:void(0)">
                                                @php
                                                    $request_data = DB::table('control_panels')->where('id', $val->cp_id)->first();
                                                    
                                                    $cpNo_of_pump = DB::table('number_of_pumps')->where('id', $request_data->no_of_pump_id)->value('value');
                                                    $cpDetails = optional($val->cpDetails); // NEW booster_carts_cp_details Data
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
                                            </a>
                                            <!-- A Code: 17-06-2026 End -->
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
                                        <td>
                                            Booster
                                        </td>

                                        <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}
                                            <span style = "">
                                                <br>[Electrical : {{App\Helpers\CurrencyHelper::withCurrency($val['electrical_items_price'])}} ]
                                                <br>[Mechanical : {{App\Helpers\CurrencyHelper::withCurrency($val['mechanical_items_price'])}} ]
                                            </span>
                                        </td>
                                        <td>
                                            <div class="qty_input">
                                                <div class="qty">
                                                    <span class="booster-minus qtyBtn">-</span>
                                                    <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                    <span class="booster-plus qtyBtn">+</span>
                                                </div>
                                            </div>
                                        </td>

                                        <input type="hidden" class="booster-cart-id" value="{{$val['id']}}">
                                        <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                        <input type="hidden"  id="booster-{{$val['id']}}" >
                                        <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                        <td>
                                            <a href="{{ URL::to('booster-set/cart-item/'.$val['id'] )}}" target="_blank">
                                                <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                            </a>
                                            <button class="delete_booster_item">
                                                <img src="{{asset('fassets/images/delIcon.png')}}" />
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $totalPrice += round($val->price * $val->qty); ?>
                                @endforeach
                                @endif
                                

                                {{-- booster cart ends--}}
                                {{-- Fire Fighting Pump Start --}}
                                @if($firefightingCartData->isNotEmpty())
                                    @foreach($firefightingCartData as $key=> $val)
                                        <tr id="ff_cart_item-{{$val['id']}}">
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
                                            <td>
                                                <div class="qty_input">
                                                    <div class="qty">
                                                        <span class="firefighting-qtyBtn qtyBtn" data-pump_type="{{ $val->category }}" data-id="{{ $val->id }}" data-qty="minus">-</span>
                                                        <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                        <span class="firefighting-qtyBtn qtyBtn" data-pump_type="{{ $val->category }}" data-id="{{ $val->id }}" data-qty="plus">+</span>
                                                    </div>
                                                </div>
                                                <!-- start - 20250106 edit model not open -->
                                                <input type="hidden" class="cp-id" value="{{$val->id}}">
                                                <input type="hidden" class="ff-cart-id" value="{{$val['id']}}">
                                                <input type="hidden"  id="cp-{{$val->id}}" >
                                                <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                                <!-- end - 20250106 edit model not open -->
                                            </td>
                                            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                            <td>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'].'/excel' )}}" target="_blank">
                                                    Excel
                                                </a>
                                                <button class="delete_ff_item">
                                                    <img src="{{asset('fassets/images/delIcon.png')}}" /> 
                                                </button>
                                            </td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                @endif
                                {{-- Fire Fighting Pump End --}}
                            </tbody>
                            @endif
                        </table>
                        <div class="text-right txt-ttl" id=""><h4>Total Price: </h4><span class="" id="total-price-updated"> {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</span></div>
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
                <a  onclick="window.history.back()" href=""><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
            <!--<div class="">
                <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
            </div>-->
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
            </div>
            <div class="right">
                <ul>
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->

<div id="detail-control-panel-modal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="detail-control-panel-modal-body">
            <!--Table-->
        </div>
        <div class="modalBtns">
            <span class="close-detail-control-panel-modal" >Close</span>
        </div>
    </div>
</div>
<input id="quotation-number" type="hidden" value="{{$quotation->quotation_number}}"/>
@endsection

@section('script')
<script>
    $(document).on('click', '.delete-firefighting-cart', function () {
        var firefighting_id = $(this).val();
        var pump_type = $(this).data('pump_type');
        var rmtr = $(this).closest('tr');

        $.ajax({
            type: "post",
            url: "{{ route('fire-fighting.store')}}",
            'dataType': 'json',
            data: {
                _token: "{{ csrf_token() }}",
                'post_type' : 'delete-cart',
                'pump_type' : pump_type,
                'firefighting_id' : firefighting_id
            },
            success: function (response) {
                rmtr.remove();
                UpdatedTotalPriceQuotation();
            },
            error: function (data) {
            }
        });
    });

    $(document).on('click', '.firefighting-qtyBtn', function () {
        var qty = $(this).parent('.qty').find('input[name="quantity"]').val();
        var qty_plus_minus = $(this).data('qty');
        var firefighting_id = $(this).data('id');
        var pump_type = $(this).data('pump_type');

        var totalprice = $(this).closest('tr').find('.total-price');
        var total_price_updated = $('#total-price-updated');
        
        if (qty >= 1) {
            $.ajax({
                type: "post",
                url: "{{ route('fire-fighting.store')}}",
                'dataType': 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    'post_type' : 'qty-change',
                    'pump_type' : pump_type,
                    'qty' : qty,
                    'qty_plus_minus' : qty_plus_minus,
                    'firefighting_id' : firefighting_id
                },
                success: function (response) {
                    totalprice.text(response['total_price']);
                    UpdatedTotalPriceQuotation();
                },
                error: function (data) {
                }
            });
        }
    });
</script>

<script>
    $("#add_quotation").on("click",function(){
        quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        $.ajax({
            type:"get",
            url:"{{url('/')}}",
            data:{quotation_number:quotation_number},
            success:function(){
                },
            error:function(){
                alert("Something getting error..!!");
            }
        });
    });

    $(".plus").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        //        if(totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        //        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function (response) {
                    $("#cp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();

                },
                error: function () {

                }

            });
        }
    });

    $(".minus").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) - 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        //        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        //        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function (response) {

                    $("#cp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();
                },
                error: function () {

                }

            });
        }

    });

    $(".atmos-plus").on("click", function () {
        var atmos_cart_id = $(this).closest('tr').find('.atmos-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (totalPrice > 0) {
            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('atmos/ajax-qty-update')}}",
                data: {qty: qty, atmos_cart_id: atmos_cart_id},
                success: function (response) {
                    $("#at-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)

                    UpdatedTotalPriceQuotation();


                },
                error: function () {

                }

            });
        }
    });

    $(".atmos-minus").on("click", function () {
        var atmos_cart_id = $(this).closest('tr').find('.atmos-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) - 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('atmos/ajax-qty-update')}}",
                data: {qty: qty, atmos_cart_id: atmos_cart_id},
                success: function (response) {
                    $("#at-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();
                },
                error: function () {

                }

            });
        }

    });
        
    //Scp
    $(".scp-plus").on("click", function () {
        var scp_cart_id = $(this).closest('tr').find('.scp-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('scp/ajax-qty-update')}}",
                data: {qty: qty, scp_cart_id: scp_cart_id},
                success: function (response) {
                    $("#scp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();


                },
                error: function () {

                }

            });
        }
    });

    $(".scp-minus").on("click", function () {
        var scp_cart_id = $(this).closest('tr').find('.scp-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) - 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('scp/ajax-qty-update')}}",
                data: {qty: qty, scp_cart_id: scp_cart_id},
                success: function (response) {
                    $("#scp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();

                },
                error: function () {

                }

            });
        }

    });

    // A Code: 25-02-2026 Start

    //Scpv
    $(".scpv-plus").on("click", function () {
        var scpv_cart_id = $(this).closest('tr').find('.scpv-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
       
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('scpv/ajax-qty-update')}}",
                data: {qty: qty, scpv_cart_id: scpv_cart_id},
                success: function (response) {
                    $("#scpv-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();
                },
                error: function () {
                }
            });
        }
    });

    $(".scpv-minus").on("click", function () {
        var scpv_cart_id = $(this).closest('tr').find('.scpv-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) - 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
       
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('scpv/ajax-qty-update')}}",
                data: {qty: qty, scpv_cart_id: scpv_cart_id},
                success: function (response) {
                    $("#scpv-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();

                },
                error: function () {
                }
            });
        }
    });

    // A Code: 25-02-2026 End

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
        var cp_id = $(this).closest('tr').find('.cp-id').val();
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

    //BOOSTER
    $(".booster-plus").on("click", function () {
        var booster_cart_id = $(this).closest('tr').find('.booster-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        //        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        //        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('booster-set/ajax-qty-update')}}",
                data: {qty: qty, booster_cart_id: booster_cart_id},
                success: function (response) {
                    $("#booster-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();
                },
                error: function () {

                }

            });
        }
    });

    $(".booster-minus").on("click", function () {
        var booster_cart_id = $(this).closest('tr').find('.booster-cart-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) - 1;
        //        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        //        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        //        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('booster-set/ajax-qty-update')}}",
                data: {qty: qty, booster_cart_id: booster_cart_id},
                success: function (response) {
                    $("#booster-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceQuotation();
                },
                error: function () {

                }

            });
        }

    });

    $(document).on("click", '.close-detail-control-panel-modal', function (event) {
        $("#detail-control-panel-modal").hide();
    });

    function withCurrency(price) {
        //        price = price.toFixed(2);
        let dollarUSLocale = Intl.NumberFormat('en-US');
        return dollarUSLocale.format(price) + '$';
    }

    function UpdatedTotalPriceQuotation() {
        var quotation_number = $("#quotation-number").val();
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/quotations/updatedTotalPrice')}}",
            data: {quotation_number: quotation_number},
            success: function (response) {
                if (response.data.total_price_updated) {

                    $("#total-price-updated").html('');
                    $("#total-price-updated").html(response.data.total_price_updated);

                }
            },
            error: function () {

            }

        });
    }

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

<!-- A Code: 26-05-2026 Start -->
<!-- <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script>
<!-- A Code: 26-05-2026 End -->

<script>
    $(".delete_controlpanel_item").on("click",function(){
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        // cp_controlpanel_cart_id = $(".cp-id").val();
        var cp_controlpanel_cart_id = $(this).closest('tr').find('.cp-id').val();

        $.ajax({
            type:"get",
            url : "{{route('deleteCPFromEditQuotation')}}",
            data : {
                        quotation_number:quotation_number,
                        cp_controlpanel_cart_id:cp_controlpanel_cart_id
                    },
            success:function( message ){
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#control_panel_data-"+cp_controlpanel_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });

    $(".delete_booster_item").on("click",function(){
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        // cp_booster_cart_id = $(".booster-cart-id").val();
        var cp_booster_cart_id = $(this).closest('tr').find('.booster-cart-id').val();
        $.ajax({
            type:"get",
            url : "{{route('deleteBoosterFromEditQuotation')}}",
            data : {
                        quotation_number:quotation_number,
                        cp_booster_cart_id:cp_booster_cart_id
                    },
            success:function( message ){
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#booster_cart_data-"+cp_booster_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });

    $(".delete_atmos_item").on("click",function(){
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        // cp_atmos_cart_id = $(".atmos-cart-id").val();
        var cp_atmos_cart_id = $(this).closest('tr').find('.atmos-cart-id').val();

        $.ajax({
            type:"get",
            url : "{{route('deleteAtmosItemFromEditQuotation')}}",
            data : {
                        quotation_number:quotation_number,
                        cp_atmos_cart_id:cp_atmos_cart_id
                    },
            success:function( message ){
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#atmos_cart_item-"+cp_atmos_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });

    $(".delete_scp_item").on("click",function(){
        //alert("test");
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        // cp_scp_cart_id = $(".scp-cart-id").val();
        var cp_scp_cart_id = $(this).closest('tr').find('.scp-cart-id').val();
        $.ajax({
            type:"get",
            url : "{{route('deleteSCPItemFromEditQuotation')}}",
            data : {
                        quotation_number:quotation_number,
                        cp_scp_cart_id:cp_scp_cart_id
                    },
            success:function( message ){
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#scp_cart_item-"+cp_scp_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });

    // A Code: 25-02-2026 Start
    $(".delete_scpv_item").on("click",function(){
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        var cp_scpv_cart_id = $(this).closest('tr').find('.scpv-cart-id').val();
        $.ajax({
            type:"get",
            url : "{{route('deleteSCPVItemFromEditQuotation')}}",
            data : {
                quotation_number:quotation_number,
                cp_scpv_cart_id:cp_scpv_cart_id
            },
            success:function( message ){
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#scpv_cart_item-"+cp_scpv_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });
    // A Code: 25-02-2026 End

    // A Code: 26-05-2026 Start
    $(".delete_ff_item").on("click",function()
    {        
        var quotation_number = `<?php echo $quotation->quotation_number; ?>`;
        var cp_ff_cart_id = $(this).closest('tr').find('.ff-cart-id').val();
        $.ajax({
            type:"get",
            url : "{{route('deleteFFItemFromEditQuotation')}}",
            data : {
                quotation_number:quotation_number,
                cp_ff_cart_id:cp_ff_cart_id
            },
            success:function( message ){
                console.log(message);
                if(message.status == "success")
                {
                    toastr.success( message.message );
                    $("#ff_cart_item-"+cp_ff_cart_id).remove();
                    UpdatedTotalPriceQuotation();
                }
                else{
                    toastr.success( message.message );
                }
            },
            error:function(){
                alert("error");
            }
        });
    });
    // A Code: 26-05-2026 End  
</script>
@endsection
