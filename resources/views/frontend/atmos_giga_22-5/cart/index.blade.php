@extends('frontend.layout.app')
@section('content')

<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="cartMidSection">
                <h2>Cart</h2>
                <div class="cartSection">
                    <div class="tableResponsive">
                        @if($controlPanelCartData->isNotEmpty())
                        <table>
                            <thead>
                                <tr>
                                    <!--<th width="5%"></th>-->
                                    <th width="15%">Item Description</th>
                                    <th width="15%">Article Number</th>


                                    <th width="10%">Unit Price</th>
                                    <th width="10%">Qty</th>
                                    <th width="10%">Total Price</th>
                                    <th width="15%">Selection</th>
                                    <th width="10%">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($controlPanelCartData as $key=> $val)


                                <tr>
                                    <td style="display: none;"><input type="checkbox" checked name="cart_id" value="{{$val['id']}}"></td>
                                    <td>
                                        <a class="detail-modal" href="javascript:void(0)">  
                                            {{$val->applications['value'] }} {{$val->noofpumps['value'] }} x {{ $val->powers['value'] }} {{$val->starter_code}}
                                        </a>
                                    </td>
                                    <td>
                                        <a class="detail-modal" href="javascript:void(0)">
                                            {{ !empty($val['article_number']) ? $val['article_number'] : '--' }}
                                        </a>
                                    </td>
                                    <td>{{$val['total_price']}}</td>
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
                            <td class="total-price">{{$val->total_price*$val->qty }}</td>
                            <td><a href="{{ URL::to('controlpanel/cart-item/'.$val['id'] )}}" target="_blank"><img src="{{asset('fassets/images/viewIcon.png')}}" />
                                </a>
                                
                                <!--<button><img src="{{asset('fassets/images/downloadIcon.png')}}" /></button>-->
                            </td>
                            <td><button class="delete-cart"><img src="{{asset('fassets/images/delIcon.png')}}" /></button></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @else
                        <h3 class="text-center">Your cart is empty!</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex cusPagination">

            <div class="">
                <a  onclick="window.history.back()" href=""><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
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
                    @if($controlPanelCartData->isNotEmpty())
                    <li>
                        <a href="javascript:void(0)" id="generate-quotation" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a>
                    </li>
                    @endif
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


<script>
    $(".plus").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        var qty = parseInt($(this).closest('tr').find('.quantity').val()) + 1;
        var totalPrice = parseFloat($(this).closest('tr').find('.total-price-input').val());
        if (totalPrice > 0) {
            var tpPriceHtml = totalPrice * qty;
            $(this).closest('tr').find('.total-price').html(
                    tpPriceHtml.toFixed(2)
                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function () {



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
        if (totalPrice > 0) {
            var tpPriceHtml = totalPrice * qty;
            $(this).closest('tr').find('.total-price').html(
                    tpPriceHtml.toFixed(2)
                    );
        }
        if (qty >= 1) {
            $.ajax({
                type: "get",
                url: "{{url('controlpanel/ajax-qty-update')}}",
                data: {qty: qty, cp_id: cp_id},
                success: function () {
                },
                error: function () {

                }

            });
        }

    });

    //Delete  cart 

    $(".delete-cart").on("click", function () {
        var cp_id = $(this).closest('tr').find('.cp-id').val();
        $(this).closest('tr').remove();

        $.ajax({
            type: "get",
            url: "{{url('controlpanel/remove-cart')}}" + "/" + cp_id,
//                data: {qty: qty, cp_id: cp_id},
            success: function () {

            },
            error: function () {

            }

        });


    });


//    Generate Quotation
    $("#generate-quotation").on("click", function () {
        var cartIds = [];
        var removeCartIds = [];
        $('input[name="cart_id"]:checked').each(function () {
            cartIds.push($(this).val());

        });

        $('input[name="cart_id"]:not(:checked)').each(function () {
            removeCartIds.push($(this).val());

        });

        if (cartIds.length >= 1) {

//            var url = "{{url('controlpanel/customer-information?cp_ids=')}}" + cartIds + "&removeCartIds=" + removeCartIds;
            var url = "{{url('controlpanel/customer-information?cp_ids=')}}" + cartIds;
            window.location = url;
        }
    });
    $(".detail-modal").on("click", function () {
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
    $(document).on("click", '.close-detail-control-panel-modal', function (event) {
        $("#detail-control-panel-modal").hide();
    });

</script>
@endsection