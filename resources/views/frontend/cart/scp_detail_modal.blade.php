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
            <td>{{ $scp_data->pump_name }}</td>
        </tr>

        <tr>
            <td>Motor Power</td>
            <td>{{ $scp_data->power }}</td>
        </tr>

        <tr>
            <td>Seal Gland Pack</td>
            @if($scp_data->seal_gland_pack_id == 1)
            <td>Mechanical seal</td>
            @else
            <td>Gland Pack</td>
            @endif
        </tr>

        <tr>
            <td>No of Pole</td>
            <td>{{ $scp_data->no_of_pole}}</td>
        </tr>


        <tr>
            <td>Frequency</td>
            <td>{{ $scp_data->frequency }}</td>
        </tr>
        <tr>
            <td>Impeller code</td>
            <td>{{ $impeller }}</td>
        </tr>
        <tr>
            <td>Application</td>
            <td>{{$scp_data->application == 1 ? "Constant" : "Variable"}} Speed</td>
        </tr>
        <tr>
            <td>Voltage</td>
            <td>{{ $scp_data->voltage }} V</td>
        </tr>
        <tr>
            <td>Effiecieny</td>
            <td>{{ $scp_data->efficiency }}</td>
        </tr>
        <tr>
            <td>Brand</td>
            <td>{{ $scp_data->brand }}</td>
        </tr>
        @if( $scp_data->is_bare_manual )
        <tr>
            <td>Manually Bare Pump Price</td>
            <td> {{ App\Helpers\CurrencyHelper::withCurrency($scp_data->bare_pump_price)}}</td>
        </tr>

        @endif
        @if( $scp_data->is_accesories_manual )
        <tr>
            <td>Manually Accessories Price</td>
            <td> {{ App\Helpers\CurrencyHelper::withCurrency($scp_data->accesories_price)}}</td>
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