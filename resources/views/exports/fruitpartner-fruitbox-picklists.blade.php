<table>
    <thead>

      <tr>
          <th>Company Name</th>
          <th>Box Type</th>
          <th>Delivery Day</th>
          <th>Fruit Boxes</th>
          @if ($deliciously_red_apples_total === 0) @else <th>Deliciously Red Apples</th> @endif
          @if ($pink_lady_apples_total === 0) @else <th>Pink Lady Apples</th> @endif
          @if ($red_apples_total === 0) @else <th>Red Apples</th> @endif
          @if ($green_apples_total === 0) @else <th>Green Apples</th> @endif
          @if ($satsumas_total === 0) @else <th>Satsumas</th> @endif
          @if ($pears_total === 0) @else <th>Pears</th> @endif
          @if ($bananas_total === 0) @else <th>Bananas</th> @endif
          @if ($nectarines_total === 0) @else <th>Stoned Fruit</th> @endif
          @if ($limes_total === 0) @else <th>Limes</th> @endif
          @if ($lemons_total === 0) @else <th>Lemons</th> @endif
          @if ($grapes_total === 0) @else <th>Grapes</th> @endif
          @if ($seasonal_berries_total === 0) @else <th>Seasonal Berries</th> @endif
          @if ($oranges_total === 0) @else <th>Oranges</th> @endif
          @if ($cucumbers_total === 0) @else <th>Cucumbers</th> @endif
          @if ($mint_total === 0) @else <th>Mint</th> @endif
          @if ($organic_lemons_total === 0) @else <th>Organic Lemons</th> @endif
          @if ($kiwis_total === 0) @else <th>Kiwis</th> @endif
          @if ($grapefruits_total === 0) @else <th>Grapefruits</th> @endif
          @if ($avocados_total === 0) @else <th>Avocados</th> @endif
          @if ($root_ginger_total === 0) @else <th>Root Ginger</th> @endif
          @if ($order_changes) <th>Updated This Week?</th> @endif
      </tr>

    </thead>
    <tbody>

        {{ $totalFruitBoxes = 0 }}

        @foreach ($picklists as $picklist)

            {{ $totalFruitBoxes += $picklist->fruitbox_total }}

            <tr>
                <td>{{ $picklist->company_name }}</td>
                <td> {{ $picklist->type }} </td>
                <td>{{ $picklist->delivery_day }}</td>
                <td>{{ $picklist->fruitbox_total }}</td>
                @if ($deliciously_red_apples_total === 0) @elseif ($picklist->deliciously_red_apples === 0) <td></td> @else <td> {{ $picklist->deliciously_red_apples }} </td> @endif
                @if ($pink_lady_apples_total === 0) @elseif ($picklist->pink_lady_apples === 0) <td></td> @else <td> {{ $picklist->pink_lady_apples }} </td> @endif
                @if ($red_apples_total === 0) @elseif ($picklist->red_apples === 0) <td></td> @else <td> {{ $picklist->red_apples }} </td> @endif
                @if ($green_apples_total === 0) @elseif ($picklist->green_apples === 0) <td></td> @else <td> {{ $picklist->green_apples }} </td> @endif
                @if ($satsumas_total === 0) @elseif ($picklist->satsumas === 0) <td></td> @else <td> {{ $picklist->satsumas }} </td> @endif
                @if ($pears_total === 0) @elseif ($picklist->pears === 0) <td></td> @else <td> {{ $picklist->pears }} </td> @endif
                @if ($bananas_total === 0) @elseif ($picklist->bananas === 0) <td></td> @else <td> {{ $picklist->bananas }} </td> @endif
                @if ($nectarines_total === 0) @elseif ($picklist->nectarines === 0) <td></td> @else <td> {{ $picklist->nectarines }} </td> @endif
                @if ($limes_total === 0) @elseif ($picklist->limes === 0) <td></td> @else <td> {{ $picklist->limes }} </td> @endif
                @if ($lemons_total === 0) @elseif ($picklist->lemons === 0) <td></td> @else <td> {{ $picklist->lemons }} </td> @endif
                @if ($grapes_total === 0) @elseif ($picklist->grapes === 0) <td></td> @else <td> {{ $picklist->grapes }} </td> @endif
                @if ($seasonal_berries_total === 0) @elseif ($picklist->seasonal_berries === 0) <td></td> @else <td> {{ $picklist->seasonal_berries }} </td> @endif
                @if ($oranges_total === 0) @elseif ($picklist->oranges === 0) <td></td> @else <td> {{ $picklist->oranges }} </td> @endif
                @if ($cucumbers_total === 0) @elseif ($picklist->cucumbers === 0) <td></td> @else <td> {{ $picklist->cucumbers }} </td> @endif
                @if ($mint_total === 0) @elseif ($picklist->mint === 0) <td></td> @else <td> {{ $picklist->mint }} </td> @endif
                @if ($organic_lemons_total === 0) @elseif ($picklist->organic_lemons === 0) <td></td> @else <td> {{ $picklist->organic_lemons }} </td> @endif
                @if ($kiwis_total === 0) @elseif ($picklist->kiwis === 0) <td></td> @else <td> {{ $picklist->kiwis }} </td> @endif
                @if ($grapefruits_total === 0) @elseif ($picklist->grapefruits === 0) <td></td> @else <td> {{ $picklist->grapefruits }} </td> @endif
                @if ($avocados_total === 0) @elseif ($picklist->avocados === 0) <td></td> @else <td> {{ $picklist->avocados }} </td> @endif
                @if ($root_ginger_total === 0) @elseif ($picklist->root_ginger === 0) <td></td> @else <td> {{ $picklist->root_ginger }} </td> @endif
                @if ($picklist->order_changes) <td> Yes </td> @endif
            </tr>

        @endforeach

        <tr>
            <td></td>
            <td></td>
            <td> Totals: </td>
            <td>{{ $totalFruitBoxes }}</td>
            @if ($deliciously_red_apples_total === 0) @else <td>{{ $deliciously_red_apples_total }}</td> @endif
            @if ($pink_lady_apples_total === 0) @else <td>{{ $pink_lady_apples_total }}</td> @endif
            @if ($red_apples_total === 0) @else <td>{{ $red_apples_total }}</td> @endif
            @if ($green_apples_total === 0) @else <td>{{ $green_apples_total }}</td> @endif
            @if ($satsumas_total === 0) @else <td>{{ $satsumas_total }}</td> @endif
            @if ($pears_total === 0) @else <td>{{ $pears_total }}</td> @endif
            @if ($bananas_total === 0) @else <td>{{ $bananas_total }}</td> @endif
            @if ($nectarines_total === 0) @else <td>{{ $nectarines_total }}</td> @endif
            @if ($limes_total === 0) @else <td>{{ $limes_total }}</td> @endif
            @if ($lemons_total === 0) @else <td>{{ $lemons_total }}</td> @endif
            @if ($grapes_total === 0) @else <td>{{ $grapes_total }}</td> @endif
            @if ($seasonal_berries_total === 0) @else <td>{{ $seasonal_berries_total }}</td> @endif
            @if ($oranges_total === 0) @else <td>{{ $oranges_total }}</td> @endif
            @if ($cucumbers_total === 0) @else <td>{{ $cucumbers_total }}</td> @endif
            @if ($mint_total === 0) @else <td>{{ $mint_total }}</td> @endif
            @if ($organic_lemons_total === 0) @else <td>{{ $organic_lemons_total }}</td> @endif
            @if ($kiwis_total === 0) @else <td>{{ $kiwis_total }}</td> @endif
            @if ($grapefruits_total === 0) @else <td>{{ $grapefruits_total }}</td> @endif
            @if ($avocados_total === 0) @else <td>{{ $avocados_total }}</td> @endif
            @if ($root_ginger_total === 0) @else <td>{{ $root_ginger_total }}</td> @endif
        </tr>

    </tbody>
</table>
