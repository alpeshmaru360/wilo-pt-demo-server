
<table id="" class="table table-bordered">
    <thead>
        <tr>
            <!--<th>S.No</th>-->
            <th>Title</th>

            <th>Code#</th>
            <th>Select</th>

        </tr>

    </thead>
    <tbody>
        @foreach($electricalListsData as $key=> $electricalList)
        <tr>
            <!--<td>{{$key + 1}}</td>-->
            <td>{{$electricalList->adder_list}}</td>
            <td>{{$electricalList->code}}</td>

            <td><input type="checkbox" name="adder_id" class="adder-checkbox" data-id="{{$electricalList->id}}" value="{{$electricalList->id}}"></td>

        </tr>
        @endforeach
    </tbody>
</table>
