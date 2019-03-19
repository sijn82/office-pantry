<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\OrderController;

class AdvanceOrdersDeliveryDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'advance:odd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running this command will advance the orders to their next scheduled delivery date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        OrderController::advanceNextOrderDeliveryDate();
    }
}
