<div class="tableResponsive">
    <table>
        <thead>
            <tr>

                <th>Adder List</th>
                <th >Select</th><th>code</th>
             
            </tr>
        </thead>
        <tbody>
            @foreach($mechanicalListsData as $key=> $mechanicalLists)
            <tr>

                <td>{{$mechanicalLists->adder_list}}</td>
               
                @if($mechanicalLists->code == 60 || $mechanicalLists->code == 61 || $mechanicalLists->code == 65 || $mechanicalLists->code == 66 || $mechanicalLists->code == 67) 
                <td>
                      @if($mechanicalLists->code == 60)
                    <input type="number" id="code60" placeholder="Enter Qty">
                    @endif
                    @if($mechanicalLists->code == 61)
                    <input type="number" id="code61" placeholder="Enter Qty">
                    @endif
                    @if($mechanicalLists->code == 65)
                    <select id="code65" name="code65">

                        <?php
                        $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('id', 'item_description', '65')
                                        ->whereNotNull('65')->where(65, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        ?>
                        <option value="">Select</option>
                        <?php
                        foreach ($arrayResult as $key => $val) {
                            ?>

                            <option value="{{$val['id']}}">{{$val['item_description']}}</option>


                        <?php }
                        ?>
                    </select>
                    @endif
                    @if($mechanicalLists->code == 66)
                    <select id="code66" name="code66">
                        <?php
                        $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('id', 'item_description', '66')
                                        ->whereNotNull('66')->where(66, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        ?>
                        <option value="">Select</option>
                        <?php
                        foreach ($arrayResult as $key => $val) {
                            ?>

                            <option value="{{$val['id']}}">{{$val['item_description']}}</option>


                        <?php }
                        ?>
                    </select>
                    @endif
                    @if($mechanicalLists->code == 67)
                    <select id="code67" name="67">
                        <?php
                        $cpRecords = DB::table('mechanical_adder_common_pressure_vessel')->select('id', 'item_description', '67')
                                        ->whereNotNull('67')->where(67, '!=', 0)->get();
                        $arrayResult = json_decode(json_encode($cpRecords), true);
                        ?>
                        <option value="">Select</option>
                        <?php
                        foreach ($arrayResult as $key => $val) {
                            ?>

                            <option value="{{$val['id']}}">{{$val['item_description']}}</option>


                        <?php }
                        ?>
                    </select>
                    @endif

                </td>
                @else
                <td>
                    <label class="tblChk">
                        <input type="checkbox" name="adder_mechnical_id" class="adder-checkbox" data-id="{{$mechanicalLists->code}}" value="{{$mechanicalLists->code}}">
                        <span class="checkmark"></span>
                    </label>
                </td>
                @endif
                 <td>{{$mechanicalLists->code}}</td>
            </tr>
            @endforeach


        </tbody>

    </table>
    <button id="add-mechanical-adder">
        Add Mechnical Optional's
    </button>
</div>