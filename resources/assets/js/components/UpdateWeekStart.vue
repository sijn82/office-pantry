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
                    
                    <!-- Really not happy with this v-for work around, I shouldn't need to pointlessly loop through the data in order to access its properties! -->
                    <p v-for="week_start in $store.state.week_start"><b> Current Value: {{ week_start.current }} </b></p>
                    
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
                    </form>
                    
                    <!-- Really not happy with this v-for work around, I shouldn't need to pointlessly loop through the data in order to access its properties! -->        
                    <p v-for="week_start in $store.state.week_start"><b> Current Value: {{ week_start.delivery_days }} </b></p>
                    
                </div>
                <div class="col-md-12">
                    <h3> Cron Data </h3>
                    <h5 class="info"> When orders were last advanced to 'next delivery' date and will next be advanced again. </h5>
                    <b-row>
                        <b-col> <h4> Command </h4> </b-col>
                        <b-col> <h4> Next Order Advance Date </h4> </b-col>
                        <b-col> <h4> Last Order Advance Date </h4> </b-col>
                        <b-col> </b-col> <!-- Empty column to align headers with content below and allow space for buttons on the right. -->
                    </b-row>
                    <div v-for="cron_data in $store.state.cron_data">
                        <b-row>
                            <b-col><p><b> {{ cron_data.command }} </b></p></b-col>
                            <!-- The only field it would be useful to edit manually (as an admin) would be the 'next_run', as changing the command would break everything and 'last_run' is otherwise irrelevant. -->
                            <b-col> <div v-if="edit"><b-form-input type="date" v-model="next_run"> </b-form-input></div> <div v-else> <p><b> {{ cron_data.next_run }} </b></p></div></b-col>
                            <b-col> <p><b> {{ cron_data.last_run }} </b></p> </b-col>
                            <b-col>
                                <b-button size="sm" variant="warning" @click="editCronData"> Edit </b-button> 
                                <b-button size="sm" variant="success" @click="saveCronData(cron_data.command)"> Save </b-button>
                            </b-col>
                        </b-row>
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
        label {
            margin-top: auto;
            margin-bottom: auto;
        }
    }
    .info {
        margin-bottom: 20px;
    }

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
                    { value: null, text: 'Please Select an Option', disabled: true },
                    { value: 'mon-tue', text: 'Monday & Tuesday' },
                    { value: 'wed-thur-fri', text: 'Wednesday, Thursday & Friday' },
                    { value: 'mon', text: 'Monday' },
                    { value: 'tue', text: 'Tuesday' },
                    { value: 'wed', text: 'Wednesday' },
                    { value: 'thur', text: 'Thursday' },
                    { value: 'fri', text: 'Friday' },
                ],
            },
            edit: false,
            next_run: '',
            //week_start: '',
            new_week_start: '',
            updated_week_start: '',
        }
    },
    // props: ['week_started', 'delivery_days'],

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
          },
          editCronData: function () {
              if (this.edit === false) {
                  this.edit = true;
              } else {
                  this.edit = false;
              }
          },
          saveCronData: function (command) {
              console.log(command);
              axios.post('api/cron-data/update', { next_run: this.next_run, command: command }).then( response => {
                  alert('Updated Next Cron Run');
                  location.reload(true);
              }).catch( error => console.log( error ));
          }
    },

    mounted() {
            console.log('Component Import Week Start mounted.');
            this.$store.commit('getWeekStart');
            this.$store.commit('getCronData');
        }
}

</script>
