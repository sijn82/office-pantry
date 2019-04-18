<?php

namespace App\Http\Controllers\Exports;

use App\Route;
use App\WeekStart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

use App\FruitBox;
use App\MilkBox;
use App\CompanyRoute;
use App\AssignedRoute;
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;


class RoutesExportNew implements WithMultipleSheets
// , WithEvents
{
    // use Exportable;

    protected $week_starting;
    protected $routescollection;

    protected $week_start;
    protected $delivery_days;

        public function __construct($week_starting)
        {
            $this->week_starting = $week_starting;

            $week_start = WeekStart::all()->toArray();
            $this->week_start = $week_start[0]['current'];
            $this->delivery_days = $week_start[0]['delivery_days'];
        }

    /**
     * @return array
     */
    public function sheets(): array
    {
        //----- Old Way To Queue Up And Process Routes - Commenting Out To Test New Way ------//
        
            // // This grabs all the current delivery routes for monday and tuesday, ordering the list by their tab order position.
            // $OrderedRoutesMonTue = AssignedRoute::whereIn('delivery_day', ['Monday', 'Tuesday'])->orderBy('tab_order', 'desc')->get();
            // // Now we only need their names, so let's just put them into a lovely array...
            // foreach ($OrderedRoutesMonTue as $route) {
            //     // ... called $correctOrderMonTue
            //     $correctOrderMonTue[] = $route->name;
            // }
            // 
            // // This grabs all the current delivery routes for wednesday, thursday and friday, ordering the list by their tab order position.
            // $OrderedRoutesWedThurFri = AssignedRoute::whereIn('delivery_day', ['Wednesday', 'Thursday', 'Friday'])->orderBy('tab_order', 'desc')->get();
            // // Now we only need their names, so lets just put them into a lovely array...
            // foreach ($OrderedRoutesWedThurFri as $route) {
            //     // ... called $correctOrderMonTue
            //     $correctOrderWedThurFri[] = $route->name;
            // }
            
        //----- End of Old Way To Queue Up And Process Routes - Commenting Out To Test New Way ------//
        
        $sheets = [];
        
        // Great, now let's check the delivery days we want to process this time and grab the array of those routes.
        // At the moment there are only two options but should we change to daily printouts, a switch statement would probably make more sense.
        
        //----- Old Way To Queue Up And Process Routes - Commenting Out To Test New Way -----//
        
            // if ($this->delivery_days == 'mon-tue') {
            // 
            //     foreach ($correctOrderMonTue as $routesolo) {
            //         $sheets[] = new RoutesCollectionNew($routesolo, $this->week_starting);
            //     }
            //     return $sheets;
            // 
            // } else {
            // 
            //     foreach ($correctOrderWedThurFri as $routesolo) {
            //         $sheets[] = new RoutesCollectionNew($routesolo, $this->week_starting);
            //     }
            //     return $sheets;
            // }

        //----- End of Old Way To Queue Up And Process Routes - Commenting Out To Test New Way -----//

        //----- How would this look as as switch case with each day of the week as an option too, let's find out! -----//
        
            switch ($this->delivery_days) {
                case 'mon-tue':
                    $orderedRoutesAll = AssignedRoute::whereIn('delivery_day', ['Monday', 'Tuesday'])->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }
                    break;
                case 'wed-thur-fri':
                    $orderedRoutesAll = AssignedRoute::whereIn('delivery_day', ['Wednesday', 'Thursday', 'Friday'])->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }
                    return $sheets;
                    break;
                case 'mon':
                    $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Monday')->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }    
                    return $sheets;
                    break;
                case 'tue':
                    $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Tuesday')->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }    
                    return $sheets;
                    break;
                case 'wed':
                    $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Wednesday')->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }    
                    return $sheets;
                    break;
                case 'thur':
                    $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Thursday')->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }    
                    return $sheets;
                    break;
                case 'fri':
                    $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Friday')->orderBy('tab_order', 'desc')->get();
                    foreach ($orderedRoutesAll as $route) {
                        $orderedRoutes[] = $route->name;
                    }
                    foreach ($orderedRoutes as $assigned_route) {
                        $sheets[] = new RoutesCollectionNew($assigned_route, $this->week_starting);
                    }    
                    return $sheets;
                    break;
            }
        
        //----- End of switch case experiment -----//
    }

} // End of - class RoutesExport implements WithMultipleSheets

