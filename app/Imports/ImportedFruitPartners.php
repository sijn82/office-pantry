<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithEvents; // Not sure we need any events for imports.

use App\FruitPartner;

class ImportedFruitPartners implements
toModel,
// WithEvents,
WithHeadingRow
{

    public function model(array $row)
    {

        return new FruitPartner([
            
            'name' => $row['name'],
            'email' => $row['email'],
            'telephone' => $row['telephone'],
            'url' => $row['url'],
            'primary_contact_first_name' => $row['primary_contact_first_name'],
            'primary_contact_surname' => $row['primary_contact_surname'],
            'secondary_contact_first_name' => $row['secondary_contact_first_name'],
            'secondary_contact_surname' => $row['secondary_contact_surname'],
            'address_line_1' => $row['address_line_1'],
            'address_line_2' => $row['address_line_2'],
            'address_line_3' => $row['address_line_3'],
            'city' => $row['city'],
            'region' => $row['region'],
            'postcode' => $row['postcode'],
            'alternative_telephone' => $row['alt_telephone'],
            'weekly_action' => $row['weekly_action'],
            'changes_action' => $row['changes_action'],
            'status' => $row['status'],
            'no_of_customers' => $row['no_of_customers'],
            'use_op_boxes' => $row['use_op_boxes'],
            'additional_info' => $row['additional_info'],
        ]);
        
    }
    
}
