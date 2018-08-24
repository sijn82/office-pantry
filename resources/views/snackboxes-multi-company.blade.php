@extends('welcome')
@section('snackboxes-multi-company')

<div>
<h3> Display Single Snackboxes Multiple Companies </h3>

    @foreach($chunks as $chunk)

    @php
     $g = 0;
     $h = 0;
     $i = 0;
    @endphp

    @foreach ($chunk as $key => $snackbox)

    @php (array) $group[$i] = $snackbox @endphp
    @php $i++ @endphp
    @endforeach

    @php
    $productNames = [
    'SN_9BA_FRU'	=>	'9NINE Carob, Apricot and Hemp Seed',
    'SN_9BA_NUT'	=>	'9NINE Pumpkin and Sunflower Seed',
    'SN_9BA_ORI'	=>	'9NINE Original Seed',
    'SN_9BA_FLA'	=>	'9NINE Carob, Sesame and Flax',
    'SN_9BA_ALM'	=>	'9NINE Breakfast Berry and Almond Seed',
    'SN_9BA_APR'	=>	'9NINE Breakfast Peanut and Raisin',
    'SN_9BA_CAS'	=>	'9NINE Breakfast Cashew and Cocoa',
    'SN_9BA_KIC'	=>	'9NINE Double Cocoa and Cashew',
    'SN_9BA_PEA'	=>	'9NINE Peanut Seed',
    'SN_9BA_CHI'	=>	'9NINE Carob, Raspberry and Chia Seed',
    'SN_9BA_HAZ'	=>	'9NINE Double Cocoa and Hazelnut',
    'SN_9BA_RAS'	=>	'9NINE Double Cocoa and Raspberry',
    'SN_CLE_STR'	=>	'Clearly Scrumptious Strawberries',
    'SN_CLE_GOL'	=>	'Clearly Scrumptious Golden Berries',
    'SN_CLE_CRA'	=>	'Clearly Scrumptious Cranberries',
    'SN_GET_MIX'	=>	'Get Fruity Mixed Berry',
    'SN_GET_STR'	=>	'Get Fruity Strawberry',
    'SN_GET_MAN'	=>	'Get Fruity Mango',
    'SN_GET_RAS'	=>	'Get Fruity Raspberry',
    'SN_GET_PIN'	=>	'Get Fruity Pineapple, Coconut & Lime',
    'SN_GET_OG'	    =>	'Get Fruity Orange & Ginger',
    'SN_NKD_CCT'	=>	'Nakd Bar Pecan Pie',
    'SN_NKD_LEM'	=>	'Nakd Bar Lemon Drizzle',
    'SN_NKD_PNT'	=>	'Nakd Bar Peanut Delight',
    'SN_NKD_BKW'	=>	'Nakd Bar Bakewell Tart',
    'SN_NKD_CDL'	=>	'Nakd Bar Cocoa Delight',
    'SN_NKD_BBM'	=>	'Nakd Bar Blueberry Muffin',
    'SN_NKD_COG'	=>	'Nakd Bar Cocoa Orange',
    'SN_NKD_CSW'	=>	'Nakd Bar Cashew Cookie',
    'SN_NKD_BRY'	=>	'Nakd Bar Gingerbread',
    'SN_SBB_APL'	=>	'Snact Banana Bar Apple and Cinnamon',
    'SN_SBB_BTR'	=>	'Snact Banana Bar Beetroot and Cacao',
    'SN_SBB_CRT'	=>	'Snact Banana Bar Carrot and Spice',
    'SN_BAB_BRO'	=>	'Urban Cake Co Gluten Free Brownie Bar',
    'SN_BAB_PEC'	=>	'Urban Cake Co Nutty Salted Caramel Bar',
    'SN_BAB_BIL'	=>	'Urban Cake Co Peanut Butter Billionaire Bar',
    'SN_BAB_FLA'	=>	'Urban Cake Co Flapjack Bar',
    'SN_BAB_APR'	=>	'Urban Cake Co Apricot & White Chocolate Flapjack Bar',
    'SN_BAB_GRA'	=>	'Urban Cake Co Granola Bar',
    'SN_BAB_BRM'	=>	'Urban Cake Co Gluten Free Mini Brownie Bar',
    'SN_BAB_PEM'	=>	'Urban Cake Co Gluten Free Mini Nutty Salted Caramel Bar',
    'SN_BAB_FLM'	=>	'Urban Cake Co Mini Flapjack Bar',
    'SN_BAB_GRM'	=>	'Urban Cake Co Mini Granola Bar',
    'SN_BAB_BWC'	=>	'Urban Cake Co Triple Chocolate Cookie',
    'SN_BAB_TFC'	=>	'Urban Cake Co Toffee Fudge Cookie',
    'SN_BAB_RCC'	=>	'Urban Cake Co White Choc & Berry Cookie',
    'SN_PUL_ALM'	=>	'Pulsin Almond and Raisin Raw Choc Brownie',
    'SN_PUL_PCC'	=>	'Pulsin Peanut Choc Chip Raw Choc Brownie',
    'SN_PUL_SCB'	=>	'Pulsin Salted Caramel Raw Choc Brownie',
    'SN_PUL_MAP'	=>	'Pulsin Maple & Peanut Protein Booster',
    'SN_PUL_PCP'	=>	'Pulsin Peanut Choc Protein Booster',
    'SN_LUD_CAS'	=>	'Ludlow Nuts Cashews',
    'SN_LUD_ALM'	=>	'Ludlow Nuts Almonds',
    'SN_LUD_UN_'	=>	'Ludlow Nuts Un_Salted Peanuts',
    'SN_LUD_HON'	=>	'Ludlow Nuts Honey Roasted Peanuts',
    'SN_LUD_PIS'	=>	'Ludlow Nuts Pistachios',
    'SN_LUD_CHI'	=>	'Ludlow Nuts Chilli Roasted Mix',
    'SN_LUD_NUT'	=>	'Ludlow Salted Nut Mingle',
    'SN_LUD_AMD'	=>	'Ludlow Nuts 65g _ Almond',
    'SN_LUD_CHW'	=>	'Ludlow Nuts 65g _ Cashew',
    'SN_LUD_WAL'	=>	'Ludlow Nuts 65g _ Walnut',
    'SN_QUI_CHI'	=>	'Quibbles Chilli Honey Peanuts',
    'SN_QUI_ALM'	=>	'Quibbles Belgian Chocolate Almonds',
    'SN_QUI_PEP'	=>	'Quibbles Peppercorn Peanuts',
    'SN_QUI_JAL'	=>	'Quibbles Jalapeno Chilli & Kaffir Lime Peanuts',
    'SN_WHI_BER'	=>	'Whitworth\'s Berry & White Chocolate Shot',
    'SN_WHI_TOF'	=>	'Whitworth\'s Fruity Biscuit Shot',
    'SN_WHI_FRU'	=>	'Whitworth\'s Toffee & Pecan Shot',
    'SN_WHI_ORA'	=>	'Whitworth\'s Orange & Chocolate Seed Shot',
    'SN_WHI_RAI'	=>	'Whitworth\'s Raisin & Chocolate Shot',
    'SN_MGH_HCB'	=>	'Mighty Fine Honeycomb Bar',
    'SN_RHA_BBZ'	=>	'Real Handful Blueberry Blitz',
    'SN_RHA_GOJ'	=>	'Real Handful Go_Go_Goji Berries',
    'SN_RHA_MBC'	=>	'Real Handful Mixed Berry Crunch',
    'SN_BOU_TAM'	=>	'Boundless Tamari & Aleppo',
    'SN_BOU_CAY'	=>	'Boundless Cayenne & Rosemary',
    'SN_BOU_ORA'	=>	'Boundless Orange, Ginger & Maple',
    'SN_MON_DAR'	=>	'Montezuma\'s Very Dark',
    'SN_MON_BUT'	=>	'Montezuma\'s Butterscotch',
    'SN_MON_CHL'	=>	'Montezuma\'s Sea Dog',
    'SN_MON_ORG'	=>	'Montezuma\'s Minted',
    'SN_MON_MIL'	=>	'Montezuma\'s Milk',
    'SN_PKZ_ORA'	=>	'Peakz Chocolate Orange',
    'SN_PKZ_CAR'	=>	'Peakz Salted Caramel',
    'SN_PKZ_CHO'	=>	'Peakz Plain Chocolate',
    'SN_JUS_YOG'	=>	'Just Live a Little Yogurtberry Granola Superjacks',
    'SN_JUS_FRU'	=>	'Just Live a Little Fruit Chew Granola Superjacks',
    'SN_JUS_AGP'	=>	'Just Live a Little Almond Granola Pot',
    'SN_JUS_CGP'	=>	'Just Live a Little Cranberry & Cashew Granola Pot',
    'SN_JUS_SGP'	=>	'Just Live a Little Strawberry Granola Pot',
    'SN_CRE_GOJ'	=>	'Creative Nature Goji Goodness Flapjack',
    'SN_CRE_CAC'	=>	'Creative Nature Raw Cacao Flapjack',
    'SN_CRE_GIN'	=>	'Creative Nature Ginger Teatox Flapjack',
    'SN_ISL_SMO'	=>	'Isle of Wight Biltong Smoked',
    'SN_ISL_ORI'	=>	'Isle of Wight Biltong Original',
    'SN_ISL_BLA'	=>	'Isle of Wight Biltong Black Pepper',
    'SN_CBC_PEP'	=>	'Chichester Biltong Peppered Steak',
    'SN_CBC_PER'	=>	'Chichester Biltong Peri Peri',
    'SN_CBC_TER'	=>	'Chichester Biltong Teriyaki',
    'SN_CBC_CHI'	=>	'Chichester Biltong Chilli',
    'SN_CBC_GAR'	=>	'Chichester Biltong Garlic',
    'SN_CBC_BBQ'	=>	'Chichester Biltong BBQ',
    'SN_PBC_CHE'	=>	'Protein Ball Co Cacao & Orange',
    'SN_PBC_PBU'	=>	'Protein Ball Co Peanut Butter & Jam',
    'SN_PBC_RAS'	=>	'Protein Ball Co Goji & Coconut',
    'SN_PBC_LEM'	=>	'Protein Ball Co Coconut & Macadamia',
    'SN_SNA_MAN'	=>	'Snact Mango Fruit Jerky',
    'SN_SNA_RAS'	=>	'Snact Raspberry Fruit Jerky',
    'SN_SNA_BLU'	=>	'Snact Blueberry Fruit Jerky',
    'SN_TYR_CHE'	=>	'Tyrrell\'s Cheese & Chive',
    'SN_TYR_LUD'	=>	'Tyrrell\'s Sweet Chilli',
    'SN_TYR_SAL'	=>	'Tyrrell\'s Salt & Vinegar',
    'SN_TYR_SWE'	=>	'Tyrrell\'s Ham & Cranberry',
    'SN_TYR_PRC'	=>	'Tyrrell\'s Prawn Cocktail',
    'SN_TYR_WOR'	=>	'Tyrrell\'s Worcester Sauce',
    'SN_TYR_BEE'	=>	'Tyrrell\'s British Beef & Butcombe Ale',
    'SN_TYR_LIG'	=>	'Tyrrell\'s Lightly Sea Salted Crisps',
    'SN_TYR_ROA'	=>	'Tyrrell\'s Roast Chicken',
    'SN_TYR_BUT'	=>	'Dorset Sour Cream & Serenade Chilli',
    'SN_TYR_VEG'	=>	'Tyrrell\'s Vegetable',
    'SN_TYR_HAB'	=>	'Tyrrell\'s Rice Puffs',
    'SN_TYR_RIC'	=>	'Tyrrell\'s Corn Nuts',
    'SN_HEC_SEA'	=>	'Hectares Sweet Potato Sea Salt',
    'SN_HEC_CHO'	=>	'Hectares Sweet Potato Chorizo & Sun_dried Tomato',
    'SN_HEC_RED'	=>	'Hectares Sweet Potato Red Onion & Black Pepper',
    'SN_SUP_CHE'	=>	'Super Moons Cheese & Onion',
    'SN_SUP_VIN'	=>	'Super Moons Salt & Vinegar',
    'SN_PAS_TOM'	=>	'Pastinos Tomato & Sweet Basil',
    'SN_PAS_PES'	=>	'Pastinos Pesto',
    'SN_PAS_ARR'	=>	'Pastinos Arrabiata',
    'SN_PAS_CHI'	=>	'Pastinos Chianti & Olive',
    'SN_EAT_LEN'	=>	'Eat Real Lentil Tomato & Basil',
    'SN_EAT_CRE'	=>	'Eat Real Lentil Creamy Dill',
    'SN_EAT_SEA'	=>	'Eat Real Lentil Sea Salt',
    'SN_EAT_DIL'	=>	'Eat Real Hummus Creamy Dill',
    'SN_EAT_BAS'	=>	'Eat Real Hummus Tomato & Basil',
    'SN_EAT_SAL'	=>	'Eat Real Hummus Sea Salt',
    'SN_EAT_SOU'	=>	'Eat Real Quinoa Sour Cream & Chive',
    'SN_EAT_HOT'	=>	'Eat Real Quinoa Hot & Spicy',
    'SN_EAT_PLA'	=>	'Eat Real Quinoa Plain',
    'SN_EAT_VG'	    =>	'Eat Real Veggie Chips & Kale',
    'SN_EAT_VG2'	=>	'Eat Real Veggie Straws & Kale',
    'SN_WOL_PEA'	=>	'Wolfy\'s Porridge Pear & Ginger',
    'SN_WOL_HON'	=>	'Wolfy\'s Porridge Honey & Nut',
    'SN_WOL_CRE'	=>	'Wolfy\'s Porridge Creamy Berry',
    'SN_PEP_PEP'	=>	'Peppersmith Peppermint',
    'SN_PEP_SPE'	=>	'Peppersmith Spearmint',
    'SN_CHE_GUM'	=>	'Chewsy Chewing Gum',
    'SN_REA_CHI'	=>	'The Real Olive Company chilli & herb',
    'SN_REA_BAS'	=>	'The Real Olive Company basil & garlic',
    'SN_CHP_BP'	    =>	'Cheeky P\'s Roasted Chickpeas Black Pepper',
    'SN_CHP_CRY'	=>	'Cheeky P\'s Roasted Chickpeas Curry',
    'SN_NIB_PES'	=>	'Nibnibs Pesto',
    'SN_NIB_ROS'	=>	'Nibnibs Rosemary',
    'SN_NIB_SOC'	=>	'Nibnibs Sour Cream',
    'SN_NIB_COB'	=>	'Nibnibs Coconut Biscuits',
    'SN_NIB_OCB'	=>	'Nibnibs Orange & Cranberry Biscuits',
    'SN_JAS_CAC'	=>	'Joe & Seph\'s Gourmet Popcorn Coconut & Chia',
    'SN_JAS_SSC'	=>	'Joe & Seph\'s Gourmet Popcorn Sea Salt & Caramel',
    'SN_HOO_SMO'	=>	'HOOTS Smoked Bacon Rounds',
    'SN_HOO_CHO'	=>	'HOOTS Cheese & Onion Rounds',
    'SN_HOO_PEP'	=>	'HOOTS Salt & Pepper Rounds',
    'SN_PS_CHE'	    =>	'Popcorn Shed Say Cheese!',
    'SN_PS_BNU'	    =>	'Popcorn Shed Butterly Nuts',
    'SN_PS_PEC'	    =>	'Popcorn Shed Pecan Pie',
    'SN_PS_CHO'	    =>	'Popcorn Shed Pop N Choc',
    'SN_PS_SAL'	    =>	'Popcorn Shed Salted Caramel',
    'SN_PS_BER'	    =>	'Popcorn Shed Berrylicious',
    'SN_STO_APP'	=>	'Stoats Apple & Cinnamon Porridge Bars',
    'SN_STO_APR'	=>	'Stoats Apricot & Sultana Porridge Bars',
    'SN_STO_BLU'	=>	'Stoats Blueberry & Honey Porridge Bars',
    'SN_STO_CHE'	=>	'Stoats Cherry & Dark Chocolate Porridge Bars',
    'SN_STO_CLA'	=>	'Stoats Classic Original Porridge Bars',
    'SN_STO_FIG'	=>	'Stoats Fig & Date Porridge Bars',
    'SN_STO_ORA'	=>	'Stoats Orange & Dark Chocolate Porridge Bars',
    'SN_STO_RAS'	=>	'Stoats Raspberry & Honey Porridge Bars',
    'SN_STO_WHI'	=>	'Stoats White Chocolate & Hazelnut Porridge Bars',
    'SN_NAH_CP'	    =>	'Neat Healthy Apricots, Chia Seeds & Pumpkin Seeds',
    'SN_NAH_CC'	    =>	'Neat Healthy Cacao, Coconut & Chia Seeds',
    'SN_NAH_CS'	    =>	'Neat Healthy Banana, Chia Seeds & Multi Seeds',
    'SN_NAH_BC'	    =>	'Neat Healthy Blueberries & Chia Seeds',
    'SN_NAH_BQ'	    =>	'Neat Healthy Red Berries & Quinoa',
    'SN_LP_SWE'	    =>	'Lord Poppington Sweet & Salted Popcorn',
    'SN_LP_SAL'	    =>	'Lord Poppington Salted Popcorn',
    'SN_NT_WAS'	    =>	'Neat\'s Rice Crackers Wasabi',
    'SN_NT_SLT'	    =>	'Neat\'s Rice Crackers Sea Salt',
    'SN_NT_HTC'	    =>	'Neat\'s Rice Crackers Hot Thai Chilli'	,
    'SN_WP_SH'	    =>	'Willy\'s  Popcorn _ Salted Honey Popcorn'	,
    'SN_WP_NN'	    =>	'Willy\'s  Popcorn _ Nearly Naked'	,
    'SN_WOL_CUR'	=>	'Wolfy\'s Curry Couscous, Sultanas Lentils & Mango Chutney'	,
    'SN_WOL_GAR'	=>	'Wolfy\'s Garlic and Herb Couscous, Sundried Tomato Relish'	,
    'SN_WOL_VEG'	=>	'Wolfy\'s Root Veg Couscous with Sweet Onion Relish'	,
    'SN_CHE_GGS'	=>	'Cheggs Chocolate Eggs'	,
    'SN_CHI_PLS'	=>	'Chika\'s _ Plantain Salted' 	,
    'SN_CHI_PLC'	=>	'Chika\'s _ Plantain Chilli' 	,
    'SN_CHI_CLS'	=>	'Chika\'s _ Chickpea Lightly Spiced' 	,
    'SN_CHI_CSC'	=>	'Chika\'s _ Chickpea Smoked Chilli' 	,
    'SN_PIP_PAC'	=>	'Pip N Nut Peanut Butter Sachet'	,
    'SN_FRA_BIS'	=>	'Frank\'s Mixed Mini Biscuits' 	,
    'DR_ROU_E25'	=>	'Roundhill Coffee Expresso (250g)'	,
    'DR_ROU_F25'	=>	'Roundhill Coffee Filter (250g)'	,
    'DR_ROU_D25'	=>	'Roundhill Coffee Beans (250g)'	,
    'DR_CRU_DAR'	=>	'Cru Kafe (Nespresso) Dark Roast (12 Capsules)' 	,
    'DR_CRU_LIG'	=>	'Cru Kafe (Nespresso) Light Roast (12 Capsules)'	,
    'DR_CRU_INT'	=>	'Cru Kafe (Nespresso) Coorg_Intense (12 Capsules)'	,
    'DR_CRU_DEC'	=>	'Cru Kafe (Nespresso) Decaf (12 Capsules)' 	,
    'DR_COL_DIS'	=>	'Colonna (Nespresso) Discovery Capsules Short (10 Capsules)'	,
    'DR_COL_DIL'	=>	'Colonna (Nespresso) Discovery Capsules Long (10 Capsules)'	,
    'DR_COL_DES'	=>	'Colonna (Nespresso) Decaf Capsules Short (10 Capsules)' 	,
    'DR_COL_DEL'	=>	'Colonna (Nespresso) Decaf Capsules Long (10 Capsules)'	,
    'DR_LIT_FRE'	=>	'Little\'s Flavoured Instant French Vanilla (50g)' 	,
    'DR_LIT_RIC'	=>	'Little\'s Flavoured Instant Rich Hazelnut (50g)' 	,
    'DR_LIT_IRI'	=>	'Little\'s Flavoured Instant Irish Coffee (50g)' 	,
    'DR_LIT_CAF'	=>	'Little\'s Flavoured Instant Café Amaretto (50g)' 	,
    'DR_LIT_CAR'	=>	'Little\'s Flavoured Instant Chocolate Caramel (50g)' 	,
    'DR_LIT_MAP'	=>	'Little\'s Flavoured Instant Maple Walnut (50g)' 	,
    'DR_LIT_SWI'	=>	'Little\'s Flavoured Instant Swiss Chocolate (50g)' 	,
    'DR_LIT_HAV'	=>	'Little\'s Flavoured Instant Havana Rum (50g)' 	,
    'DR_LIT_SPI'	=>	'Little\'s Flavoured Instant Spicy Cardamom (50g)' 	,
    'DR_LIT_CHO'	=>	'Little\'s Flavoured Instant Chocolate Orange (50g)' 	,
    'DR_LIT_ISL'	=>	'Little\'s Flavoured Instant Island Coconut (50g)' 	,
    'DR_LIT_CHR'	=>	'Little\'s Flavoured Instant Christmas Spirit (50g)' 	,
    'DR_LIT_COL'	=>	'Little\'s Premium Instant Colombian (50g)' 	,
    'DR_LIT_AFR'	=>	'Little\'s Premium Instant African (50g)'	,
    'DR_LIT_ITA'	=>	'Little\'s Premium Instant Italian Roast (50g)'	,
    'DR_LIT_DEC'	=>	'Little\'s Premium Instant Decaffeinated (50g)'	,
    'DR_CLI_DES'	=>	'Clippers Instant Coffee Sticks Decaf (200 sticks)'	,
    'DR_CLI_NOR'	=>	'Clippers Instant Coffee Sticks Normal (200 sticks)' 	,
    'DR_CLI_HOT'	=>	'Clippers Instant Hot Chocolate Sachets'	,
    'DR_CLI_TEA'	=>	'Clipper Fairtrade Tea (1100 bags)' 	,
    'DR_PG_¬11'	    =>	'PG Tips (1150 bags)'	,
    'DR_YOR_¬12'	=>	'Yorkshire Tea (1200 bags)' 	,
    'DR_CLI_PEP'	=>	'Clipper Fairtrade Organic Peppermint (250 enveloped tea bags)' 	,
    'DR_CLI_ENG'	=>	'Clipper Fairtrade Organic English Breakfast (250 enveloped tea bags)' 	,
    'DR_CLI_EAR'	=>	'Clipper Fairtrade Organic Earl of Grey (250 enveloped tea bags)' 	,
    'DR_CLI_GRE'	=>	'Clipper Fairtrade Organic Green Tea (250 enveloped tea bags)' 	,
    'DR_CLI_CAM'	=>	'Clipper Fairtrade Organic Camomile (250 enveloped tea bags)' 	,
    'DR_CLI_RED'	=>	'Clipper Fairtrade Organic Infusion Redbush (250 enveloped tea bags)' 	,
    'DR_CLI_DEE'	=>	'Clipper Fairtrade Organic Decaf (250 enveloped tea bags)' 	,
    'DR_CLI_WIL'	=>	'Clipper Fairtrade Organic Wild Berry Infusion (250 enveloped tea bags)' 	,
    'DR_CLI_BLA'	=>	'Clipper Fairtrade Organic Blackcurrant & Acai Berry (250 enveloped tea bags)' 	,
    'DR_CLI_ARO'	=>	'Clipper Fairtrade Organic Everyday (250 enveloped tea bags)' 	,
    'DR_CLI_INF'	=>	'Clipper Fairtrade Organic Infusion Lemon & Ginger (250 enveloped tea bags)' 	,
    'DR_JOE_ENG'	=>	'Joe\'s Tea English Breakfast (100 bags)' 	,
    'DR_JOE_EAR'	=>	'Joe\'s Tea Earl of Grey (100 bags)' 	,
    'DR_JOE_GRE'	=>	'Joe\'s Tea Green (100 bags)' 	,
    'DR_JOE_JAS'	=>	'Joe\'s Tea Jasmine\'s Green Glory (100 bags)' 	,
    'DR_JOE_WHI'	=>	'Joe\'s Tea Whiter than White (100 bags)' 	,
    'DR_JOE_PRO'	=>	'Joe\'s Tea Proper Peppermint (100 bags)' 	,
    'DR_JOE_CHA'	=>	'Joe\'s Tea Chamonmile (100 bags)' 	,
    'DR_JOE_ST'	    =>	'Joe\'s Tea St Clements Lemon (100 bags)' 	,
    'DR_JOE_BER'	=>	'Joe\'s Tea Berry Blast (100 bags)' 	,
    'DR_JOE_MIN'	=>	'Joe\'s Tea Minted_Up_Fruit (100 bags)' 	,
    'DR_JOE_CHO'	=>	'Joe\'s Tea Chocca_Roo_Brew (100 bags)' 	,
    'DR_JEN_ENG'	=>	'Jenier Decaf English Breakfast Tea (100 bags)' 	,
    'DR_JEN_EAR'	=>	'Jenier Decaf Earl Grey (100 bags)' 	,
    'DR_PRI_ST5'	=>	'Princes Gates 500ml Still (24 Bottles)' 	,
    'DR_PRI_SP5'	=>	'Princes Gates 500ml Sparkling (24 Bottles)' 	,
    'DR_PRI_ST1'	=>	'Princes Gates 1500ml Still (12 Bottles)' 	,
    'DR_PRI_SP1'	=>	'Princes Gates 1500ml Sparkling (12 Bottles)' 	,
    'DR_PRI_ST3'	=>	'Princes Gates 330ml Glass Still (24 Bottles)' 	,
    'DR_PRI_SP3'	=>	'Princes Gates 330ml Glass Sparkling (24 Bottles)' 	,
    'DR_PRI_ST7'	=>	'Princes Gates 750ml Glass Still (12 Bottles)' 	,
    'DR_PRI_SP7'	=>	'Princes Gates 750ml Glass Sparkling (12 Bottles)' 	,
    'DR_LUS_ORA'	=>	'Luscombe Carrot & Orange Glass 250ml (24 Bottles)'	,
    'DR_LUS_GIN'	=>	'Luscombe Apple & Ginger Glass 250ml (24 Bottles)' 	,
    'DR_LUS_ELD'	=>	'Luscombe Apple & Elderflower Glass 250ml (24 Bottles)'	,
    'DR_LUS_APR'	=>	'Luscombe Apple & Apricot Glass 250ml (24 Bottles)'	,
    'DR_LUS_PEA'	=>	'Luscombe Apple & Pear Glass 250ml (24 Bottles)'	,
    'DR_LOV_PEA'	=>	'Lovely Pear 250ml (24 Bottles)' 	,
    'DR_LOV_GIN'	=>	'Lovely Apple & Ginger Glass 250ml (24 Bottles)'	,
    'DR_LOV_GLA'	=>	'Lovely  Apple Glass 250ml (24 Bottles)'	,
    'DR_LOV_RAS'	=>	'Lovely Presse Raspberry Lemonade Glass 250ml (24 Bottles)'	,
    'DR_LOV_DAN'	=>	'Lovely Presse Dandilion & Burdock Glass 250ml (24  Bottles)'	,
    'DR_LOV_ELD'	=>	'Lovely Presse Elderflower Glass 250ml (24 Bottles)'	,
    'DR_FRO_TOM'	=>	'Frobishers Tomato Glass 250ml (24 Bottles)'	,
    'DR_FRO_GRA'	=>	'Frobishers Grapefruit Glass 250ml (24 Bottles)'	,
    'DR_FRO_MAN'	=>	'Frobishers Mango Glass 250ml (24 Bottles)'	,
    'DR_FRO_APP'	=>	'Frobishers Apple Glass 250ml (24 Bottles)'	,
    'DR_FRO_CRA'	=>	'Frobishers Cranberry Glass 250ml (24 Bottles)'	,
    'DR_FRO_PIN'	=>	'Frobishers Pineapple Glass 250ml (24 Bottles)'	,
    'DR_FRO_ORA'	=>	'Frobishers Orange  Glass 250ml (24 Bottles)'	,
    'DR_BER_STB'	=>	'Berry White Still Pomegranate & Blueberry 330ml (12 Bottles)'	,
    'DR_BER_STP'	=>	'Berry White Still Peach 330ml (12 Bottles)'	,
    'DR_BER_STC'	=>	'Berry White Still Cranberry & Apple 330ml (12 Bottles)'	,
    'DR_BER_STL'	=>	'Berry White Still Lemon & Lime 330ml (12 Bottles)'	,
    'DR_BER_SPB'	=>	'Berry White Sparkling Pomegranate & Blueberry Can 250ml (24 Cans)'	,
    'DR_BER_SPP'	=>	'Berry White Sparkling Peach & Goji Berry Can 250ml (24 Cans)'	,
    'DR_BER_SPC'	=>	'Berry White Sparkling Cranberry & Guava Can 250ml (24 Cans)'	,
    'DR_BER_SPL'	=>	'Berry White Sparkling Lemon & Ginger Can 250ml (24 Cans)'	,
    'DR_BEL_CEL'	=>	'Belvoir Presse Cans Elderflower 250ml (12 Cans)' 	,
    'DR_BEL_CRL'	=>	'Belvoir Presse Cans Raspberry Lemonade 250ml (12 Cans)' 	,
    'DR_BEL_CCL'	=>	'Belvoir Presse Cans Coconut & Lime 250ml (12 Cans)' 	,
    'DR_BEL_GEL'	=>	'Belvoir Presse Glass Elderflower 250ml (24 Bottles)' 	,
    'DR_BEL_GRL'	=>	'Belvoir Presse Glass Raspberry & Lemonade 250ml (24 Bottles)' 	,
    'DR_BEL_GLL'	=>	'Belvoir Presse Glass Lime & Lemongrass 250ml (24 Bottles)' 	,
    'DR_BEL_GER'	=>	'Belvoir Presse Glass Elderflower & Rose 250ml (24 Bottles)' 	,
    'DR_BEL_GMP'	=>	'Belvoir Presse Glass Mango & Peach 250ml (24 Bottles)' 	,
    'DR_BEL_GCP'	=>	'Belvoir Presse Glass Cucumber & Peppermint 250ml (24 Bottles)' 	,
    'DR_BEL_ECO'	=>	'Belvoir Elderflower Cordial 500ml' 	,
    'DR_BEL_LCO'	=>	'Belvoir  Lemongrass Cordial 500ml' 	,
    'DR_BEL_RAR'	=>	'Belvoir Raspberry & Rose Cordial 500ml' 	,
    'DR_BEL_RAL'	=>	'Belvoir Raspberry & Lemon Cordial 500ml' 	,
    'DR_BEL_HLG'	=>	'Belvoir Honey, Lemon & Ginger Cordial 500ml' 	,
    'DR_BEL_BLB'	=>	'Belvoir Blueberry & Blackcurrent Cordial 500ml' 	,
    'DR_KAR_C25'	=>	'Karma Cola Cans 250ml (24 Cans)' 	,
    'DR_KAR_S25'	=>	'Karma Cola Sugar Free Cans 250ml (24 Cans)' 	,
    'DR_KAR_G25'	=>	'Karma Cola Gingerella Cans 250ml (24 Cans)' 	,
    'DR_KAR_L25'	=>	'Karma Cola Lemony Cans 250ml (24 Cans)'  	,
    'DR_KAR_GL3'	=>	'Karma Cola Glass 330ml (12 Bottles)' 	,
    'DR_KAR_GL4'	=>	'Karma Cola Sugar Free Glass 330ml (12 Bottles)' 	,
    'DR_KAR_GI3'	=>	'Karma Cola Gingerella Glass 330ml (12 Bottles)' 	,
    'DR_KAR_LE3'	=>	'Karma Cola Lemony Glass 330ml (12 Bottles)' 	,
    'DR_VIR_LEM'	=>	'Virtue Energy Water Lemon & Lime 250ml (24 Cans)'	,
    'DR_VIR_BER'	=>	'Virtue Energy Water Berries 250ml (24 Cans)' 	,
    'DR_JIM_ORI'	=>	'Jimmy\'s Iced Coffee Original 330ml (12 Cartons)' 	,
    'DR_JIM_SKI'	=>	'Jimmy\'s Iced Coffee Skinny 330ml (12 Cartons)'	,
    'DR_JIM_MOC'	=>	'Jimmy\'s Iced Coffee Mocha 330ml (12 Cartons)'	,
    'DR_BAM_CHO'	=>	'BAM Chocolate Milk Shake 330ml (12 Cartons)' 	,
    'DR_BAM_BAN'	=>	'BAM Banana Milk Shake 330ml (12 Cartons)' 	,
    'DR_SIM_GRA'	=>	'Simply Aloe Grape & Lemon 330ml (12 Cartons)' 	,
    'DR_SIM_ALO'	=>	'Simply Aloe Apple & Mango 330ml (12 Cartons)'	,
    'DR_CHI_COC'	=>	'Chi Coconut Water 330ml (12 Cartons)' 	,
    'DR_GM_VTA'	    =>	'Get More Vitamin A (12 Bottles)' 	,
    'DR_GM_VTB'	    =>	'Get More Vitamin B (12 Bottles)' 	,
    'DR_GM_VTC'	    =>	'Get More Vitamin C (12 Bottles)' 	,
    'DR_GM_VTD'	    =>	'Get More Vitamin D (12 Bottles)' 	,
    'DR_GM_MUL'	    =>	'Get More Vitamin Multivitamin (12 Bottles)' 	,
    'DR_JOH_APP'	=>	'Johsons Apple Juice 250ml (12 Bottles)'	,
    'DR_JOH_ORA'	=>	'Johsons Orange Juice 250ml (12 Bottles)' 	,
    'DR_TEA_ALL'	=>	'Tea Huggers All day Breakfast (100 bags)' 	,
    'DR_TEA_EAR'	=>	'Tea Huggers Earl Grey (100 bags)' 	,
    'DR_TEA_CHI'	=>	'Tea Huggers Chill Out (100 bags)' 	,
    'DR_TEA_DET'	=>	'Tea Huggers Detox (100 bags)' 	,
    'DR_TEA_SKI'	=>	'Tea Huggers Skinny Fit (100 bags)' 	,
    'DR_TEA_EVE'	=>	'Tea Huggers Ever Green (100 bags)' 	,
    'DR_TEA_FLU'	=>	'Tea Huggers Flu Fighter (100 bags)'	,
    'DR_TEA_GOO'	=>	'Tea Huggers Good Morning (100 bags)' 	,
    'DR_SUG_WHI'	=>	'Sugar Sticks White 1000 Sticks (2g)'	,
    'DR_SUG_DAM'	=>	'Sugar Sticks Damerara 1000 Sticks (2g)' 	,
    'DR_YOR_WB'	    =>	'York Coffee Emporium Coffee Beans (1kg)'	,
    'DR_WGN_GND'	=>	'York Coffee Emporium Ground Coffee (1kg)'	,
    'DR_CRB_ONE'	=>	'Craft Beer Delivery' 	,
    'DR_CRB_THR'	=>	'Craft Beer Delivery 3 Cases'	,
    'DR_CRB_CASE'	=>	'Craft Beer Full Case (24 Bottles)'	,
    'DR_WIN_PIN'	=>	'White Wine £7 a Bottle (6 Bottles)'	,
    'DR_WIN_PRI'	=>	'Red Wine £7 a Bottle (6 Bottles)'	,
    'DR_WIN_CAT'	=>	'Ca Di Ponti Catarrato (6 Bottles)'	,
    'DR_WIN_PRO'	=>	'Prosecco Di Paolo  (6 Bottles)'	,
    'DR_COC_COL'	=>	'Coca Cola (24 Cans)'	,
    'DR_COC_DIE'	=>	'Diet Coke (24 Cans)'	,
    'DR_FAN_ZER'	=>	'Fanta Zero (24 Cans)'	,
    'DR_PNT_BTR'	=>	'Peanut Butter'	,
    'DR_BRD_WML'	=>	'Wholemeal Bread'	,
    'DR_HON_SQU'	=>	'Rowse Runny Honey 340g'	,
    'DR_MAR_MTE'	=>	'Marmite 250g'	,
    'DR_BUT_YEO'	=>	'Yeo Valley Butter 250g'	,
    'DR_NAT_CPJ'	=>	'Natur Cold Press Juice (6 bottles)'	,
    'DR_PEP_MIN'	=>	'Peppersmith Peppermint Mints (1 case)'	,
    ];

    $productNames = array_flip($productNames);

    @endphp


    <table>
        <thead>
            <tr>
                <th> Packed By: ..................... </th>
                <th></th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>Product Name</th>
                <th>Total</th>
                <th>@if (isset($group[0]->company_name)) {{ $group[0]->company_name }} @endif</th>
                <th>@if (isset($group[1]->company_name)) {{ $group[1]->company_name }} @endif</th>
                <th>@if (isset($group[2]->company_name)) {{ $group[2]->company_name }} @endif</th>
                <th>@if (isset($group[3]->company_name)) {{ $group[3]->company_name }} @endif</th>
                <th>Packed?</th>
            </tr>
        </thead>
        <tbody>
            @php
            if (isset($group[0])) { (array) $group[0] = $group[0]->getAttributes(); }
            if (isset($group[1])) { (array) $group[1] = $group[1]->getAttributes(); }
            if (isset($group[2])) { (array) $group[2] = $group[2]->getAttributes(); }
            if (isset($group[3])) { (array) $group[3] = $group[3]->getAttributes(); }
            @endphp

            @php while($h <= 4) {
                if (isset($group[0]) && is_array($group[0])) { $unwanted = array_shift($group[0]); };
                if (isset($group[1]) && is_array($group[1])) { $unwanted = array_shift($group[1]); };
                if (isset($group[2]) && is_array($group[2])) { $unwanted = array_shift($group[2]); };
                if (isset($group[3]) && is_array($group[3])) { $unwanted = array_shift($group[3]); };
                $h++;
            }; @endphp

            @php while($g < 2) {
                if (isset($group[0]) && is_array($group[0])) { $unwanted = array_pop($group[0]); };
                if (isset($group[1]) && is_array($group[1])) { $unwanted = array_pop($group[1]); };
                if (isset($group[2]) && is_array($group[2])) { $unwanted = array_pop($group[2]); };
                if (isset($group[3]) && is_array($group[3])) { $unwanted = array_pop($group[3]); };
                $g++;
            }; @endphp

            @foreach ($group[0] as $key => $snack )

                @if (isset($group[0]))
                @php $snackOne = array_shift($group[0]); @endphp
                @endif

                @if (isset($group[1]))
                @php $snackTwo = array_shift($group[1]); @endphp
                @endif

                @if (isset($group[2]))
                @php $snackThree = array_shift($group[2]); @endphp
                @endif

                @if (isset($group[3]))
                @php $snackFour = array_shift($group[3]); @endphp
                @endif

                @php
                    $snackTotal = 0;

                    (isset($snackOne)) ? $snackTotal += $snackOne : $snackTotal += ($snackOne = 0);
                    (isset($snackTwo)) ? $snackTotal += $snackTwo : $snackTotal += ($snackTwo = 0);
                    (isset($snackThree)) ? $snackTotal += $snackThree : $snackTotal += ($snackThree = 0);
                    (isset($snackFour)) ? $snackTotal += $snackFour : $snackTotal += ($snackFour = 0);
                @endphp

                @if ($snackTotal != 0)
                @php $keyProductName = array_search($key, $productNames) ? array_search($key, $productNames) : $key; @endphp
                    <tr>
                        <td>{{ $keyProductName }}</td>
                        <td>{{ $snackTotal }}</td>

                        <td>{{ $snackOne }}</td>
                        @php unset($snackOne); @endphp

                        <td>{{ $snackTwo }}</td>
                        @php unset($snackTwo); @endphp

                        <td>{{ $snackThree }}</td>
                        @php unset($snackThree); @endphp

                        <td>{{ $snackFour }}</td>
                        @php unset($snackFour); @endphp
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>
                @php unset($group); @endphp
                @endforeach
</div>
@endsection

@section('routing_assets')
<style>

  h3.route_header {
    margin_top: 30px;
    margin_bottom: 80px;
    padding_left: 50px;
    padding_right: 50px;
  }

</style>
@endsection
