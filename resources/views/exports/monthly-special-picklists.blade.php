<table>
    <thead>

      <tr>
          <th> Week Start </th>
          <th>  Packed By ?  </th>
          <th>  </th>
      </tr>
      <tr>
          <th> {{ $week_start }} </th>
          <th colspan="2"> ........................ </th>
      </tr>
      <tr>
          <th colspan="3"></th>
      </tr>
      <tr>
          <th colspan="3"></th>
      </tr>
      <tr>
          <th> Company Name </th>
          <th> Product Name </th>
          <th> Quantity </th>
          <th> Delivery Day </th>
          <th> Route Name </th>
      </tr>
      <tr>
          <th>  </th>
          <th>  </th>
          <th>  </th>
          <th>  </th>
          <th>  </th>
      </tr>
    </thead>
    <tbody>

        @php
            // dd($monthly_specials_all)
        @endphp
        
        @if ($monthly_specials_all == 'None for this week.')
        
            <tr>
                <td> {{ $monthly_specials_all }} </td>
            </tr>
            
        @else 
        
            @foreach ($monthly_specials_all as $key => $company_order_group)

            <tr>
                <td> {{ $key }} </td>
                <td>  </td>
                <td>  </td>
                <td>  </td>
                <!-- <td>  </td> -->
            </tr>

                @foreach ($company_order_group as $order_box_type)
                    @foreach ($order_box_type as $monthly_special_item)

                        <tr>
                            <td> {{ $monthly_special_item->company_name }} </td>
                            <td> {{ $monthly_special_item->name }} </td>
                            <td> {{ $monthly_special_item->quantity }} </td>
                            <td> {{ $monthly_special_item->delivery_day }} </td>
                            <td> {{ $monthly_special_item->assigned_route_name }} </td>
                        </tr>

                    @endforeach
                @endforeach
            @endforeach
        @endif
    </tbody>
</table>
