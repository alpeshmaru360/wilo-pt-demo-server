<table id="" class="table table-bordered">
    <thead>
    <tr>
        <th>Title</th>
        <th>User Information</th>
    </tr>

    </thead>
    <tbody>
    <tr>
        <td>Model Number</td>
        <td>{{ $controlPanelData->model_no}}</td>
    </tr>
    <tr>
        <td>Manifold</td>
        <td>{{ $controlPanelData->manifold}}</td>
    </tr>
    <tr>
        <td>System Pressure</td>
        <td>{{ $controlPanelData->system_pressure}}</td>
    </tr>
    <tr>
        <td>Number Of Pump</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->noofpumps['value']}}</td>
    </tr>
    
    <tr>
        <td>Motor Power</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->powers['value']}}</td>
    </tr>
    <tr>
        <td>Supply Voltage</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->voltages['value']}}</td>
    </tr>
    <tr>
        <td>Application</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->applications['value']}}</td>
    </tr>
    <tr>
        <td>Ambient Temp</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->ambienttemps['value']}}</td>
    </tr>
    <tr>
        <td>Starter Type</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->startertypes['value']}}</td>
    </tr>
    <tr>
        <td>Communication Protocol</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->comunicationprotocols['value']}}</td>
    </tr>
    <tr>
        <td>IP Rating </td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->ipratings['value']}}</td>
    </tr>
    <tr>
        <td>Component</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->components['value']}}</td>
    </tr>

    <tr>
        <td>Enclosure</td>
        <td>{{ $controlPanelData->BoosterCPdata[0]->enclousres['value']}}</td>
    </tr>

    </tbody>
</table>

@if($addersData)
    <table id="" class="table table-bordered">
        <thead>
        <tr>

            <th>Optional Electrical Data</th>
        </tr>

        </thead>
        <tbody>
        @foreach($addersData as $row)
            <tr>
                <td>{{$row->id}} -- {{$row->adder_list}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@if($response)
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>Optional Mechanical Data</th>
            </tr>
        </thead>
        <tbody>
        @foreach($response as $row)
            <tr>
                <td>{{$row['code']}} -- {{$row['adder_list']}} 
                @if($row['code'] == "60" || $row['code'] == "61")
                    [Qty = {{$row['qty']}}]
                @endif

                @if($row['code'] == "65" || $row['code'] == "66" || $row['code'] == "67")
                    ["{{$row['item_description']}}"]
                @endif
              </td>
            </tr>
        @endforeach
        
        </tbody>
    </table>
@endif
