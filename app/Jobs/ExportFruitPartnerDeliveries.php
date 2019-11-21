<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Exports\FruitPartnerPicklists;
use App\Events\FruitPartnerProcessed;

class ExportFruitPartnerDeliveries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fruitpartner;
    //public $orders;
    public $week_start;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public function __construct($orders, $fruitpartner, $week_start)
    public function __construct($fruitpartner, $week_start)
    {
        //$orders, $fruitpartner, $week_start

        //$this->orders = $orders;
        $this->fruitpartner = $fruitpartner;
        $this->week_start = $week_start;
        //dump($this->fruitpartner);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $orders = new \stdClass;

        // These are all the boxes due for delivery this week.
        $fruitboxes = $this->fruitpartner->fruitbox->where('next_delivery', $this->week_start->current)->where('is_active', 'Active');
        $milkboxes = $this->fruitpartner->milkbox->where('next_delivery', $this->week_start->current)->where('is_active', 'Active');

        //---------- Archive Checks ----------//

        // EDIT: Reviewing this again on 18/10/19 was after writing this!
        //       At a glance I realsise why these are unlikely to need using, unless the fruit/milk boxes get updated before exprting the fruit partner orders.
        //       If these are required though I don't see where I'm using/adding these potential orders to the export?

        // // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
        // if ($fruitpartner->fruitbox_archive) {
        //     // These are the archived boxes, although I'm not sure how relevant they'll be as this function is run (weekly?) before orders have been delivered.
        //     $archived_fruitboxes = $fruitpartner->fruitbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
        // }
        // // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
        // if ($fruitpartner->milkbox_archive) {
        //     // Still not sure we'll actually be using them but all the more reason to make sure they don't throw errors.
        //     $archived_milkboxes = $fruitpartner->milkbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
        // }

        //---------- End of Archive Checks ----------//

        // I probably don't need to worry about empty collections, so let's check that before adding to the orders.
        if ($fruitboxes->isNotEmpty()) {
            $orders->fruitboxes[$this->fruitpartner->name] = $fruitboxes;
        //    return \Excel::download(new Exports\FruitPartnerPicklists($fruitboxes), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
        } else {
            $orders->fruitboxes = null;

            // FOR TESTING PURPOSES LET'S STOP HOGGING THE LOGS WITH THIS OUTPUT!

             Log::channel('slack')->info('Fruit Partner: ' . $this->fruitpartner->name . ' has no Fruit Orders to be delivered this week.' );
        }

        if ($milkboxes->isNotEmpty()) {

            $orders->milkboxes[$this->fruitpartner->name] = $milkboxes;
        } else {
            $orders->milkboxes = null;

            // FOR TESTING PURPOSES LET'S STOP HOGGING THE LOGS WITH THIS OUTPUT!

             Log::channel('slack')->info('Fruit Partner: ' . $this->fruitpartner->name . ' has no Milk Orders to be delivered this week.' );
        }
        // What's going on here, I think if it fails the first check (no milk), then it goes straight into the do nothing pile regardless of havibng fruit orders?
        if ($orders->fruitboxes == null && $orders->milkboxes == null) {
            // dd($orders);
        } else {

            //$this->download($orders, $fruitpartner, $week_start);
            //dump($fruitpartner);
            // Instead of calling the download function here let's push it into the queue
            // This way we can break the time intensive task into single fruitpartner jobs
            // we can also move them onto a background worker to keep the main site running without pause.

            // ExportFruitPartnerDeliveries::dispatch($orders, $fruitpartner, $week_start);
            return Excel::store(new FruitPartnerPicklists($orders), '/' . $this->week_start->current . '/' . 'fruit-partner-' . $this->fruitpartner->name . '-orders-' . $this->week_start->current . '.xlsx', 's3');

        }
        //dump($this->fruitpartner);
        //dd('Job received!');

        //----- Braodcast event to pusher -----//

        // I want to send the job success, or failure to the event below - if I can't access the success failure of the queue, maybe I can use try/catch intsead sending a string 'sucess' or 'failure' to it instead?
        // event(new FruitPartnerProcessed($this->fruitpartner));
        
        //----- End of broadcast event with pusher -----//

        // instead of calling the download function, we can just place the (download function) code into the handler
    //    return Excel::store(new FruitPartnerPicklists($this->orders), '/' . $this->week_start->current . '/' . 'fruit-partner-' . $this->fruitpartner->name . '-orders-' . $this->week_start->current . '.xlsx', 's3');
    }

}
