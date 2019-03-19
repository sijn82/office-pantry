

@foreach($chunks as $chunk)

    @php
        $g = 0;
        $h = 0;
        $i = 0; // still using
    @endphp

    @foreach ($chunk as $key => $snackbox)

        @php (array) $group[$i] = $snackbox @endphp
        @php $i++ @endphp
        
    @endforeach
    @php
        //dd($group);
    @endphp

    <table>
        <thead>
            <tr>
                <th colspan='3'> Packed By: ..................... </th>
                <th colspan='3'> {{ $group[0]['delivered_by'] }}{{-- This will be where we show the delivered_by value before unsetting it. --}} </th>
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
                <th>@if (isset($group[0]['company_name'])) {{ $group[0]['company_name'] }} @php unset($group[0]['company_name']); unset($group[0]['delivered_by']); @endphp @endif</th>
                <th>@if (isset($group[1]['company_name'])) {{ $group[1]['company_name'] }} @php unset($group[1]['company_name']); unset($group[1]['delivered_by']); @endphp @endif</th>
                <th>@if (isset($group[2]['company_name'])) {{ $group[2]['company_name'] }} @php unset($group[2]['company_name']); unset($group[2]['delivered_by']); @endphp @endif</th>
                <th>@if (isset($group[3]['company_name'])) {{ $group[3]['company_name'] }} @php unset($group[3]['company_name']); unset($group[3]['delivered_by']); @endphp @endif</th>
                <th>Packed?</th>
                
            </tr>
        </thead>
        <tbody>

            @foreach ($products as $id => $product)
            
                @php
                    //dd($group);
                    $snackOne = 0;
                    $snackTwo = 0;
                    $snackThree = 0;
                    $snackFour = 0;
                    $snackTotal = 0;
                    
                @endphp
                
                @if (isset($group[0]))
                    @foreach ($group[0] as $snackbox_item)
                        @php // dd($id); dd($snackbox_item->product_id); @endphp
                        @if ($id === $snackbox_item->product_id) {
                            @php $snackOne = $snackbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[1]))
                    @foreach ($group[1] as $snackbox_item)
                        @if ($id === $snackbox_item->product_id) {
                            @php $snackTwo = $snackbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[2]))
                    @foreach ($group[2] as $snackbox_item)
                        @if ($id === $snackbox_item->product_id) {
                            @php $snackThree = $snackbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[3]))
                    @foreach ($group[3] as $snackbox_item)
                        @if ($id === $snackbox_item->product_id) {
                            @php $snackFour = $snackbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                            
                @php
                    $snackTotal = $snackOne + $snackTwo + $snackThree + $snackFour;
                    
                    if ($snackOne === 0) {
                        $snackOne = '';
                    }
                    if ($snackTwo === 0) {
                        $snackTwo = '';
                    }
                    if ($snackThree === 0) {
                        $snackThree = '';
                    }
                    if ($snackFour === 0) {
                        $snackFour = '';
                    }
                    
                @endphp


                @if ($snackTotal != 0)

                    <tr bgcolor="#ddd">
                        <td>{{ $product }}</td>
                        <td>{{ $snackTotal }}</td>

                        <td>{{ $snackOne }}</td>
                        @php // unset($snackOne); @endphp

                        <td>{{ $snackTwo }}</td>
                        @php // unset($snackTwo); @endphp

                        <td>{{ $snackThree }}</td>
                        @php // unset($snackThree); @endphp

                        <td>{{ $snackFour }}</td>
                        @php // unset($snackFour); @endphp
                        <td> [  ....  ] </td>
                    </tr>

                @endif
            @endforeach

        </tbody>
    </table>
                @php unset($group); @endphp
@endforeach
