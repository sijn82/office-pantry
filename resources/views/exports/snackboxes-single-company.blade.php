
@foreach($chunks as $snackbox)

    @php
        $productNames = $product_list;
    @endphp

    @php
     $g = 0;
     $h = 0;
     $i = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th> {{  $snackbox[2] }} {{-- Company Name --}} </th>
                <th colspan='3'></th>
                <th></th>
            </tr>
            <tr>
                <th> {{ $snackbox[0] }}{{-- Delivered By --}} </th>
                <th colspan='3'> Packed By: ..................... </th>
                <th></th>
            </tr>
            <tr>
                <th></th>
                <th colspan='3'></th>
                <th></th>
            </tr>
            <tr>
                <th>Product Name</th>
                <th> In Each Box </th>
                <th> No. of Boxes </th>
                <th> Total </th>
                <th> Packed? </th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            @php
                if (isset($snackbox) && is_array($snackbox)) { $NoOFBoxes = $snackbox[1]; $snackbox = array_slice($snackbox, 3); };
                $h++;
            @endphp

            @foreach ($snackbox as $key => $snack )

                @if (isset($snackbox))
                    @php 
                        $snackOne = array_shift($snackbox); 
                        (!empty($snackOne)) ? $snackOne : $snackOne = 0;
                        $inEachBox = ($snackOne / $NoOFBoxes);
                    @endphp
                @endif

                @php
                    $keyProductName = array_shift($productNames);
                    $snackTotal = 0;
                    (!empty($snackOne)) ? $snackTotal = $snackOne : $snackTotal = 0;
                @endphp

                @if ($snackTotal != 0)

                    <tr>
                        <td> {{ $keyProductName }} </td>
                        @php unset($keyProductName) @endphp
                        <td> {{ $inEachBox }} </td>
                        @php unset($inEachBox); @endphp
                        <td> {{ $NoOFBoxes }} {{-- No. of Boxes --}} </td>

                        <td> {{ $snackOne }} {{-- Value of order, this is all the cell information that needs looping through with the $product_list --}}</td>
                        @php unset($snackOne); @endphp
                        <td> [  ....  ] </td>
                    </tr>

                @endif
            @endforeach
                    <tr>
                        <td colspan='5'></td>
                    </tr>
                    <tr>
                        <td colspan='5'></td>
                    </tr>
        </tbody>
    </table>
                @php unset($NoOFBoxes); @endphp
@endforeach
