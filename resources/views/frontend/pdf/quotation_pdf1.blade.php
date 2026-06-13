<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
        <!-- <title>Export Notes List PDF - Tutsmake.com</title> -->
        <title>Quotation</title>

        <style>
            /*             html { margin: 0px !important}*/
            body{
                font-family: Arial, Helvetica, sans-serif;
            }
            @page { margin: 180px 80px; }
            footer{position:fixed;bottom:-110px;left:0px;right:0px;height:120px;font-size:9px;}

            header {
                position: fixed;
                top: -170px;
                left: 0;
                right: 0;
                height: 100px;
                padding: 10px 0px;
                /*background-color: #ccc;*/
                /*border-bottom: 1px solid #1f1f1f;*/
                z-index: 1000;
            }
            th {
                text-align: left;
            }
            /*Zubiar css*/
            .inv-footer {
                border-top: 1px solid #2c9d83;

            }
            table.inv-det-table {

                border-collapse: collapse;
            }

            table.inv-det-table tr th {
                text-align: left;
                border-top: 1px solid;
                padding: 0 30px;
                font-size: 12px;
                vertical-align: top;
            }

            table.inv-det-table tr {
                border-top: 2px solid #000 !important;
                border-color: #000 !important;
            }

            table.inv-det-table tr th:first-child {
                border-right: 2px solid;
                padding: 0 0;
            }

            table.inv-det-table tr td {
                text-align: left;
                border-top: 1px solid;
                padding: 0 40px;
                font-size: 12px;
                vertical-align: baseline;
                padding-bottom: 20px;
                border-bottom: 2px solid;
            }

            table.inv-det-table tr td:first-child {
                border-right: 2px solid;
                padding: 0 0;
            }

            table.inv-det-table tr td p {
                margin: 0;
                font-size: 22px;
            }
        </style>
    </head>
    <body>

    <header>
        <table width="100%" cellpadding="25" cellspacing="0">
            <tr>
                <td align="left" width="75%" style="font-size:13px;">
                    <h2>Quotation</h2>
                    <table class="customer-det-table">
                        <tr>
                            <th style='padding-right: 15px:'>Quotation number   <br> with revision
                            </th>
                            <th style='vertical-align: top; padding-right: 45px;'>Date</th>
                            <th style='padding-left: 15px;vertical-align: top;text-align:right;'>Page</th>
                        </tr>
                        <tr>
                            <td>{{$quotations[0]->quotation_number}} / {{$customer->revision_number}}</td>
                            <td>   {{ \Carbon\Carbon::now()->format('m/d/Y')   }} </td>
                            <td style="text-align:left; float: none;">
                                <script type="text/php">
                                    $x = 250;
                                    $y = 105;
                                    $text = "{PAGE_NUM} / {PAGE_COUNT}";
                                    $font = null;
                                    $size = 10;
                                    $color = array(0,0,0);
                                    $word_space = 0.0;  //  default
                                    $char_space = 0.0;  //  default
                                    $angle = 0.0;   //  default
                                    {{--$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);--}}
                                </script>
                            </td>
                        </tr>
                    </table>
                </td>

                <td align="right" width="25%" style="font-size:13px;">
                    <div class="logo">
                        <img src="{{asset('fassets/images/logo_1.png')}}">
                    </div>
                </td>
            </tr>
        </table>
    </header>

