<table>
    <thead>

      <tr>
          <th>Week Start</th>
          <th>Company Name</th>
          <th>Fruit Crate</th>
          <th>Fruit Boxes</th>
          <th>Deliciously Red Apples</th>
          <th>Pink Lady Apples</th>
          <th>Red Apples</th>
          <th>Green Apples</th>
          <th>Satsumas</th>
          <th>Pears</th>
          <th>Bananas</th>
          <th>Nectarines</th>
          <th>Limes</th>
          <th>Lemons</th>
          <th>Grapes</th>
          <th>Seasonal Berries</th>
          <th>Oranges</th>
          <th>Cucumbers</th>
          <th>Mint</th>
          <th>Assigned To</th>
          <th>Delivery Day</th>
          <th>Position On Route</th>
      </tr>

    </thead>
    <tbody>

        {{ $totalFruitCrates = 0 }}
        {{ $totalFruitBoxes = 0 }}
        {{ $totalDeliciouslyRedApples = 0 }}
        {{ $totalPinkLadyApples = 0 }}
        {{ $totalRed_apples = 0 }}
        {{ $totalGreen_apples = 0 }}
        {{ $totalSatsumas = 0 }}
        {{ $totalPears = 0 }}
        {{ $totalBananas = 0 }}
        {{ $totalNectarines = 0 }}
        {{ $totalLimes = 0 }}
        {{ $totalLemons = 0 }}
        {{ $totalGrapes = 0 }}
        {{ $totalSeasonalBerries = 0 }}
        {{ $totalOranges = 0 }}
        {{ $totalCucumbers = 0 }}
        {{ $totalMint = 0 }}
        {{ $standard_count = 0 }}

        @foreach ($picklists as $picklist)

        {{ $totalFruitCrates += $picklist->fruit_crates }}
        {{ $totalFruitBoxes += $picklist->fruit_boxes }}
        {{ $totalDeliciouslyRedApples += $picklist->deliciously_red_apples * $picklist->fruit_boxes }}
        {{ $totalPinkLadyApples += $picklist->pink_lady_apples * $picklist->fruit_boxes }}
        {{ $totalRed_apples += $picklist->red_apples * $picklist->fruit_boxes }}
        {{ $totalGreen_apples += $picklist->green_apples * $picklist->fruit_boxes }}
        {{ $totalSatsumas += $picklist->satsumas * $picklist->fruit_boxes }}
        {{ $totalPears += $picklist->pears * $picklist->fruit_boxes }}
        {{ $totalBananas += $picklist->bananas * $picklist->fruit_boxes }}
        {{ $totalNectarines += $picklist->nectarines * $picklist->fruit_boxes }}
        {{ $totalLimes += $picklist->limes * $picklist->fruit_boxes }}
        {{ $totalLemons += $picklist->lemons * $picklist->fruit_boxes }}
        {{ $totalGrapes += $picklist->grapes * $picklist->fruit_boxes }}
        {{ $totalSeasonalBerries += $picklist->seasonal_berries * $picklist->fruit_boxes }}
        {{ $totalOranges += $picklist->oranges * $picklist->fruit_boxes }}
        {{ $totalCucumbers += $picklist->cucumbers * $picklist->fruit_boxes }}
        {{ $totalMint += $picklist->mint * $picklist->fruit_boxes }}

        <?php
            if (
                    $picklist->red_apples == 6 && $picklist->green_apples == 3 && $picklist->satsumas == 10 
                    && $picklist->pears == 3 && $picklist->bananas == 16 && $picklist->nectarines == 12
             ){
                     $standard_count += $picklist->fruit_boxes; 
            } 
        ?>

        <tr>
            <td>{{ $picklist->week_start }}</td>
            <td>{{ $picklist->company_name }}</td>
            <td>{{ $picklist->fruit_crates }}</td>
            <td>{{ $picklist->fruit_boxes }}</td>
            <td>{{ $picklist->deliciously_red_apples }}</td>
            <td>{{ $picklist->pink_lady_apples }}</td>
            <td>{{ $picklist->red_apples }}</td>
            <td>{{ $picklist->green_apples }}</td>
            <td>{{ $picklist->satsumas }}</td>
            <td>{{ $picklist->pears }}</td>
            <td>{{ $picklist->bananas }}</td>
            <td>{{ $picklist->nectarines }}</td>
            <td>{{ $picklist->limes }}</td>
            <td>{{ $picklist->lemons }}</td>
            <td>{{ $picklist->grapes }}</td>
            <td>{{ $picklist->seasonal_berries }}</td>
            <td>{{ $picklist->oranges }}</td>
            <td>{{ $picklist->cucumbers }}</td>
            <td>{{ $picklist->mint }}</td>
            <td>{{ $picklist->assigned_to }}</td>
            <td>{{ $picklist->delivery_day }}</td>
            <td>{{ $picklist->position_on_route }}</td>
        </tr>

        @endforeach

        <tr>
            <td></td>
            <td>Standard Boxes on Route: {{ $standard_count }}</td>
            <td>{{ $totalFruitCrates }}</td>
            <td>{{ $totalFruitBoxes }}</td>
            <td>{{ $totalDeliciouslyRedApples }}</td>
            <td>{{ $totalPinkLadyApples }}</td>
            <td>{{ $totalRed_apples }}</td>
            <td>{{ $totalGreen_apples }}</td>
            <td>{{ $totalSatsumas }}</td>
            <td>{{ $totalPears }}</td>
            <td>{{ $totalBananas }}</td>
            <td>{{ $totalNectarines }}</td>
            <td>{{ $totalLimes }}</td>
            <td>{{ $totalLemons }}</td>
            <td>{{ $totalGrapes }}</td>
            <td>{{ $totalSeasonalBerries }}</td>
            <td>{{ $totalOranges }}</td>
            <td>{{ $totalCucumbers }}</td>
            <td>{{ $totalMint }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
