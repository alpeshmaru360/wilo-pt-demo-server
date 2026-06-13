<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>User Information</th>
        </tr>

    </thead>
    <tbody>

        <tr>
            <td>Model</td>
            <td>{{ $atmos_data->pump_name }}</td>
        </tr>

        <tr>
            <td>Motor Power</td>
            <td>{{ $atmos_data->power }}</td>
        </tr>



        <tr>
            <td>No of Pole</td>
            <td>{{ $atmos_data->no_of_pole}} P</td>
        </tr>


        <tr>
            <td>Frequency</td>
            <td>{{ $atmos_data->frequency }}</td>
        </tr>
        <tr>
            <td>Impeller code</td>
            <td>{{ $impeller }}</td>
        </tr>
        <tr>
        <td>Application</td>
            <td>{{$atmos_data->application == 1 ? "Constant" : "Variable"}} Speed</td>
        </tr>
        <tr>
            <td>Voltage</td>
            <td>{{ $atmos_data->voltage }} V</td>
        </tr>
        <tr>
            <td>Effiecieny</td>
            <td>{{ $atmos_data->efficiency }}</td>
        </tr>
        <tr>
            <td>Brand</td>
            <td>{{ $atmos_data->brand }}</td>
        </tr>
        @if( $atmos_data->is_bare_manual )
        <tr>
            <td>Manually Bare Pump Price</td>
            <td> {{ App\Helpers\CurrencyHelper::withCurrency($atmos_data->bare_pump_price)}}</td>
        </tr>

        @endif
        @if( $atmos_data->is_accesories_manual )
        <tr>
            <td>Manually Accessories Price</td>
            <td> {{ App\Helpers\CurrencyHelper::withCurrency($atmos_data->accesories_price)}}</td>
        </tr>
        @endif

        @if($atmos_data->is_bare_manual && $atmos_data->is_accesories_manual)
        <tr>
            <td>Shipping Price</td>
            <td> {{ App\Helpers\CurrencyHelper::withCurrency($atmos_data->shipping_cost_price)}}</td>
        </tr>
        @endif
    </tbody>
</table>
@if($adderData)
<table id="" class="table table-bordered">
    <thead>
        <tr>

            <th>Optional</th>
        </tr>

    </thead>
    <tbody>
        @foreach($adderData as $row)
        <tr>
            <td>{{$row['name']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif