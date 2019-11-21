<?php

namespace App\Observers;

use App\FruitBox;
use Carbon\CarbonImmutable;

class FruitBoxObserver
{
    /**
     * Handle the fruitbox "updated" event.
     *
     * @param  \App\FruitBox  $fruitbox
     * @return void
     */
    public function updating(FruitBox $fruitbox)
    {
        // Sweet, we finally got to see this message after changing the way the
        // dd('Now I can take it from here.');
        dump($fruitbox->getChanges());
        dump($fruitbox->isDirty());
        //dd($fruitbox);
        $fieldsWeNeedToWatch = [
            'delivery_day',
            'fruitbox_total',
            'deliciously_red_apples',
            'pink_lady_apples',
            'red_apples',
            'green_apples',
            'satsumas',
            'pears',
            'bananas',
            'nectarines',
            'limes',
            'lemons',
            'grapes',
            'seasonal_berries',
            'oranges',
            'cucumbers',
            'mint',
            'organic_lemons',
            'kiwis',
            'grapefruits',
            'avocados',
            'root_ginger'
        ];

        if ($fruitbox->isDirty($fieldsWeNeedToWatch)) {
            // If we're in here, at least one of the values has changed from the currently held db data.
            // Not all changes need to be flagged for our current purposes, only order quantities
            dump('Yep the field was in the list');

            // Create the array to hold the order changes.
            $order_changes = [];
            $carbon_now = CarbonImmutable::now('Europe/London');

            foreach ($fieldsWeNeedToWatch as $field) {

                if ($fruitbox->isDirty($field)) {
                    dump($field . ' was updated to a new value.');
                    dump($fruitbox->{$field});
                    // Push each order change into the array.
                    // Using the field name as the key along with the value.
                    $order_changes[$field] = $fruitbox->{$field};

                }
            }

        } else {
            dd('Nope it wasn\'t in the list, so we don\'t care about that right now.');
        }
        $fruitbox->update([
            'order_changes' => $order_changes,
            'date_changed' => $carbon_now,
        ]);
        //dd($order_changes);
    }

    // This is what I get pulled through as fruitbox

//     FruitBox {#437
//   #fillable: array:34 [
//     0 => "is_active"
//     1 => "fruit_partner_id"
//     2 => "name"
//     3 => "company_details_id"
//     4 => "type"
//     5 => "previous_delivery"
//     6 => "next_delivery"
//     7 => "frequency"
//     8 => "week_in_month"
//     9 => "delivery_day"
//     10 => "fruitbox_total"
//     11 => "deliciously_red_apples"
//     12 => "pink_lady_apples"
//     13 => "red_apples"
//     14 => "green_apples"
//     15 => "satsumas"
//     16 => "pears"
//     17 => "bananas"
//     18 => "nectarines"
//     19 => "limes"
//     20 => "lemons"
//     21 => "grapes"
//     22 => "seasonal_berries"
//     23 => "oranges"
//     24 => "cucumbers"
//     25 => "mint"
//     26 => "organic_lemons"
//     27 => "kiwis"
//     28 => "grapefruits"
//     29 => "avocados"
//     30 => "root_ginger"
//     31 => "tailoring_fee"
//     32 => "discount_multiple"
//     33 => "invoiced_at"
//   ]
//   #hidden: []
//   #connection: "pgsql_local"
//   #table: "fruit_boxes"
//   #primaryKey: "id"
//   #keyType: "int"
//   +incrementing: true
//   #with: []
//   #withCount: []
//   #perPage: 15
//   +exists: true
//   +wasRecentlyCreated: false
//   #attributes: array:37 [
//     "id" => 24
//     "is_active" => "Active"
//     "fruit_partner_id" => 120
//     "name" => "Twitch Orange Box"
//     "company_details_id" => 1
//     "type" => "Orange Juicer"
//     "previous_delivery" => "2019-08-26"
//     "next_delivery" => "2019-09-02"
//     "frequency" => "Fortnightly"
//     "week_in_month" => null
//     "delivery_day" => "Wednesday"
//     "fruitbox_total" => 2
//     "deliciously_red_apples" => 0
//     "pink_lady_apples" => 0
//     "red_apples" => 0
//     "green_apples" => 0
//     "satsumas" => 0
//     "pears" => 0
//     "bananas" => 0
//     "nectarines" => 0
//     "limes" => 0
//     "lemons" => 0
//     "grapes" => 0
//     "seasonal_berries" => 0
//     "oranges" => 40
//     "cucumbers" => 0
//     "mint" => 0
//     "organic_lemons" => 0
//     "kiwis" => 0
//     "grapefruits" => 0
//     "avocados" => 0
//     "root_ginger" => 0
//     "tailoring_fee" => "0"
//     "discount_multiple" => "Yes"
//     "invoiced_at" => null
//     "created_at" => "2019-10-18 13:57:25"
//     "updated_at" => "2019-10-25 09:24:58"
//   ]
//   #original: array:37 [
//     "id" => 24
//     "is_active" => "Active"
//     "fruit_partner_id" => 120
//     "name" => "Twitch Orange Box"
//     "company_details_id" => 1
//     "type" => "Orange Juicer"
//     "previous_delivery" => "2019-08-26"
//     "next_delivery" => "2019-09-02"
//     "frequency" => "Weekly"
//     "week_in_month" => null
//     "delivery_day" => "Wednesday"
//     "fruitbox_total" => 2
//     "deliciously_red_apples" => 0
//     "pink_lady_apples" => 0
//     "red_apples" => 0
//     "green_apples" => 0
//     "satsumas" => 0
//     "pears" => 0
//     "bananas" => 0
//     "nectarines" => 0
//     "limes" => 0
//     "lemons" => 0
//     "grapes" => 0
//     "seasonal_berries" => 0
//     "oranges" => 40
//     "cucumbers" => 0
//     "mint" => 0
//     "organic_lemons" => 0
//     "kiwis" => 0
//     "grapefruits" => 0
//     "avocados" => 0
//     "root_ginger" => 0
//     "tailoring_fee" => "0"
//     "discount_multiple" => "Yes"
//     "invoiced_at" => null
//     "created_at" => "2019-10-18 13:57:25"
//     "updated_at" => "2019-10-25 09:24:58"
//   ]
//   #changes: []
//   #casts: []
//   #dates: []
//   #dateFormat: null
//   #appends: []
//   #dispatchesEvents: []
//   #observables: []
//   #relations: []
//   #touches: []
//   +timestamps: true
//   #visible: []
//   #guarded: array:1 [
//     0 => "*"
//   ]
// }

}
