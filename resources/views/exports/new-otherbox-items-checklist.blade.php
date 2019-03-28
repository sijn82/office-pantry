<table>
    <h3> Other Items To Order </h3>
    <thead>

      <tr>
          <th> Week Start </th>
          <th> Delivery Day </th>
          <th></th>
          <th> Ordered By ? </th>
      </tr>
      <tr>
          <th>{{ $week_start }}</th>
          <th>{{ $out_for_delivery_day }}</th>
          <th colspan="3"> ........................ </th>
      </tr>
      <tr>
          <th colspan="5"></th>
      </tr>
      <tr>
          <th colspan="5"></th>
      </tr>
      <tr>
          <th> Product </th>
          <th> Quantity (Total Per Day) </th>
      </tr>
      <tr>
          <th></th>
          <th></th>
      </tr>
    </thead>
    <tbody>

        @php
            // dd($products);
        @endphp

    @foreach ($products as $product)

        @php
            // dd($product->name);
        @endphp
        <tr>
            <td> {{ $product->name }} </td>
            <td> {{ $product->quantity }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
