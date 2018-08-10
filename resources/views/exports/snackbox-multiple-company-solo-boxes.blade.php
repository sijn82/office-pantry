<table>
    <thead>
        <tr>
            <th> Packed By: ..................... </th>
            <th>{{ $snackbox->delivered_by }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>{{ $snackbox->company_1_office_size }}</th>
            <th>{{ $snackbox->company_2_office_size }}</th>
            <th>{{ $snackbox->company_3_office_size }}</th>
            <th>{{ $snackbox->company_4_office_size }}</th>
            <th></th>
        </tr>
        <tr>
            <th>Product Name</th>
            <th>Total</th>
            <th>{{ $snackbox->company_1_name }}</th>
            <th>{{ $snackbox->company_2_name }}</th>
            <th>{{ $snackbox->company_3_name }}</th>
            <th>{{ $snackbox->company_4_name }}</th>
            <th>Packed?</th>
        </tr>
    </thead>
    <tbody>
        @foreach($snackboxes as $snackbox)
        {{ $snackbox->total = 0 }}
        <tr>
            <td>{{ $snackbox->product_name }}</td>
            <td>{{ $snackbox->total = $snackbox->company_1_product_value + $snackbox->company_2_product_value + $snackbox->company_3_product_value + $snackbox->company_4_product_value }}</td>
            <td>{{ $snackbox->company_1_product_value }}</td>
            <td>{{ $snackbox->company_2_product_value }}</td>
            <td>{{ $snackbox->company_3_product_value }}</td>
            <td>{{ $snackbox->company_4_product_value }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
