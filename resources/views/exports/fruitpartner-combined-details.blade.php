<table>
    {{ $totalFruitBoxes = 0 }}
    
    <thead>
      <tr>
          <th>Company Name</th>
          <th>Postcode</th>
          <th>Address</th>
          <th>Delivery Information</th>
          <th>Fruit Boxes</th>
          @if ($semi_skimmed_2l_total === 0) @else <th>Milk 2l Semi-Skimmed</th> @endif
          @if ($skimmed_2l_total === 0) @else <th>Milk 2l Skimmed</th> @endif
          @if ($whole_2l_total === 0) @else <th>Milk 2l Whole</th> @endif
          @if ($semi_skimmed_1l_total === 0) @else <th>Milk 1l Semi-Skimmed</th> @endif
          @if ($skimmed_1l_total === 0) @else <th>Milk 1l Skimmed</th> @endif
          @if ($whole_1l_total === 0) @else <th>Milk 1l Whole</th> @endif
          @if ($organic_semi_skimmed_2l_total === 0) @else <th>Organic Semi Skimmed 1l</th> @endif
          @if ($organic_skimmed_2l_total === 0) @else <th>Organic Skimmed 1l</th> @endif
          @if ($organic_whole_2l_total === 0) @else <th>Organic Whole 1l</th> @endif
          @if ($organic_semi_skimmed_1l_total === 0) @else <th>Organic Semi Skimmed 2l</th> @endif
          @if ($organic_skimmed_1l_total === 0) @else <th>Organic Skimmed 2l</th> @endif
          @if ($organic_whole_1l_total === 0) @else <th>Organic Whole 2l</th> @endif
          @if ($alt_coconut_total === 0) @else <th>Milk 1l Alt Coconut</th> @endif
          @if ($alt_unsweetened_almond_total === 0) @else <th>Milk 1l Alt Unsweetened Almond</th> @endif
          @if ($alt_almond_total === 0) @else <th>Milk 1l Alt Almond</th> @endif
          @if ($alt_unsweetened_soya_total === 0) @else <th>Milk 1l Alt Unsweetened Soya</th> @endif
          @if ($alt_soya_total === 0) @else <th>Milk 1l Alt Soya</th> @endif
          @if ($alt_oat_total === 0) @else <th>Milk 1l Alt Oat</th> @endif
          @if ($alt_rice_total === 0) @else <th>Milk 1l Alt Rice</th> @endif
          @if ($alt_cashew_total === 0) @else <th>Milk 1l Alt Cashew</th> @endif
          @if ($alt_lactose_free_semi_total === 0) @else <th>Milk 1l Alt Lactose Free Semi</th> @endif
          <th>Delivery Day</th>
      </tr>
    </thead>
    
    <tbody>

        @foreach ($deliveries as $delivery)

        {{ $totalFruitBoxes += $delivery->fruitbox_total }}

        <tr>
            <td>{{ $delivery->company_details_route_name }}</td>
            <td>{{ $delivery->company_details_postcode }}</td>
            <td>{{ $delivery->company_details_summary_address }}</td>
            <td>{{ $delivery->company_details_delivery_information }}</td>
            <td>{{ $delivery->fruitbox_total }}</td>
            @if ($semi_skimmed_2l_total === 0) @elseif ($delivery->semi_skimmed_2l === 0) <td></td> @else <td>{{ $delivery->semi_skimmed_2l }}</td> @endif
            @if ($skimmed_2l_total === 0) @elseif ($delivery->skimmed_2l === 0) <td></td> @else <td>{{ $delivery->skimmed_2l }}</td> @endif
            @if ($whole_2l_total === 0) @elseif ($delivery->whole_2l === 0) <td></td> @else <td>{{ $delivery->whole_2l }}</td> @endif
            @if ($semi_skimmed_1l_total === 0) @elseif ($delivery->semi_skimmed_1l === 0) <td></td> @else <td>{{ $delivery->semi_skimmed_1l }}</td> @endif
            @if ($skimmed_1l_total === 0) @elseif ($delivery->skimmed_1l === 0) <td></td> @else <td>{{ $delivery->skimmed_1l }}</td> @endif
            @if ($whole_1l_total === 0) @elseif ($delivery->whole_1l === 0) <td></td> @else <td>{{ $delivery->whole_1l }}</td> @endif
            @if ($organic_semi_skimmed_2l_total === 0) @elseif ($delivery->organic_semi_skimmed_2l === 0) <td></td> @else <td>{{ $delivery->organic_semi_skimmed_2l }}</td> @endif
            @if ($organic_skimmed_2l_total === 0) @elseif ($delivery->organic_skimmed_2l === 0) <td></td> @else <td>{{ $delivery->organic_skimmed_2l }}</td> @endif
            @if ($organic_whole_2l_total === 0) @elseif ($delivery->organic_whole_2l === 0) <td></td> @else <td>{{ $delivery->organic_whole_2l }}</td> @endif
            @if ($organic_semi_skimmed_1l_total === 0) @elseif ($delivery->organic_semi_skimmed_1l === 0) <td></td> @else <td>{{ $delivery->organic_semi_skimmed_1l }}</td> @endif
            @if ($organic_skimmed_1l_total === 0) @elseif ($delivery->organic_skimmed_1l === 0) <td></td> @else <td>{{ $delivery->organic_skimmed_1l }}</td> @endif
            @if ($organic_whole_1l_total === 0) @elseif ($delivery->organic_whole_1l === 0) <td></td> @else <td>{{ $delivery->organic_whole_1l }}</td> @endif
            @if ($alt_coconut_total === 0) @elseif ($delivery->milk_1l_alt_coconut === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_coconut }}</td> @endif
            @if ($alt_unsweetened_almond_total === 0) @elseif ($delivery->milk_1l_alt_unsweetened_almond === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_unsweetened_almond }}</td> @endif
            @if ($alt_almond_total === 0) @elseif ($delivery->milk_1l_alt_almond === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_almond }}</td> @endif
            @if ($alt_unsweetened_soya_total === 0) @elseif ($delivery->milk_1l_alt_unsweetened_soya === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_unsweetened_soya }}</td> @endif
            @if ($alt_soya_total === 0) @elseif ($delivery->milk_1l_alt_soya === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_soya }}</td> @endif
            @if ($alt_oat_total === 0) @elseif ($delivery->milk_1l_alt_oat === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_oat }}</td> @endif
            @if ($alt_rice_total === 0) @elseif ($delivery->milk_1l_alt_rice === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_rice }}</td> @endif
            @if ($alt_cashew_total === 0) @elseif ($delivery->milk_1l_alt_cashew === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_cashew }}</td> @endif
            @if ($alt_lactose_free_semi_total === 0) @elseif ($delivery->milk_1l_alt_lactose_free_semi === 0) <td></td> @else <td>{{ $delivery->milk_1l_alt_lactose_free_semi }}</td> @endif
            <td>{{ $delivery->delivery_day }}</td>
        </tr>

        @endforeach

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Totals:</td>
            <td>{{ $totalFruitBoxes }}</td>
            @if ($semi_skimmed_2l_total === 0) @else <td>{{ $semi_skimmed_2l_total }}</td> @endif
            @if ($skimmed_2l_total === 0) @else <td>{{ $skimmed_2l_total }}</td> @endif
            @if ($whole_2l_total === 0) @else <td>{{ $whole_2l_total }}</td> @endif
            @if ($semi_skimmed_1l_total === 0) @else <td>{{ $semi_skimmed_1l_total }}</td> @endif
            @if ($skimmed_1l_total === 0) @else <td>{{ $skimmed_1l_total }}</td> @endif
            @if ($whole_1l_total === 0) @else <td>{{ $whole_1l_total }}</td> @endif
            @if ($organic_semi_skimmed_2l_total === 0) @else <td>{{ $organic_semi_skimmed_2l_total }}</td> @endif
            @if ($organic_skimmed_2l_total === 0) @else <td>{{ $organic_skimmed_2l_total }}</td> @endif
            @if ($organic_whole_2l_total === 0) @else <td>{{ $organic_whole_2l_total }}</td> @endif
            @if ($organic_semi_skimmed_1l_total === 0) @else <td>{{ $organic_semi_skimmed_1l_total }}</td> @endif
            @if ($organic_skimmed_1l_total === 0) @else <td>{{ $organic_skimmed_1l_total }}</td> @endif
            @if ($organic_whole_1l_total === 0) @else <td>{{ $organic_whole_1l_total }}</td> @endif
            @if ($alt_coconut_total === 0) @else <td>{{ $alt_coconut_total }}</td> @endif
            @if ($alt_unsweetened_almond_total === 0) @else <td>{{ $alt_unsweetened_almond_total }}</td> @endif
            @if ($alt_almond_total === 0) @else <td>{{ $alt_almond_total }}</td> @endif
            @if ($alt_unsweetened_soya_total === 0) @else <td>{{ $alt_unsweetened_soya_total }}</td> @endif
            @if ($alt_soya_total === 0) @else <td>{{ $alt_soya_total }}</td> @endif
            @if ($alt_oat_total === 0) @else <td>{{ $alt_oat_total }}</td> @endif
            @if ($alt_rice_total === 0) @else <td>{{ $alt_rice_total }}</td> @endif
            @if ($alt_cashew_total === 0) @else <td>{{ $alt_cashew_total }}</td> @endif
            @if ($alt_lactose_free_semi_total === 0) @else <td>{{ $alt_lactose_free_semi_total }}</td> @endif
            <td></td>

        </tr>

    </tbody>

</table>
