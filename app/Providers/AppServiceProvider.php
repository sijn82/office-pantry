<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \Maatwebsite\Excel\Sheet;
use App\FruitBox;
use App\Observers\FruitBoxObserver;
use App\MilkBox;
use App\Observers\MilkBoxObserver;

use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // This is a custom macro used in the picklist/routing excel export functions/classes to style the exported file.
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        // These are the Models and their respective Observers, booted up an ready to listen for changes.
        // See the Observer folder, to review the classes and which actions are monitored.
        // FruitBox::observe(FruitBoxObserver::class);
        // MilkBox::observe(MilkBoxObserver::class);

        Queue::after(function ($connection, $job, $data) {

            event(new FruitPartnerProcessed($data));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
