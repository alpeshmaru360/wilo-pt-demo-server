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
                                    <!--<th width="10%">Brand Code</th>
                                    <th width="8%">Function Code</th>
                                    <th width="8%">Range</th>-->
                                    @if (auth()->user()->isAdmin())
                                    <th width="8%">Adder Code</th>
                                    <th width="8%">Brand Code</th>
                                    <th width="8%">Function Code</th>
                                    <th width="8%">Range</th>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                    <th width="10%">Unit Price</th>
                                    @endif
                                    <th width="12%">Qty</th>
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key=> $item)
                                    @if(auth()->user()->isAdmin())
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->wilo_artilce_no}}</td>
                                        @if(auth()->user()->isAdmin())
                                        <td>
                                            {{$item->adder_code}}
                                        </td>
    									 <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        @endif
                                        @if(auth()->user()->isAdmin())
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
                                        @if(auth()->user()->isAdmin())
                                        <td>
                                            {{$item->adder_code}}
                                        </td>
                                         <td>{{$item->brand_code}}</td>
                                        <td>{{$item->function_code}}</td>
                                        <td>{{$item->ranges}}</td>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                        <td>{{$item->price}}</td>
                                        @endif
                                        <td>{{$item->qty}}</td>
                                        @if (auth()->user()->isAdmin())
                                        <td>{{$item->price*$item->qty }}</td>
                                        @endif
                                    </tr>
                                    @else
                                    @endif
                                @endforeach
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
<!-- mid section end -->
@endsection
