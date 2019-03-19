<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New Test Upload </h3>
                    </div>
                    <b-form validated action="/api/upload-test-csv" enctype="multipart/form-data" method="post" name="newTest" @submit.prevent="uploadTestCSV">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Upload Test CSV:</b></label>
                                <input class="form-control" type="file" name="test_csv" @change="newFileUpload" required>
                            </div>
                        
                            <div class="submit-button input-group input-group-md">
                                <input class="col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload Test CSV">
                            </div>
                    </b-form>

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

</style>

<script>

import axios from 'axios';
export default {
    data () {
        return {
            form: {
                test_csv: '',
            },

        }
    },
    props: ['success'],

    methods: {
          newFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.test_csv = event.target.result
              }
          },
          uploadTestCSV: function () {
              let self = this;
              axios.post('api/upload-test-csv', {
                  test_csv: self.form.test_csv,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id
              }).then(function (response) {
                  alert('Uploaded Test CSV successfully!');
                  console.log(response.data);
                  console.log('saved Test CSV successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import Test CSV mounted.');
        }
}

</script>
