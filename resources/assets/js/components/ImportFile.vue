<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New FOD Upload </h3>
                        <p> This section will allow new FOD (Fruit Ordering Document) CSV files to be uploaded. </p>
                    </div>
                    <form class="" action="/api/import-csv" enctype="multipart/form-data" method="post" name="newFOD" @submit.prevent="uploadCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label class="input-group-addon">Delivery Days:</label>
                                <select class="form-control" v-model="form.delivery_days" name="delivery_days">
                                    <option disabled value="">Select Delivery Days</option>
                                    <option value="mon_tue">Monday and Tuesday</option>
                                    <option value="wed_thur_fri">Wednesday, Thursday and Friday</option>
                                </select>
                            </div>

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label class="input-group-addon">Upload FOD CSV:</label>
                                <input class="form-control" type="file" name="fod_csv" @change="newFileUpload">
                            </div>

                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload FOD CSV">
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
                fod_csv: ''
            },

        }
    },

    methods: {
          newFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.fod_csv = event.target.result
              }
          },
          uploadCSV: function () {
              let self = this;
              axios.post('api/import-csv', {
                  delivery_days: self.form.delivery_days,
                  fod_csv: self.form.fod_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  console.log(response.data);
                  console.log('saved FOD CSV successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import CSV mounted.');
        }
}

</script>
