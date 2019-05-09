<table>
    <thead>
        <tr>
            <th> *ContactName </th>
            <th> EmailAddress </th>
            <th> POAddressLine1 </th>
            <th> POAddressLine2 </th>
            <th> POCity </th>
            <th> PORegion </th>
            <th> POPostCode </th>
            <th> *Description </th>
            <th> *Quantity </th>
            <th> *AccountCode </th>
            <th> *UnitAmount </th>
            <th> TaxAmount </th>
            <th> *TaxType </th>
            <th> BrandingTheme </th>
            <th> *InvoiceNumber </th>
            <th> *InvoiceDate </th>
            <th> *DueDate </th>
        </tr>
    </thead>
    <tbody>
        @php
        //    dd($invoices);
        @endphp
        
        @foreach ($invoices as $invoice)
            <tr>
                <td> {{ $invoice->invoice_name }} </td>
                <td> {{ $invoice->email_address }} </td>
                <td> {{ $invoice->po_address_line_1 }} </td>
                <td> {{ $invoice->po_address_line_2 }} </td>
                <td> {{ $invoice->po_city }} </td>
                <td> {{ $invoice->po_region }} </td>
                <td> {{ $invoice->po_post_code }} </td>
                <td> {{ $invoice->description }} </td>
                <td> {{ $invoice->quantity }} </td>
                
                @if (isset($invoice->account_code))
                    <td> {{ $invoice->account_code }} </td>
                @else
                    <td> {{ $invoice->sales_nominal }} </td>
                @endif
                
                <td> {{ $invoice->unit_amount }} </td>
                <td> {{ $invoice->tax_amount }} </td>
                <td> {{ $invoice->tax_type }} </td>
                <td> {{ $invoice->branding_theme }} </td>
                <td> {{ $invoice->invoice_number }} </td>
                <td> {{ $invoice->invoice_date }} </td>
                <td> {{ $invoice->due_date }} </td>
                @php
                //    dd($invoice);
                @endphp
            </tr>
        @endforeach
    </tbody>
    @php
    //    dd($invoices);
    @endphp
</table>