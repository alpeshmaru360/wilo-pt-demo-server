 <table id="control-panel-table" class="table table-bordered">
          <thead>
          <th>Item Description</th>
           <th>Material Number</th>
            <th>wilo_article_number</th>
             <th>weight</th>
              <th>brand_code</th>
               <th>function_code</th>
                <th>range</th>
                 <th>unit_price</th>
                 <th>margin</th>
                 <th>Qty</th>
          </thead>
          <tbody>
              @foreach($cpRecordsData as $data)
            <tr>
              <td>{{$data['item_description']}}</td>
              <td>{{$data['material_number']}}</td>
              <td>{{$data['wilo_article_number']}}</td>
              <td>{{$data['weight']}}</td>
              <td>{{$data['brand_code']}}</td>
              <td>{{$data['function_code']}}</td>
              <td>{{$data['range']}}</td>
              <td>{{$data['unit_price']}}</td>
              <td>{{$data['margin']}}</td>
              <td>{{$data['item_description']}}</td>
              
            </tr>
            @endforeach
          </tbody>
        </table>
