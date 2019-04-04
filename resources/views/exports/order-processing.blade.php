<table>
    <thead>
        <tr>
              <th> Week Start </th>
              <th> Company Name </th>
              <th> Postcode </th>
              <th> Address </th>
              <th> Delivery Information </th>
              <th> Fruit Crate </th>
              <th> Fruit Boxes </th>
              <th> Milk 2l Semi-Skimmed </th>
              <th> Milk 2l Skimmed </th>
              <th> Milk 2l Whole </th>
              <th> Milk 1l Semi-Skimmed </th>
              <th> Milk 1l Skimmed </th>
              <th> Milk 1l Whole </th>
              <th> Milk 1l Alt Coconut </th>
              <th> Milk 1l Alt Unsweetened Almond </th>
              <th> Milk 1l Alt Almond </th>
              <th> Milk 1l Alt Unsweetened Soya </th>
              <th> Milk 1l Alt Soya </th>
              <th> Milk 1l Alt Oat </th>
              <th> Milk 1l Alt Rice </th>
              <th> Milk 1l Alt Cashew </th>
              <th> Milk 1l Alt Lactose Free Semi </th>
              <th> Drinks </th>
              <th> Boxes </th>
              <th> Other </th>
              <th> Assigned To </th>
              <th> Delivery Day </th>
              <th> Position On Route </th>
        </tr>
    </thead>
    <tbody>

    @php

         $totalFruitCrates = 0;
         $totalFruitBoxes = 0;
         $totalMilk2lSemiSkimmed = 0;
         $totalMilk2lSkimmed = 0;
         $totalMilk2lWhole = 0;
         $totalMilk1lSemiSkimmed = 0;
         $totalMilk1lSkimmed = 0;
         $totalMilk1lWhole = 0;
         $totalMilk1lAltCoconut = 0;
         $totalMilk1lAltUnsweetenedAlmond = 0;
         $totalMilk1lAltAlmond = 0;
         $totalMilk1lAltUnsweetenedSoya = 0;
         $totalMilk1lAltSoya = 0;
         $totalMilk1lAltOat = 0;
         $totalMilk1lAltRice = 0;
         $totalMilk1lAltCashew = 0;
         $totalMilk1lAltLactoseFreeSemi = 0;


    @endphp

        @foreach ($routes as $route)

        @php

             $totalFruitCrates += $route->fruit_crates;
             $totalFruitBoxes += $route->fruit_boxes;

            if ($route->milk == 'None for this week!') {

                $totalMilk2lSemiSkimmed += 0;
                $totalMilk2lSkimmed += 0;
                $totalMilk2lWhole += 0;
                $totalMilk1lSemiSkimmed += 0;
                $totalMilk1lSkimmed += 0;
                $totalMilk1lWhole += 0;
                $totalMilk1lAltCoconut += 0;
                $totalMilk1lAltUnsweetenedAlmond += 0;
                $totalMilk1lAltAlmond += 0;
                $totalMilk1lAltUnsweetenedSoya += 0;
                $totalMilk1lAltSoya += 0;
                $totalMilk1lAltOat += 0;
                $totalMilk1lAltRice += 0;
                $totalMilk1lAltCashew += 0;
                $totalMilk1lAltLactoseFreeSemi += 0;

            } else {

                $totalMilk2lSemiSkimmed += $route->milk[0]->semi_skimmed_2l;
                $totalMilk2lSkimmed += $route->milk[0]->skimmed_2l;
                $totalMilk2lWhole += $route->milk[0]->whole_2l;
                $totalMilk1lSemiSkimmed += $route->milk[0]->semi_skimmed_1l;
                $totalMilk1lSkimmed += $route->milk[0]->skimmed_1l;
                $totalMilk1lWhole += $route->milk[0]->whole_1l;
                $totalMilk1lAltCoconut += $route->milk[0]->milk_1l_alt_coconut;
                $totalMilk1lAltUnsweetenedAlmond += $route->milk[0]->milk_1l_alt_unsweetened_almond;
                $totalMilk1lAltAlmond += $route->milk[0]->milk_1l_alt_almond;
                $totalMilk1lAltUnsweetenedSoya += $route->milk[0]->milk_1l_alt_unsweetened_soya;
                $totalMilk1lAltSoya += $route->milk[0]->milk_1l_alt_soya;
                $totalMilk1lAltOat += $route->milk[0]->milk_1l_alt_oat;
                $totalMilk1lAltRice += $route->milk[0]->milk_1l_alt_rice;
                $totalMilk1lAltCashew += $route->milk[0]->milk_1l_alt_cashew;
                $totalMilk1lAltLactoseFreeSemi += $route->milk[0]->milk_1l_alt_lactose_free_semi;

            }

    @endphp
            <tr>
                <!-- <td>{{ $route->next_delivery_week_start }}</td> -->
                <td>{{ $route->week_start }}</td>
                <td>{{ $route->route_name }}</td>
                <td>{{ $route->postcode }}</td>
                <td>{{ $route->address }}</td>
                <td>{{ $route->delivery_information }}</td>
                
                @if ($route->fruit_crates === 0)
                    <td>  </td>
                @else
                    <td>{{ $route->fruit_crates }}</td>
                @endif
                
                @if ($route->fruit_boxes === 0)
                    <td>  </td>
                @else
                    <td>{{ $route->fruit_boxes }}</td>
                @endif

                @if ($route->milk == 'None for this week!')

                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>

                @else
                    @if ($route->milk[0]->semi_skimmed_2l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->semi_skimmed_2l }}</td>
                    @endif
                    @if ($route->milk[0]->skimmed_2l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->skimmed_2l }}</td>
                    @endif
                    @if ($route->milk[0]->whole_2l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->whole_2l }}</td>
                    @endif
                    @if ($route->milk[0]->semi_skimmed_1l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->semi_skimmed_1l }}</td>
                    @endif
                    @if ($route->milk[0]->skimmed_1l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->skimmed_1l }}</td>
                    @endif
                    @if ($route->milk[0]->whole_1l === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->whole_1l }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_coconut === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_coconut }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_unsweetened_almond === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_unsweetened_almond }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_almond === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_almond }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_unsweetened_soya === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_unsweetened_soya }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_soya === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_soya }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_oat === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_oat }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_rice === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_rice }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_cashew === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_cashew }}</td>
                    @endif
                    @if ($route->milk[0]->milk_1l_alt_lactose_free_semi === 0)
                        <td>  </td>
                    @else
                        <td>{{ $route->milk[0]->milk_1l_alt_lactose_free_semi }}</td>
                    @endif

                @endif
                @if ($route->drinks === 0)
                    <td>  </td>
                @else
                    <td>{{ $route->drinks }}</td>
                @endif
                @if ($route->snacks === 0)
                    <td>  </td>
                @else
                    <td>{{ $route->snacks }}</td>
                @endif
                @if ($route->other === 'None for this week!')
                    <td>  </td>
                @else
                    <td>{{ $route->other }}</td>
                @endif
                
                <td>{{ $route->assigned_route_name }}</td>
                <td>{{ $route->delivery_day }}</td>
                <td>{{ $route->position_on_route }}</td>
            </tr>


        @endforeach
        <tr>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
        </tr>
        <tr>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td> Route Totals: </td>
            @if ($totalFruitCrates === 0)
                <td>  </td>
            @else
                <td>{{ $totalFruitCrates }}</td>
            @endif
            @if ($totalFruitBoxes === 0)
                <td>  </td>
            @else
                <td>{{ $totalFruitBoxes }}</td>
            @endif
            @if ($totalMilk2lSemiSkimmed === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk2lSemiSkimmed }}</td>
            @endif
            @if ($totalMilk2lSkimmed === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk2lSkimmed }}</td>
            @endif
            @if ($totalMilk2lWhole === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk2lWhole }}</td>
            @endif
            @if ($totalMilk1lSemiSkimmed === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lSemiSkimmed }}</td>
            @endif
            @if ($totalMilk1lSkimmed === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lSkimmed }}</td>
            @endif
            @if ($totalMilk1lWhole === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lWhole }}</td>
            @endif
            @if ($totalMilk1lAltCoconut === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltCoconut }}</td>
            @endif
            @if ($totalMilk1lAltUnsweetenedAlmond === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltUnsweetenedAlmond }}</td>
            @endif
            @if ($totalMilk1lAltAlmond === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltAlmond }}</td>
            @endif
            @if ($totalMilk1lAltUnsweetenedSoya === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltUnsweetenedSoya }}</td>
            @endif
            @if ($totalMilk1lAltSoya === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltSoya }}</td>
            @endif
            @if ($totalMilk1lAltOat === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltOat }}</td>
            @endif
            @if ($totalMilk1lAltRice === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltRice }}</td>
            @endif
            @if ($totalMilk1lAltCashew === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltCashew }}</td>
            @endif
            @if ($totalMilk1lAltLactoseFreeSemi === 0)
                <td>  </td>
            @else
                <td>{{ $totalMilk1lAltLactoseFreeSemi }}</td>
            @endif
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
            <td>  </td>
        </tr>

    </tbody>
</table>
