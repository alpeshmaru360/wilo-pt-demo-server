<table id="" class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Select</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scpvAdderData as $key=> $scpvAdderRow)
        <tr>
            <td>{{$scpvAdderRow->adder_list}}</td>
            <td><input type="checkbox" name="adder_id" class="adder-checkbox" data-id="{{$scpvAdderRow->id}}" value="{{$scpvAdderRow->id}}"></td>
        </tr>
        @endforeach
    </tbody>
</table>
