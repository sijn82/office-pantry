<table>
    {{-- <title>Berries On Route</title> --}}
    <h3>Berries On Route</h3>
    <thead>

      <tr>
          <th>Week Start</th>
          <th>Delivery Day</th>
      </tr>
      <tr>
          <th>{{ $week_start }}</th>
          <th>{{ $route_day }}</th>
      </tr>
      <tr>
          <th colspan="3"></th>
      </tr>
      <tr>
          <th colspan="3"></th>
      </tr>
      <tr>
          <th>Route Name</th>
          <th>Company Name (Picklist Box Name)</th>
          <th>No. of Berries</th>
      </tr>
      <tr>
          <th colspan="3"></th>
      </tr>
    </thead>
    <tbody>
        
        @php
            // dd($routes);
        @endphp
        
    @foreach ($routes as $key => $route)
    
    @php
        $totalBerries = 0;
    @endphp
        <tr>
            <th>{{ $key }} {{-- Picklists have been grouped by 'assigned_to', creating this trait as the key --}}</th>
            <td></td>
            <td></td>
        </tr>

        @foreach ($route as $picklist)

        @php
            $totalBerries += $picklist->seasonal_berries; 
            $value = 1;
        @endphp
        
        <tr>
            <td></td>
            @if ($value % 2 == 0)
                <td style="background-color: #93DA38";>{{ $picklist->company_name }}</td>
            @else
                <td>{{ $picklist->company_name }}</td>
             @endif
            <td>{{ $picklist->seasonal_berries }}</td>
            <td></td>
        </tr>
        @php $value++; @endphp
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <th>Total: {{ $totalBerries }}</th>
        </tr>
    @endforeach
    </tbody>
</table>
