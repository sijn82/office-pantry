@extends('welcome')
@section('companies')

<h3 class="route-header"> Display Companies </h3>

<table>
    <thead>
      <tr>
          <th>Company ID</th>
          <th>Is Active?</th>
          <th>Invoice Name</th>
          <th>Route Name</th>
          <th>Box Names</th>
          <th>Primary Contact</th>
          <th>Primary Email</th>
          <!-- <th>Secondary Email (Optional)</th> -->
          <th>Delivery Information</th>
          <th>Route Summary Address</th>
          <th>Address Line 1</th>
          <th>Address Line 2</th>
          <th>City</th>
          <th>Region</th>
          <th>Postcode</th>
          <th>Branding Theme</th>
          <th>Supplier</th>
          <!-- <th>Delivery Monday</th>
          <th>Delivery Tuesday</th>
          <th>Delivery Wednesday</th>
          <th>Delivery Thursday</th>
          <th>Delivery Friday</th>
          <th>Assigned To Monday</th>
          <th>Assigned To Tuesday</th>
          <th>Assigned To Wednesday</th>
          <th>Assigned To Thursday</th>
          <th>Assigned To Friday</th> -->
      </tr>
    </thead>
    <tbody>
    @foreach ($companies as $company)

        <tr>
            <td>{{ $company->company_id }}</td>
            <td>{{ $company->is_active }}</td>
            <td>{{ $company->invoice_name }}</td>
            <td>{{ $company->route_name }}</td>
            <td>
                @foreach ($company->box_names as $i => $company->box_name)
                  @if (empty($company->box_name) == FALSE)
                     {{ $i+1 }}: {{ $company->box_name }} <br>
                  @endif
                @endforeach
            </td>
            <td>{{ $company->primary_contact }}</td>
            <td>{{ $company->primary_email }}</td>
            <!-- <td>{{ $company->secondary_email }}</td> -->
            <td>{{ $company->delivery_information }}</td>
            <td>{{ $company->route_summary_address }}</td>
            <td>{{ $company->address_line_1 }}</td>
            <td>{{ $company->address_line_2 }}</td>
            <td>{{ $company->city }}</td>
            <td>{{ $company->region }}</td>
            <td>{{ $company->postcode }}</td>
            <td>{{ $company->branding_theme }}</td>
            <td>{{ $company->supplier }}</td>
            <!-- <td>{{ $company->delivery_monday }}</td>
            <td>{{ $company->delivery_tuesday }}</td>
            <td>{{ $company->delivery_wednesday }}</td>
            <td>{{ $company->delivery_thursday }}</td>
            <td>{{ $company->delivery_friday }}</td>
            <td>{{ $company->assigned_to_monday }}</td>
            <td>{{ $company->assigned_to_tuesday }}</td>
            <td>{{ $company->assigned_to_wednesday }}</td>
            <td>{{ $company->assigned_to_thursday }}</td>
            <td>{{ $company->assigned_to_friday }}</td> -->
        </tr>

    @endforeach
    </tbody>
</table>
<!-- @endsection

@section('company-assets')
  <style>

    table {
      margin: 10px 30px;
    }

    table thead tr th {
      font-size: 0.6rem;
      border-right: 1px solid #636b6f;
      border-bottom: 1px solid #636b6f;
    }
    table tbody tr td {
      font-size: 0.6rem;
      font-weight: 400;
      color: #212529;
      border-bottom: 1px solid #636b6f;
      border-right: 1px solid #636b6f;
    }
    tr:nth-child(even) {
      background: rgba(99, 107, 111, 0.5);
    }
    table tbody tr:nth-child(even) td {
      color: #fff;
    }
    .delivery-info {
      text-align:left;
      padding-left: 20px;
    }
    #assigned_route-dropdown {
      margin-bottom: 60px;
    }
    .flex-center {
      display: block;
    }
    h3.route-header {
      margin-top: 30px;
      margin-bottom: 10px;
      padding-left: 50px;
      padding-right: 50px;
    }

  </style>
@endsection -->
