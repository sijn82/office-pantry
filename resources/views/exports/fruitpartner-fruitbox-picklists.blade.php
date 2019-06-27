<table>
    <thead>

      <tr>
          <th>Company Name</th>
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
          <th>Organic Lemons</th>
          <th>Kiwis</th>
          <th>Grapefruits</th>
          <th>Avocados</th>
          <th>Root Ginger</th>
          <th>Delivery Day</th>

      </tr>

    </thead>
    <tbody>
        @php
             $totalFruitBoxes = 0; 
             $totalDeliciouslyRedApples = 0; 
             $totalPinkLadyApples = 0; 
             $totalRed_apples = 0; 
             $totalGreen_apples = 0; 
             $totalSatsumas = 0; 
             $totalPears = 0; 
             $totalBananas = 0; 
             $totalNectarines = 0; 
             $totalLimes = 0; 
             $totalLemons = 0; 
             $totalGrapes = 0; 
             $totalSeasonalBerries = 0; 
             $totalOranges = 0; 
             $totalCucumbers = 0; 
             $totalMint = 0; 
             $standard_count = 0; 
             $totalOrganicLemons = 0;
             $totalKiwis = 0;
             $totalGrapefruits = 0;
             $totalAvocados = 0;
             $totalRootGinger = 0;
        @endphp
        
        @foreach ($picklists as $picklist)

        @php
            
             $totalFruitBoxes += $picklist->fruitbox_total;
             $totalDeliciouslyRedApples += $picklist->deliciously_red_apples * $picklist->fruitbox_total;
             $totalPinkLadyApples += $picklist->pink_lady_apples * $picklist->fruitbox_total;
             $totalRed_apples += $picklist->red_apples * $picklist->fruitbox_total;
             $totalGreen_apples += $picklist->green_apples * $picklist->fruitbox_total;
             $totalSatsumas += $picklist->satsumas * $picklist->fruitbox_total;
             $totalPears += $picklist->pears * $picklist->fruitbox_total;
             $totalBananas += $picklist->bananas * $picklist->fruitbox_total;
             $totalNectarines += $picklist->nectarines * $picklist->fruitbox_total;
             $totalLimes += $picklist->limes * $picklist->fruitbox_total;
             $totalLemons += $picklist->lemons * $picklist->fruitbox_total;
             $totalGrapes += $picklist->grapes * $picklist->fruitbox_total;
             $totalSeasonalBerries += $picklist->seasonal_berries * $picklist->fruitbox_total;
             $totalOranges += $picklist->oranges * $picklist->fruitbox_total;
             $totalCucumbers += $picklist->cucumbers * $picklist->fruitbox_total;
             $totalMint += $picklist->mint * $picklist->fruitbox_total;
             
             $totalOrganicLemons += $picklist->organic_lemons * $picklist->fruitbox_total;
             $totalKiwis += $picklist->kiwis * $picklist->fruitbox_total;
             $totalGrapefruits += $picklist->grapefruits * $picklist->fruitbox_total;
             $totalAvocados += $picklist->avocados * $picklist->fruitbox_total;
             $totalRootGinger += $picklist->root_ginger * $picklist->fruitbox_total;
             

            if ($picklist->type == 'Standard') {
                $standard_count += $picklist->fruitbox_total;
            }
            
            // dd($picklist);
        @endphp
            
        <tr>
            <td>{{ $picklist->next_delivery }}</td>
            <td>{{ $picklist->name }}</td>
            <td>{{ $picklist->fruitbox_total }}</td>
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
            <td>{{ $picklist->organic_lemons }}</td>
            <td>{{ $picklist->kiwis }}</td>
            <td>{{ $picklist->grapefruits }}</td>
            <td>{{ $picklist->avocados }}</td>
            <td>{{ $picklist->root_ginger }}</td>
            <td>{{ $picklist->delivery_day }}</td>
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
            <td>{{ $totalOrganicLemons }}</td>
            <td>{{ $totalKiwis }}</td>
            <td>{{ $totalGrapefruits }}</td>
            <td>{{ $totalAvocados }}</td>
            <td>{{ $totalRootGinger }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
