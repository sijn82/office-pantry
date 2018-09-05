<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New Rejigged Routes Upload </h3>
                        <p> This section will allow the rejigged routes CSV file to be uploaded, reordering the routes which can then reorder the picklists to match. </p>
                    </div>
                    <form class="" action="/api/import-rejigged-routes-csv" enctype="multipart/form-data" method="post" name="newRejiggedRoutes" @submit.prevent="uploadRejiggedRoutesCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label class="input-group-addon">Delivery Days:</label>
                                <select class="form-control" v-model="form.delivery_days" name="delivery_days">
                                    <option disabled value="">Select Delivery Days</option>
                                    <option value="mon-tue">Monday and Tuesday</option>
                                    <option value="wed-thur-fri">Wednesday, Thursday and Friday</option>
                                </select>
                            </div>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label class="input-group-addon">Upload Rejigged Routes CSV:</label>
                                <input class="form-control" type="file" name="rejigged_routes_csv" @change="newFileUpload">
                            </div>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Rejigged Routes CSV">
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
                rejigged_routes_csv: ''
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
          uploadCSV: function () {
              let self = this;
              axios.post('api/import-rejigged-routes-csv', {
                  delivery_days: self.form.delivery_days,
                  rejigged_routes_csv: self.form.rejigged_routes_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
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
