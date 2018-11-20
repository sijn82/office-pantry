<table>
    {{-- <title>Berries On Route</title> --}}
    <h3>Berries On Route</h3>
    <thead>

      <tr>
          <th>Week Start</th>
          <th>Delivery Day</th>
          <th></th>
          <th>Packed By ?</th>
      </tr>
      <tr>
          <th>{{ $week_start }}</th>
          <th>{{ $route_day }}</th>
          <th colspan="3"> ........................ </th>
      </tr>
      <tr>
          <th colspan="5"></th>
      </tr>
      <tr>
          <th colspan="5"></th>
      </tr>
      <tr>
          <th>Route Name</th>
          <th>Company Name (Picklist Box Name)</th>
          <th>No. of Boxes</th>
          <th>No. of Berries</th>
          <th>Total</th>
      </tr>
      <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
      </tr>
    </thead>
    <tbody>

        @php
            // dd($routes);
        @endphp

    @foreach ($routes as $key => $route)

    @php
        $totalBerriesOnRoute = 0;
    @endphp
        <tr>
            <th>{{ $key }} {{-- Picklists have been grouped by 'assigned_to', creating this trait as the key --}}</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        @foreach ($route as $row => $picklist)

        @php
            //  dd($picklist); 
            $totalBerriesOnRoute += $picklist->fruit_boxes * $picklist->seasonal_berries;
        @endphp

        <tr>
            <td></td>
            <td>{{ $picklist->company_name }}</td>
            <td>{{ $picklist->fruit_boxes }}</td>
            <td>{{ $picklist->seasonal_berries }}</td>
            <td>{{ $picklist->fruit_boxes * $picklist->seasonal_berries }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th>Total On Route: {{ $totalBerriesOnRoute }}</th>
        </tr>
    @endforeach
    </tbody>
</table>
