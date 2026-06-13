@php
    if(count($items) >= 0){
        $scpvData = App\ScpvCart::where('id', $cartId)->first();
    }elseif(!Schema::hasColumn('scpv_carts', 'scpv_cart_id') && $is_manual == "1"){
        $scpvData = App\ScpvCart::where('id', $cartId)->first(); // A Code: 16-03-2026 
    }else{
        $scpvData = App\ScpvCart::where('id', $items[0]->scpv_cart_id)->get()[0];
    }    

    $article_number = DB::table('scpv_pump_types')->where('id',$scpvData->pump_id)->pluck('bare_shaft_article_number')->first();
    
    $motor_price =  DB::table('scpv_master_motor_prices')
                    ->where('brand',$scpvData->brand)
                    ->where('power',$scpvData->power)
                    ->where('no_of_pole',$scpvData->no_of_pole)
                    ->where('frequency',$scpvData->frequency)
                    ->where('voltage',$scpvData->voltage)
                    ->get();

    $scpv_master_motor_prices_article_number = $motor_price[0]->wilo_article_number; // A Code: 24-03-2026 
    $scpv_master_motor_prices_item_desc = $motor_price[0]->item_desc; // A Code: 24-03-2026 

    if($scpvData->application == 2){     
        @$motor_price = $motor_price[0]->price + $motor_price[0]->insulate_bearing;
    }else{
        @$motor_price = $motor_price[0]->price;
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
                                    <th width="8%">Adder Code</th>
                                    @if (auth()->user()->isAdmin())
                                        <th width="10%">Unit Price</th>
                                    @endif
                                    <th width="29%">Qty</th>
                                    @if (auth()->user()->isAdmin())
                                        <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            
                            <!-- A Code: 24-02-2026 Start -->
                            <tbody>
                                @php
                                $j = 1
                                @endphp
                                @if($items->isNotEmpty())
                                    @foreach($items as $key=> $item)
                                        @if($item->item_description != null)
                                            <tr>
                                                <!-- S.No -->
                                                <td>{{$key + 1}}</td> 

                                                <!-- Description -->
                                                <td>{{$item->item_description}}</td>

                                                <!-- Article No. -->
                                                <td>{{$item->wilo_artilce_no}}</td>

                                                <!-- Adder Code -->
                                                <td></td>
                                                    <!--<td>
                                                        <?php
                                                        $txtArr = explode("x", $item->adder_code);
                                                        $i = array_search("x", explode("x", $item->adder_code));
                                                        unset($txtArr[$i + 2]);
                                                        unset($txtArr[$i + 1]);
                                                        echo implode(" ", $txtArr);
                                                        ?>
                                                    </td>-->

                                                <!-- Unit Price -->
                                                @if (auth()->user()->isAdmin())                                                    
                                                    <td>{{ round($item->unit_price,2) }}</td>                                  
                                                @endif

                                                <!-- Qty -->
                                                <td>{{$item->qty}}</td>
                                                
                                                <!-- Total Price -->
                                                @if (auth()->user()->isAdmin())
                                                    <td>{{ round($item->unit_price*$item->qty,2) }}</td>
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
                                            <!-- S.No -->
                                            <td>{{$j}}</td>

                                            <!-- Description -->
                                            <td>{{$item['name']}}</td>

                                            <!-- Article No. -->
                                            <td>{{$scpv_master_motor_prices_article_number}} / {{$item['id']}} </td>

                                            <!-- Adder Code -->
                                            <td>{{$item['id']}}</td>

                                            <!-- Unit Price -->
                                            @if(auth()->user()->isAdmin())
                                                <td>{{ round($item['price'],2) }}</td>
                                            @endif

                                            <!-- Qty -->
                                            <td>1</td>

                                            <!-- Total Price -->
                                            @if(auth()->user()->isAdmin())
                                                <td>{{ round($item['price'],2) }}</td>
                                            @endif
                                        </tr>
                                        @php
                                        $j++
                                        @endphp
                                    @endforeach
                                @endif

                                <tr>
                                    <!-- S.No -->
                                    <td>{{$j}}</td>

                                    <!-- Description -->
                                    <td>
                                        <!-- {{ $scpvData->power}}KW {{$scpvData->no_of_pole}}P {{$scpvData->effieciency}} {{$scpvData->voltage}}V {{$scpvData->frequency}}Hz {{$scpvData->brand}} {{$scpvData->application == 1 
                                        ? "constant" : "Variable"}} Speed -->

                                        {{$scpv_master_motor_prices_item_desc}}

                                    </td>

                                    <!-- Article No. -->
                                    <!-- <td>{{--$motor_price--}}</td> -->
                                    <td>{{$scpv_master_motor_prices_article_number }}</td>    

                                    <!-- Adder Code -->
                                    <td></td>

                                    <!-- Unit Price -->
                                    @if (auth()->user()->isAdmin())
                                        <td>{{ round($motor_price,2) }}</td>
                                    @else
                                        <td></td>
                                    @endif

                                    <!-- Qty -->
                                    <td>1</td>

                                    <!-- Total Price -->
                                    @if (auth()->user()->isAdmin())
                                        <td>{{ round($motor_price,2) }}</td>
                                    @endif   
                                </tr>

                                <tr>
                                    <!-- S.No -->
                                    <td>{{$j+1}}</td>

                                    <!-- Description -->
                                    <td>{{$scpvData->pump_name}}</td>

                                    <!-- Article No. -->
                                    <td>{{$article_number}}</td>

                                    <!-- Adder Code -->
                                    <td></td>

                                    <!-- Unit Price -->
                                    @if (auth()->user()->isAdmin()) 
                                        <td>{{ round($scpvData->bare_pump_price,2) }}</td>
                                    @endif

                                    <!-- Qty -->
                                    <td>1</td>

                                    <!-- Total Price -->
                                    @if (auth()->user()->isAdmin())
                                        <td>{{ round($scpvData->bare_pump_price,2) }}</td>
                                    @endif
                                </tr>

                                @if($scpvData->is_accesories_manual == "1")
                                    <tr>
                                        <!-- S.No -->
                                        <td>{{$j+2}}</td>

                                        <!-- Description -->
                                        <td>Accessories-Manual</td>

                                        <!-- Article No. -->
                                        <td></td>

                                        <!-- Adder Code -->
                                        <td></td>

                                        <!-- Unit Price -->
                                        @if (auth()->user()->isAdmin())
                                            <td>{{ round($scpvData->accesories_price,2) }}</td>
                                        @endif
                                        
                                        <!-- Qty -->
                                        <td>1</td>

                                        <!-- Total Price -->
                                        @if(auth()->user()->isAdmin())
                                            <td>{{ round($scpvData->accesories_price,2) }}</td>
                                        @endif
                                    </tr>
                                @endif    
                                
                                <!-- A Code: 16-03-2026 Start -->                                
                                @if(auth()->user()->isAdmin())
                                    <!-- Assembly Charge -->
                                    @if($scpvData->assembly_charge)
                                    <tr>
                                        <td>{{$j+2}}</td>
                                        <td>Assembly Charge</td>
                                        <td></td>
                                        <td></td>
                                        @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->assembly_charge,2) }}</td>
                                        @endif
                                        <td>1</td>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->assembly_charge,2) }}</td>
                                        @endif
                                    </tr>
                                    @endif 
    
                                    <!-- Painting Charge -->
                                    @if($scpvData->painting_charge)
                                    <tr>
                                        <td>{{$j+3}}</td>
                                        <td>Painting Charge</td>
                                        <td></td>
                                        <td></td>
                                        @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->painting_charge,2) }}</td>
                                        @endif
                                        <td>1</td>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->painting_charge,2) }}</td>
                                        @endif
                                    </tr>
                                    @endif

                                    <!-- Packing Charge -->
                                    @if($scpvData->packing_charge)
                                    <tr>
                                        <td>{{$j+4}}</td>
                                        <td>Packing Charge</td>
                                        <td></td>
                                        <td></td>
                                        @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->packing_charge,2) }}</td>
                                        @endif
                                        <td>1</td>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->packing_charge,2) }}</td>
                                        @endif
                                    </tr>
                                    @endif

                                    <!-- Shipping Charge -->
                                    @if($scpvData->shipping_cost_price)
                                    <tr>
                                        <td>{{$j+5}}</td>
                                        <td>Shipping Charge</td>
                                        <td></td>
                                        <td></td>
                                        @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->shipping_cost_price,2) }}</td>
                                        @endif
                                        <td>1</td>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                            <td>{{ round($scpvData->shipping_cost_price,2) }}</td>
                                        @endif
                                    </tr>
                                    @endif       
                                @endif
                                <!-- A Code: 16-03-2026 End -->
                                
                            </tbody>
                            <!-- A Code: 24-02-2026 End -->

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
                    <!--<li><a href="{{URL::to('controlpanel/customer-information?cp_id=' . $cartId) }}" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->
@endsection