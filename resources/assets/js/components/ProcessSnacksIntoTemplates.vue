<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> Upload Current Snackbox Product Codes</h3>
                        <p><b> This section will allow the latest product codes to be married up and accurate to the latest snackbox orders. </b></p>
                    </div>
                    <form class="" action="api/upload-snackbox-product-codes" enctype="multipart/form-data" method="post" name="upload-products-and-codes-form" @submit.prevent="uploadSnackboxProductCodesCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                <b-form-select
                                    v-model="form.delivery_days"
                                    :options="form.options">
                                </b-form-select>
                            </div>
                            <b-form-text>
                                Select the delivery days for processing and choose the Product/Codes CSV.
                            </b-form-text>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload Products/Codes CSV:</b></label>
                                <input class="form-control" type="file" name="products_and_codes" @change="newProductCodesFileUpload">
                            </div>
                            <b-form-text>
                                If you need to change the file, click cancel on the currently held one before making the change, especially if you wish to select a file with the same name!
                            </b-form-text>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Products and Codes CSV">
                            </div>
                    </form>
                    <form class="" action="api/upload-snackbox-orders" enctype="multipart/form-data" method="post" name="upload-snackbox-orders-form" @submit.prevent="uploadSnackboxOrdersCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                <b-form-select
                                    v-model="form.delivery_days_orders"
                                    :options="form.options">
                                </b-form-select>
                            </div>
                            <b-form-text>
                                Select the delivery days for processing and choose the Product/Codes CSV.
                            </b-form-text>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload Snackbox Orders CSV:</b></label>
                                <input class="form-control" type="file" name="snackbox-orders" @change="newOrderUpload">
                            </div>
                            <b-form-text>
                                If you need to change the file, click cancel on the currently held one before making the change, especially if you wish to select a file with the same name!
                            </b-form-text>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Snackbox Orders CSV">
                            </div>
                    </form>
                    <div id="buttonsToProcessSnackboxes">
                        <div class="title-headers">
                            <h3> Process Snackboxes and Export as Excel Files </h3>
                            <p><b> Each button will export a seperate file to your downloads folder. </b></p>
                        </div>
                        <div id="saveToSessions">
                            <b-button href="auto_process_snackboxes"> Save Data To Sessions </b-button>
                            <b-form-text>
                                Run this process first, as it saves the uploaded data into session variables.  The data is destoyed after 30 mins of inactivity, but that should be more than enough ;).
                            </b-form-text> 
                        </div>
                        
                        <div id="exportMultiCompanyButtons">
                            <b-button :pressed.sync="op_multi" variant="outline-primary" href="export-snackbox-op-multicompany" target="_blank"> Export OP Multi Company </b-button>
                            <b-button :pressed.sync="dpd_multi" variant="outline-primary" href="export-snackbox-dpd-multicompany"> Export DPD Multi Company </b-button>
                            <b-button :pressed.sync="apc_multi" variant="outline-primary" href="export-snackbox-apc-multicompany"> Export APC Multi Company </b-button>
                            <b-form-text>
                                This first row of buttons will use the multi-company, single box templates.
                            </b-form-text> 
                        </div>
                        <div id="exportSingleCompanyButtons">
                            <b-button :pressed.sync="op_single" variant="outline-primary" href="export-snackbox-op-singlecompany"> Export OP Single Company </b-button>
                            <b-button :pressed.sync="dpd_single" variant="outline-primary" href="export-snackbox-dpd-singlecompany"> Export DPD Single Company </b-button>
                            <b-button :pressed.sync="apc_single" variant="outline-primary" href="export-snackbox-apc-singlecompany"> Export APC Single Company </b-button>
                            <b-form-text>
                                This second row uses the single company, multiple boxes template.
                            </b-form-text> 
                        </div>
                        <div id="exportUniqueButtons">
                            <b-button :pressed.sync="op_unique" variant="outline-primary" href="export-snackbox-op-unique"> Export OP Unique </b-button>
                            <b-button :pressed.sync="dpd_unique" variant="outline-primary" href="export-snackbox-dpd-unique"> Export DPD Unique </b-button>
                            <b-button :pressed.sync="apc_unique" variant="outline-primary" href="export-snackbox-apc-unique"> Export APC Unique </b-button>
                            <b-form-text>
                                This final row caters for unique orders, using the same multi-company template as the first row.
                            </b-form-text> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<style lang="scss">

    .title-headers {
        margin-top: 30px;
    }
    .input-group label {
        margin-right: 30px;
    }
    form div {
        margin-top: 10px;
        margin-bottom: 10px;
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
        }
    }

</style>

<script>

import axios from 'axios';
export default {
    data () {
        return {
            form: {
                delivery_days: '',
                delivery_days_orders: '',
                products_and_codes: '',
                options: [
                    {value: null, text: 'Please Select an Option', disabled: true},
                    {value: 'mon-tue', text: 'Monday & Tuesday'},
                    {value: 'wed-thur-fri', text: 'Wednesday, Thursday & Friday'},
                ],
            },
            op_multi: false,
            dpd_multi: false,
            apc_multi: false,
            op_single: false,
            dpd_single: false,
            apc_single: false,
            op_unique: false,
            dpd_unique: false,
            apc_unique: false,

        }
    },

    methods: {
          newProductCodesFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.products_and_codes = event.target.result
              }
          },
          newOrderUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.snackbox_orders = event.target.result
              }
          },
          uploadSnackboxProductCodesCSV: function () {
              let self = this;
              axios.post('api/upload-snackbox-product-codes', {
                  delivery_days: self.form.delivery_days,
                  products_and_codes: self.form.products_and_codes,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded Snackbox Product Codes CSV successfully!');
                  console.log(response.data);
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          },
          uploadSnackboxOrdersCSV: function () {
              let self = this;
              axios.post('api/upload-snackbox-orders', {
                  delivery_days: self.form.delivery_days_orders,
                  snackbox_orders: self.form.snackbox_orders,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded Snackbox Orders CSV successfully!');
                  console.log(response.data);
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Upload and Process Snackbox CSV mounted.');
        }
}

</script>
