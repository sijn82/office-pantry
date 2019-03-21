<table>
    <thead>
      <tr>
          <th>Week Start</th>
          <th>Company Name</th>
          <th>Postcode</th>
          <th>Address</th>
          <th>Delivery Information</th>
          <th>Fruit Crate</th>
          <th>Fruit Boxes</th>
          <th>Milk 2l Semi-Skimmed</th>
          <th>Milk 2l Skimmed</th>
          <th>Milk 2l Whole</th>
          <th>Milk 1l Semi-Skimmed</th>
          <th>Milk 1l Skimmed</th>
          <th>Milk 1l Whole</th>
          <th>Milk 1l Alt Coconut</th>
          <th>Milk 1l Alt Unsweetened Almond</th>
          <th>Milk 1l Alt Almond</th>
          <th>Milk 1l Alt Unsweetened Soya</th>
          <th>Milk 1l Alt Soya</th>
          <th>Milk 1l Alt Oat</th>
          <th>Milk 1l Alt Rice</th>
          <th>Milk 1l Alt Cashew</th>
          <th>Milk 1l Alt Lactose Free Semi</th>
          <th>Drinks</th>
          <th>Boxes</th>
          <th>Other</th>
          <th>Assigned To</th>
          <th>Delivery Day</th>
          <th>Position On Route</th>
      </tr>
    </thead>
    <tbody>

        {{ $totalFruitCrates = 0 }}
        {{ $totalFruitBoxes = 0 }}
        {{ $totalMilk2lSemiSkimmed = 0 }}
        {{ $totalMilk2lSkimmed = 0 }}
        {{ $totalMilk2lWhole = 0 }}
        {{ $totalMilk1lSemiSkimmed = 0 }}
        {{ $totalMilk1lSkimmed = 0 }}
        {{ $totalMilk1lWhole = 0 }}
        {{ $totalMilk1lAltCoconut = 0 }}
        {{ $totalMilk1lAltUnsweetenedAlmond = 0 }}
        {{ $totalMilk1lAltAlmond = 0 }}
        {{ $totalMilk1lAltUnsweetenedSoya = 0 }}
        {{ $totalMilk1lAltSoya = 0 }}
        {{ $totalMilk1lAltOat = 0 }}
        {{ $totalMilk1lAltRice = 0 }}
        {{ $totalMilk1lAltCashew = 0 }}
        {{ $totalMilk1lAltLactoseFreeSemi = 0 }}

        @foreach ($routes as $route)

        {{ $totalFruitCrates += $route->fruit_crates }}
        {{ $totalFruitBoxes += $route->fruit_boxes }}
        {{ $totalMilk2lSemiSkimmed += $route->milk_2l_semi_skimmed }}
        {{ $totalMilk2lSkimmed += $route->milk_2l_skimmed }}
        {{ $totalMilk2lWhole += $route->milk_2l_whole }}
        {{ $totalMilk1lSemiSkimmed += $route->milk_1l_semi_skimmed }}
        {{ $totalMilk1lSkimmed += $route->milk_1l_skimmed }}
        {{ $totalMilk1lWhole += $route->milk_1l_whole }}
        {{ $totalMilk1lAltCoconut += $route->milk_1l_alt_coconut }}
        {{ $totalMilk1lAltUnsweetenedAlmond += $route->milk_1l_alt_unsweetened_almond }}
        {{ $totalMilk1lAltAlmond += $route->milk_1l_alt_almond }}
        {{ $totalMilk1lAltUnsweetenedSoya += $route->milk_1l_alt_unsweetened_soya }}
        {{ $totalMilk1lAltSoya += $route->milk_1l_alt_soya }}
        {{ $totalMilk1lAltOat += $route->milk_1l_alt_oat }}
        {{ $totalMilk1lAltRice += $route->milk_1l_alt_rice }}
        {{ $totalMilk1lAltCashew += $route->milk_1l_alt_cashew }}
        {{ $totalMilk1lAltLactoseFreeSemi += $route->milk_1l_alt_lactose_free_semi }}

        <tr>
            <td>{{ $route->week_start }}</td>
            <td>{{ $route->company_name }}</td>
            <td>{{ $route->postcode }}</td>
            <td>{{ $route->address }}</td>
            <td>{{ $route->delivery_information }}</td>
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
            <td>{{ $route->other }}</td>
            <td>{{ $route->assigned_to }}</td>
            <td>{{ $route->delivery_day }}</td>
            <td>{{ $route->position_on_route }}</td>
        </tr>

        @endforeach

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $totalFruitCrates }}</td>
            <td>{{ $totalFruitBoxes }}</td>
            <td>{{ $totalMilk2lSemiSkimmed }}</td>
            <td>{{ $totalMilk2lSkimmed }}</td>
            <td>{{ $totalMilk2lWhole }}</td>
            <td>{{ $totalMilk1lSemiSkimmed }}</td>
            <td>{{ $totalMilk1lSkimmed }}</td>
            <td>{{ $totalMilk1lWhole }}</td>
            <td>{{ $totalMilk1lAltCoconut }}</td>
            <td>{{ $totalMilk1lAltUnsweetenedAlmond }}</td>
            <td>{{ $totalMilk1lAltAlmond }}</td>
            <td>{{ $totalMilk1lAltUnsweetenedSoya }}</td>
            <td>{{ $totalMilk1lAltSoya }}</td>
            <td>{{ $totalMilk1lAltOat }}</td>
            <td>{{ $totalMilk1lAltRice }}</td>
            <td>{{ $totalMilk1lAltCashew }}</td>
            <td>{{ $totalMilk1lAltLactoseFreeSemi }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
