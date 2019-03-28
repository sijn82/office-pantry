<table>
    <h3> Other Items To Order </h3>
    <thead>

      <tr>
          <th> Ordered By ? </th>
          <th> Week Start </th>
      </tr>
      <tr>
          <th> ........................ </th>
          <th> {{ $week_start }} </th>
      </tr>
      <tr>
          <th> Product </th>
          <th> Quantity (Weekly Total) </th>
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
