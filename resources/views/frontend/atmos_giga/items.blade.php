@php
if(count($items) <= 0){
    $atmos = App\atmosCart::where('id', $cartId)->first();
}
elseif(!Schema::hasColumn('atmos_carts', 'atmos_cart_id') && $is_manual == "1"){
    $atmos = App\atmosCart::where('id', $cartId)->first();
}
else{
    $atmos = App\atmosCart::where('id', $items[0]->atmos_cart_id)->get()[0];
}

$article_number = DB::table('atmos_pumps')->where('pump_id',$atmos->pump_id)
                ->where('material_id',$atmos->material_id)->first();

$atmos_master_motor_prices = DB::table('atmos_master_motor_prices')
                                ->where('brand', 'like', '%' . $atmos->brand . '%')
                                ->where('power',$atmos->power)
                                ->where('no_of_pole',$atmos->no_of_pole)
                                ->where('frequency',$atmos->frequency)
                                ->where('voltage',$atmos->voltage)
                                ->get();

$atmos_master_motor_prices_article_number = $atmos_master_motor_prices[0]->wilo_article_number; 
$atmos_master_motor_prices_item_desc = $atmos_master_motor_prices[0]->item_desc; 

if($atmos->is_bareshaft_selection != 1){     
    if($atmos->application == 2){     
        $atmos_master_motor_prices = $atmos_master_motor_prices[0]->price + $atmos_master_motor_prices[0]->insulate_bearing + $atmos_master_motor_prices[0]->shipping_cost;
        }else{
        $atmos_master_motor_prices = $atmos_master_motor_prices[0]->price + $atmos_master_motor_prices[0]->shipping_cost;
    }
}			
@endphp
@extends('frontend.layout.app')
@section('content')
<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="cartMidSection">
                <h2>Bill Of Material</h2>
                <div class="cartSection">
                    <div class="tableResponsive">
                        <table>
                            <thead>
                                <tr>
                                    <th width="8%">S.No</th>
                                    <th width="25%">Description</th>
                                    <th width="25%">Article No.</th>
                                    <th width="25%">Adder code</th>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <th>Unit Price</th>
                                    @endif
                                    <th width="20%">Qty</th>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $j = 1
                                @endphp

                                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                @if($items->isNotEmpty())
                                    @foreach($items as $key=> $item)
                                        @if($item->item_description != null)
                                            <tr>
                                                <td>{{$key + 1}}  </td>
                                                <td>{{$item->item_description}}</td>
                                                <td>{{$item->wilo_artilce_no}}</td>
                                                <td>this are accessories</td>
                                                @if(auth()->user()->isAdmin())
                                                <td>{{$item->unit_price}}</td>
                                                @elseif(auth()->user()->isSupervisor())
                                                <td>{{$item->unit_price * $otpMargin}}</td>
                                                @endif
                                                <td>
                                                    {{$item->qty}}
                                                </td>
                                                @if(auth()->user()->isAdmin())
                                                <td>{{$item->unit_price*$item->qty }}</td>
                                                @elseif(auth()->user()->isSupervisor())
                                                <td>{{(($item->unit_price * $otpMargin)*$item->qty)}}</td>
                                                @endif
                                            </tr>
                                        @php
                                        $j++
                                        @endphp
                                        @endif
                                    @endforeach
                                @endif
                                @endif

                                @if(auth()->user()->isAdmin())
                                    @if($atmosBOMitems->isNotEmpty())
                                        @foreach($atmosBOMitems as $key=> $item)
                                            @if($item->item_description != null)
                                                <tr>
                                                    <td>{{$j}}</td>
                                                    <td>{{$item->item_description}}</td>
                                                    <td>{{$item->wilo_artilce_no}}</td>
                                                    <td></td>
                                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                                    <td>{{$item->unit_price}}</td>
                                                    @endif
                                                    <td>
                                                        {{$item->qty}}
                                                    </td>
                                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                                    <td>{{$item->unit_price*$item->qty }}</td>
                                                    @endif
                                                </tr>
                                            @php
                                            $j++
                                            @endphp
                                            @endif
                                        @endforeach
                                    @endif
                                @endif

                                <!-- Hrer -->
                                @if(!auth()->user()->isAdmin())
                                @if($atmosBOMitemsSupervisor)
                                    <tr>
                                        <td>{{$j}}</td>
                                        <td>{{$atmosBOMitemsSupervisor->item_description}}</td>
                                        <td>{{$atmosBOMitemsSupervisor->wilo_artilce_no}}</td>
                                        <td></td>
                                        @if(auth()->user()->isSupervisor())
                                        <td>{{$atmosBOMitemsSupervisor->unit_price}}</td>
                                        @endif
                                        <td>
                                            {{$atmosBOMitemsSupervisor->qty}}
                                        </td>
                                        @if(auth()->user()->isSupervisor())
                                        <td>{{$atmosBOMitemsSupervisor->unit_price*$atmosBOMitemsSupervisor->qty }}</td>
                                        @endif
                                    </tr>
                                    @php
                                    $j++
                                    @endphp
                                @endif
                                @endif

                                @if(count($adderData) > 0)
                                @foreach($adderData as $key=> $item)
                                <tr>
                                    <td>{{$j}}</td>
                                    <td>{{$item['name']}}</td>
                                    <td>{{$atmos_master_motor_prices_article_number}} / {{$item['id']}} </td>
                                    <td>{{$item['id']}}</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$item['price']}}</td>@endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                      <td>{{$item['price']}}</td>
                                    @endif
                                </tr>
                                @php
                                $j++
                                @endphp
                                @endforeach
                                @endif

                                @if($atmos->is_bareshaft_selection != "1")
                                    @if(auth()->user()->isAdmin())
                                    <tr>
                                        <td>{{$j}}</td>
                                        <td>
                                            {{--
                                            {{$atmos->power}}KW {{$atmos->no_of_pole}}P {{$atmos->effieciency}} {{$atmos->voltage}}V {{$atmos->frequency}}Hz {{$atmos->brand}} {{$atmos->application == 1 ? "constant" : "Variable"}} Speed 
                                            --}} {{$atmos_master_motor_prices_item_desc}}
                                        </td>
        								<td>{{$atmos_master_motor_prices_article_number}}</td>
                                        <td></td>
                                        <td>{{$atmos_master_motor_prices ?? 0}}</td>
        								<td>1</td>
                                        <td>{{$atmos_master_motor_prices ?? 0}}</td>
                                    </tr>
                                    @elseif(auth()->user()->isSupervisor())
                                    <tr>
                                        <td>{{$j}}</td>
                                        <td>
                                            {{$atmos->power}}KW {{$atmos->no_of_pole}}P {{$atmos->effieciency}} {{$atmos->voltage}}V {{$atmos->frequency}}Hz {{$atmos->brand}} {{$atmos->application == 1 ? "constant" : "Variable"}} Speed  
                                        </td>
                                        <td>
                                        </td>
                                        <td></td>
                                        
                                        <td>{{$atmos_master_motor_prices* $otpMargin ?? 0}}</td>
                                        <td>1</td>
                                        <td>{{$atmos_master_motor_prices* $otpMargin ?? 0}}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td>{{$j}}</td>
                                        <td>
                                            {{$atmos->power}}KW {{$atmos->no_of_pole}}P {{$atmos->effieciency}} {{$atmos->voltage}}V {{$atmos->frequency}}Hz {{$atmos->brand}} {{$atmos->application == 1 ? "constant" : "Variable"}} Speed  
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td>1</td>
                                    </tr>
                                    @endif  
                                @endif 

                                {{--<tr>
                                    <td>{{$j+1}}</td>
                                    <td>
                                    {{$atmos->pump_name}} 
                                    </td>
                                    <td>
                                    {{$article_number->bare_pump_article_no ?? ""}}
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$article_number->tpl_fob_price ?? ""}}</td>
                                    @endif
    								<td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$article_number->tpl_fob_price ?? ""}}
                                    </td>
                                    @endif
                                </tr>--}}

                                @if($atmos->is_accesories_manual == "1")
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                        Accessories-Manual
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$atmos->accesories_price}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$atmos->accesories_price}}
                                    </td>
                                    @endif
                                </tr>
                                @endif

                            @if($atmos->is_accesories_manual == "1")
                            <tr>
                                <td>{{$j+2}}</td>
                                <td>
                                    {{$atmos->pump_name}}
                                </td>
                                <td>
                                </td>
                                <td></td>
                                @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                <td>{{$atmos->bare_pump_price}}</td>
                                @endif
                                <td>1</td>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                <td>
                                {{$atmos->bare_pump_price}}
                                </td>
                                @endif
                            </tr>
                            @endif

                            @if(auth()->user()->isAdmin())

                                <!-- Assembly Charge -->
                                @if($atmosCart->assembly_charge)
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                       Assembly Charge
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$atmosCart->assembly_charge}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$atmos->assembly_charge}}
                                    </td>
                                    @endif
                                </tr>
                                @endif

                                <!-- Painting Charge -->
                                @if($atmosCart->painting_charge)
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                       Painting Charge
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$atmosCart->painting_charge}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$atmos->painting_charge}}
                                    </td>
                                    @endif
                                </tr>
                                @endif

                                <!-- Packing Charge -->
                                @if($atmosCart->packing_charge)
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                       Packing Charge
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$atmosCart->packing_charge}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$atmos->packing_charge}}
                                    </td>
                                    @endif
                                </tr>
                                @endif

                                <!-- Shipping Charge -->
                                @if($atmosCart->shipping_cost_price)
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                       Shipping Charge
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>{{$atmosCart->shipping_cost_price}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <td>
                                    {{$atmos->shipping_cost_price}}
                                    </td>
                                    @endif
                                </tr>
                                @endif
                            @endif

                            </tbody>
                        </table>
                    </div>
                </div>
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
                <!-- Unit Price: <button class="clcBtn">Calculate</button> <span>750€</span> -->
            </div>
            <div class="right">
                <?php $cartId = Request::segment(3); ?>
                <ul>
                    <!--<li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>-->
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>                     
                    <li><a href="{{URL::to('controlpanel/cart/' . $cartId) }}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->
@endsection