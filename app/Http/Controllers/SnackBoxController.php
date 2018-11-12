<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Session;

use Illuminate\Http\Request;
use App\SnackBox;
use App\WeekStart;



set_time_limit(0);
class SnackBoxController extends Controller
{
        protected $week_start;

        public function __construct()
        {
            // $this->week_start = 170918;
            $week_start = WeekStart::all()->toArray();
            $this->week_start = $week_start[0]['current'];
            $this->delivery_days = $week_start[0]['delivery_days'];

        }
        // There are a couple of options here, use the same function with a switch statement value based on the button pressed, or as I'm going to do for now, create several functions
        // one to handle each scenario.

        // Single Company, Multiple Boxes
        public function download_snackbox_op_singlecompany()
        {
            return \Excel::download(new Exports\SnackboxOPSingleCompanyExport, 'snackboxesOPSingleCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_singlecompany()
        {
            return \Excel::download(new Exports\SnackboxDPDSingleCompanyExport, 'snackboxesDPDSingleCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_singlecompany()
        {
            return \Excel::download(new Exports\SnackboxAPCSingleCompanyExport, 'snackboxesAPCSingleCompany' . $this->week_start . '.xlsx');
        }

        // Single Box, Multiple Companies
        public function download_snackbox_op_multicompany()
        {
            return \Excel::download(new Exports\SnackboxOPMultiCompanyExport, 'snackboxesOPMultiCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_multicompany()
        {
            return \Excel::download(new Exports\SnackboxDPDMultiCompanyExport, 'snackboxesDPDMultiCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_multicompany()
        {
            return \Excel::download(new Exports\SnackboxAPCMultiCompanyExport, 'snackboxesAPCMultiCompany' . $this->week_start . '.xlsx');
        }

        // Unique Box, Multiple Companies
        public function download_snackbox_op_unique()
        {
            return \Excel::download(new Exports\SnackboxOPUniqueExport, 'snackboxesOPUnique' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_unique()
        {
            return \Excel::download(new Exports\SnackboxDPDUniqueExport, 'snackboxesDPDUnique' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_unique()
        {
            return \Excel::download(new Exports\SnackboxAPCUniqueExport, 'snackboxesAPCUnique' . $this->week_start . '.xlsx');
        }

    // This is an attempt to send the data for snacks and drinks to the templates without troubling a database for anything.
    // This is to allow the product list to be dynamic and not hard coded, otherwise weekly code changes would be required and the database table rebuilt each time.

    public function upload_products_and_codes(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function
        // $delivery_days = $request->delivery_days ? 'wed-thur-fri' : '';
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->products_and_codes);
        // now we can decode the remainder of the encoded data string
        $requestcsv = base64_decode($request_mime_fix);
        // however it now has some unwated unicode characters i.e the 'no break space' - (U+00A0) and use json_encode to make them visible
        $csv_data_with_unicode_characters = json_encode($requestcsv);
        // now they're no longer hidden characters, we can strip them out of the data
        $csv_data_fixed = str_replace('\u00a0', ' ', $csv_data_with_unicode_characters);
        // and return the file ready for storage
        $ready_csv = json_decode($csv_data_fixed);

        // this is how we determine where to put the file, these variables are populated with the $week_start variable at the top of this class
        // and the request parameters attached to the form on submission.
        Storage::put('public/snackboxes/productcodes-' . $this->week_start . '-' . $delivery_days . '.csv', $ready_csv);

        $message = 'Uploaded latest product codes as of ' . $this->week_start . ' for delivery on ' . $delivery_days;

        Log::channel('slack')->info($message);
    }

    public function upload_snackbox_orders(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function
        // $delivery_days = $request->delivery_days ? 'wed-thur-fri' : '';
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->snackbox_orders);
        // now we can decode the remainder of the encoded data string
        $requestcsv = base64_decode($request_mime_fix);
        // however it now has some unwated unicode characters i.e the 'no break space' - (U+00A0) and use json_encode to make them visible
        $csv_data_with_unicode_characters = json_encode($requestcsv);
        // now they're no longer hidden characters, we can strip them out of the data
        $csv_data_fixed = str_replace('\u00a0', ' ', $csv_data_with_unicode_characters);
        // and return the file ready for storage
        $ready_csv = json_decode($csv_data_fixed);

        // this is how we determine where to put the file, these variables are populated with the $week_start variable at the top of this class
        // and the request parameters attached to the form on submission.
        Storage::put('public/snackboxes/snackbox_orders-' . $this->week_start . '-' . $delivery_days . '.csv', $ready_csv);

        $message = 'Uploaded latest snackbox orders as of ' . $this->week_start . ' for delivery on ' . $delivery_days;

        Log::channel('slack')->info($message);
    }

    // This function primarily uploads the snackbox/drinks orders and latest product lines, seperating them into groups and saving the collections to session variables.
    public function auto_process_snackboxes()
    {
                // this will pull in the product name/code information so long as the file exists to be read.
                // it is then saved to a $product_list variable and passed to the template.

              if (($handle = fopen('../storage/app/public/snackboxes/productcodes-' . $this->week_start . '-' . $this->delivery_days . '.csv', 'r')) !== FALSE) {

                    while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                        $product_list[$data[0]] = trim($data[1]);
                    }

                fclose ($handle);

                // dd($product_list);
              }

                // Next we need to do the same with this week's snackbox data which needs to be processed and spat out into the appropriate templates.

                if (($handle = fopen('../storage/app/public/snackboxes/snackbox_orders-' . $this->week_start . '-' . $this->delivery_days . '.csv', 'r')) !== FALSE) {

                      while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                            $company_order = $data;

                            // I don't like how anti DRY this seems but neither do I think a switch case will work or be more readable?

                            // if delivered by Office Pantry
                            if      ($company_order[0] == 'OP' && $company_order[1] > 1) { $snd_OP_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'OP' && $company_order[1] == 1) { $snd_OP_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'OP' && $company_order[1] == 0) { $snd_OP_uniqueBoxes[] = $company_order; }

                            // if delivered by DPD
                            if      ($company_order[0] == 'DPD' && $company_order[1] > 1) { $snd_DPD_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'DPD' && $company_order[1] == 1) { $snd_DPD_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'DPD' && $company_order[1] == 0) { $snd_DPD_uniqueBoxes[] = $company_order; }

                            // if delivered by APC
                            if      ($company_order[0] == 'APC' && $company_order[1] > 1) { $snd_APC_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'APC' && $company_order[1] == 1) { $snd_APC_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'APC' && $company_order[1] == 0) { $snd_APC_uniqueBoxes[] = $company_order; }
                      }
                  fclose ($handle);
                }

              // $chunks = [];

              // These 3 arrays need chunking into groups of four, so we can loop through them outputting 4 company orders per template.
              $snd_OP_singleBoxes_chunks = (!empty($snd_OP_singleBoxes)) ? array_chunk($snd_OP_singleBoxes, 4) : 'None for this week';
              $snd_DPD_singleBoxes_chunks = (!empty($snd_DPD_singleBoxes)) ? array_chunk($snd_DPD_singleBoxes, 4) : 'None for this week';
              $snd_APC_singleBoxes_chunks = (!empty($snd_APC_singleBoxes)) ? array_chunk($snd_APC_singleBoxes, 4) : 'None for this week';
              // Unique boxes will use the same multi-company template, and also need chunking into groups of 4.
              $snd_OP_UniqueBoxes_chunks = (!empty($snd_OP_uniqueBoxes)) ? array_chunk($snd_OP_uniqueBoxes, 4) : 'None for this week';
              $snd_DPD_UniqueBoxes_chunks = (!empty($snd_DPD_uniqueBoxes)) ? array_chunk($snd_DPD_uniqueBoxes, 4) : 'None for this week';
              $snd_APC_UniqueBoxes_chunks = (!empty($snd_APC_uniqueBoxes)) ? array_chunk($snd_APC_uniqueBoxes, 4) : 'None for this week';

              // Now the data has been chunked into groups of 4, we can add them to a session variable.
              session()->put('snackbox_OP_multicompany', $snd_OP_singleBoxes_chunks);
              session()->put('snackbox_DPD_multicompany', $snd_DPD_singleBoxes_chunks);
              session()->put('snackbox_APC_multicompany', $snd_APC_singleBoxes_chunks);
              // And do the same with unique boxes.
              session()->put('snackbox_OP_unique', $snd_OP_UniqueBoxes_chunks);
              session()->put('snackbox_DPD_unique', $snd_DPD_UniqueBoxes_chunks);
              session()->put('snackbox_APC_unique', $snd_APC_UniqueBoxes_chunks);

              // These 3 are for multiple box orders, which get a template of their own and the order split between the amount of boxes they require.
              if (!empty($snd_OP_multipleBoxes))     { session()->put('snackbox_OP_singlecompany', $snd_OP_multipleBoxes);      } else { session()->put('snackbox_OP_singlecompany', 'None for this week'); };
              if (!empty($snd_DPD_multipleBoxes))    { session()->put('snackbox_DPD_singlecompany', $snd_DPD_multipleBoxes);    } else { session()->put('snackbox_DPD_singlecompany', 'None for this week'); };
              if (!empty($snd_APC_multipleBoxes))    { session()->put('snackbox_APC_singlecompany', $snd_APC_multipleBoxes);    } else { session()->put('snackbox_APC_singlecompany', 'None for this week'); };

              // This is for the latest product list, we'll be matching these values up to the orders for each of the above variables.
              session()->put('snackbox_product_list', $product_list);

              // dd(session()->all());
              Log::channel('slack')->info('Snackbox data stored in sessions!');
              // This redirect was more for testing purposes as I want to redirect the user back to the upload snackbox and products page, with buttons to run/output each order option.
              return back();

              // This was also for testing purposes, so I could see the data being produced before exporting the results as an excel file.
              // return view('snackboxes-multi-company')->with('product_list', $product_list)->with('chunks', $snd_OP_singleBoxes_chunks);

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_OP()
    {
        //
        $snd_OP_multipleBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '>', 1)->get();

        $snd_OP_singleBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '=', 1)->get();

        $chunks = $snd_OP_singleBoxes->chunk(4);
        $chunks = $chunks->all();


        // dd($chunks);
        $snd_OP_uniqueBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '=', 0)->get();

        return view ('snackboxes-multi-company', ['snd_OP_singleBoxes' => $snd_OP_singleBoxes, 'snd_OP_multipleBoxes' => $snd_OP_multipleBoxes, 'snd_OP_uniqueBoxes' => $snd_OP_uniqueBoxes, 'chunks' => $chunks ]);
    }

    public function index_DPD()
    {
        //
        $snd_DPD = SnackBox::where('delivered_by', 'DPD')->get();
    }

    public function index_APC()
    {
        //
        $snd_APC = SnackBox::where('delivered_by', 'APC')->get();
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
    public function store(Request $request, $week_start = 60818)
    {
        //

              if (($handle = fopen(public_path() . '/snackboxes/snackboxes-' . $week_start . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

                    while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                        // function default_value()
                        // {
                        //     isset( $data ) ? $data : null;
                        //     return $this->data;
                        // }
                        //
                        // array_walk($data, 'default_value');

                        $newSnackBox = new SnackBox();
                        $newSnackBox->week_start = $data[0];
                        $newSnackBox->delivered_by = $data[1];
                        // $data[2] = The delivery? yes column which we need to keep for prior excel filtering but otherwise have no use for, so we jump to 3.
                        $newSnackBox->no_of_boxes_split_between = $data[3];
                        $newSnackBox->company_name = $data[4];
                        $newSnackBox->SN_9BA_FRU = $data[5];
                        $newSnackBox->SN_9BA_NUT = $data[6];
                        $newSnackBox->SN_9BA_ORI = $data[7];
                        $newSnackBox->SN_9BA_FLA = $data[8];
                        $newSnackBox->SN_9BA_ALM = $data[9];
                        $newSnackBox->SN_9BA_APR = $data[10];
                        $newSnackBox->SN_9BA_CAS = $data[11];
                        $newSnackBox->SN_9BA_KIC = $data[12];
                        $newSnackBox->SN_9BA_PEA = $data[13];
                        $newSnackBox->SN_9BA_CHI = $data[14];
                        $newSnackBox->SN_9BA_HAZ = $data[15];
                        $newSnackBox->SN_9BA_RAS = $data[16];
                        $newSnackBox->SN_CLE_STR = $data[17];
                        $newSnackBox->SN_CLE_GOL = $data[18];
                        $newSnackBox->SN_CLE_CRA = $data[19];
                        $newSnackBox->SN_GET_MIX = $data[20];
                        $newSnackBox->SN_GET_STR = $data[21];
                        $newSnackBox->SN_GET_MAN = $data[22];
                        $newSnackBox->SN_GET_RAS = $data[23];
                        $newSnackBox->SN_GET_PIN = $data[24];
                        $newSnackBox->SN_GET_OG = $data[25];
                        $newSnackBox->SN_NKD_CCT = $data[26];
                        $newSnackBox->SN_NKD_LEM = $data[27];
                        $newSnackBox->SN_NKD_PNT = $data[28];
                        $newSnackBox->SN_NKD_BKW = $data[29];
                        $newSnackBox->SN_NKD_CDL = $data[30];
                        $newSnackBox->SN_NKD_BBM = $data[31];
                        $newSnackBox->SN_NKD_COG = $data[32];
                        $newSnackBox->SN_NKD_CSW = $data[33];
                        $newSnackBox->SN_NKD_BRY = $data[34];
                        $newSnackBox->SN_SBB_APL = $data[35];
                        $newSnackBox->SN_SBB_BTR = $data[36];
                        $newSnackBox->SN_SBB_CRT = $data[37];
                        $newSnackBox->SN_BAB_BRO = $data[38];
                        $newSnackBox->SN_BAB_PEC = $data[39];
                        $newSnackBox->SN_BAB_BIL = $data[40];
                        $newSnackBox->SN_BAB_FLA = $data[41];
                        $newSnackBox->SN_BAB_APR = $data[42];
                        $newSnackBox->SN_BAB_GRA = $data[43];
                        $newSnackBox->SN_BAB_BRM = $data[44];
                        $newSnackBox->SN_BAB_PEM = $data[45];
                        $newSnackBox->SN_BAB_FLM = $data[46];
                        $newSnackBox->SN_BAB_GRM = $data[47];
                        $newSnackBox->SN_BAB_BWC = $data[48];
                        $newSnackBox->SN_BAB_TFC = $data[49];
                        $newSnackBox->SN_BAB_RCC = $data[50];
                        $newSnackBox->SN_PUL_ALM = $data[51];
                        $newSnackBox->SN_PUL_PCC = $data[52];
                        $newSnackBox->SN_PUL_SCB = $data[53];
                        $newSnackBox->SN_PUL_MAP = $data[54];
                        $newSnackBox->SN_PUL_PCP = $data[55];
                        $newSnackBox->SN_LUD_CAS = $data[56];
                        $newSnackBox->SN_LUD_ALM = $data[57];
                        $newSnackBox->SN_LUD_UN_ = $data[58];
                        $newSnackBox->SN_LUD_HON = $data[59];
                        $newSnackBox->SN_LUD_PIS = $data[60];
                        $newSnackBox->SN_LUD_CHI = $data[61];
                        $newSnackBox->SN_LUD_NUT = $data[62];
                        $newSnackBox->SN_LUD_AMD = $data[63];
                        $newSnackBox->SN_LUD_CHW = $data[64];
                        $newSnackBox->SN_LUD_WAL = $data[65];
                        $newSnackBox->SN_QUI_CHI = $data[66];
                        $newSnackBox->SN_QUI_ALM = $data[67];
                        $newSnackBox->SN_QUI_PEP = $data[68];
                        $newSnackBox->SN_QUI_JAL = $data[69];
                        $newSnackBox->SN_WHI_BER = $data[70];
                        $newSnackBox->SN_WHI_TOF = $data[71];
                        $newSnackBox->SN_WHI_FRU = $data[72];
                        $newSnackBox->SN_WHI_ORA = $data[73];
                        $newSnackBox->SN_WHI_RAI = $data[74];
                        $newSnackBox->SN_MGH_HCB = $data[75];
                        $newSnackBox->SN_RHA_BBZ = $data[76];
                        $newSnackBox->SN_RHA_GOJ = $data[77];
                        $newSnackBox->SN_RHA_MBC = $data[78];
                        $newSnackBox->SN_BOU_TAM = $data[79];
                        $newSnackBox->SN_BOU_CAY = $data[80];
                        $newSnackBox->SN_BOU_ORA = $data[81];
                        $newSnackBox->SN_MON_DAR = $data[82];
                        $newSnackBox->SN_MON_BUT = $data[83];
                        $newSnackBox->SN_MON_CHL = $data[84];
                        $newSnackBox->SN_MON_ORG = $data[85];
                        $newSnackBox->SN_MON_MIL = $data[86];
                        $newSnackBox->SN_PKZ_ORA = $data[87];
                        $newSnackBox->SN_PKZ_CAR = $data[88];
                        $newSnackBox->SN_PKZ_CHO = $data[89];
                        $newSnackBox->SN_JUS_YOG = $data[90];
                        $newSnackBox->SN_JUS_FRU = $data[91];
                        $newSnackBox->SN_JUS_AGP = $data[92];
                        $newSnackBox->SN_JUS_CGP = $data[93];
                        $newSnackBox->SN_JUS_SGP = $data[94];
                        $newSnackBox->SN_CRE_GOJ = $data[95];
                        $newSnackBox->SN_CRE_CAC = $data[96];
                        $newSnackBox->SN_CRE_GIN = $data[97];
                        $newSnackBox->SN_ISL_SMO = $data[98];
                        $newSnackBox->SN_ISL_ORI = $data[99];
                        $newSnackBox->SN_ISL_BLA = $data[100];
                        $newSnackBox->SN_CBC_PEP = $data[101];
                        $newSnackBox->SN_CBC_PER = $data[102];
                        $newSnackBox->SN_CBC_TER = $data[103];
                        $newSnackBox->SN_CBC_CHI = $data[104];
                        $newSnackBox->SN_CBC_GAR = $data[105];
                        $newSnackBox->SN_CBC_BBQ = $data[106];
                        $newSnackBox->SN_PBC_CHE = $data[107];
                        $newSnackBox->SN_PBC_PBU = $data[108];
                        $newSnackBox->SN_PBC_RAS = $data[109];
                        $newSnackBox->SN_PBC_LEM = $data[110];
                        $newSnackBox->SN_SNA_MAN = $data[111];
                        $newSnackBox->SN_SNA_RAS = $data[112];
                        $newSnackBox->SN_SNA_BLU = $data[113];
                        $newSnackBox->SN_TYR_CHE = $data[114];
                        $newSnackBox->SN_TYR_LUD = $data[115];
                        $newSnackBox->SN_TYR_SAL = $data[116];
                        $newSnackBox->SN_TYR_SWE = $data[117];
                        $newSnackBox->SN_TYR_PRC = $data[118];
                        $newSnackBox->SN_TYR_WOR = $data[119];
                        $newSnackBox->SN_TYR_BEE = $data[120];
                        $newSnackBox->SN_TYR_LIG = $data[121];
                        $newSnackBox->SN_TYR_ROA = $data[122];
                        $newSnackBox->SN_TYR_BUT = $data[123];
                        $newSnackBox->SN_TYR_VEG = $data[124];
                        $newSnackBox->SN_TYR_HAB = $data[125];
                        $newSnackBox->SN_TYR_RIC = $data[126];
                        $newSnackBox->SN_HEC_SEA = $data[127];
                        $newSnackBox->SN_HEC_CHO = $data[128];
                        $newSnackBox->SN_HEC_RED = $data[129];
                        $newSnackBox->SN_SUP_CHE = $data[130];
                        $newSnackBox->SN_SUP_VIN = $data[131];
                        $newSnackBox->SN_PAS_TOM = $data[132];
                        $newSnackBox->SN_PAS_PES = $data[133];
                        $newSnackBox->SN_PAS_ARR = $data[134];
                        $newSnackBox->SN_PAS_CHI = $data[135];
                        $newSnackBox->SN_EAT_LEN = $data[136];
                        $newSnackBox->SN_EAT_CRE = $data[137];
                        $newSnackBox->SN_EAT_SEA = $data[138];
                        $newSnackBox->SN_EAT_DIL = $data[139];
                        $newSnackBox->SN_EAT_BAS = $data[140];
                        $newSnackBox->SN_EAT_SAL = $data[141];
                        $newSnackBox->SN_EAT_SOU = $data[142];
                        $newSnackBox->SN_EAT_HOT = $data[143];
                        $newSnackBox->SN_EAT_PLA = $data[144];
                        $newSnackBox->SN_EAT_VG = $data[145];
                        $newSnackBox->SN_EAT_VG2 = $data[146];
                        $newSnackBox->SN_WOL_PEA = $data[147];
                        $newSnackBox->SN_WOL_HON = $data[148];
                        $newSnackBox->SN_WOL_CRE = $data[149];
                        $newSnackBox->SN_PEP_PEP = $data[150];
                        $newSnackBox->SN_PEP_SPE = $data[151];
                        $newSnackBox->SN_CHE_GUM = $data[152];
                        $newSnackBox->SN_REA_CHI = $data[153];
                        $newSnackBox->SN_REA_BAS = $data[154];
                        $newSnackBox->SN_CHP_BP = $data[155];
                        $newSnackBox->SN_CHP_CRY = $data[156];
                        $newSnackBox->SN_NIB_PES = $data[157];
                        $newSnackBox->SN_NIB_ROS = $data[158];
                        $newSnackBox->SN_NIB_SOC = $data[159];
                        $newSnackBox->SN_NIB_COB = $data[160];
                        $newSnackBox->SN_NIB_OCB = $data[161];
                        $newSnackBox->SN_JAS_CAC = $data[162];
                        $newSnackBox->SN_JAS_SSC = $data[163];
                        $newSnackBox->SN_HOO_SMO = $data[164];
                        $newSnackBox->SN_HOO_CHO = $data[165];
                        $newSnackBox->SN_HOO_PEP = $data[166];
                        $newSnackBox->SN_PS_CHE = $data[167];
                        $newSnackBox->SN_PS_BNU = $data[168];
                        $newSnackBox->SN_PS_PEC = $data[169];
                        $newSnackBox->SN_PS_CHO = $data[170];
                        $newSnackBox->SN_PS_SAL = $data[171];
                        $newSnackBox->SN_PS_BER = $data[172];
                        $newSnackBox->SN_STO_APP = $data[173];
                        $newSnackBox->SN_STO_APR = $data[174];
                        $newSnackBox->SN_STO_BLU = $data[175];
                        $newSnackBox->SN_STO_CHE = $data[176];
                        $newSnackBox->SN_STO_CLA = $data[177];
                        $newSnackBox->SN_STO_FIG = $data[178];
                        $newSnackBox->SN_STO_ORA = $data[179];
                        $newSnackBox->SN_STO_RAS = $data[180];
                        $newSnackBox->SN_STO_WHI = $data[181];
                        $newSnackBox->SN_NAH_CP = $data[182];
                        $newSnackBox->SN_NAH_CC = $data[183];
                        $newSnackBox->SN_NAH_CS = $data[184];
                        $newSnackBox->SN_NAH_BC = $data[185];
                        $newSnackBox->SN_NAH_BQ = $data[186];
                        $newSnackBox->SN_LP_SWE = $data[187];
                        $newSnackBox->SN_LP_SAL = $data[188];
                        $newSnackBox->SN_NT_WAS = $data[189];
                        $newSnackBox->SN_NT_SLT = $data[190];
                        $newSnackBox->SN_NT_HTC = $data[191];
                        $newSnackBox->SN_WP_SH = $data[192];
                        $newSnackBox->SN_WP_NN = $data[193];
                        $newSnackBox->SN_WOL_CUR = $data[194];
                        $newSnackBox->SN_WOL_GAR = $data[195];
                        $newSnackBox->SN_WOL_VEG = $data[196];
                        $newSnackBox->SN_CHE_GGS = $data[197];
                        $newSnackBox->SN_CHI_PLS = $data[198];
                        $newSnackBox->SN_CHI_PLC = $data[199];
                        $newSnackBox->SN_CHI_CLS = $data[200];
                        $newSnackBox->SN_CHI_CSC = $data[201];
                        $newSnackBox->SN_PIP_PAC = $data[202];
                        $newSnackBox->SN_FRA_BIS = $data[203];
                        $newSnackBox->DR_ROU_E25 = $data[204];
                        $newSnackBox->DR_ROU_F25 = $data[205];
                        $newSnackBox->DR_ROU_D25 = $data[206];
                        $newSnackBox->DR_CRU_DAR = $data[207];
                        $newSnackBox->DR_CRU_LIG = $data[208];
                        $newSnackBox->DR_CRU_INT = $data[209];
                        $newSnackBox->DR_CRU_DEC = $data[210];
                        $newSnackBox->DR_COL_DIS = $data[211];
                        $newSnackBox->DR_COL_DIL = $data[212];
                        $newSnackBox->DR_COL_DES = $data[213];
                        $newSnackBox->DR_COL_DEL = $data[214];
                        $newSnackBox->DR_LIT_FRE = $data[215];
                        $newSnackBox->DR_LIT_RIC = $data[216];
                        $newSnackBox->DR_LIT_IRI = $data[217];
                        $newSnackBox->DR_LIT_CAF = $data[218];
                        $newSnackBox->DR_LIT_CAR = $data[219];
                        $newSnackBox->DR_LIT_MAP = $data[220];
                        $newSnackBox->DR_LIT_SWI = $data[221];
                        $newSnackBox->DR_LIT_HAV = $data[222];
                        $newSnackBox->DR_LIT_SPI = $data[223];
                        $newSnackBox->DR_LIT_CHO = $data[224];
                        $newSnackBox->DR_LIT_ISL = $data[225];
                        $newSnackBox->DR_LIT_CHR = $data[226];
                        $newSnackBox->DR_LIT_COL = $data[227];
                        $newSnackBox->DR_LIT_AFR = $data[228];
                        $newSnackBox->DR_LIT_ITA = $data[229];
                        $newSnackBox->DR_LIT_DEC = $data[230];
                        $newSnackBox->DR_CLI_DES = $data[231];
                        $newSnackBox->DR_CLI_NOR = $data[232];
                        $newSnackBox->DR_CLI_HOT = $data[233];
                        $newSnackBox->DR_CLI_TEA = $data[234];
                        $newSnackBox->DR_PG_¬11 = $data[235];
                        $newSnackBox->DR_YOR_¬12 = $data[236];
                        $newSnackBox->DR_CLI_PEP = $data[237];
                        $newSnackBox->DR_CLI_ENG = $data[238];
                        $newSnackBox->DR_CLI_EAR = $data[239];
                        $newSnackBox->DR_CLI_GRE = $data[240];
                        $newSnackBox->DR_CLI_CAM = $data[241];
                        $newSnackBox->DR_CLI_RED = $data[242];
                        $newSnackBox->DR_CLI_DEE = $data[243];
                        $newSnackBox->DR_CLI_WIL = $data[244];
                        $newSnackBox->DR_CLI_BLA = $data[245];
                        $newSnackBox->DR_CLI_ARO = $data[246];
                        $newSnackBox->DR_CLI_INF = $data[247];
                        $newSnackBox->DR_JOE_ENG = $data[248];
                        $newSnackBox->DR_JOE_EAR = $data[249];
                        $newSnackBox->DR_JOE_GRE = $data[250];
                        $newSnackBox->DR_JOE_JAS = $data[251];
                        $newSnackBox->DR_JOE_WHI = $data[252];
                        $newSnackBox->DR_JOE_PRO = $data[253];
                        $newSnackBox->DR_JOE_CHA = $data[254];
                        $newSnackBox->DR_JOE_ST = $data[255];
                        $newSnackBox->DR_JOE_BER = $data[256];
                        $newSnackBox->DR_JOE_MIN = $data[257];
                        $newSnackBox->DR_JOE_CHO = $data[258];
                        $newSnackBox->DR_JEN_ENG = $data[259];
                        $newSnackBox->DR_JEN_EAR = $data[260];
                        $newSnackBox->DR_PRI_ST5 = $data[261];
                        $newSnackBox->DR_PRI_SP5 = $data[262];
                        $newSnackBox->DR_PRI_ST1 = $data[263];
                        $newSnackBox->DR_PRI_SP1 = $data[264];
                        $newSnackBox->DR_PRI_ST3 = $data[265];
                        $newSnackBox->DR_PRI_SP3 = $data[266];
                        $newSnackBox->DR_PRI_ST7 = $data[267];
                        $newSnackBox->DR_PRI_SP7 = $data[268];
                        $newSnackBox->DR_LUS_ORA = $data[269];
                        $newSnackBox->DR_LUS_GIN = $data[270];
                        $newSnackBox->DR_LUS_ELD = $data[271];
                        $newSnackBox->DR_LUS_APR = $data[272];
                        $newSnackBox->DR_LUS_PEA = $data[273];
                        $newSnackBox->DR_LOV_PEA = $data[274];
                        $newSnackBox->DR_LOV_GIN = $data[275];
                        $newSnackBox->DR_LOV_GLA = $data[276];
                        $newSnackBox->DR_LOV_RAS = $data[277];
                        $newSnackBox->DR_LOV_DAN = $data[278];
                        $newSnackBox->DR_LOV_ELD = $data[279];
                        $newSnackBox->DR_FRO_TOM = $data[280];
                        $newSnackBox->DR_FRO_GRA = $data[281];
                        $newSnackBox->DR_FRO_MAN = $data[282];
                        $newSnackBox->DR_FRO_APP = $data[283];
                        $newSnackBox->DR_FRO_CRA = $data[284];
                        $newSnackBox->DR_FRO_PIN = $data[285];
                        $newSnackBox->DR_FRO_ORA = $data[286];
                        $newSnackBox->DR_BER_STB = $data[287];
                        $newSnackBox->DR_BER_STP = $data[288];
                        $newSnackBox->DR_BER_STC = $data[289];
                        $newSnackBox->DR_BER_STL = $data[290];
                        $newSnackBox->DR_BER_SPB = $data[291];
                        $newSnackBox->DR_BER_SPP = $data[292];
                        $newSnackBox->DR_BER_SPC = $data[293];
                        $newSnackBox->DR_BER_SPL = $data[294];
                        $newSnackBox->DR_BEL_CEL = $data[295];
                        $newSnackBox->DR_BEL_CRL = $data[296];
                        $newSnackBox->DR_BEL_CCL = $data[297];
                        $newSnackBox->DR_BEL_GEL = $data[298];
                        $newSnackBox->DR_BEL_GRL = $data[299];
                        $newSnackBox->DR_BEL_GLL = $data[300];
                        $newSnackBox->DR_BEL_GER = $data[301];
                        $newSnackBox->DR_BEL_GMP = $data[302];
                        $newSnackBox->DR_BEL_GCP = $data[303];
                        $newSnackBox->DR_BEL_ECO = $data[304];
                        $newSnackBox->DR_BEL_LCO = $data[305];
                        $newSnackBox->DR_BEL_RAR = $data[306];
                        $newSnackBox->DR_BEL_RAL = $data[307];
                        $newSnackBox->DR_BEL_HLG = $data[308];
                        $newSnackBox->DR_BEL_BLB = $data[309];
                        $newSnackBox->DR_KAR_C25 = $data[310];
                        $newSnackBox->DR_KAR_S25 = $data[311];
                        $newSnackBox->DR_KAR_G25 = $data[312];
                        $newSnackBox->DR_KAR_L25 = $data[313];
                        $newSnackBox->DR_KAR_GL3 = $data[314];
                        $newSnackBox->DR_KAR_GL4 = $data[315];
                        $newSnackBox->DR_KAR_GI3 = $data[316];
                        $newSnackBox->DR_KAR_LE3 = $data[317];
                        $newSnackBox->DR_VIR_LEM = $data[318];
                        $newSnackBox->DR_VIR_BER = $data[319];
                        $newSnackBox->DR_JIM_ORI = $data[320];
                        $newSnackBox->DR_JIM_SKI = $data[321];
                        $newSnackBox->DR_JIM_MOC = $data[322];
                        $newSnackBox->DR_BAM_CHO = $data[323];
                        $newSnackBox->DR_BAM_BAN = $data[324];
                        $newSnackBox->DR_SIM_GRA = $data[325];
                        $newSnackBox->DR_SIM_ALO = $data[326];
                        $newSnackBox->DR_CHI_COC = $data[327];
                        $newSnackBox->DR_GM_VTA = $data[328];
                        $newSnackBox->DR_GM_VTB = $data[329];
                        $newSnackBox->DR_GM_VTC = $data[330];
                        $newSnackBox->DR_GM_VTD = $data[331];
                        $newSnackBox->DR_GM_MUL = $data[332];
                        $newSnackBox->DR_JOH_APP = $data[333];
                        $newSnackBox->DR_JOH_ORA = $data[334];
                        $newSnackBox->DR_TEA_ALL = $data[335];
                        $newSnackBox->DR_TEA_EAR = $data[336];
                        $newSnackBox->DR_TEA_CHI = $data[337];
                        $newSnackBox->DR_TEA_DET = $data[338];
                        $newSnackBox->DR_TEA_SKI = $data[339];
                        $newSnackBox->DR_TEA_EVE = $data[340];
                        $newSnackBox->DR_TEA_FLU = $data[341];
                        $newSnackBox->DR_TEA_GOO = $data[342];
                        $newSnackBox->DR_SUG_WHI = $data[343];
                        $newSnackBox->DR_SUG_DAM = $data[344];
                        $newSnackBox->DR_YOR_WB = $data[345];
                        $newSnackBox->DR_WGN_GND = $data[346];
                        $newSnackBox->DR_CRB_ONE = $data[347];
                        $newSnackBox->DR_CRB_THR = $data[348];
                        $newSnackBox->DR_CRB_CASE = $data[349];
                        $newSnackBox->DR_WIN_PIN = $data[350];
                        $newSnackBox->DR_WIN_PRI = $data[351];
                        $newSnackBox->DR_WIN_CAT = $data[352];
                        $newSnackBox->DR_WIN_PRO = $data[353];
                        $newSnackBox->DR_COC_COL = $data[354];
                        $newSnackBox->DR_COC_DIE = $data[355];
                        $newSnackBox->DR_FAN_ZER = $data[356];
                        $newSnackBox->DR_PNT_BTR = $data[357];
                        $newSnackBox->DR_BRD_WML = $data[358];
                        $newSnackBox->DR_HON_SQU = $data[359];
                        $newSnackBox->DR_MAR_MTE = $data[360];
                        $newSnackBox->DR_BUT_YEO = $data[361];
                        $newSnackBox->DR_NAT_CPJ = $data[362];
                        $newSnackBox->DR_PEP_MIN = $data[363];
                        $newSnackBox->save();

                        echo $data[4] . '<br>';
                    }
                fclose ($handle);
              }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
    }
}
