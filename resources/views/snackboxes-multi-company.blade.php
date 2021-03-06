@extends('welcome')
@section('snackboxes-multi-company')

<div>
<h3> Display Single Snackboxes Multiple Companies </h3>




@foreach($chunks as $chunk)

    @php
        $productNames = $product_list;
    @endphp

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
                <th>@if (isset($group[0][2])) {{ $group[0][2] }} @endif</th>
                <th>@if (isset($group[1][2])) {{ $group[1][2] }} @endif</th>
                <th>@if (isset($group[2][2])) {{ $group[2][2] }} @endif</th>
                <th>@if (isset($group[3][2])) {{ $group[3][2] }} @endif</th>
                <th>Packed?</th>
            </tr>
        </thead>
        <tbody>

            @php while($h < 1) {
                if (isset($group[0]) && is_array($group[0])) { $group[0] = array_slice($group[0], 3); };
                if (isset($group[1]) && is_array($group[1])) { $group[1] = array_slice($group[1], 3); };
                if (isset($group[2]) && is_array($group[2])) { $group[2] = array_slice($group[2], 3); };
                if (isset($group[3]) && is_array($group[3])) { $group[3] = array_slice($group[3], 3); };
                $h++;
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

                @php $keyProductName = array_shift($productNames) @endphp
                @php
                    $snackTotal = 0;

                    (!empty($snackOne)) ? $snackTotal += $snackOne : $snackTotal += ($snackOne = 0);
                    (!empty($snackTwo)) ? $snackTotal += $snackTwo : $snackTotal += ($snackTwo = 0);
                    (!empty($snackThree)) ? $snackTotal += $snackThree : $snackTotal += ($snackThree = 0);
                    (!empty($snackFour)) ? $snackTotal += $snackFour : $snackTotal += ($snackFour = 0);

                @endphp


                @if ($snackTotal != 0)

                    <tr>
                        <td>{{ $keyProductName }}</td>
                        @php unset($keyProductName) @endphp
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

  .flex-center {
    display: block;
  }
  tr:nth-child(even) td {
     background: grey;
  }

</style>
@endsection
