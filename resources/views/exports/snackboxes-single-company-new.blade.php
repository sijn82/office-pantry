
@foreach($snackboxes as $snackbox)



    <table>
        <thead>
            <tr>
                <th> {{  $snackbox['company_name'] }} {{-- Company Name --}} </th>
                <th colspan='3'></th>
                <th></th>
            </tr>
            <tr>
                <th> {{ $snackbox['delivered_by'] }}{{-- Delivered By --}} </th>
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
            unset($snackbox['company_name']);
            unset($snackbox['delivered_by']);

            //dd($snackbox);
        
            @endphp

            @foreach ($snackbox as $snack )
                    @php
                    //dd($snack);
                    @endphp
                    <tr>
                        <td> {{ $snack->name }} </td>
                        <td> {{ $snack->quantity }} </td>
                        <td> {{ $snack->no_of_boxes }} </td>
                        <td> {{ $snack->quantity * $snack->no_of_boxes }} </td>
                        <td> [  ....  ] </td>
                    </tr>

            @endforeach
                    <tr>
                        <td colspan='5'></td>
                    </tr>
                    <tr>
                        <td colspan='5'></td>
                    </tr>
        </tbody>
    </table>

@endforeach
