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
        @foreach($scpAdderData as $key=> $scpAdderRow)
        <tr>
            <!--<td>{{$key + 1}}</td>-->
            <td>{{$scpAdderRow->adder_list}}</td>
            <!--<td>{{$scpAdderRow->code}}</td>-->

            <td><input type="checkbox" name="adder_id" class="adder-checkbox" data-id="{{$scpAdderRow->id}}" value="{{$scpAdderRow->id}}"></td>

        </tr>
        @endforeach
    </tbody>
</table>
