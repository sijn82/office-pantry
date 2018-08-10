<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Input;
use App\Post;
use DB;
use Session;
use Excel;

class MaatwebsiteController extends Controller
{
    public function importExport()
    {
        return view('importExport');
    }
    public function downloadExcel($type)
    {
        $data = Post::get()->toArray();
        return Excel::create('laravelcode', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }
    public function importExcel(Request $request)
    {
        if($request->hasFile('import_file')){
            var_dump($request);
            var_dump('we got here');
            Excel::load($request->file('import_file')->getRealPath(), function ($reader) {
                foreach ($reader->toArray() as $key => $row) {
                    $data['name'] = $row['name'];
                    $data['primary_contact'] = $row['primary_contact'];
                    $data['primary_email'] = $row['primary_email'];
                    $data['secondary_email'] = $row['secondary_email'];
                    $data['address_line_1'] = $row['address_line_1'];
                    $data['address_line_2'] = $row['address_line_2'];
                    $data['city'] = $row['city'];
                    $data['region'] = $row['region'];
                    $data['postcode'] = $row['postcode'];
                    $data['branding theme'] = $row['branding theme'];
                    $data['supplier'] = $row['supplier'];

                    // $table->string('name');
                    // $table->string('primary_contact');
                    // $table->string('primary_email');
                    // $table->string('secondary_email');
                    // $table->string('address_line_1');
                    // $table->string('address_line_2');
                    // $table->string('city');
                    // $table->string('region');
                    // $table->string('postcode');
                    // $table->string('branding_theme');
                    // $table->string('supplier');

                    if(!empty($data)) {
                        DB::table('companies')->insert($data);
                    }
                }
            });
        }else {
          var_dump('well this is something');
        }

        Session::put('success', 'Your file was successfully imported into the database!!!');

        return back();
    }
}
