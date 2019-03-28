<table>
    <h3> Other Items To Order </h3>
    <thead>

      <tr>
          <th> Week Start </th>
          <th> Delivery Day </th>
          <th> Palleted By ? </th>
      </tr>
      <tr>
          <th>{{ $week_start }}</th>
          <th>{{ $out_for_delivery_day }}</th>
          <th colspan="2"> ........................ </th>
      </tr>
      <tr>
          <th colspan="4"></th>
      </tr>
      <tr>
          <th colspan="4"></th>
      </tr>
      <tr>
          <th> Company </th>
          <th> Product </th>
          <th> Quantity </th>
          <th> Route </th>
      </tr>
      <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
      </tr>
    </thead>
    <tbody>

        @php
            // dd($otherboxes);
        @endphp

    @foreach ($otherboxes as $otherbox)

        @php
            // dd($otherbox);
        @endphp
        <tr>
            <td> {{ $otherbox->company_name }} </td>
            <td> {{ $otherbox->name }} </td>
            <td> {{ $otherbox->quantity }} </td>
            <td> {{ $otherbox->route }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
