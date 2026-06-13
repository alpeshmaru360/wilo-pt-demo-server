@extends('frontend.layout.app')
@section('content')
<style>
    /* A Code: 17-02-2026 Start */
    .qty_input .qty .scpv-minus {
        cursor: pointer;display: inline-block;vertical-align: top;color: #169e88;width: 30px;height: 30px;
        font: 30px / 0.9 "Noto Sans", sans-serif, sans-serif;text-align: center;background-clip: padding-box;
    }
    .qty_input .qty .scpv-plus {
        cursor: pointer;display: inline-block;vertical-align: top;color: #169e88;width: 30px;height: 30px;
        font: 30px / 0.9 "Noto Sans", sans-serif, sans-serif;text-align: center;
    }
    /* A Code: 17-02-2026 End */
</style>
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="cartMidSection">
                <h2>Cart</h2>
                <div class="cartSection">
                    <div class="tableResponsive">
                        <!-- A Code: 24-02-2026 Start -->
                        @if($controlPanelCartData->isNotEmpty() || $atmosCartData->isNotEmpty() ||  $scpCartData->isNotEmpty() || $scpvCartData->isNotEmpty() || $boosterCartData->isNotEmpty() || $fireFightingCartData->isNotEmpty())
                        <!-- A Code: 24-02-2026 End -->
                            <table>
                                <thead>
                                    <tr>
                                        <!--<th width="5%"></th>-->
                                        <th width="15%">Item Description</th>
                                        <th width="15%">Article Number</th>
                                        <th width="10%">Unit Price</th>
                                        <th width="15%">Qty</th>
                                        <th width="15%">Total Price</th>
                                        <th width="05%">Selection</th>
                                        <th width="05%">Remove</th>
                                    </tr>
                                </thead>
                                <?php $totalPrice = 0.00; ?>
                                <tbody>
                                    @if($controlPanelCartData->isNotEmpty())
                                    @foreach($controlPanelCartData as $key=> $val)
                                        <tr>
                                            <td style="display: none;"><input type="checkbox" checked name="cart_id" value="{{$val['id']}}"></td>
                                            <td>
                                                <a class="detail-modal" href="javascript:void(0)">
                                                    Control Panel {{$val->noofpumps['value'] }} x {{ $val->powers['value'] }}KW {{$val->starter_code}}/AE
                                                </a>
                                            </td>
                                            <td>
                                                <a class="detail-modal" href="javascript:void(0)">
                                                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                                                </a>
                                            </td>
                                            <td>{{  App\Helpers\CurrencyHelper::withCurrency($val['price'])}}</td>
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
                                            <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                            <input type="hidden"  id="cp-{{$val['id']}}" >
                                            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty)}}</td>
                                            <td>
                                                <a href="{{ URL::to('controlpanel/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                                <!--<button><img src="{{asset('fassets/images/downloadIcon.png')}}" /></button>-->
                                            </td>
                                            <td><button class="delete-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
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
                                            <td style="display: none;"><input type="checkbox" checked name="atmos_checked_id" value="{{$val['id']}}"></td>
                                            <td>
                                                <a class="detail-modal-atmos" href="javascript:void(0)">
                                                    {{$val->pump_name}} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                                                </a>
                                            </td>
                                            <td>
                                                <a class="detail-modal-scp" href="javascript:void(0)">
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

                                                <a class="detail-modal-atmos" href="javascript:void(0)">
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
                                                <a href="{{ URL::to('atmos/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                            </td>
                                            <td><button class="delete-atmos-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
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
                                            <td style="display: none;"><input type="checkbox" checked name="scp_checked_id" value="{{$val['id']}}"></td>
                                            <td>
                                                <a class="detail-modal-scp" href="javascript:void(0)">
                                                    {{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                                                </a>
                                            </td>
                                            <td>
                                                <a class="detail-modal-scp" href="javascript:void(0)">
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
                                            <td>{!! App\Helpers\CurrencyHelper::withCurrency($val['price']) !!}</td>
                                            <td>
                                                <div class="qty_input">
                                                    <div class="qty">
                                                        <span class="scp-minus qtyBtn">-</span>
                                                        <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                        <span class="scp-plus qtyBtn">+</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <input type="hidden" class="scp-cart-id" value="{{$val['id']}}">
                                            <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                            <input type="hidden"  id="scp-{{$val['id']}}" >
                                            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                            <td>
                                                <a href="{{ URL::to('scp/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                            </td>
                                            <td><button class="delete-scp-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                    @endif

                                    <!-- Add Here SCPV Data -->
                                    <!-- A Code: 17-02-2026 Start -->
                                    @if($scpvCartData->isNotEmpty())
                                    @foreach($scpvCartData as $key=> $val)
                                        @php
                                        $short_code = DB::table('scpv_materials')->where('id',$val->material_id)->pluck("short_code")->first();
                                        @endphp
                                        <tr>
                                            <td style="display: none;"><input type="checkbox" checked name="scpv_checked_id" value="{{$val['id']}}"></td>
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
                                            <td>{!! App\Helpers\CurrencyHelper::withCurrency($val['price']) !!}</td>
                                            <td>
                                                <div class="qty_input">
                                                    <div class="qty">
                                                        <span class="scpv-minus qtyBtn">-</span>
                                                        <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                        <span class="scpv-plus qtyBtn">+</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <input type="hidden" class="scpv-cart-id" value="{{$val['id']}}">
                                            <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                            <input type="hidden"  id="scpv-{{$val['id']}}" >
                                            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                                            <td>                                  
                                                <a href="{{ URL::to('scpv/cart-item/'.$val['id'] )}}" target="_blank">                                          
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                            </td>
                                            <td><button class="delete-scpv-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                    @endif
                                    <!-- A Code: 17-02-2026 End -->

                                    {{--booster cart starts--}}
                                    @if($boosterCartData->isNotEmpty())
                                    @foreach($boosterCartData as $key=> $val)
                                        <tr>
                                            <td style="display: none;"><input type="checkbox" checked name="booster_checked_id" value="{{$val['id']}}"></td>
                                            <td>
                                                <a class="detail-modal-booster" href="javascript:void(0)">
                                                    @php
                                                        $const =null;
                                                        if(str_starts_with($val->boosterCpData[0]->table_name, 'basic_')  == true)
                                                            $const = "COE";
                                                        else{
                                                            $const = 'CO';
                                                            $array_check = array(3,4,7);
                                                            if(in_array($val->boosterCpData[0]->stater_type_id,$array_check) ){
                                                                $const = 'COR';
                                                            }
                                                        }
                                                    @endphp
                                                    {{$const}} {{$val->boosterCpData[0]->noofpumps['value']}} {{$val->model_no}}/{{$val->boosterCpData[0]->starter_code}}/AE
                                                </a>
                                                <br>
                                                    @if(!empty($val['mechanical_article_number']))
                                                    [{{$val['mechanical_article_number']}} - Mechnical Assembly]
                                                    @endif
                                                    <br>
                                                    @if(!empty($val['electrical_article_number']))
                                                    [{{$val['electrical_article_number']}} - Control panel]
                                                @endif
                                            </td>

                                            <td>
                                                <a class="detail-modal-booster" href="javascript:void(0)">
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
                                            <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price'])}}
                                                <span style = "">
                                                    <br>[Electrical : {{App\Helpers\CurrencyHelper::withCurrency($val['electrical_items_price'])}} ]
                                                    <br>[Mechanical : {{App\Helpers\CurrencyHelper::withCurrency($val['mechanical_items_price'])}} ]
                                            </td>
                                            <td>
                                                <div class="qty_input">
                                                    <div class="qty">
                                                        <span class="booster-minus qtyBtn">-</span>
                                                        <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                        <span class="booster-plus  qtyBtn">+</span>
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
                                            </td>
                                            <td><button class="delete-booster-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                    @endif
                                    {{-- booster cart ends--}}
                                    
                                    {{-- Fire Fighting Cart Data Start --}}
                                    @if($fireFightingCartData->isNotEmpty())
                                    @foreach($fireFightingCartData as $key=> $val)
                                        <tr>
                                            <td style="display: none;"><input type="checkbox" checked name="firefighting_checked_id" value="{{ $val->id }}"></td>
                                            <td>
                                                <a class="detail-modal-firefighting" href="javascript:void(0)" data-id="{{ $val->id }}">
                                                    @php
                                                        $firefightingmodal = '';
                                                        $firefightingmodal .= ucwords(str_replace('-pump', '', $val->category));
                                                        $firefightingmodal .= ' '.$val->pump_models;
                                                    @endphp
                                                    {{ $firefightingmodal }}/AE
                                                </a>
                                            </td>
                                            <td>
                                                <a class="" href="javascript:void(0)">
                                                    {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ App\Helpers\CurrencyHelper::withCurrency($val->price)}}
                                            </td>
                                            <td>
                                                <div class="qty_input">
                                                    <div class="qty">
                                                        <span class="firefighting-qtyBtn qtyBtn" data-pump_type="{{ $val->category }}" data-id="{{ $val->id }}" data-qty="minus">-</span>
                                                        <input type="number" class="icount quantity"  id="quantity" name="quantity" value="{{$val->qty}}"  min="1" max="" />
                                                        <span class="firefighting-qtyBtn qtyBtn" data-pump_type="{{ $val->category }}" data-id="{{ $val->id }}" data-qty="plus">+</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <input type="hidden" class="firefighting-cart-id" value="{{$val['id']}}">
                                            <input type="hidden" class="total-price-input" name="" value="{{$val->total_price}}"  min="1" max="" />
                                            <input type="hidden"  id="firefighting-{{$val['id']}}" >
                                            <td class="total-price">
                                                {{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}
                                            </td>
                                            <td>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'] )}}" target="_blank">
                                                    <img src="{{asset('fassets/images/viewIcon.png')}}" />
                                                </a>
                                                <a href="{{ URL::to('firefighting-set/cart-item/'.$val['id'].'/excel' )}}" target="_blank">
                                                    Excel
                                                </a>
                                            </td>
                                            <td>
                                                <button class="delete-firefighting-cart" data-pump_type="{{ $val->category }}" value="{{ $val->id }}"><img src="{{asset('fassets/images/delIcon.png')}}" /></button>
                                            </td>
                                        </tr>
                                        <?php $totalPrice += round($val->price * $val->qty); ?>
                                    @endforeach
                                    @endif
                                    {{-- Fire Fighting Cart Data End --}}

                                </tbody>
                            </table>
                            <div class="text-right txt-ttl" id=""><h4>Total Price: </h4><span class="" id="total-price-updated"> {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</span></div>
                        @else
                            <h3 class="text-center">Your cart is empty!</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex cusPagination">
            <div class="">
                <a  onclick="window.history.back()" href="javascript:void(0)"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
                <!-- Unit Price: <button class="clcBtn">Calculate</button> <span>750€</span> -->
            </div>
            <div class="right">
                <?php $cartId = Request::segment(3); ?>
                <ul>
                    <!--<li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>-->
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    
                    <!-- A Code: 24-02-2026 Start -->
                    @if($controlPanelCartData->isNotEmpty() || $atmosCartData->isNotEmpty() ||  $scpCartData->isNotEmpty() ||  $scpvCartData->isNotEmpty() || $boosterCartData->isNotEmpty() || $fireFightingCartData->isNotEmpty())
                    <li><a href="javascript:void(0)" id="generate-quotation" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>
                    @endif
                    <!-- A Code: 24-02-2026 End -->

                    <!--<li><a href="{{URL::to('controlpanel/customer-information?cp_id=' . $cartId) }}" id="generate-quotation" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>
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

@endsection

@section('script')
{{-- Fire Fighting Script --}}
<script>
    $(document).on('click', '.detail-modal-firefighting', function () {
        var firefighting_id = $(this).data('id');
        $.ajax({
            type: "get",
            url: "{{ url('fire-fighting') }}" + '/' + firefighting_id + '/edit',
            success: function (response) {
                if (response.data.html) {
                    $("#detail-control-panel-modal-body").html('');
                    $("#detail-control-panel-modal-body").html(response.data.html);
                    $("#detail-control-panel-modal").show();
                }
            },
            error: function () {}
        });
    });

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
                UpdatedTotalPriceCart();
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
                    UpdatedTotalPriceCart();
                },
                error: function (data) {
                }
            });
        }
    });
