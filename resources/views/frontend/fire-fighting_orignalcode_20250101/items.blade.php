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
                                    <th width="10%">Article Number</th>
                                    <th width="8%">Adder Code</th>
                                    
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Unit Price</th>
                                    @endif

                                    <th width="12%">Qty</th>
                                    
                                    @if (auth()->user()->isAdmin())
                                    <th width="10%">Total Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $k => $v)
                                    <tr>
                                        <td width="8%">{{ $k+1 }}</td>
                                        <td width="25%">{{ $v['description'] }}</td>
                                        <td width="10%">{{ $v['article_number'] }}</td>
                                        <td width="8%">{{ $v['addder_code'] }}</td>
                                        
                                        @if (auth()->user()->isAdmin())
                                        <td width="10%">{{ $v['unit_price'] }}</td>
                                        @endif

                                        <td width="12%">{{ $v['qty'] }}</td>
                                        
                                        @if (auth()->user()->isAdmin())
                                        <td width="10%">{{ $v['total_price'] }}</td>
                                        @endif
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
            </div>
            <div class="right">
                <?php $cartId = Request::segment(3); ?>
                <ul>
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>                     
                    <li><a href="{{URL::to('controlpanel/cart/' . $cartId) }}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <li><a href="{{URL::to('controlpanel/customer-information?cp_id=' . $cartId) }}" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection