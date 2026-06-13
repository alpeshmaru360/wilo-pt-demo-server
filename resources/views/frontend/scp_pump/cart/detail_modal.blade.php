<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>User Information</th>
        </tr>

    </thead>
    <tbody>

        <tr>
            <td>Number Of Pump</td>
            <td>{{ $controlPanelData->noofpumps['value']}}</td>
        </tr>
        <tr>
            <td>Motor Power</td>
            <td>{{ $controlPanelData->powers['value']}}</td>
        </tr>
        <tr>
            <td>Supply Voltage</td>
            <td>{{ $controlPanelData->voltages['value']}}</td>
        </tr>
        <tr>
            <td>Application</td>
            <td>{{ $controlPanelData->applications['value']}}</td>
        </tr>
        <tr>
            <td>Ambient Temp</td>
            <td>{{ $controlPanelData->ambienttemps['value']}}</td>
        </tr>
        <tr>
            <td>Starter Type</td>
            <td>{{ $controlPanelData->startertypes['value']}}</td>
        </tr>
        <tr>
            <td>Communication Protocol</td>
            <td>{{ $controlPanelData->comunicationprotocols['value']}}</td>
        </tr>
        <tr>
            <td>IP Rating </td>
            <td>{{ $controlPanelData->ipratings['value']}}</td>
        </tr>
        <tr>
            <td>Component</td>
            <td>{{ $controlPanelData->components['value']}}</td>
        </tr> 

        <tr>
            <td>Enclosure</td>
            <td>{{ $controlPanelData->enclousres['value']}}</td>
        </tr>

    </tbody>
</table>
@if($addersData)
<table id="" class="table table-bordered">
    <thead>
        <tr>

            <th>Optional</th>
        </tr>

    </thead>
    <tbody>
        @foreach($addersData as $row)
        <tr>
            <td>{{$row->adder_list}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif