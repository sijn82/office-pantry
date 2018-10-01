<template>
    <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="title-headers">
                        <h3> New Week Start Upload </h3>
                        <p class="col-md-10 offset-md-1">
                            <b> This section will allow a new Week Start Value to be uploaded. This is the 1st step in processing the weekly orders and must be updated prior to uploading any files for the new week! </b>
                        </p>
                    </div>
                    <form class="" action="/api/import-week-start" enctype="multipart/form-data" method="post" name="newWeekStart" @submit.prevent="uploadWeekStart">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Week Start:</b></label>
                                    <b-form-input
                                        v-model="form.week_start"
                                        type="date"
                                        placeholder="Edit the current week_start">
                                    </b-form-input>
                            </div>
                            <b-form-text>
                                After uploading the New Week Start, double check the 'Current Value' below to ensure the correct date is now in use.
                            </b-form-text>
                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Upload New Week Start">
                            </div>

                    </form>
                    <p><b>Current Value: {{ this.new_week_start }}</b></p>
                    <form class="" action="/api/import-week-start-days" enctype="multipart/form-data" method="post" name="deliveryDaysSelect" @submit.prevent="uploadDeliveryDays">

                            <div class="input-group input-group-md col-md-8 offset-md-2">
                                <label><b>Delivery Days:</b></label>
                                    <b-form-select
                                        v-model="form.delivery_days"
                                        placeholder="Please Select an Option"
                                        :options="form.options">
                                    </b-form-select>
                            </div>
                            <b-form-text>
                                After changing the selected Week Days, double check the 'Current Value' below to ensure the correct days are now in use.
                            </b-form-text>
                            <div class="submit-button input-group input-group-md">
                                <input class=" col-md-2 col-sm-3 offset-md-5 btn btn-success" type="submit" value="Change Delivery Days">
                            </div>
                    <p><b>Current Value: {{ this.delivery_days }}</b></p>
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
        label {
            margin-top: auto;
            margin-bottom: auto;
        }
    }
    // b-form-input[type=date]::-webkit-inner-spin-button,
    // b-form-input[type=date]::-webkit-outer-spin-button {
    //   -webkit-appearance: none;
    //   margin: 0;

</style>

<script>

import axios from 'axios';
export default {
    data () {
        return {
            types: ['date'],

            form: {
                week_start: '',
                delivery_days: null,
                options: [
                    {value: null, text: 'Please Select an Option', disabled: true},
                    {value: 'mon-tue', text: 'Monday & Tuesday'},
                    {value: 'wed-thur-fri', text: 'Wednesday, Thursday & Friday'},
                ],
            },
            new_week_start: this.week_started,
            updated_week_start: '',
        }
    },
    props: ['week_started', 'delivery_days'],

    watch: {
        // updated_week_start(){
        //     console.log('it changed');
        // }
    },
    methods: {
          newFileUpload(event) {
          let fileReader = new FileReader();
          fileReader.readAsDataURL(event.target.files[0])
              fileReader.onload = (event) => {
              this.form.week_start = event.target.result
              }
          },
          uploadWeekStart: function () {
              let self = this;
              axios.post('api/import-week-start', {
                  week_start: self.form.week_start,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  // user_id: self.userData.id
              }).then(function (response) {
                  location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                  console.log('saved Week_Start successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          },
          uploadDeliveryDays: function () {
              let self = this;
              axios.post('api/import-week-start-days', {
                  delivery_days: self.form.delivery_days,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                  // user_id: self.userData.id
              }).then(function (response) {
                  location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                  console.log('saved Delivery_Days successfully, or have i?');
              }).catch(error => console.log(error));
              // this.$router.push('/thank-you')
          }
    },

    mounted() {
            console.log('Component Import Week Start mounted.');
        }
}

</script>
