<template lang="html">
    <div class="margin-30">
        <h3> Export Stuff </h3>
        <b-row class="padding-top-20">
            <b-col></b-col>
            <b-col>
                <b-button href="api/export-fruitbox-picklists" variant="info"> Export Fruitbox Picklists </b-button>
            </b-col>
            <b-col>
                <b-button href="api/export-routing" variant="info"> Export Routes </b-button>
            </b-col>
            <!-- MOVED THIS TO THE EXPORT.BLADE.PHP TEMPLATE SO LARAVEL CAN HANDLE THE FILE UPLOAD.
                <b-col cols="6">
                <b-form enctype="multipart/form-data" @submit.prevent="importRejiggedRoutes">
                <b-form-file v-model="rejigged_routes_file" :state="Boolean(rejigged_routes_file)" placeholder="Choose a file..." drop-placeholder="Drop file here" @change="newFileUpload"></b-form-file>
                
                <b-button type="submit" variant="info"> Import Rejigged Routes (Unwritten) </b-button>
            </b-form>
            </b-col> -->
            <b-col>
                <b-button href="api/export-fruitpartner-deliveries" type="submit" variant="info"> Export Fruit Partner Deliveries </b-button>
            </b-col>
            <b-col></b-col>
        </b-row>
        <div class="padding-top-20">
            <h4> Override Box Totals </h4>
            <b-form-text class="margin-height-10"> Only run this override if you've manually adjusted the 'boxes' and/or 'drinks' column in the company routes and wish to fudge a new total. </b-form-text>
            <b-row>
                <b-col></b-col>
                <b-col></b-col>
                <b-col><b-button href="api/export-routing-override" variant="outline-danger"> Export Routes (Override) </b-button></b-col>
                <b-col></b-col>
                <b-col></b-col>
            </b-row>
            <b-form-text class="margin-height-10"> Make sure you <b class="warning-font-red"> run the regular routes button first </b> to get an accurate total for the other routes! </b-form-text>
        </div>
        <b-row>
            <b-col>
                <div id="buttonsToProcessSnackboxes">
                    <div class="title-headers">
                        <h3> Process Snackbox Orders into Picklists </h3>
                        <p><b> Each button will export a seperate file to your downloads folder. </b></p>
                    </div>
                    <div id="exportMultiCompanyButtons">
                        <h4> Companies Receiving One Box Only </h4>
                        <b-form-text class="margin-height-10"> Export the full week </b-form-text>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-op-multicompany"> Export OP (Weekly) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-dpd-multicompany"> Export DPD (Weekly) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-apc-multicompany"> Export APC (Weekly) </b-button>
                        <b-form-text class="margin-height-10"> Only export the selected delivery days </b-form-text>
                        <b-button variant="outline-primary" href="export-snackbox-op-multicompany"> Export OP (Selected Days) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-multicompany"> Export DPD (Selected Days) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-multicompany"> Export APC (Selected Days) </b-button>
                    </div>
                    <div id="exportSingleCompanyButtons">
                        <h4> Companies Receiving Multiple Boxes </h4>
                        <b-form-text class="margin-height-10"> Export the full week </b-form-text>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-op-singlecompany"> Export OP (Weekly) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-dpd-singlecompany"> Export DPD (Weekly) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-weekly-apc-singlecompany"> Export APC (Weekly) </b-button>
                        <b-form-text class="margin-height-10"> Only export the selected delivery days </b-form-text>
                        <b-button variant="outline-primary" href="export-snackbox-op-singlecompany"> Export OP (Selected Days) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-singlecompany"> Export DPD (Selected Days) </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-singlecompany"> Export APC (Selected Days) </b-button>
                    </div>
                    <div>
                        <h4> Unique Snackbox Items </h4>
                        <h6> (Peanut Butter, Himalayan Salt, Cereal etc) </h6>
                        <b-form-text class="margin-height-10"> Companies Receiving Multiple Boxes </b-form-text>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-op-singlecompany"> Export OP (Selected Days) </b-button>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-dpd-singlecompany"> Export DPD (Selected Days) </b-button>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-apc-singlecompany"> Export APC </b-button>
                        <b-form-text class="margin-height-10"> Companies Receiving One Box Only </b-form-text>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-op-multicompany"> Export OP (Selected Days) </b-button>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-dpd-multicompany"> Export DPD (Selected Days) </b-button>
                        <b-button variant="outline-secondary" href="export-snackbox-unique-apc-multicompany"> Export APC (Selected Days) </b-button>
                    </div>
                    <div>
                        <h4> Process Otherbox Orders into Checklists or Picklists </h4>
                        <b-button variant="outline-primary" href="export-otherbox-checklist-op"> Export OP Otherboxes (Checklist) </b-button>
                        <b-button variant="outline-primary" href="export-otherbox-checklist-op-weekly-total"> Export OP Otherboxes (Checklist) Weekly Total </b-button>
                        <b-button variant="outline-primary" href="export-otherbox-op-multicompany"> Export OP Otherboxes </b-button>
                        <b-form-text>
                            The checklist is for use when checking deliveries as they arrive, the other for adding to routes (pallets).
                        </b-form-text>
                    </div>
                    <!-- <div id="exportUniqueButtons">
                        <b-button variant="outline-primary" href="export-snackbox-op-unique"> Export OP Unique </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-unique"> Export DPD Unique </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-unique"> Export APC Unique </b-button>
                        <b-form-text>
                          This final row caters for unique orders, using the same multi-company template as the first row.
                        </b-form-text> 
                    </div> -->
                    <div id="exportWholesaleButtons">
                        <h4> Process Wholesale (Snackbox, Drinkbox & Otherbox) Orders into Picklists </h4>
                        <b-form-text class="margin-height-10"> Export the full week </b-form-text>
                        <b-button variant="outline-primary" href="export-wholesale-weekly-snackbox-op-singlecompany"> Export OP Snackboxes (Weekly) </b-button>
                        <b-button variant="outline-primary" href="export-wholesale-weekly-drinkbox-op-multicompany"> Export OP Drinkboxes (Weekly) </b-button>
                        <b-form-text class="margin-height-10"> Only export the selected delivery days </b-form-text>
                        <b-button variant="outline-primary" href="export-wholesale-snackbox-op-singlecompany"> Export OP Snackboxes (Selected Days) </b-button>
                        <b-button variant="outline-primary" href="export-wholesale-drinkbox-op-multicompany"> Export OP Drinkboxes (Selected Days) </b-button>
                        <!-- <b-button variant="outline-primary" href="export-wholesale-otherbox-op-multicompany"> Export OP Otherboxes (Not Needed?) </b-button> -->
                    </div>
                    <div id="exportMonthlySpecialButtons">
                        <h4> Process Monthly Special Items </h4>
                        <h6> Whether it's a Snack, Drink, Other (or a combination) they all get processed here! </h6>
                        <b-form-text class="margin-height-10"> Export the full week </b-form-text>
                        <b-button variant="outline-warning" href="export-monthly-special-op-weekly"> Export OP (Weekly) </b-button>
                        <b-button variant="outline-warning" href="export-monthly-special-dpd-weekly"> Export DPD (Weekly) </b-button>
                        <b-button variant="outline-warning" href="export-monthly-special-apc-weekly"> Export APC (Weekly) </b-button>
                        <b-form-text class="margin-height-10"> Only export the selected delivery days </b-form-text>
                        <b-button variant="outline-warning" href="export-monthly-special-op"> Export OP (Selected Days) </b-button>
                        <b-button variant="outline-warning" href="export-monthly-special-dpd"> Export DPD (Selected Days) </b-button>
                        <b-button variant="outline-warning" href="export-monthly-special-apc"> Export APC (Selected Days) </b-button>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>
    .margin-30 {
        margin: 30px;
    }
    .padding-top-20 {
        padding-top: 20px;
    }
    .warning-font-red {
        color: red;
    }
    .margin-height-10 {
        margin: 10px 0;
    }
    #buttonsToProcessSnackboxes {
        margin-top: 20px;
        div {
            margin: 10px;
        
            #buttonsToProcessSnackboxes {
                
            }
            #exportMultiCompanyButtons {
            
            }
            #exportSingleCompanyButtons {
            
            }
            #exportUniqueButtons {
            
            }
            #exportWholesaleButtons {
            
            }
        }
    }
</style>

<script>
export default {
    data () {
        return {
            rejigged_routes_file: null,
        }
    },
    methods: {
        newFileUpload(event) {
        // 
        // let formData = new FormData();
        //     formData.append(this.rejigged_routes_file, event.target.result)
        //     console.log(formData)
        let fileReader = new FileReader();
        fileReader.readAsDataURL(event.target.files[0])
            fileReader.onload = (event) => {
            this.rejigged_routes_file = event.target.result
            }
        },
        
        importRejiggedRoutes() {
            let self = this;
            axios.post('api/import-rejigged-routes', {
                rejigged_routes: self.rejigged_routes_file,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/xlsx'},
                // user_id: self.userData.id
            }).then(function (response) {
                alert('Uploaded Rejigged Routes successfully!');
                console.log(response.data);
                // console.log('saved FOD CSV successfully, or have i?');
            }).catch(error => console.log(error));
            // this.$router.push('/thank-you')
        }
    }
}
</script>
