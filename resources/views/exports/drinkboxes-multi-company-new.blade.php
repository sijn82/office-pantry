

@foreach($chunks as $chunk)

    @php
        $g = 0;
        $h = 0;
        $i = 0; // still using
    @endphp

    @foreach ($chunk as $key => $drinkbox)

        @php (array) $group[$i] = $drinkbox @endphp
        @php $i++ @endphp
        
    @endforeach
    @php
        //dd($group);
    @endphp

    <table>
        <thead>
            <tr>
                <th colspan='2'> Packed By: ..................... </th>
                <th colspan='4'> {{ $group[0]['delivered_by'] }}{{-- This will be where we show the delivered_by value before unsetting it. --}} </th>
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
                <th> Product Name </th>
                <th> Total (Cases) </th>
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
                    $drinkOne = 0;
                    $drinkTwo = 0;
                    $drinkThree = 0;
                    $drinkFour = 0;
                    $drinkTotal = 0;
                    
                @endphp
                
                @if (isset($group[0]))
                    @foreach ($group[0] as $drinkbox_item)
                        @php // dd($id); dd($drinkbox_item->product_id); @endphp
                        @if ($id === $drinkbox_item->product_id) {
                            @php $drinkOne = $drinkbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[1]))
                    @foreach ($group[1] as $drinkbox_item)
                        @if ($id === $drinkbox_item->product_id) {
                            @php $drinkTwo = $drinkbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[2]))
                    @foreach ($group[2] as $drinkbox_item)
                        @if ($id === $drinkbox_item->product_id) {
                            @php $drinkThree = $drinkbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                
                @if (isset($group[3]))
                    @foreach ($group[3] as $drinkbox_item)
                        @if ($id === $drinkbox_item->product_id) {
                            @php $drinkFour = $drinkbox_item->quantity; @endphp
                        }
                        @endif
                    @endforeach
                @endif
                            
                @php
                    $drinkTotal = $drinkOne + $drinkTwo + $drinkThree + $drinkFour;
                    
                    if ($drinkOne === 0) {
                        $drinkOne = '';
                    }
                    if ($drinkTwo === 0) {
                        $drinkTwo = '';
                    }
                    if ($drinkThree === 0) {
                        $drinkThree = '';
                    }
                    if ($drinkFour === 0) {
                        $drinkFour = '';
                    }
                    
                @endphp


                @if ($drinkTotal != 0)

                    <tr bgcolor="#ddd">
                        <td>{{ $product }}</td>
                        <td>{{ $drinkTotal }}</td>

                        <td>{{ $drinkOne }}</td>
                        @php // unset($drinkOne); @endphp

                        <td>{{ $drinkTwo }}</td>
                        @php // unset($drinkTwo); @endphp

                        <td>{{ $drinkThree }}</td>
                        @php // unset($drinkThree); @endphp

                        <td>{{ $drinkFour }}</td>
                        @php // unset($drinkFour); @endphp
                        <td> [  ....  ] </td>
                    </tr>

                @endif
            @endforeach

        </tbody>
    </table>
                @php unset($group); @endphp
@endforeach
