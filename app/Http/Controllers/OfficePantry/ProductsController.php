<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Product;
use App\Allergy;
use App\AllergyInfo;

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
        $products = Product::with('allergy_info.allergy')->orderBy('brand', 'asc')->orderBy('flavour', 'asc')->get();

        // dd($products);

        return response()->json($products);
        // return view ('products', ['products' => $products]);
    }

    public function search(Request $request)
    {
        // $product_search = Product::where('name', 'LIKE', '%' . $request->keywords . '%')->get();
        $product_search = Product::with('allergy_info.allergy')->where('brand', 'ILIKE', '%' . $request->keywords_brand . '%')->get();

        return response()->json($product_search);
    }


    public function store(Request $request)
    {
        // Everything wrapped in this transaction must complete or the all the changes are rolled back to before the code within the transaction was run.
        DB::transaction(function () {
            //dd(request());

            $newProduct = new Product();
            $newProduct->is_active = request('product_data.is_active');
            $newProduct->brand = request('product_data.brand');
            $newProduct->flavour = request('product_data.flavour');
            $newProduct->code = request('product_data.code');
            $newProduct->buying_case_cost = request('product_data.buying_case_cost');
            $newProduct->selling_case_price = request('product_data.selling_case_price');
            $newProduct->buying_case_size = request('product_data.buying_case_size');
            $newProduct->selling_case_size = request('product_data.selling_case_size');
            $newProduct->buying_unit_cost = request('product_data.buying_unit_cost');
            $newProduct->selling_unit_price = request('product_data.selling_unit_price');
            $newProduct->vat = request('product_data.vat');
            $newProduct->supplier = request('product_data.supplier');
            $newProduct->sales_nominal = request('product_data.sales_nominal');
            $newProduct->profit_margin = request('product_data.profit_margin');
            $newProduct->stock_level = request('product_data.stock_level');
            //$newProduct->shortest_stock_date = $request['company_data']['shortest_stock_date']; <-- I think it's highly unlikely we'll have this info to hand when adding the product to the system.
            $newProduct->save();

            // Now we need to save the allergens to the product.  To do this we'll need to save the product first and retrieve the newly created id.
            // Which Laravel has kindly made immediately available to us.
            // Laravel also allows us to use the established variable to tap into its relationships.

            foreach (request('product_data.selected_allergens') as $selected_allergy) {

                $allergy = Allergy::where('slug', $selected_allergy)->first();
                // Due to the relationship already established, the connection_id and connection_type are automatically added.
                // This just leaves the allergy_id which I think has to be added by grabbing the matching allergy entry and saving it below.
                $newProduct->allergy_info()->create([
                    'allergy_id' => $allergy->id,
                ]);
            }
        });
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
        // Product Info
        $product = Product::find($id);
        // Product Allergies via allergy_info relationship
        $allergy_info = $product->allergy_info;
        // As I can't remove items from the request, saving this to a variable, then it works fine.
        $selected_allergens = request('selected_allergens');

        // This will check all held allergies for this product, removing them from the requested allergies to submit
        // i.e request('selected_allergies').  If the allergy isn't in the array, we're going to remove it from the held values.
        foreach ($product->allergy_info as $info) {

            if (in_array($info->allergy->slug, $selected_allergens)) {
                // Then we already have a record of this allergy/product combo
                $key = array_search($info->allergy->slug, $selected_allergens);
                // Get the key for this item in the array and remove it (from consideration).
                unset($selected_allergens[$key]);
            } else {
                // We have a record of the Product/Allergy but this no longer seems to be the case.
                // Perhaps it was added by mistake, or a new receipe has removed that allergen.
                AllergyInfo::destroy($info->id);
            }
        }

        // What we're left with should only be a list of allergies not currently associated with the product.
        foreach ($selected_allergens as $new_allergen) {
            // Grab info on this allergy by checking it's 'slug' value.
            //dd($selected_allergens);
            $allergy = Allergy::where('slug', $new_allergen)->first();
            // Save this info along with the product_id as a new allergy info connection.
            $newAllergyInfo = new AllergyInfo();
            $newAllergyInfo->allergy_id = $allergy->id;
            $newAllergyInfo->connection_id = $product->id;
            $newAllergyInfo->connection_type = 'App\\Product';
            $newAllergyInfo->save();
        }

        // dd($request);
        Product::where('id', $id)->update([
            'is_active' => request('is_active'),
            'brand' => request('brand'),
            'flavour' => request('flavour'),
            'code' => request('code'),
            'buying_case_cost' => request('buying_case_cost'),
            'selling_case_price' => request('selling_case_price'),
            'buying_case_size' => request('buying_case_size'),
            'selling_case_size' => request('selling_case_size'),
            'buying_unit_cost' => request('buying_unit_cost'),
            'selling_unit_price' => request('selling_unit_price'),
            'vat' => request('vat'),
            'supplier' => request('supplier'),
            'sales_nominal' => request('sales_nominal'),
            'profit_margin' => request('profit_margin'),
            'stock_level' => request('stock_level'),
            // NEITHER OF THESE ARE GOING TO BE ATTACHED TO THE PRODUCT TABLE, USING RELATIONAL TABLES INSTEAD.

            // 'allergen_info' => request('selected_allergens'),
            // 'dietary_requirements' => request('selected_dietary_requirements'),

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
        // Now if we destroy the product we should also destroy the connected allergies as well.

        DB::transaction(function () use ($id) {

            // dd($id);
            // destroy the connected allergies
            $product = Product::find($id);

            // Now we need to loop through each associated allergy_info and delete it.
            foreach ($product->allergy_info as $allergy_info) {

                $allergy_info->delete();
            }

            // Now we can destroy the product itself.
            Product::destroy($id);
        });



    }
}
