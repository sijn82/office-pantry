<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

// require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = Product::all();
        
        return response()->json($products);
        // return view ('products', ['products' => $products]);
    }
    
    public function search(Request $request)
    {
        $product_search = Product::where('name', 'LIKE', '%' . $request->keywords . '%')->get();
        
        return response()->json($product_search);
    }
    
    
    public function store(Request $request)
    {    
        $newProduct = new Product();
        $newProduct->is_active = $request['company_data']['is_active'];
        $newProduct->code = $request['company_data']['code'];
        $newProduct->name = $request['company_data']['name'];
        $newProduct->case_price = $request['company_data']['case_price'];
        $newProduct->case_size = $request['company_data']['case_size'];
        $newProduct->unit_cost = $request['company_data']['unit_cost'];
        $newProduct->unit_price = $request['company_data']['unit_price'];
        $newProduct->vat = $request['company_data']['vat'];
        $newProduct->sales_nominal = $request['company_data']['sales_nominal'];
        $newProduct->cost_nominal = $request['company_data']['cost_nominal'];
        $newProduct->profit_margin = $request['company_data']['profit_margin'];
        $newProduct->stock_level = $request['company_data']['stock_level'];
        //$newProduct->shortest_stock_date = $request['company_data']['shortest_stock_date']; <-- I think it's highly unlikely we'll have this info to hand when adding the product to the system.
        $newProduct->save();
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        Product::where('id', $id)->update([
            'is_active' => request('is_active'),
            'code' => request('code'),
             'name' => request('name'),
             'case_price' => request('case_price'),
             'case_size' => request('case_size'),
             'unit_cost' => request('unit_cost'),
             'unit_price' => request('unit_price'),
             'vat' => request('vat'),
             'sales_nominal' => request('sales_nominal'),
             'cost_nominal' => request('cost_nominal'),
             'profit_margin' => request('profit_margin'),
             'stock_level' => request('stock_level'),
             'shortest_stock_date' => request('shortest_stock_date')
        ]);
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

    public function import(Request $request)
    {
        // dd($request);

        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');



        if(isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {



               $arr_file = explode('.', $_FILES['file']['name']);

               $extension = end($arr_file);

               var_dump($extension);

               if('csv' == $extension) {

                   $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

               } else {

                   $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

               }



           $spreadsheet = $reader->load($_FILES['file']['tmp_name']);



           $sheetData = $spreadsheet->getActiveSheet()->toArray();
           // dd($sheetData);
           // print_r($sheetData);

       }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCSV(Request $request)
    {
        if (($handle = fopen(public_path() . '/product-import-test-noheaders.csv', 'r')) !== FALSE) {

          while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            $productData = new Product();
            $productData->is_active = $data[0];
            $productData->code = $data[1];
            $productData->name = $data[2];
            $productData->case_price = $data[3];
            $productData->case_size = $data[4];
            $productData->unit_cost = $data[5];
            $productData->unit_price = $data[6];
            $productData->vat = $data[7];
            $productData->sales_nominal = $data[8];
            $productData->cost_nominal = $data[9];
            $productData->stock_level = $data[10];
        //    $productData->shortest_stock_date = $data[11];
            $productData->save();

        }
        fclose ($handle);
      }
        return redirect('/');
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Product $id)
    // {
    //     //
    //     $product = $id;
    // 
    //     return ['product' => $product];
    // }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Product::destroy($id);
    }
}
