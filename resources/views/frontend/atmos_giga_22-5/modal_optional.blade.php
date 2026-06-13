
<table id="" class="table table-bordered">
    <thead>
        <tr>
            <!--<th>S.No</th>-->
            <th>Title</th>

            <!--<th>Code#</th>-->
            <th>Select</th>

        </tr>

    </thead>
    <tbody>
        @foreach($atmosAdderData as $key=> $atmosAdderRow)
        <tr>
            <!--<td>{{$key + 1}}</td>-->
            <td>{{$atmosAdderRow->adder_list}}</td>
            <!--<td>{{$atmosAdderRow->code}}</td>-->

            <td><input type="checkbox" name="adder_id" class="adder-checkbox" data-id="{{$atmosAdderRow->id}}" value="{{$atmosAdderRow->id}}"></td>

        </tr>
        @endforeach
    </tbody>
</table>
