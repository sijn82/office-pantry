<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New Drinks, Snacks and Other Upload </h3>
                        <p><b> This section will allow new Snacks and Drinks CSV files to be uploaded and attached to the routes (prior to exporting and rejigging). </b></p>
                    </div>
                    <form class="" action="/api/upload-snacks-n-drinks-csv" enctype="multipart/form-data" method="post" name="newSnacksNDrinks" @submit.prevent="uploadSnacksNDrinksCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                <b-form-select
                                    v-model="form.delivery_days"
                                    :options="form.options">
                                </b-form-select>
                            </div>
                            <b-form-text>
                                Select the delivery days for processing and choose the Snacks, Drinks and Other file you wish to import.
                                This will be imported into the routes during the import snacks and drinks process.
                            </b-form-text>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload Snacks N Drinks CSV:</b></label>
                                <input class="form-control" type="file" name="snacks_n_drinks_csv" @change="newFileUpload">
                            </div>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Snacks N Drinks CSV">
                            </div>
                            <div>
                                <b-button href="api/import-drinks-n-snacks"> Import Drinks, Snacks and Other into Routes </b-button>
                                <b-button href="api/export-routing" variant="info"> Export Routes for Rerouting </b-button>
                            </div>
                    </form>
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

</style>

<script>

import axios from 'axios';
export default {
    data () {
        return {
            form: {
                delivery_days: '',
                snacks_n_drinks_csv: '',
                options: [
                    {value: null, text: 'Please Select an Option', disabled: true},
                    {value: 'mon-tue', text: 'Monday & Tuesday'},
                    {value: 'wed-thur-fri', text: 'Wednesday, Thursday & Friday'},
                ],
            },

        }
    },

    methods: {
          newFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.snacks_n_drinks_csv = event.target.result
              }
          },
          uploadSnacksNDrinksCSV: function () {
              let self = this;
              axios.post('api/upload-snacks-n-drinks-csv', {
                  delivery_days: self.form.delivery_days,
                  snacks_n_drinks_csv: self.form.snacks_n_drinks_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded Snacks, Drinks and Other CSV successfully!');
                  console.log(response.data);
                  console.log('saved Snacks N Drinks CSV successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import Snacks N Drinks CSV mounted.');
        }
}

</script>
