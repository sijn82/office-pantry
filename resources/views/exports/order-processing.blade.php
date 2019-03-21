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
                <td></td>
                <td>{{ $route->company_name }}</td>
                <td>{{ $route->postcode }}</td>
                <td>{{ $route->address }}</td>
                <td>{{ $route->delivery_information }}</td>
                <td>{{ $route->fruit_crates }}</td>
                <td>{{ $route->fruit_boxes }}</td>

                @if ($route->milk == 'None for this week!')

                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>
                    <td> 0 </td>

                @else

                    <td>{{ $route->milk[0]->semi_skimmed_2l }}</td>
                    <td>{{ $route->milk[0]->skimmed_2l }}</td>
                    <td>{{ $route->milk[0]->whole_2l }}</td>
                    <td>{{ $route->milk[0]->semi_skimmed_1l }}</td>
                    <td>{{ $route->milk[0]->skimmed_1l }}</td>
                    <td>{{ $route->milk[0]->whole_1l }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_coconut }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_unsweetened_almond }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_almond }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_unsweetened_soya }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_soya }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_oat }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_rice }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_cashew }}</td>
                    <td>{{ $route->milk[0]->milk_1l_alt_lactose_free_semi }}</td>

                @endif

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
