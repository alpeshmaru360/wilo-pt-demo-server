@extends('frontend.layout.app')
@section('content')

    <!-- mid section start-->
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_filter{margin-bottom:20px;margin-top:20px;}
    </style>
    <section class="midContent" id="midContent">
        <div class="container">
            <div class="d-flex flex-center">
                <div class="addQuotationMidSection">
                    <h2>Documents</h2>
                    <!--
                    <select id="component" class="form-control" name="component" style="font-size: 20px;padding: 10px;">
                        <option value="">Select Component</option>
                        <option value="booster">Booster</option>
                        <option value="control_panel">Control Panel</option>
                        <option value="atmos">Atmos</option>
                        <option value="scp">Scp</option>
                    </select>
                    <input style="font-size: 20px;padding: 10px;" type="text" id="article_number" name="article_number" placeholder="Article Number" value={{$query_param}}>
                    <input style="font-size: 20px;padding: 10px;" type="button" id="search" value="search">
                    <input style="font-size: 20px;padding: 10px;" type="button" id="clear" value="clear">
                    -->
                    <div class="quotationBottomSection">
                        <div class="tableResponsive">
                            <table id = "dataTable">
                                <thead>
                                <tr>
                                    <th width="25%">Item Description</th>
                                    <th width="25%">Article Number</th>
                                    <th width="25%">Component</th>
                                    <th width="25%">Documents</th>
                                </tr>
                                </thead>
                                <tbody>                                   

                                    <!-- A Code: 20-02-2026 Start -->
                                     
                                    @if(isset($controlPanelCartData) && $controlPanelCartData->isNotEmpty())
                                        @foreach($controlPanelCartData as $val)
                                            <tr>
                                                <td>
                                                    Control Panel 
                                                    {{ $val->noofpumps['value'] ?? '' }} x 
                                                    {{ $val->powers['value'] ?? '' }}KW 
                                                    {{ $val->starter_code }}/AE
                                                </td>
                                                <td>{{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}</td>
                                                <td>Control Panel</td>
                                                <td>
                                                    @if(!empty($val->documents) && count($val->documents) > 0)
                                                        @foreach($val->documents as $d)
                                                            <a href="{{ URL::to('public/articles/'.$d->file_name) }}" target="_blank">
                                                                <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                                {{ $d->file_name }}
                                                            </a>
                                                            <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if(isset($atmosCartData) && $atmosCartData->isNotEmpty())
                                        @foreach($atmosCartData as $val)
                                            @php
                                                $short_code = DB::table('atmos_materials')
                                                    ->where('id', $val->material_id)
                                                    ->value('short_code');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ $val->pump_name }} - {{ $short_code }}/{{ $val->power }}KW/{{ $val->no_of_pole }}/AE
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                    </a>
                                                </td>
                                                <td>Atmos Giga</td>
                                                <td>
                                                    @if(!empty($val->documents) && count($val->documents) > 0)
                                                        @foreach($val->documents as $d)
                                                            <a href="{{ URL::to('public/articles/'.$d->file_name) }}" target="_blank">
                                                                <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                                {{ $d->file_name }}
                                                            </a>
                                                            <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if(isset($scpCartData) && $scpCartData->isNotEmpty())
                                        @foreach($scpCartData as $val)
                                            @php
                                                $short_code = DB::table('scp_materials')
                                                    ->where('id', $val->material_id)
                                                    ->value('short_code');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ $val->pump_name }} - {{ $short_code }}/{{ $val->power }}KW/{{ $val->no_of_pole }}/AE
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                    </a>
                                                </td>
                                                <td>Scp Pump</td>
                                                {{-- <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price']) }}</td> --}}
                                                <td>
                                                    @if(!empty($val->documents) && count($val->documents) > 0)
                                                        @foreach($val->documents as $d)
                                                            <a href="{{ URL::to('public/articles/'.$d->file_name) }}" target="_blank">
                                                                <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                                {{ $d->file_name }}
                                                            </a>
                                                            <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif  

                                    @if(isset($scpvCartData) && $scpvCartData->isNotEmpty())
                                        @foreach($scpvCartData as $val)
                                            @php
                                                $short_code = DB::table('scpv_materials')
                                                    ->where('id', $val->material_id)
                                                    ->value('short_code');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ $val->pump_name }} - {{ $short_code }}/{{ $val->power }}KW/{{ $val->no_of_pole }}/AE
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                    </a>
                                                </td>
                                                <td>Scpv Pump</td>
                                                {{-- <td>{{ App\Helpers\CurrencyHelper::withCurrency($val['price']) }}</td> --}}
                                                <td>
                                                    @if(!empty($val->documents) && count($val->documents) > 0)
                                                        @foreach($val->documents as $d)
                                                            <a href="{{ URL::to('public/articles/'.$d->file_name) }}" target="_blank">
                                                                <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                                {{ $d->file_name }}
                                                            </a>
                                                            <br>
                                                        @endforeach                                                   
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if(isset($boosterCartData) && $boosterCartData->isNotEmpty())
                                        @foreach($boosterCartData as $val)
                                            @php
                                                $const = null;
                                                $cpData = $val->boosterCpData[0] ?? null;
                                                if($cpData) {
                                                    if(str_starts_with($cpData->table_name, 'basic_')) {
                                                        $const = "COE";
                                                    } else {
                                                        $const = "CO";
                                                        $array_check = [3,4,7];

                                                        if(in_array($cpData->stater_type_id, $array_check)) {
                                                            $const = "COR";
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ $const ?? '--' }}
                                                        {{ $cpData->noofpumps['value'] ?? '' }}
                                                        {{ $val->model_no ?? '' }}/{{ $cpData->starter_code ?? '' }}/AE
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="detail-modal" href="javascript:void(0)">
                                                        {{ !empty($val->full_article_number) ? $val->full_article_number : '--' }}
                                                    </a>
                                                </td>
                                                <td>Booster</td>
                                                <td>
                                                    @if(!empty($val->documents) && count($val->documents) > 0)
                                                        @foreach($val->documents as $d)
                                                            <a href="{{ URL::to('public/articles/'.$d->file_name) }}" target="_blank">
                                                                <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                                {{ $d->file_name }}
                                                            </a>
                                                            <br>
                                                        @endforeach                                                    
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <!-- A Code: 20-02-2026 End -->
                                    
                                </tbody>
                            </table>
                            {{--       
                            <div class="text-left" id=""><h4>Total Price: </h4><span class="" id="total-price-updated"> {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</span></div>
                            --}}
                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex cusPagination">
                <!--
                <div class="">
                    <?php $cpId = Request::get('cp_id'); ?>
                    <a href="{{URL::to('controlpanel/customer-information/' . $customer->id ) }}">
                        <img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back
                    </a>
                </div>
                -->
                <div class="">
                    <a  onclick="window.history.back()" href="javascript:void(0)"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
                </div>
                <!--            
                <div class="">
                    <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
                </div>
                -->
            </div>
            <div class="d-flex formPageFooter">
                <div class="left"></div>
                <div class="right">
                    <ul>
                        <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                        <li><a href="{{URL::to('/')}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                        <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- mid section end -->
     
    <script>
        $("#search").click(function(){
            current_url = window.location.href.split('?')[0]+"?component="+$("#component :selected").val()+"&article_number="+$("#article_number").val();
            if($("#component :selected").val() == "" || $("#article_number").val() == "")
            {
                alert("Article Number and Component Name are required to search document.");
            }else{        
                window.location.href=current_url;
            }
        });
        $("#clear").click(function(){
            window.location.href=window.location.href.split('?')[0];
        });
    </script>

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        jQuery.noConflict();
        var table = jQuery('#dataTable').DataTable({
            "bPaginate": false,
            "targets": 'no-sort',
            "bSort": false,
            "order": [],
        });
        jQuery('.dataTables_filter input[type="search"]').
        attr('placeholder','Enter Article Number Here..').
        css({'width':'261px','display':'inline-block','height':'43px'});
    </script>
@endsection









