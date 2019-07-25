<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

use App\FruitPartner;
use Illuminate\Http\Request;
use App\WeekStart;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

class FruitPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    
    public function listFruitPartners() {
        $fruit_partners = FruitPartner::all();
        // dd($fruit_partners);
        return $fruit_partners;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd(request('fruit_partner'));
        
        $new_fruit_partner = new FruitPartner();
        $new_fruit_partner->name = request('fruit_partner.name');
        $new_fruit_partner->email = request('fruit_partner.email');
        $new_fruit_partner->telephone = request('fruit_partner.telephone');
        $new_fruit_partner->url = request('fruit_partner.url');
        $new_fruit_partner->primary_contact = request('fruit_partner.primary_contact');
        $new_fruit_partner->secondary_contact = request('fruit_partner.secondary_contact');
        $new_fruit_partner->alternative_telephone = request('fruit_partner.alt_phone');
        $new_fruit_partner->location = request('fruit_partner.location');
        $new_fruit_partner->coordinates = request('fruit_partner.coordinates');
        $new_fruit_partner->weekly_action = request('fruit_partner.weekly_action');
        $new_fruit_partner->changes_action = request('fruit_partner.changes_action');
        $new_fruit_partner->no_of_customers = request('fruit_partner.no_of_customers');
        $new_fruit_partner->additional_info = request('fruit_partner.additional_info');
        $new_fruit_partner->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function show(FruitPartner $fruitPartner, $id)
    {
        //
        $fruit_partner = FruitPartner::where('id', $id)->get();
        
        
        return $fruit_partner;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function edit(FruitPartner $fruitPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FruitPartner $fruitPartner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(FruitPartner $fruitPartner)
    {
        //
    }
    
    public function download($orders, $fruitpartner, $week_start) 
    {
        //return Excel::store(new Exports\FruitPartnerPicklists($orders), 'FruitPartners/fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.pdf', \Maatwebsite\Excel\Excel::TCPDF);
        
        // works locally but heroku having issues, simplifying the folder structure.
        // return Excel::store(new Exports\FruitPartnerPicklists($orders), 'FruitPartners/' . $week_start->current . '/fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
        
        return Excel::store(new Exports\FruitPartnerPicklists($orders), '/' . $week_start->current . '/' . 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx', 's3');
    }

    
    // This has become a weird part of my process but moving this logic to the export folder now so I can break $orders up into exports, probably two.
    // AT SOME POINT I REALLY NEED TO DELETE SUPERFLUOUS CODE!!!
    public function groupOrdersByFruitPartner()
    {
        // This will grab all fruit partners except for Office Pantry, so long as Office Pantry remains the 1st fruitpartner in the db.   
        // This is easy to guarantee, so long as I don't forget to add it during datbase refresh and setup!
        
        $fruitpartners = FruitPartner::all()->whereNotIn('id', [1]);
        $week_start = WeekStart::first();
        // dd($week_start->current);
        
        // Not sure why but I tried to use new \stdClass again and this time it worked fine?!?  Ah well, life is filled with surprises.
        
        foreach ($fruitpartners as $fruitpartner) {
            // dump($fruitpartner);
            unset($orders);
            $orders = new \stdClass;
            
            // These are all the boxes due for delivery this week.
            $fruitboxes = $fruitpartner->fruitbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            $milkboxes = $fruitpartner->milkbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            
            //---------- Archive Checks ----------//
            
            // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
            if ($fruitpartner->fruitbox_archive) {
                // These are the archived boxes, although I'm not sure how relevant they'll be as this function is run (weekly?) before orders have been delivered.
                $archived_fruitboxes = $fruitpartner->fruitbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            }
            // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
            if ($fruitpartner->milkbox_archive) {
                // Still not sure we'll actually be using them but all the more reason to make sure they don't throw errors.
                $archived_milkboxes = $fruitpartner->milkbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            }
            
            //---------- End of Archive Checks ----------//

            // I probably don't need to worry about empty collections, so let's check that before adding to the orders.
            if ($fruitboxes->isNotEmpty()) {
                $orders->fruitboxes[$fruitpartner->name] = $fruitboxes;
            //    return \Excel::download(new Exports\FruitPartnerPicklists($fruitboxes), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
            } else {
                $orders->fruitboxes = null;
                Log::channel('slack')->info('Fruit Partner: ' . $fruitpartner->name . ' has no Fruit Orders to be delivered this week.' );
            }
            
            if ($milkboxes->isNotEmpty()) {
                
                $orders->milkboxes[$fruitpartner->name] = $milkboxes;
            } else {
                $orders->milkboxes = null;
                Log::channel('slack')->info('Fruit Partner: ' . $fruitpartner->name . ' has no Milk Orders to be delivered this week.' );
            }
            
            if ($orders->milkboxes == null && $orders->fruitboxes == null) {
                // dd($orders);
            } else {
            
                $this->download($orders, $fruitpartner, $week_start);
            }
             
             // return \Excel::download(new Exports\FruitPartnerPicklists($orders), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx'); 
            // return Excel::store(new Exports\FruitPartnerPicklists($orders),'invoices.pdf');
            //return Excel::store(new Exports\FruitPartnerPicklists($orders), 'FruitPartners/fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf());
            // dd($orders);
            //return \Excel::store(new Exports\FruitPartnerPicklists($orders), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
        }
        // Cool(io) - $orders is now filled with orders.  Just orders, and the key used is the fruitpartner name as I'm sure that'll save some bother cometh the template.
        // However, do I really want to put/keep them together when they're going to different templates?
        
        // return \Excel::download(new Exports\FruitPartnerPicklists($orders), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx'); <-- All files are named after last fruit partner.
        // dd($orders);
        
        // $zip_test = new \ZipArchive();
        // dd($zip_test);
        
        // Code source -https://laraveldaily.com/how-to-create-zip-archive-with-files-and-download-it-in-laravel/
        // This appears to work great locally, the next test is whether it will behave the same in the server environment (heroku)?
        
        // works locally, heroku struggling
        $zip_file = 'FruitPartner/fruitpartnerorders-' . $week_start->current . '.zip';
        $zip_file = 'fruitpartnerorders-' . $week_start->current . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        
        //----- Had to make some wholesale changes to the previous code, now much smaller and using laravel 'Storage' functions rather than standard php -----//
        
        // Made a tweak to where the files are stored, adding another sub directory limiting the zip download to only grab files in the folder of the current week start.
        $files = Storage::disk('s3')->files($week_start->current);
        //$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        //dd($files);
        // debugging anything with dd() in this foreach causes the page to timeout, dump() works but must be removed to successfully download anything.
        foreach ($files as $file)
        {
            $content = Storage::disk('s3')->get($file);
            // // We're skipping all subfolders
            // if (!$file->isDir()) {
            //     $filePath = $file->getRealPath();
            // 
            //     // extracting filename with substr/strlen
            //     $relativePath = substr($filePath, strlen($path) + 1);
            //     //dump($filePath);
            //     $zip->addFile($filePath, $relativePath);
            // }
            $zip->addFromString($file, $content);
        }
        
        $zip->close();
        return response()->download($zip_file);
        
        
        
        
    }
}
