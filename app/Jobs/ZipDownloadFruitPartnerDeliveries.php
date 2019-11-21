<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


// use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

// use App\Http\Controllers\Exports\FruitPartnerPicklists;
use Illuminate\Contracts\Filesystem\Filesystem;

class ZipDownloadFruitPartnerDeliveries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $week_start;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($week_start)
    {
        $this->week_start = $week_start;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    //     // Cool(io) - $orders is now filled with orders.  Just orders, and the key used is the fruitpartner name as I'm sure that'll save some bother cometh the template.
    //     // However, do I really want to put/keep them together when they're going to different templates?
    //
    //     // Code source -https://laraveldaily.com/how-to-create-zip-archive-with-files-and-download-it-in-laravel/
    //     // This appears to work great locally, the next test is whether it will behave the same in the server environment (heroku)?
    //
    //     // works locally, heroku struggling
    //     $zip_file = 'FruitPartner/fruitpartnerorders-' . $this->week_start->current . '.zip';
    //     $zip_file = 'fruitpartnerorders-' . $this->week_start->current . '.zip';
    //     $zip = new \ZipArchive();
    //     $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    //
    //     //----- Had to make some wholesale changes to the previous code, now much smaller and using laravel 'Storage' functions rather than standard php -----//
    //
    //     // Made a tweak to where the files are stored, adding another sub directory limiting the zip download to only grab files in the folder of the current week start.
    //     $files = Storage::disk('s3')->files($this->week_start->current);
    //     //$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
    //     //dd($files);
    //     // debugging anything with dd() in this foreach causes the page to timeout, dump() works but must be removed to successfully download anything.
    //     foreach ($files as $file)
    //     {
    //         $content = Storage::disk('s3')->get($file);
    //         // // We're skipping all subfolders
    //         // if (!$file->isDir()) {
    //         //     $filePath = $file->getRealPath();
    //         //
    //         //     // extracting filename with substr/strlen
    //         //     $relativePath = substr($filePath, strlen($path) + 1);
    //         //     //dump($filePath);
    //         //     $zip->addFile($filePath, $relativePath);
    //         // }
    //         $zip->addFromString($file, $content);
    //     }
    //
    //     $zip->close();
    //     return response()->download($zip_file);
    //
    //
    // }
}
