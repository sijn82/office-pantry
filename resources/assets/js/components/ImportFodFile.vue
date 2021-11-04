<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New FOD Upload </h3>
                        <p><b> This section will allow the latest FOD (Fruit Ordering Document) CSV file to be uploaded.</b></p>
                    </div>
                    <b-form validated action="/api/upload-fod-csv" enctype="multipart/form-data" method="post" name="newFOD" @submit.prevent="uploadFodCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                <b-form-select
                                placeholder="Please select an option"
                                    v-model="form.delivery_days"
                                    :options="form.options" required>
                                </b-form-select>
                            </div>
                            <b-form-text>
                                Select the delivery days for processing and choose the FOD file you wish to import.
                                This will save the file and automatically import the fod entries into the database.
                            </b-form-text>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload FOD CSV:</b></label>
                                <input class="form-control" type="file" name="fod_csv" @change="newFileUpload" required>
                            </div>
                            <b-form-text>
                                If you need to change the file, click cancel on the currently held one before making the change, especially if you wish to select a file with the same name!
                            </b-form-text>

                            <div class="submit-button input-group input-group-md">
                                <input class="col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload FOD CSV">
                                <b-button class="reset-fod" href="api/reset-fod" variant="danger">Reset FOD</b-button>
                            </div>
                    </b-form>
                    <div>
                        <b-button href="api/picklists-vs-fod"> Run FOD over Picklists </b-button>
                        <b-button href="api/update-routing" variant="warning"> Run FOD over Routes </b-button>
                        <b-button href="api/reset-routing" variant="danger"> Reset Routes </b-button>
                    </div>
                    <b-form-text text-variant="danger">
                        Warning: Do not run FOD over ROUTES more than once, as this will multiply the expected values.  The routes need resetting in the database (with the big red button on the right!) if the first run was unsuccessful.
                    </b-form-text>
                </div>
            </div>
        </div>
</template>

<style lang="scss">

    a.reset-fod {
        margin-left: 4px;
    }
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
    .form-control {
        height: auto;
    }

</style>

<script>

import axios from 'axios';
export default {
    data () {
        return {
            form: {
                delivery_days: '',
                fod_csv: '',
                options: [
                    {value: null, text: 'Please Select an Option', disabled: true},
                    {value: 'mon-tue', text: 'Monday & Tuesday'},
                    {value: 'wed-thur-fri', text: 'Wednesday, Thursday & Friday'},
                ],
            },

        }
    },
    props: ['success'],

    methods: {
          newFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.fod_csv = event.target.result
              }
          },
          uploadFodCSV: function () {
              let self = this;
              axios.post('api/upload-fod-csv', {
                  delivery_days: self.form.delivery_days,
                  fod_csv: self.form.fod_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded FOD CSV successfully!');
                  console.log(response.data);
                  console.log('saved FOD CSV successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import FOD CSV mounted.');
        }
}

</script>
