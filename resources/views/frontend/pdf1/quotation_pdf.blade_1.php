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
            @page { margin: 180px 50px; }
            footer{position:fixed;bottom:-180px;left:0px;right:0px;height:60px;font-size:13px;}

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
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->

        <header>
            <table width="100%" cellpadding="25" cellspacing="0">
                <tr>
                    <td align="left" width="25%" style="font-size:13px;">
                        {{ \Carbon\Carbon::now()->format('h:i:s A')   }}
                    </td>
                    <td align="center" width="50%">
                        <h1 style="font-style:none;font-size:20px;font-weight: 400;margin: 0">Payment List</h1>

                        <h4 style="font-style:none;font-size:20px;font-weight: 400;margin: 0">
                            {{ \Carbon\Carbon::parse($from)->format('m/d/yy')   }} - {{  \Carbon\Carbon::parse($to)->format('m/d/yy')  }}
                        </h4>

                        <h1 style="font-style:none;font-size:20px;font-weight: 400;margin: 0">Auto Glass Sales</h1>

                    </td>
                    <td align="right" width="25%" style="font-size:13px;">{{ \Carbon\Carbon::now()->format('m/d/yy')   }}</td>
                </tr>
            </table>

        </header>

        <footer>
            	<div class="inv-footer">
			<div class="ft-col ft1">
				<p>Wilo Middle East FZE
					Jebel Ali Free Zone - South
					PO Box 262720
					Dubai, UAE</p>
			</div>
			<div class="ft-col ft2">
				<p>T +971 4 8239500</p>
				<p>info.ae@wilo.com</p>
				<p>www.wilo.ae</p>
			</div>
			<div class="ft-col ft3">
				<p>Dubai Islamic Bank</p>
				<p>IBAN EUR:</p>
				<p>AE95 0240 0015 2106 9567 002</p>
				<p>IBAN USD:</p>
				<p>AE25 0240 0015 2106 9567 001</p>
				<p>BAN AED:</p>
				<p>AE70 0240 0015 2006 9567 001</p>
			</div>
			<div class="ft-col ft2">
				<p>Swift Code: DUIBAEADXXX</p>
				<p>VAT: TRN100344632300003</p>
			</div>
		</div>
            <table width="100%" cellpadding="25" cellspacing="0" >
                <tr>
                    <td align="left" width="30%">
                        Auto Glass YES
                    </td>
                    <td align="center" width="40%">
                        <!--<h1 style="font-style:none;font-size:20px;font-weight: 400;margin: 0">GLASSquote 7.5.2.3</h1>-->

                    </td>

                    <td align="right" width="30%">
                        <script type="text/php">
                            if (isset($pdf)) {
                            $x = 500;
                            $y = 813;
                            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                            $font = null;
                            $size = 12;
                            $color = array(255,0,0);
                            $word_space = 0.0;  //  default
                            $char_space = 0.0;  //  default
                            $angle = 0.0;   //  default
                            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
                            }
                        </script>
                    </td>
                </tr>
            </table>
        </footer>
        <!--       -->
        <!-- Wrap the content of your PDF inside a main tag -->
        <!-- Wrap the content of your PDF inside a main tag -->
        <main style="position: relative;z-index: 1; ">
            <table style="width:100%">
                <tr>
                    <th style="text-align:left;border-bottom:1px solid #000;">Date</th>
                    <th style="text-align:left;border-bottom:1px solid #000;">Invoice#</th>
                    <th style="text-align:left;border-bottom:1px solid #000;">Type</th>
                    <th style="text-align:left;border-bottom:1px solid #000;">Payment Description</th>
                    <th style="text-align:left;border-bottom:1px solid #000;">Amount</th>
                </tr>
                @foreach ($data as $name => $valData )
                <tr>
                    <td><b>{{ $valData[0]['customer_id'] }}</b></td>
                    <td><b>{{ $name }}</b></td>

                </tr>
                @php ($subTotal = 0.00)
                @foreach($valData as $val)
                <tr>
                    <td> {{ $val['date']}}</td>
                    <td>{{ $val['invoice_number']}}</td>
                    <td>{{ ($val['payment_type'] =='cheque') ? 'Check': ucfirst($val['payment_type']) }}</td>
                    <td>
                        @if($val['payment_description'])
                        @foreach ($val['payment_description'] as $key => $value)
                        {!! ucwords(str_replace('_', ' ', $key))  !!} : {{$value}} <br>
                        @endforeach
                        @elseif($val['payment_type'] == 'partial')
                            @foreach ($val['partial_payment_array'] as $key => $value)
                                {{ucfirst(str_replace('_', ' ', $key))}} <br>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if($val['payment_type'] == 'partial')
                            @foreach ($val['partial_payment_array'] as $key => $value)
                                {{ App\Helpers\Helper::formatPrice($value) }} <br>
                            @endforeach
                        @else
                        {{ App\Helpers\Helper::formatPrice($val['total_amount'])}}
                        @endif
                    </td>

                </tr>
                <?php $subTotal += $val['total_amount'] ?>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Sub Total</td>
                    <td style="border-top:1px solid #000">{{ App\Helpers\Helper::formatPrice($subTotal) }}</td>

                </tr>


                @endforeach
            </table>
        </main>
    </body>
</html>



