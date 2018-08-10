<table>
    <thead>
        <tr>
            <th>{{ $snackbox->company_name }}</th>
            <th>{{ $snackbox->delivered_by }}</th>
            <th>{{ $snackbox->office_size }}</th>
            <th> Packed By: ..................... </th>
        </tr>
        <tr>
            <th>Product Name</th>
            <th>In Each Box</th>
            <th>No. of Boxes</th>
            <th>Total</th>
            <th>Packed?</th>
        </tr>
    </thead>
    <tbody>
        @foreach($snackboxes as $snackbox)
        <tr>
            <td>{{ $snackbox->product }}</td>
            <td>{{ $snackbox->in_each_box }}</td>
            <td>{{ $snackbox->no_of_boxes }}</td>
            <td>{{ $snackbox->total }}</td>
            <td>[ ]</td>
        </tr>
        @endforeach
    </tbody>
</table>
