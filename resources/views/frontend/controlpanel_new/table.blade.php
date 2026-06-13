<input type="hidden" id="cp-record-data" value="{{$cpRecordsData}}">
<div class="columns">
    <ul class="price" style="list-style: none;">
        <li class="header">{{$starter}}</li>
        <li class="grey">{{$application }}</li>
         <li class="grey">{{$noOfPump }} x {{ $power }} {{$starterCode}}</li>
         <li>Total Price: <b>{{number_format($price, 2)}}</b><span>$</span> </li>  
    </ul>
</div>
