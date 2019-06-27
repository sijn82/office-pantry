<table>
    <thead>
      <tr>
          
          <th>Company Name</th>
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
          <th>Delivery Day</th>

      </tr>
    </thead>
    <tbody>

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

        @foreach ($picklists as $picklist)


        {{ $totalMilk2lSemiSkimmed += $picklist->milk_2l_semi_skimmed }}
        {{ $totalMilk2lSkimmed += $picklist->milk_2l_skimmed }}
        {{ $totalMilk2lWhole += $picklist->milk_2l_whole }}
        {{ $totalMilk1lSemiSkimmed += $picklist->milk_1l_semi_skimmed }}
        {{ $totalMilk1lSkimmed += $picklist->milk_1l_skimmed }}
        {{ $totalMilk1lWhole += $picklist->milk_1l_whole }}
        {{ $totalMilk1lAltCoconut += $picklist->milk_1l_alt_coconut }}
        {{ $totalMilk1lAltUnsweetenedAlmond += $picklist->milk_1l_alt_unsweetened_almond }}
        {{ $totalMilk1lAltAlmond += $picklist->milk_1l_alt_almond }}
        {{ $totalMilk1lAltUnsweetenedSoya += $picklist->milk_1l_alt_unsweetened_soya }}
        {{ $totalMilk1lAltSoya += $picklist->milk_1l_alt_soya }}
        {{ $totalMilk1lAltOat += $picklist->milk_1l_alt_oat }}
        {{ $totalMilk1lAltRice += $picklist->milk_1l_alt_rice }}
        {{ $totalMilk1lAltCashew += $picklist->milk_1l_alt_cashew }}
        {{ $totalMilk1lAltLactoseFreeSemi += $picklist->milk_1l_alt_lactose_free_semi }}

        <tr>
            <td>{{ $picklist->company_name }}</td>
            <td>{{ $picklist->milk_2l_semi_skimmed }}</td>
            <td>{{ $picklist->milk_2l_skimmed }}</td>
            <td>{{ $picklist->milk_2l_whole }}</td>
            <td>{{ $picklist->milk_1l_semi_skimmed }}</td>
            <td>{{ $picklist->milk_1l_skimmed }}</td>
            <td>{{ $picklist->milk_1l_whole }}</td>
            <td>{{ $picklist->milk_1l_alt_coconut }}</td>
            <td>{{ $picklist->milk_1l_alt_unsweetened_almond }}</td>
            <td>{{ $picklist->milk_1l_alt_almond }}</td>
            <td>{{ $picklist->milk_1l_alt_unsweetened_soya }}</td>
            <td>{{ $picklist->milk_1l_alt_soya }}</td>
            <td>{{ $picklist->milk_1l_alt_oat }}</td>
            <td>{{ $picklist->milk_1l_alt_rice }}</td>
            <td>{{ $picklist->milk_1l_alt_cashew }}</td>
            <td>{{ $picklist->milk_1l_alt_lactose_free_semi }}</td>
            <td>{{ $picklist->delivery_day }}</td>
        </tr>

        @endforeach

        <tr>

            <td></td>
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

        </tr>

    </tbody>
</table>
