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
                                ->where('brand',$atmos->brand)
                                ->where('power',$atmos->power)
                                ->where('no_of_pole',$atmos->no_of_pole)
                                ->where('frequency',$atmos->frequency)
                                ->where('voltage',$atmos->voltage)
                                ->get();

if($atmos->application == 2){     
    $atmos_master_motor_prices = $atmos_master_motor_prices[0]->price + $atmos_master_motor_prices[0]->insulate_bearing;
    }else{
    $atmos_master_motor_prices = $atmos_master_motor_prices[0]->price;
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
                                    <!--<th width="8%">Adder Code</th>-->
                                    {{--@if (auth()->user()->isAdmin())--}}
                                    {{--<th width="10%">Unit Price</th>--}}
                                    {{--@endif--}}
                                    @if (auth()->user()->isAdmin())
                                    <th>Unit Price</th>
                                    @endif
                                    <th width="20%">Qty</th>
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $j = 1
                                @endphp
                                @if($items->isNotEmpty())
                                @foreach($items as $key=> $item)
                                @if($item->item_description != null)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$item->item_description}}</td>
                                    <td>{{$item->wilo_artilce_no}}</td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin())
                                    <td>{{$item->unit_price}}</td>
                                    @endif
                                    <td>
                                        {{$item->qty}}
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                    <td>{{$item->unit_price*$item->qty }}</td>
                                    @endif
                                </tr>
                                @php
                                $j++
                                @endphp
                                @endif
                                @endforeach
                                @endif
                                @if(count($adderData) > 0)
                                @foreach($adderData as $key=> $item)
                                <tr>
                                    <td>{{$j}}</td>
                                    <td>{{$item['name']}}</td>
                                    <td></td>
                                    <td>{{$item['id']}}</td>
                                    @if(auth()->user()->isAdmin())
                                    <td>{{$item['price']}}</td>@endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin())
                                      <td>{{$item['price']}}</td>
                                    @endif
                                </tr>
                                @php
                                $j++
                                @endphp
                                @endforeach
                                @endif
                                <tr>
                                    <td>{{$j}}</td>
                                    <td>
                                        {{$atmos->power}}KW {{$atmos->no_of_pole}}P {{$atmos->effieciency}} {{$atmos->voltage}}V {{$atmos->frequency}}Hz {{$atmos->brand}} {{$atmos->application == 1 ? "constant" : "Variable"}} Speed  
                                    </td>
    								@if(!auth()->user()->isAdmin())
    								<td>
    								</td>
    								@endif
                                    @if (auth()->user()->isAdmin())
                                    <td></td>
                                    @endif 
                                    <td></td>
    								@if (auth()->user()->isAdmin())
                                    <td>{{$atmos_master_motor_prices ?? 0}}</td>
                                    @endif  
    												
                                    <td>1</td>
                                    @if (auth()->user()->isAdmin())
                                    <td>
                                        {{$atmos_master_motor_prices ?? 0}}
                                    </td>
                                    @endif  
                                </tr>
                                <tr>
                                    <td>{{$j+1}}</td>
                                    <td>
                                    {{$atmos->pump_name}}
                                    </td>
                                    <td>
                                    {{$article_number->bare_pump_article_no ?? ""}}
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin())
                                    <td>{{$article_number->tpl_fob_price ?? ""}}</td>
                                    @endif
    								<td>1</td>
                                    @if(auth()->user()->isAdmin())
                                    <td>
                                    {{$article_number->tpl_fob_price ?? ""}}
                                    </td>
                                    @endif
                                </tr>
                                @if($atmos->is_accesories_manual == "1")
                                <tr>
                                    <td>{{$j+2}}</td>
                                    <td>
                                        Accessories-Manual
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    @if (auth()->user()->isAdmin())
                                    <td>{{$atmos->accesories_price}}</td>
                                    @endif
                                    <td>1</td>
                                    @if(auth()->user()->isAdmin())
                                    <td>
                                    {{$atmos->accesories_price}}
                                    </td>
                                    @endif
                                </tr>
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