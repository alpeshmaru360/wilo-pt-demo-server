
{{--<input type="hidden" id="booster-record-data" value="{{$boosterData}}">--}}
{{--@dd($boosterData)--}}
<div class="columns">
    <ul class="price" style="list-style: none;">
        <li class="header">Pump Model * Price: {{$boosterData['pump_model'] }} * {{$boosterData['pump_unit_price'] }}</li>
        <li class="header">Manifold: {{$boosterData['manifold']}}</li>
        <li class="header">No of Pumps: {{$boosterData['no_of_pumps'] }} </li>
        <li class="header">Power: {{$boosterData['power'] }} </li>
        <li class="header">Voltage: {{$boosterData['voltage'] }} </li>
        <li class="header">Starter Type: {{$boosterData['starter_type']}}</li>
        <li class="header">System Pressure: {{$boosterData['pressure'] }} </li>
{{--        <li class="header">Control Panel Price: {{number_format($boosterData['cp_price'],2) }} </li>--}}
{{--        <li class="header">Base Frame Size: {{$boosterData['base_frame_size'] }} </li>--}}
{{--        <li class="header">Pressure Monitor: {{number_format($boosterData['power_monitor_flag_price'],2) }} </li>--}}
{{--        <li class="header">Cable Size * Cable Length * Cable Price: {{$boosterData['cable_size'] }} * {{$boosterData['Cablelength'] }} * {{number_format($boosterData['cablePrice'],2) }} </li>--}}
{{--        <li class="header">Standard Component Price: {{number_format($boosterData['standard_component_price'] ,2) }}</li>--}}
{{--        <li class="header">Mechanical Component Price: {{number_format($boosterData['mechanical_system_price'],2) }} </li>--}}
{{--        <li class="header">Electrical Code Price: {{number_format($boosterData['code_price'],2) }} </li>--}}
{{--        <li class="header">Mechanical Code Price: {{number_format($boosterData['mechanical_code_price'],2) }} </li>--}}
        <li>Booster Price: <b>{{number_format($boosterData['booster_price'], 2)}}</b><span>$</span> </li>
    </ul>
</div>
