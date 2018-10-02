<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New Rejigged Routes Upload </h3>
                        <p><b>This section will allow the rejigged routes CSV file to be uploaded, reordering the routes which can then reorder the picklists to match.</b></p>
                    </div>
                    <form class="" action="/api/upload-rejigged-routes-csv" enctype="multipart/form-data" method="post" name="newRejiggedRoutes" @submit.prevent="uploadRejiggedRoutesCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                <b-form-select
                                    v-model="form.delivery_days"
                                    :options="form.options">
                                </b-form-select>
                            </div>
                            <b-form-text>
                                Select the delivery days for processing and choose the Rejigged Routing file you wish to import.
                                This will ... blah blah blah.
                            </b-form-text>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload Rejigged Routes CSV:</b></label>
                                <input class="form-control" type="file" name="rejigged_routes_csv" @change="newFileUpload">
                            </div>
                            <b-form-text>
                                If you need to change the file, click cancel on the currently held one before making the change, especially if you wish to select a file with the same name!
                            </b-form-text>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Rejigged Routes CSV">
                            </div>
                            <div>
                                <b-button href="api/rejig-routing"> Rejig Routes </b-button>
                                <b-button href="api/update-picklists-with-routes"> Rejig Picklists </b-button>
                                <b-button href="api/export-picklists" variant="info"> Export Picklists </b-button>
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
                rejigged_routes_csv: '',
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
              this.form.rejigged_routes_csv = event.target.result
              }
          },
          uploadRejiggedRoutesCSV: function () {
              let self = this;
              axios.post('api/upload-rejigged-routes-csv', {
                  delivery_days: self.form.delivery_days,
                  rejigged_routes_csv: self.form.rejigged_routes_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded Rejigged Routes CSV successfully!');
                  console.log(response.data);
                  console.log('saved Rejigged Routes CSV successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import Rejigged Routes CSV mounted.');
        }
}

</script>
