@extends('welcome')
@section('snackboxes-multi-company')

<div>
<h3> Display Single Snackboxes Multiple Companies </h3>


@php
    $productNames = array_flip($product_list);
@endphp

    @foreach($chunks as $chunk)

    @php
     $g = 0;
     $h = 0;
     $i = 0;
    @endphp

    @foreach ($chunk as $key => $snackbox)

    @php (array) $group[$i] = $snackbox @endphp
    @php $i++ @endphp
    @endforeach


    <table>
        <thead>
            <tr>
                <th> Packed By: ..................... </th>
                <th></th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>Product Name</th>
                <th>Total</th>
                <th>@if (isset($group[0]->company_name)) {{ $group[0]->company_name }} @endif</th>
                <th>@if (isset($group[1]->company_name)) {{ $group[1]->company_name }} @endif</th>
                <th>@if (isset($group[2]->company_name)) {{ $group[2]->company_name }} @endif</th>
                <th>@if (isset($group[3]->company_name)) {{ $group[3]->company_name }} @endif</th>
                <th>Packed?</th>
            </tr>
        </thead>
        <tbody>
            @php
            if (isset($group[0])) { (array) $group[0] = $group[0]->getAttributes(); }
            if (isset($group[1])) { (array) $group[1] = $group[1]->getAttributes(); }
            if (isset($group[2])) { (array) $group[2] = $group[2]->getAttributes(); }
            if (isset($group[3])) { (array) $group[3] = $group[3]->getAttributes(); }
            @endphp

            @php while($h <= 4) {
                if (isset($group[0]) && is_array($group[0])) { $unwanted = array_shift($group[0]); };
                if (isset($group[1]) && is_array($group[1])) { $unwanted = array_shift($group[1]); };
                if (isset($group[2]) && is_array($group[2])) { $unwanted = array_shift($group[2]); };
                if (isset($group[3]) && is_array($group[3])) { $unwanted = array_shift($group[3]); };
                $h++;
            }; @endphp

            @php while($g < 2) {
                if (isset($group[0]) && is_array($group[0])) { $unwanted = array_pop($group[0]); };
                if (isset($group[1]) && is_array($group[1])) { $unwanted = array_pop($group[1]); };
                if (isset($group[2]) && is_array($group[2])) { $unwanted = array_pop($group[2]); };
                if (isset($group[3]) && is_array($group[3])) { $unwanted = array_pop($group[3]); };
                $g++;
            }; @endphp

            @foreach ($group[0] as $key => $snack )

                @if (isset($group[0]))
                @php $snackOne = array_shift($group[0]); @endphp
                @endif

                @if (isset($group[1]))
                @php $snackTwo = array_shift($group[1]); @endphp
                @endif

                @if (isset($group[2]))
                @php $snackThree = array_shift($group[2]); @endphp
                @endif

                @if (isset($group[3]))
                @php $snackFour = array_shift($group[3]); @endphp
                @endif

                @php
                    $snackTotal = 0;

                    (isset($snackOne)) ? $snackTotal += $snackOne : $snackTotal += ($snackOne = 0);
                    (isset($snackTwo)) ? $snackTotal += $snackTwo : $snackTotal += ($snackTwo = 0);
                    (isset($snackThree)) ? $snackTotal += $snackThree : $snackTotal += ($snackThree = 0);
                    (isset($snackFour)) ? $snackTotal += $snackFour : $snackTotal += ($snackFour = 0);
                @endphp

                @if ($snackTotal != 0)
                @php $keyProductName = array_search($key, $productNames) ? array_search($key, $productNames) : $key; @endphp
                    <tr>
                        <td>{{ $keyProductName }}</td>
                        <td>{{ $snackTotal }}</td>

                        <td>{{ $snackOne }}</td>
                        @php unset($snackOne); @endphp

                        <td>{{ $snackTwo }}</td>
                        @php unset($snackTwo); @endphp

                        <td>{{ $snackThree }}</td>
                        @php unset($snackThree); @endphp

                        <td>{{ $snackFour }}</td>
                        @php unset($snackFour); @endphp
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>
                @php unset($group); @endphp
                @endforeach
</div>
@endsection

@section('routing_assets')
<style>

  h3.route_header {
    margin_top: 30px;
    margin_bottom: 80px;
    padding_left: 50px;
    padding_right: 50px;
  }

</style>
@endsection
