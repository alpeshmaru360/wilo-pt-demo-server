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
                                <th width="8%">Article No.</th>

                            @if(auth()->user()->isAdmin())
                                <th width="10%">Brand Code</th>
                                <th width="8%">Function Code</th>
                                <th width="8%">Range</th>
                                <th width="8%">Adder Code</th>
                                <th width="10%">Unit Price</th>
                            @endif

                                <th width="12%">Qty</th>
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
                                @if($boosterCartData)
                                    <tr>
                                        <td>1</td>
                                        <td>{{$boosterCartData->model_no}}</td>
                                        <td>{{$boosterCartData->booster_article_number}}</td>
                                        @if (auth()->user()->isAdmin())
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$boosterCartData->pump_price}}</td>
                                        @endif
                                        <td>
                                            {{$boosterCartData->boosterCpData[0]->no_of_pump_id}}
                                        </td>
                                        {{--@if (auth()->user()->isAdmin())--}}
                                        {{--<td>--}}
                                        {{--{{$item->price*$item->qty }}--}}
                                        {{--</td>--}}
                                        {{--@endif--}}
                                        @if (auth()->user()->isAdmin())
                                            <td>{{$boosterCartData->pump_price * $boosterCartData->boosterCpData[0]->no_of_pump_id}}</td>
                                        @endif
                                    </tr>
                                @endif

                                @foreach($items as $key=> $item)
                                    @if(auth()->user()->isAdmin())
                                    
                                    <tr>
                                        <td>{{$key+2}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->wilo_artilce_no}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        <td>
                                            <?php
                                            $txtArr = explode("x", $item->adder_code);
                                            $i = array_search("x", explode("x", $item->adder_code));
                                            unset($txtArr[$i + 2]);
                                            unset($txtArr[$i + 1]);
                                            echo implode(" ", $txtArr);
                                            ?>
                                        </td>
                                        <td>{{$item->price}}</td>
                                        @endif
                                        <td>{{$item->qty}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->price*$item->qty }}</td>
                                        @endif
                                    </tr>

                                    @elseif($item->item_description != "Accessories" && $item->item_description != "Pallect and packing" && $item->item_description != "Labour charges" && $item->item_description != "Labour cost" && $item->item_description != "Packing charges" && auth()->user()->isAdmin() == false)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->wilo_artilce_no}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        <td>
                                            <?php
                                            $txtArr = explode("x", $item->adder_code);
                                            $i = array_search("x", explode("x", $item->adder_code));
                                            unset($txtArr[$i + 2]);
                                            unset($txtArr[$i + 1]);
                                            echo implode(" ", $txtArr);
                                            ?>
                                        </td>
                                        <td>{{$item->price}}</td>
                                        @endif
                                        <td>{{$item->qty}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->price*$item->qty }}</td>
                                        @endif
                                    </tr>
                                    @else
                                    @endif
                                    @php
                                    $j++
                                    @endphp
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <h3>Control Panel BOM</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th width="8%">S.No</th>
                                    <th width="25%">Description</th>
                                    <th width="8%">Article No.</th>
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Brand Code</th>
                                    <th width="8%">Function Code</th>
                                    <th width="8%">Range</th>
                                    <th width="8%">Adder Code</th>
                                    <th width="10%">Unit Price</th>
                                    @endif
                                    <th width="12%">Qty</th>
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($cpBoosterItems->isNotEmpty())
                                @foreach($cpBoosterItems as $key=> $item)
                                    @if(auth()->user()->isAdmin())
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->wilo_artilce_no}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        <td>
                                            <?php
                                            $txtArr = explode("x", $item->adder_code);
                                            $i = array_search("x", explode("x", $item->adder_code));
                                            unset($txtArr[$i + 2]);
                                            unset($txtArr[$i + 1]);
                                            echo implode(" ", $txtArr);
                                            ?>
                                        </td>
                                        <td>{{$item->price}}</td>
                                        @endif
                                        <td>{{$item->qty}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->price*$item->qty }}</td>
                                        @endif
                                    </tr>
                                    @elseif($item->item_description != "Accessories" && $item->item_description != "Pallect and packing" && $item->item_description != "Labour charges" && auth()->user()->isAdmin() == false)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->wilo_artilce_no}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        <td>
                                            <?php
                                            $txtArr = explode("x", $item->adder_code);
                                            $i = array_search("x", explode("x", $item->adder_code));
                                            unset($txtArr[$i + 2]);
                                            unset($txtArr[$i + 1]);
                                            echo implode(" ", $txtArr);
                                            ?>
                                        </td>
                                        <td>{{$item->price}}</td>
                                        @endif
                                        <td>{{$item->qty}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->price*$item->qty }}</td>
                                        @endif
                                    </tr>
                                    @else
                                    @endif

                                    @php
                                    $j++
                                    @endphp
                                @endforeach
                                @endif

                                {{--@if(count($adderData) > 0)--}}
                                {{--@foreach($adderData as $key=> $item)--}}
                                {{--<tr>--}}
                                {{--<td>{{$j}}</td>--}}
                                {{--<td>{{$item['name']}}</td>--}}
                                {{--<td>--}}
                                {{--{{$item['id']}}--}}
                                {{--</td>--}}
                                {{--@if (auth()->user()->isAdmin())--}}
                                {{--<td>{{$item['price']}}</td>--}}
                                {{--@endif--}}
                                {{--<td>--}}
                                {{--1--}}
                                {{--</td>--}}
                                {{--@if (auth()->user()->isAdmin())--}}
                                {{--<td>{{$item['price']}}</td>--}}
                                {{--@endif--}}
                                {{--</tr>--}}
                                {{--@php--}}
                                {{--$j++--}}
                                {{--@endphp--}}
                                {{--@endforeach--}}
                                {{--@endif--}}
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
                    <!--<li><a href="{{URL::to('controlpanel/customer-information?cp_id=' . $cartId) }}" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