</script>

<script>
    $(".plus").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function (response) {
                    $("#cp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
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
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function (response) {
                    $("#cp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
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
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('atmos/ajax-qty-update')}}",
                data: {qty: qty, atmos_cart_id: atmos_cart_id},
                success: function (response) {
                    $("#at-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
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
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('atmos/ajax-qty-update')}}",
                data: {qty: qty, atmos_cart_id: atmos_cart_id},
                success: function (response) {
                    $("#at-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
                },
                error: function () {
                }
            });
        }
    });

    $(".delete-cart").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('controlpanel/remove-cart')}}" + "/" + cp_id,
            success: function () {
                UpdatedTotalPriceCart();
            },
            error: function () {
            }
        });
    });

    $(".delete-atmos-cart").on("click", function () {
        var atmos_cart_id = $(this).closest('tr').find('.atmos-cart-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('atmos/remove-cart')}}" + "/" + atmos_cart_id,
            success: function () {
                UpdatedTotalPriceCart();
            },
            error: function () {
            }
        });
    });

    //Scp
    $(".scp-plus").on("click", function () {
        var scp_cart_id = $(this).closest('tr').find('.scp-cart-id').val();
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
                url: "{{url('scp/ajax-qty-update')}}",
                data: {qty: qty, scp_cart_id: scp_cart_id},
                success: function (response) {
                    $("#scp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();

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
        //        if (totalPrice > 0) {
        //            var tpPriceHtml = totalPrice * qty;
        //            $(this).closest('tr').find('.total-price').html(
        //                    withCurrency(tpPriceHtml)
        //                    );
        //        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('scp/ajax-qty-update')}}",
                data: {qty: qty, scp_cart_id: scp_cart_id},
                success: function (response) {
                    $("#scp-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
                },
                error: function () {

                }

            });
        }

    });

    $(".delete-scp-cart").on("click", function () {
        var scp_cart_id = $(this).closest('tr').find('.scp-cart-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('scp/remove-cart')}}" + "/" + scp_cart_id,
            success: function () {
                UpdatedTotalPriceCart();
            },
            error: function () {

            }
        });
    });
    // A Code: 18-02-2026 Start
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
                    UpdatedTotalPriceCart();

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
                    UpdatedTotalPriceCart();
                },
                error: function () {

                }

            });
        }

    });

    $(".delete-scpv-cart").on("click", function () {
        var scpv_cart_id = $(this).closest('tr').find('.scpv-cart-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('scpv/remove-cart')}}" + "/" + scpv_cart_id,
            success: function () {
                UpdatedTotalPriceCart();
            },
            error: function () {

            }
        });
    });
    // A Code: 18-02-2026 End
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

                    UpdatedTotalPriceCart();
                },
                error: function () {

                }

            });
        }
    });

    $(".booster-minus").on("click", function () {
        var booster_cart_id = $(this).closest('tr').find('.booster-cart-id').val();
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
                url: "{{url('booster-set/ajax-qty-update')}}",
                data: {qty: qty, booster_cart_id: booster_cart_id},
                success: function (response) {
                    $("#booster-" + response.data.id).closest('tr').find('.total-price').html(response.data.total_price_update)
                    UpdatedTotalPriceCart();
                },
                error: function () {

                }

            });
        }

    });

    $(".delete-booster-cart").on("click", function () {
        var booster_cart_id = $(this).closest('tr').find('.booster-cart-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('booster-set/remove-cart')}}" + "/" + booster_cart_id,
                //                data: {qty: qty, cp_id: cp_id},
            success: function () {
                UpdatedTotalPriceCart();
            },
            error: function () {

            }
        });
    });

    //    Generate Quotation
    $("#generate-quotation").on("click", function () {
        var cartIds = [];
        var atmosIds = [];
        var scpIds = [];
        var scpvIds = []; // A Code: 24-02-2026
        var boosterIds = [];
		var firefightingIds = [];

        var removeCartIds = [];
        $('input[name="cart_id"]:checked').each(function () {
            cartIds.push($(this).val());

        });
        $('input[name="atmos_checked_id"]:checked').each(function () {
            atmosIds.push($(this).val());

        });
        $('input[name="scp_checked_id"]:checked').each(function () {
            scpIds.push($(this).val());
        });
        
        // A Code: 24-02-2026 Start
        $('input[name="scpv_checked_id"]:checked').each(function () {
            scpvIds.push($(this).val());
        });
        // A Code: 24-02-2026 End

        $('input[name="booster_checked_id"]:checked').each(function () {
            boosterIds.push($(this).val());

        });
        $('input[name="firefighting_checked_id"]:checked').each(function () {
            firefightingIds.push($(this).val());
        });
        $('input[name="cart_id"]:not(:checked)').each(function () {
            removeCartIds.push($(this).val());

        });
        // A Code: 24-02-2026 Start
        if (cartIds.length >= 1 || atmosIds.length >= 1 || scpIds.length >= 1 || scpvIds.length >= 1 || boosterIds.length >= 1 || firefightingIds.length >= 1) {
            var url = "{{url('controlpanel/customer-information?cp_ids=')}}" + cartIds + "&atmosIds=" + atmosIds + "&scpIds=" + scpIds + "&scpvIds=" + scpvIds + "&boosterIds=" + boosterIds + "&firefightingIds=" + firefightingIds;
            window.location = url;
        }
        // A Code: 24-02-2026 End
    });

    $(".detail-modal").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        // alert(cp_id);
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

    $(document).on("click", '.close-detail-control-panel-modal', function (event) {
        $("#detail-control-panel-modal").hide();
    });

    //detail-modal-scp

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

    // A Code: 18-02-2026 Start
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
    // A Code: 18-02-2026 End

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

    $(document).on("click", '.close-detail-control-panel-modal', function (event) {
        $("#detail-control-panel-modal").hide();
    });

    $(".detail-modal-atmos").on("click", function () {

        var atmos_id = $(this).closest('tr').find('.atmos-cart-id').val();
        $.ajax({
            type: "get",
            url: "{{url('atmos/ajax-detail-modal-atmos')}}",
            data: {atmos_id: atmos_id},
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

    function withCurrency(price) {
        let dollarUSLocale = Intl.NumberFormat('en-US');
        return dollarUSLocale.format(price) + '$';
    }

    function UpdatedTotalPriceCart() {
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/updatedTotalPrice')}}",
            data: {},
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
</script>
@endsection
