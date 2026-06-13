<div class="tableResponsive">
    <table id="" class="">
        <thead>
            <tr>
                <th>Adder List</th>
                <th width="150">Select</th>
<th>Adder code</th>
            </tr>

        </thead>
        <tbody>
            @foreach($electricalListsData as $key=> $electricalList)
            <tr> 

                <td>{{$electricalList->adder_list}}</td>
                <td>
                    <label class="tblChk">
                        <input type="checkbox" name="adder_id" class="adder-checkbox" data-id="{{$electricalList->id}}" value="{{$electricalList->id}}">
                        <span class="checkmark"></span>
                    </label>
                </td>
<td>{{$electricalList->id}}</td>


            </tr>
            @endforeach
        </tbody>
    </table>
    <input type="button" value="Add Electrical Optional's" id="optional-button-add">
</div>