<main style="position: relative;z-index: 1; ">
    <table style="width:100%">
        <tr>
            <td> <p style="font-size:10px">Wilo Middle East FZE Jebel Ali Free Zone - South PO Box 262720 Dubai, UAE</p></td>
        </tr>
        <tr>
            <td align="" width="60%" style="font-size:14px">
            </td>
            <td align="" width="40%">
            </td>
        </tr>
    </table>

    <table style="width:100%">
        <tr>
            <td> <h1 >Customer Information </h1></td>
        </tr>
        <tr>

            <td align=""  >
                Name: {{$customer->name}}
            </td>
        </tr>
        <tr>
            <td align="" width="40%"  >
                Project Name:{{$customer->project_name}}
            </td>
        </tr>
        <tr>
            <td align="">
                Country:{{$customer->country}}
            </td>
        </tr>
        <tr>
            <td align=""  >
                Segment Country: {{$customer->segment_category}}
            </td>
        </tr>
        <tr>
            <td align="">
                Project Location:{{$customer->project_location}}
            </td>
        </tr>
        <tr>
            <td align="">
                Email: {{$customer->email_id}}
            </td>
        </tr>
        <tr>
            <td align="">
                Phone Number:{{$customer->phone_no}}
            </td>
        </tr>
        <tr>
            <td align=""  >
                Address: {{$customer->address}}
            </td>
        </tr>
        <tr>
            <td align="">
                Enquiry Form Number:{{$customer->enquiry_form_number}}
            </td>
        </tr>
        <tr>
            <td align=""   >
                Consultant: {{$customer->consultant}}
            </td>
        </tr>
        <tr>
            <td align="">
                Contractor:{{$customer->contractor}}
            </td>
        </tr>
        </tr>
    </table>

    <div style="page-break-before:always;"> </div>
    <h3><strong>Concerning:</strong>{{$customer->project_name}} -RFQ</h3>
    
    <table  class="inv-det-table">
        <tr>
            <th style="">Pos</th>
            <th style="">Article Number#</th>
            <th style="">Description</th>
            <th style="">Qty</th>
            <th style="">Unit Price</th>
            <th style="">Total</th>
        </tr>
        <?php $totalPrice = 0.00; ?>
        @php
        $i = 1
        @endphp
        @if($controlPanelCartData->isNotEmpty())
        @foreach($controlPanelCartData as $key=> $val)
        <tr>
            <td>{{$i}}</td>
            <td>{{$val['full_article_number']}}</td>
            <td>Control Panel {{$val->noofpumps['value'] }} x {{ $val->powers['value'] }}KW {{$val->starter_code}}/AE
                @if(!empty($val->adder_ids))
                    [Adder code :- {{$val->adder_ids}}]
                @endif
            </td>
            <td>
                {{$val->qty}}
            </td>
            <td>{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>
            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>
        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
        @php
        $atmos_ksa_count = 0;
        $atmos_count = 0;
        $booster_ksa_count = 0;
        $booster_count = 0;
        $scp_ksa_count = 0;
        $scp_count = 0;
        @endphp
        @if($atmosCartData->isNotEmpty())
        @php
            $atmos_count = count($atmosCartData);
        @endphp
        @foreach($atmosCartData as $key=> $val)
            @php
            $short_code = DB::table('atmos_materials')->where('id',$val->material_id)->pluck("short_code")->first();
            @endphp
            @if($val->country_origin == "ksa")
                @php
                    $atmos_ksa_count = $atmos_ksa_count + 1;
                @endphp
            @endif
            <tr>
                <td>{{$i}}
                </td>
                <td>
                    @if(auth()->user() && auth()->user()->country_id == "6")
                        @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                        {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                        @else
                        {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                        @endif 
                    @else
                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                    @endif
                {{--{{$val['full_article_number']}}--}}

                </td>
                <td>{{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                    @if(!empty($val->adder_ids))
                        [Adder code :- {{$val->adder_ids}}]
                    @endif
                </td>
                <td>
                    {{$val->qty}}
                </td>
                <td>{{ App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>

                <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
            </tr>
            @php
            $i++
            @endphp
            <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif

        @if($scpCartData->isNotEmpty())
        @php
            $scp_count = count($scpCartData);
        @endphp
        @foreach($scpCartData as $key=> $val)
        @php
        $short_code = DB::table('scp_materials')->where('id',$val->material_id)->pluck("short_code")->first();
        @endphp
        @if($val->country_origin == "ksa")
            @php
                $scp_ksa_count = $scp_ksa_count + 1;
            @endphp
        @endif
        <tr>
            <td>{{ $i}}</td>
            <td>
                {{--{{$val['full_article_number']}}--}}
                @if(auth()->user() && auth()->user()->country_id == "6")
                    @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                    {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                    @else
                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                    @endif 
                @else
                {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                @endif
            </td>
            <td>{{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
                @if(!empty($val->adder_ids))
                    [Adder code :- {{$val->adder_ids}}]
                @endif
            </td>
            <td>
                {{$val->qty}}
            </td>
            <td>{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>

            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>

        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
        <!--Booster quotation-->
        @if($boosterCartData->isNotEmpty())
        @php
            $booster_count = count($boosterCartData);
        @endphp
        @foreach($boosterCartData as $key=> $val)
        @php
            $short_code = DB::table('scp_materials')->where('id',$val->material_id)->pluck("short_code")->first();
        @endphp
        @if($val->country_origin == "ksa")
            @php
                $booster_ksa_count = $booster_ksa_count + 1;
            @endphp
        @endif
        <tr>
            <td>{{$i}}</td>
            <td>
                {{--{{$val['full_article_number']}}--}}
                @if(auth()->user() && auth()->user()->country_id == "6")
                    @if($val['country_origin'] != null && $val['country_origin'] == "ksa")
                    {{ !empty($val['ksa_full_article_number']) ? $val['ksa_full_article_number'] : '--' }}
                    @else
                    {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                    @endif 
                @else
                {{ !empty($val['full_article_number']) ? $val['full_article_number'] : '--' }}
                @endif
            </td>

            <td>                        
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
                {{$const}} {{$val->boosterCpData[0]->noofpumps['value'] }} {{$val->model_no }}/{{$val->boosterCpData[0]->starter_code}}/AE
                    <br>
                    @if(!empty($val->adder_ids))
                        [Electrical Adder Code :- {{$val->adder_ids}}]
                    @endif
                    <br>
                    @if(!empty($val->mechanical_adder_ids))
                        [Mechanical Adder Code :- {{$val->mechanical_adder_ids}}]
                    @endif
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
                {{$val->qty}}
            </td>
            <td>{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>

            <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>

        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif

        {{-- Fire Fighting --}}
        @if($firefightingCartData->isNotEmpty())
            @foreach($firefightingCartData as $key=> $val)
                <tr>
                    <td>{{ $i}}</td>
                    <td>{{$val['full_article_number']}}</td>
                    <td>{{ ucwords(str_replace('-pump', '', $val->category)) }} - {{ $val->pump_models }}/AE
                        @if(!empty($val->adder_ids))
                            [Adder code :- {{ implode(',', $val->adder_ids) }}]
                        @endif
                    </td>
                    <td>
                        {{$val->qty}}
                    </td>
                    <td>{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>

                    <td class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
                </tr>

                @php
                    $i++
                @endphp
                <?php $totalPrice += round($val->price * $val->qty); ?>
            @endforeach
        @endif
    </table>

    <table style="width:100%;" >
        <tr>
            <td>
                <div class="inv-prc">
                    <div class="price-wrap">
                        <div class="total-prc">
                            <h3>Total net price, excl. VAT : {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</h3>
                        </div>
                        <div class="final-prc">
                            <h3>Final Total net price, excl. VAT </h3>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table style="width:100%;" class="paymnt-table">
        <tr>
            <td>Delivery conditions:  </td>
            <!-- <td>Ex works Jebel Ali, Ex works KSA</td> -->
            <td>
                @if( ($atmos_ksa_count > 0 || $booster_ksa_count > 0 || $scp_ksa_count > 0) && $atmos_count != $atmos_ksa_count && $booster_count != $booster_ksa_count && $scp_count != $scp_ksa_count)
                    delivery shows only KSA & jabel ali
                @elseif( $atmos_ksa_count == 0 && $booster_ksa_count == 0 && $scp_ksa_count == 0)
                    delivery shows only jabel ali
                @elseif($atmos_count == $atmos_ksa_count && $booster_count == $booster_ksa_count && $scp_count == $scp_ksa_count)
                    delivery shows only KSA
                @endif
            </td>
        </tr>
        <tr>
            <td>Delivery time:
            </td>
            <td>
                Booster - 16 - 18 Weeks<br>
                Atmos GIGA - 16 - 18 Weeks<br>
                SCP pump - 20 - 22 Weeks<br>
                Control panel - 08 - 10 Weeks
            </td>
        </tr>
        <tr>
            <td>Price and validity:  </td>
            <td>30 days
              </td>
        </tr>
        <tr>
            <td>Notes:  </td>
            <td>{{$customer->notes ?? ""}}</td>
        </tr>
    </table>
</main>

</body>
</html>