class RoutesCollectionNew implements
FromView,
WithTitle,
WithHeadings,
WithEvents
{
    // use Exportable;

    private $routesolo;
    private $week_starting;

    public function __construct($routesolo, $week_starting)
    {
        $this->routesolo = $routesolo;
        $this->week_starting = $week_starting;
    }

    public function headings(): array
    {
      return [
          'id',
          'week_start',
          'company_name',
          'postcode',
          'address',
          'delivery_information',
          'fruit_crates',
          'fruit_boxes',
          'milk_2l_semi_skimmed',
          'milk_2l_skimmed',
          'milk_2l_whole',
          'milk_1l_semi_skimmed',
          'milk_1l_skimmed',
          'milk_1l_whole',
          'milk_1l_alt_coconut',
          'milk_1l_alt_unsweetened_almond',
          'milk_1l_alt_almond',
          'milk_1l_alt_unsweetened_soya',
          'milk_1l_alt_soya',
          'milk_1l_alt_oat',
          'milk_1l_alt_rice',
          'milk_1l_alt_cashew',
          'milk_1l_alt_lactose_free_semi',
          'drinks',
          'snacks',
          'other',
          'assigned_to',
          'delivery_day',
          'position_on_route',
          'created_at',
          'updated_at',
      ];
    }

     public function view(): View
    {
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        $currentWeekStart = Weekstart::findOrFail(1);

        // If it matches, it's on for delivery this week.
        $fruitboxesForDelivery = FruitBox::where('next_delivery', $currentWeekStart->current)->where('is_active', 'Active')->get();

        // Now the same for milk, and yes I called the same field, with the same purpose something different each time.  I shouldn't be allowed to wield this much power.
        $milkboxesForDelivery = MilkBox::where('next_delivery', $currentWeekStart->current)->where('is_active', 'Active')->get();

        // Let's grab all the routes. (old approach)
        //$routeInfoAll = Route::where('assigned_to', $this->routesolo)->where('is_active', 'Active')->get();
        
        // New system approach
        $assigned_route = AssignedRoute::where('name', $this->routesolo)->get();
        $routeInfoAll = CompanyRoute::where('assigned_route_id', $assigned_route[0]->id)->where('is_active', 'Active')->get();
        

        // We will want to build the routes based on the 'Assigned To' column, so let's grab that now.
        // $assigned_route = Route::select('assigned_to')->distinct()->get();

        foreach ($routeInfoAll as $routeInfoSolo)
        {
            //dd($routeInfoSolo);
            
            // ---------- Fruit Deliveries ---------- //

            // For each route in the routes table, we check the associated Company ID for a FruitBox - that's Active, On Delivery For This Week and on this Delivery Day.
            $fruitboxesForDelivery = FruitBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery', $currentWeekStart->current)
                                                ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->where('fruit_partner_id', 1)->get();
            // Set variable value.
            $fruitbox_totals = 0;

            // If there are more than one we need to generate a total for the route by adding the box totals together.
            if (count($fruitboxesForDelivery) >= 1) {
                foreach($fruitboxesForDelivery as $fruitbox) {
                        $fruitbox_totals += $fruitbox->fruitbox_total;
                }
                // Then we overwrite the existing total attached to the route.
                $routeInfoSolo->fruit_boxes = $fruitbox_totals;
            }

            // If we didn't find any then we can add a string to search for in the template.  This was also for testing purposes and we could move this logic to the template.
            if (count($fruitboxesForDelivery) == 0) {
                // dd('None for this week!');
                $fruitboxesForDelivery = 'None for this week!';
                $routeInfoSolo->fruit_boxes = $fruitbox_totals;
            }

            // Now we can create a fruit entry into the route collection and add the fruitbox(es).
            $routeInfoSolo['fruit'] = $fruitboxesForDelivery;

            // ---------- Milk Deliveries ---------- //

            // For each route in the routes table, we check the associated Company ID for a MilkBox - that's Active, On Delivery For This Week and on this Delivery Day.
            $milkboxesForDelivery = MilkBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery', $currentWeekStart->current)
                                           ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->where('fruit_partner_id', 1)->get();

            // Unlike FruitBoxes there shouldn't be any more than one entry, so totalling isn't necessary - however there may be no milk on the route.
            // If this is the case we need to set the milk totals to 0 for all the options, either here or in the template.  For now I'm going with another 'None For This Week!'.
            if (count($milkboxesForDelivery) == 0) {
                // dd('None for this week!');
                $milkboxesForDelivery = 'None for this week!';
            }

            // Same process with milk, create milk entry and add the information we have.
            $routeInfoSolo['milk'] = $milkboxesForDelivery;
            
            // ------------ Snacks, Drinks & Other ------------ //
            
            // Declare the total variables and set them to 0 before we begin each route.
            
            $snackbox_total = 0;
            $drinkbox_total = 0;
            $other_items_list = '';
            
            //----- Snackbox Processing Pt1 - Regular Boxes and Wholesale -----//
            
            // This will grab each entry in the snackbox, not each specific snackbox (i.e we only need to grab one entry per snackbox_id)
            $snackboxesForDelivery = SnackBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery_week', $currentWeekStart->current)
                                             ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')
                                             ->where('delivered_by', 'OP')->get();
                                             
            // Good old groupBy will give us the means to select the first entry from each box and grab the number of boxes.
            $snackboxesGroupedById = $snackboxesForDelivery->groupBy('snackbox_id');
            
            // If there happened to be two snackboxes out for delivery on the same route, this would still keep an accurate number of boxes.
            foreach ($snackboxesGroupedById as $snackbox) {
                
                // By default we want to treat wholesale orders differently to a mixed snackbox
                if ($snackbox[0]->type === 'wholesale') {
                    foreach ($snackbox as $snackbox_item) {
                        // For each item in the wholesale box we want to treat each quantity as another box
                        $snackbox_total += $snackbox_item->quantity;
                    }
                }
                
                $snackbox_total += $snackbox[0]->no_of_boxes;
            }
            
            //----- End of Snackbox Processing Pt1 - Regular Boxes and Wholesale -----//
            
            //----- Snackbox Processing Pt2 - Unique Drinkboxes (Adding Unique Drinkboxes to the Box Total shared with Snackboxes on Routing) -----//
                
            $unique_drinkboxes = Drinkbox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery_week', $currentWeekStart->current)
                                         ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->where('delivered_by_id', 1)
                                         ->where('type', 'Unique')->get();
                
                //dd($unique_drinkboxes);
                
            $uniqueDrinkboxesGroupedById = $unique_drinkboxes->groupBy('drinkbox_id');
            
            // The collection of all possible drink boxes, as drinkbox.                         
            foreach ($uniqueDrinkboxesGroupedById as $drink_box) {
                
                $unique_box_totals = 0;
                $unique_total = 0;
                
                // Each drinkbox order as item.
                foreach ($drink_box as $item) {
                    
                    // We can only use $item->quantity if it's set, so let's check for that, first.
                    if (isset($item->quantity)) {
                        $unique_total += $item->quantity;
                    }
                }
    
                // If we have at least one item, we need to determine how many boxes the order will fit in.
                // As a general rule, we're going for 8 items per box.
                
                // First let's make sure there's an item to be packed into a box.
                if ($unique_total > 0) {
                    // If there is, we can devide that number by 8, but ensure we always round up to the nearest whole number.
                    // This means 4 items will round up to 1 box, 15 (and 16) becomes 2 etc.
                    $unique_box_totals = ceil($unique_total / 8);
                    
                }
    
            }
            
            // If we have any unique drinks, such as coffee, etc we want to add this to the box totals rather than drinks,
            // so let's check we have a value to add, and if so, add it here.
            if (isset($unique_box_totals)) {
                $snackbox_total += $unique_box_totals;
            }
                
            //----- End of Snackbox Processing Pt2 - Unique Drinkboxes (Adding Unique Drinkboxes to the Box Total shared with Snackboxes on Routing) -----//
            
            //---------- Saving Snack, Drink and Other route info to CompanyRoute, and overriding function discussion ----------//
            
                // As it stands this is only temporarily held in the $routeInfoSolo variable, as nothing is saved to the database.
                // If I save it here, then we'll have data saved to the CompanyRoute tables, which will be overwritten everytime this export route function is called.
                // The issue with this is that the cell will appear to be updatable on the office dashboard but in reality the exported route won't keep the values.
                
                // I'm thinking it should save the info here but have another function (and button) which can be run to specifically override the information held.
                // It'll still need to run most of this code, just without the parts like this one which will try to override the data.
            
            //---------- End of saving Snack, Drink and Other route info to CompanyRoute, and overriding function discussion ----------//
            
            if (empty($snackbox_total)) {
                
                // We don't actually need to do anything here, snacks (boxes) are already set to 0, 
                // so let's just check if the value is still 0 at the end.
                
            } else {
                        
                // And on the database entry for the route too.
                CompanyRoute::where('id', $routeInfoSolo->id)->update([
                    'snacks' => $snackbox_total,
                ]);
            }
            
            // Now we save the (possibly combined) total to the route export, even if the total is still 0 we need this value added to check later.
            $routeInfoSolo->snacks = $snackbox_total;
            
            //----- Regular Drinkboxes -----//
            
            // Time to do the same with drinks, however this will need to be handled slightly differently as the drinks are sold in cases,
            // so the box total here really reflects the number of cases, or in other words, each individual entry in the box.                         
            $drinkboxesForDelivery = DrinkBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery_week', $currentWeekStart->current)
                                             ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->where('delivered_by_id', 1)
                                             ->where('type', 'Regular')->get();
                                             
            // We still want to group them.
            $drinkboxesGroupedById = $drinkboxesForDelivery->groupBy('drinkbox_id');
            
            foreach ($drinkboxesGroupedById as $drinkbox) {
                // But now we need to go down another level and get to the items themselves, in order to loop through.
                foreach ($drinkbox as $drink) {
                    $drinkbox_total += $drink->quantity;
                }
                
            }
            
            if (empty($drinkbox_total)) {
                
                // We don't actually need to do anything here, drinks are already set to 0, 
                // so let's just check if the value is still 0 at the end.
                
            } else {  
                
                // As well as to the db.
                CompanyRoute::where('id', $routeInfoSolo->id)->update([
                    'drinks' => $drinkbox_total,
                ]);
            }         
            
            // And save this total to the route.
            $routeInfoSolo->drinks = $drinkbox_total;
            
            //----- End of Drinkboxes -----//
            
            //----- Otherbox processing -----//      
            
            // Finally we have the other category, which is a mishmash of items.  Each entry needs to display its name and quantity.                    
            $otherboxesForDelivery = OtherBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery_week', $currentWeekStart->current)
                                             ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->where('delivered_by_id', 1)->get();                     
            
            // We still want to group them.                     
            $otherboxesGroupedById = $otherboxesForDelivery->groupBy('otherbox_id');
            
            foreach ($otherboxesGroupedById as $otherbox) {
                // But now we need to go down another level and get to the items themselves, in order to loop through.
                foreach ($otherbox as $other_item) {
                    
                    // The blade template wants a string, so let's create one with all the orders chained together and separated by a comma.
                    // In the export styling code, I want to explode the string with line breaks in place of comma's but this isn't an immediate priority.
                    $other_items_list .= $other_item->quantity . ' x ' . $other_item->name . ', ';
                }
            }
            
            if (empty($other_items_list)) {
                // Let's set the variable to a predetermined string I can check on at the end,
                // before deciding whether to add it to the exported routes or not.
                $other_items_list = 'None for this week!';
                
            } else {
                
                // all finally to the db.
                CompanyRoute::where('id', $routeInfoSolo->id)->update([
                    'other' => $other_items_list,
                ]);
            }
            
            // Same again with other
            $routeInfoSolo->other = $other_items_list;
            
            //----- End of Otherbox Processing -----//

            // ------------ End of Snacks, Drinks & Other ------------ //
            
            // ----- For Debugging Purposes ----- //
            
            // A nice little way to check a specific result for testing purposes.  I can comment it out for now but may reuse again in the near future.
            
            // if ($routeInfoSolo->company_details_id == 965 && $routeInfoSolo->delivery_day == 'Monday') {
            //     dd($routeInfoSolo);
            // }
            
              // dd($routeInfoSolo);
              
            // ----- End Of For Debugging Purposes ----- //

            // Now we've added the entries we need to the route, we can build an array of collections and send it to the 'order-processing' template for outputting.
            // Although if they don't have anything scheduled for delivery we can ignore them this week.
            
            // I think this check needs to account for snacks, drinks and other now that i've completed the addition of them to the previous steps. <--  EDIT: Done!
            if (    $routeInfoSolo->fruit === 'None for this week!' 
                 && $routeInfoSolo->milk === 'None for this week!' 
                 && $routeInfoSolo->snacks === 0 
                 && $routeInfoSolo->drinks === 0 
                 && $routeInfoSolo->other === 'None for this week!' 
            ) {
            
                // If we're here then fingers crossed the route doesn't need processing this week!  
                // Because, err, I'm not currently going to do anything else with it here.
                // I could send the route to a slack channel, but even I won't be checking that one.
                     
            } else {
                // Otherwise we can add them here.
                // Let's also add the week start to each entry we're putting into the routes so it can display before each entry to mimic existing routes.
                $routeInfoSolo->week_start = $currentWeekStart->current;
                // And we can also check the assigned route id and replace it with the written name, for labels, etc.
                $assigned_route = AssignedRoute::findOrFail($routeInfoSolo->assigned_route_id);
                $routeInfoSolo->assigned_route_name = $assigned_route->name;
                $routesAndOrders[] = $routeInfoSolo;
            }

        }  // end of foreach ($routeInfoAll as $routeInfoSolo)
        
        if (empty($routesAndOrders)) {
            
            $routesAndOrders = [];
            
        } else {
            
            $reorder_by_position = function($a, $b)
            {
                // // This is using the new and sexy spaceship operator to compare company string names and return them in alphabetical order.
                // $outcome = $a->assigned_to <=> $b->assigned_to;
                // 
                // if ($a->assigned_to == $b->assigned_to) {
                    $outcome = $a->position_on_route <=> $b->position_on_route;
                // }
                // Combined with usort, some background php magic will return the (alpabetically prior) item.
                return $outcome;
            };
            
            usort($routesAndOrders, $reorder_by_position);
        }
        // dd($routesAndOrders);
        
        return view ('exports.order-processing', [
            'routes' => $routesAndOrders
        //    'assigned_route' => $assigned_route // I don't think this is actually being used for anything.  Holding on to it for now as a reminder to that chain of thought.
        ]);

    }

   /**
    * @return string
    */
   public function title(): string
   {
       return $this->routesolo;
   }

   /**
    * @return array
    */
   public function registerEvents(): array
   {

       return [
           AfterSheet::class    => function(AfterSheet $event) {
               // dd($event);
               $highestRow = $event->sheet->getHighestRow();
               $rowWidth = $event->sheet->getHighestColumn();

               foreach ($event->sheet->getRowIterator() as $row) {

                   $cellIterator = $row->getCellIterator();
                   $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                   foreach ($cellIterator as $cell) {
                       if ($cell !== null) {

                           if ($cell == 'Week Start') {

                               $selectedRow = $row->getRowIndex();

                               $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                               // $selectedRow = $row->getRowIndex();
                               $event->sheet->styleCells(
                                 $selectedCell, // Cell Range
                                 [ // Styles Array
                                     'borders' => [
                                         'outline' => [
                                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                             'color' => ['argb' => 'AFFF46'],
                                         ],
                                     ], // end of borders
                                     'fill' => [
                                                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                 'color' => [
                                                   'argb' => '93DA38'
                                                 ]
                                     ] // end of fill
                                 ] // end of styles array
                             ); // end of styleCells function parameters.
                         } // end of if ($cell == TBC)
                       } else {
                           continue;
                       }
                   }
               } // end of foreach statement
               $cellRange = 'A1:AA1'; // All headers
               $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
           },
       ];
   }

       /**
       * @return Builder
       */

       // Keeping this here purely as an example of using query() with laravel excel as this was working before I opted for View() instead.

      // public function query()
      // {
      //     // dd($this->$routesolo);
      //     return Route
      //         ::query()
      //         ->where('assigned_to', $this->routesolo)
      //         ->where('week_start', $this->week_starting)
      //         ->orderBy('position_on_route', 'asc');
      // }

}
