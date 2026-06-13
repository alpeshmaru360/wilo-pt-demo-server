@extends('frontend.layout.app');
@section('content')

<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="cartMidSection">
                <h2>Cart</h2>
                <div class="cartSection">
                    <div class="tableResponsive">
                        <table>
                            <thead>
                                <tr>
                                    <th width="8%">S.No</th>
                                    <th width="25%">Description</th>
                                    <th width="10%">Unit Price</th>
                                    <th width="12%">Qty</th>
                                    <th width="10%">Total Price</th>
                                    <th width="10%">Selection</th>
                                    <th width="8%">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key=> $item)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$item->item_description}}</td>
                                    <td>{{$item->price}}</td>
                                    <td>
                                        <div class="qty_input">
                                            <div class="qty">
                                                <span class="minus qtyBtn">-</span>
                                                <input type="number" class="icount"  id="quantity" name="quantity" value="{{$item->qty}}"  min="1" max="" />
                                                <span class="plus qtyBtn">+</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$item->price*$item->qty }}</td>
                                    <td><button><img src="{{asset('fassets/images/viewIcon.png')}}" /></button> <button><img src="{{asset('fassets/images/downloadIcon.png')}}" /></button></td>
                                    <td><button><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
                                </tr>
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
                  <?php $cartId = Request::segment(3);?>
                <ul>
                    <!--<li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>-->
                     <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>                     
                    <li><a href="{{URL::to('controlpanel/cart/' . $cartId) }}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('controlpanel/customer-information?cp_id=' . $cartId) }}" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- mid section end -->
@endsection