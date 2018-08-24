@extends('welcome')
@section('display-routes')

<h3 class="route-header"> Display Routes </h3>

<table>
    <thead>
      <tr>
          <th>Week Start</th>
          <th>Company Name</th>
          <th>Post Code</th>
          <th>Address</th>
          <!-- <th>Delivery Information</th> -->
          <th>Fruit Crates</th>
          <th>Fruit Boxes</th>
          <th>Milk 2l Semi Skimmed</th>
          <th>Milk 2l Skimmed</th>
          <th>Milk 2l Whole</th>
          <th>Milk 1l Semi Skimmed</th>
          <th>Milk 1l Skimmed</th>
          <th>Milk 1l Whole</th>
          <th>Milk 1l Coconut</th>
          <th>Milk 1l Unsweetened Almond</th>
          <th>Milk 1l Almond</th>
          <th>Milk 1l Unsweetened Soya</th>
          <th>Milk 1l Soya</th>
          <th>Milk 1l Oat</th>
          <th>Milk 1l Rice</th>
          <th>Milk 1l Cashew</th>
          <th>Milk 1l Lactose Free Semi</th>
          <th>Drinks</th>
          <th>Snacks</th>
          <th>Assigned To</th>
          <th>Delivery Day</th>
          <th>Position On Route</th>
      </tr>
    </thead>
    <tbody>
    @foreach ($routes as $route)

        <tr>
            <td>{{ $route->week_start }}</td>
            <td>{{ $route->company_name }}</td>
            <td>{{ $route->postcode }}</td>
            <td>{{ $route->address }}</td>
            <!-- <td>{{ $route->delivery_information }}</td> -->
            <td>{{ $route->fruit_crates }}</td>
            <td>{{ $route->fruit_boxes }}</td>
            <td>{{ $route->milk_2l_semi_skimmed }}</td>
            <td>{{ $route->milk_2l_skimmed }}</td>
            <td>{{ $route->milk_2l_whole }}</td>
            <td>{{ $route->milk_1l_semi_skimmed }}</td>
            <td>{{ $route->milk_1l_skimmed }}</td>
            <td>{{ $route->milk_1l_whole }}</td>
            <td>{{ $route->milk_1l_alt_coconut }}</td>
            <td>{{ $route->milk_1l_alt_unsweetened_almond }}</td>
            <td>{{ $route->milk_1l_alt_almond }}</td>
            <td>{{ $route->milk_1l_alt_unsweetened_soya }}</td>
            <td>{{ $route->milk_1l_alt_soya }}</td>
            <td>{{ $route->milk_1l_alt_oat }}</td>
            <td>{{ $route->milk_1l_alt_rice }}</td>
            <td>{{ $route->milk_1l_alt_cashew }}</td>
            <td>{{ $route->milk_1l_alt_lactose_free_semi }}</td>
            <td>{{ $route->drinks }}</td>
            <td>{{ $route->snacks }}</td>
            <td>{{ $route->assigned_to }}</td>
            <td>{{ $route->delivery_day }}</td>
            <td>{{ $route->position_on_route }}</td>
        </tr>

    @endforeach
    </tbody>
</table>

<table>
    <thead>
      <tr>
          <th>Company Name</th>
          <th>Delivery Information</th>
      </tr>
      <tbody>
        @foreach ($routes as $route)
          @if ($route->assigned_to == 'Thames Valley' && $route->delivery_day == 'Monday' && $route->week_start == '90718')
        <tr>
          <td>{{ $route->company_name }}</td>
          <td class="delivery-info">{{ $route->delivery_information }}</td>
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>

@endsection

@section('routing-display-assets')
<style>

  table {
    margin: 10px 30px;
  }

  table thead tr th {
    font-size: 0.6rem;
    border-right: 1px solid #636b6f;
    border-bottom: 1px solid #636b6f;
  }
  table tbody tr td {
    font-size: 0.6rem;
    font-weight: 400;
    color: #212529;
    border-bottom: 1px solid #636b6f;
    border-right: 1px solid #636b6f;
  }
  tr:nth-child(even) {
    background: rgba(99, 107, 111, 0.5);
  }
  table tbody tr:nth-child(even) td {
    color: #fff;
  }
  .delivery-info {
    text-align:left;
    padding-left: 20px;
  }
  #assigned_route-dropdown {
    margin-bottom: 60px;
  }
  .flex-center {
    display: block;
  }
  h3.route-header {
    margin-top: 30px;
    margin-bottom: 10px;
    padding-left: 50px;
    padding-right: 50px;
  }

</style>
@endsection
