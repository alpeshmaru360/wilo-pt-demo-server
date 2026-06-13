<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Quotation</title>
        <style>
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
                z-index: 1000;
            }
            th {
                text-align: left;
            }
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
    <h1 style="font-size:16x;"><b>Quotation</b></h1>
        <table class="customer-det-table">
            <tr>
                <th style='vertical-align:top; padding-right:15px;font-weight:bold;'>Quotation number with revision
                </th>
                <th style='vertical-align:top; padding-right:45px;font-weight:bold;'>Date</th>
                <th style='padding-left:15px;vertical-align:top;font-weight:bold;'>Page</th>
                <th></th>
                <th align="right" style="font-size:13px;" rowspan="3">
                   					            <div class="logo">
												{{--<img src="{{asset('fassets/images/logo.png')}}">--}}
            
<img src = "C:\inetpub\wwwroot\wilo2\wilo2\public\fassets\images\logo_1.png">
                    {{--<img src="http://wme-estimationtool/fassets/images/logo.png">--}}
                    </div>
                </th>
            </tr>
            <tr>
                <td>{{$quotations[0]->quotation_number}} / {{$quotations_revision_no}}
                    {{-- {{$customer->revision_number}} --}}
                </td>

                <td>{{\Carbon\Carbon::now()->format('m/d/Y')}}</td>
                <td style="text-align:left; float: none;">1</td>
            </tr>
        </table>
          
                      
    <table style="width:100%">
        <tr >
            <td colspan="3"><p style="font-size:10px">Wilo Middle East FZE Jebel Ali Free Zone - South PO Box 262720 Dubai, UAE</p></td>
        </tr>
    </table>

    <table style="width:100%">
        <tr>
            <td colspan="2" style="font-weight:bold;"><h1>Customer Information </h1></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Name:
            </td>
            
            <td>
            {{$customer->name}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Project Name:
            </td>
            <td>
            {{$customer->project_name}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Country:
            </td>
            <td>
            {{$customer->country}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Segment Country: 
            </td>
            <td>
            {{$customer->segment_category}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Project Location:
            </td>
            <td>
            {{$customer->project_location}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Email: 
            </td>
            <td>
            {{$customer->email_id}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Phone Number:
            </td>
            <td>
            {{$customer->phone_no}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Address: 
            </td>
            <td>
            {{$customer->address}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Enquiry Form Number:
            </td>
            <td>
            {{$customer->enquiry_form_number}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Consultant: 
            </td>
            <td>
            {{$customer->consultant}}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">
                Contractor:
            </td>
            <td>
            {{$customer->contractor}}
            </td>
        </tr>
    </table>

    <div style="page-break-before:always;"> </div>

    <h3><strong>Concerning:</strong>{{$customer->project_name}} -RFQ</h3>

    <table  class="inv-det-table">
        <tr>
            <th style="font-weight:bold;">Pos</th>
            <th style="font-weight:bold;">Article Number#</th>
            <th style="font-weight:bold;">Description</th>
            <th style="font-weight:bold;">Qty</th>
            <th style="font-weight:bold;">Unit Price</th>
            <th style="font-weight:bold;">Total</th>
        </tr>
        <?php $totalPrice = 0.00; ?>
        @php
        $i = 1
        @endphp
        @if($controlPanelCartData->isNotEmpty())
        @foreach($controlPanelCartData as $key=> $val)
        <tr>
            <td align="left">{{$i}}</td>
            <td align="left">{{$val['full_article_number']}}</td>
            <td align="left">Control Panel {{$val->noofpumps['value'] }} x {{ $val->powers['value'] }}KW {{$val->starter_code}}/AE
			@if(!empty($val->adder_ids))
                    [Adder code :- {{$val->adder_ids}}]
                @endif
			</td>
            <td align="left">{{$val->qty}}</td>
            <td align="left">{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>
            <td align="left" class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>
        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
        @if($atmosCartData->isNotEmpty())
        @foreach($atmosCartData as $key=> $val)
        @php
        $short_code = DB::table('atmos_materials')->where('id',$val->material_id)->pluck("short_code")->first();
        @endphp
        <tr>
            <td align="left">{{ $i}}</td>
            <td align="left">
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
				
            <td align="left">{{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
			@if(!empty($val->adder_ids))
                    [Adder code :- {{$val->adder_ids}}]
                @endif
			</td>
            <td align="left">{{$val->qty}}</td>
            <td align="left">{{ App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>
            <td align="left" class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>
        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
        @if($scpCartData->isNotEmpty())
        @foreach($scpCartData as $key=> $val)
        @php
        $short_code = DB::table('scp_materials')->where('id',$val->material_id)->pluck("short_code")->first();
        @endphp
        <tr>
            <td align="left">{{ $i}}</td>
            <td align="left">
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
            <td align="left">{{$val->pump_name }} -{{$short_code}}/{{$val->power}}KW/{{$val->no_of_pole}}/AE
			@if(!empty($val->adder_ids))
                    [Adder code :- {{$val->adder_ids}}]
                @endif
			</td>
            <td align="left">
                {{$val->qty}}
            </td>
            <td align="left">{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>

            <td align="left" class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>

        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
        <!--Booster quotation-->
        @if($boosterCartData->isNotEmpty())

        @foreach($boosterCartData as $key=> $val)
        <tr>
            <td align="left">{{ $i}}</td>
            <td align="left">
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
            <td align="left">                        @php
                    $const =null;
                    // dd(str_starts_with($val->boosterCpData[0]->table_name, 'standard_'));
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
            	
			@endif</td>
            <td align="left">
                {{$val->qty}}
            </td>
            <td align="left">{{App\Helpers\CurrencyHelper::withCurrency($val->price)}}</td>
            <td align="left" class="total-price">{{ App\Helpers\CurrencyHelper::withCurrency($val->price*$val->qty) }}</td>
        </tr>

        @php
        $i++
        @endphp
        <?php $totalPrice += round($val->price * $val->qty); ?>
        @endforeach
        @endif
    </table>

    <table style="width:100%;">
        <tr>
            <td colspan="2" rowspan="2">
                <div class="inv-prc">
                    <div class="price-wrap">
                        <div class="total-prc">
                            <strong><h2>Total net price, excl. VAT : {{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}</h2></strong>
                        </div>
                        <div class="final-prc">
                            <strong><h2>Final Total net price, excl. VAT </h2></strong>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table style="width:100%;" class="paymnt-table">
        <tr>
            <td>Delivery conditions:  </td>
            <td colspan="2">Ex works Jebel Ali</td>
        </tr>
        <tr>
            <td>Delivery time: </td>
            <td rowspan="5">
                Booster - 16 - 18 Weeks<br>
                Atmos GIGA - 16 - 18 Weeks<br>
                SCP pump - 20 - 22 Weeks<br>
                Control panel - 08 - 10 Weeks
				Fire fighting pump - 10-12 weeks
            </td>
          </tr>
          <tr></tr>
          <tr></tr>
          <tr></tr>
        <tr>
            <td>Price and validity:  </td>
            <td colspan="2">30 days
              </td>
        </tr>
        <tr>
            <td >Notes:  </td>
            <td colspan="2">{{$customer->notes ?? ""}}</td>
        </tr>
    </table>
        
</body>
</html>



