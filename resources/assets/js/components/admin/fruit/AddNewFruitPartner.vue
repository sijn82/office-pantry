<template lang="html">
    <div>

        <h4> Add New Fruit Partner </h4>
        <b-form @submit="onSubmit" @reset="onReset" v-if="show" id="fruit-partner-form">
            <b-row>
                <!-- Name -->
                <b-col>
                    <label> Fruit Partner Name </label>
                    <b-form-input v-model="form.name" placeholder="" type="text" required>  </b-form-input>
                </b-col>
                <!-- Email -->
                <b-col>
                    <label> Email </label>
                    <b-form-input v-model="form.email" placeholder="" type="email">  </b-form-input>
                </b-col>
                <!-- Telephone -->
                <b-col>
                    <label> Telephone </label>
                    <b-form-input v-model="form.telephone" placeholder="" type="tel">  </b-form-input>
                </b-col>
                <!-- Url -->
                <b-col>
                    <label> Url </label>
                    <b-form-input v-model="form.url" placeholder="" type="url">  </b-form-input>
                </b-col>
            </b-row>
            <b-row class="row">
                
                <!-- Primary Contact -->
                <b-col>
                    <label> Primary Contact First Name </label>
                    <b-form-input v-model="form.primary_contact_first_name" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Primary Contact Surname </label>
                    <b-form-input v-model="form.primary_contact_surname" placeholder="" type="text">  </b-form-input>
                </b-col>
                <!-- Secondary Contact -->
                <b-col>
                    <label> Secondary Contact First Name </label>
                    <b-form-input v-model="form.secondary_contact_first_name" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Secondary Contact Surname </label>
                    <b-form-input v-model="form.secondary_contact_surname" placeholder="" type="text">  </b-form-input>
                </b-col>
            </b-row>
            <b-row class="row">
                
                <!-- Location -->
                <b-col>
                    <label> Address Line 1 </label>
                    <b-form-input v-model="form.address_line_1" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Address Line 2 </label>
                    <b-form-input v-model="form.address_line_2" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Address Line 3 </label>
                    <b-form-input v-model="form.address_line_3" placeholder="" type="text">  </b-form-input>
                </b-col>
            </b-row>
            <b-row class="row">
                <b-col>
                    <label> City </label>
                    <b-form-input v-model="form.city" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Region </label>
                    <b-form-input v-model="form.region" placeholder="" type="text">  </b-form-input>
                </b-col>
                <b-col>
                    <label> Postcode </label>
                    <b-form-input v-model="form.postcode" placeholder="" type="text">  </b-form-input>
                </b-col>
            </b-row>
            <b-row class="row">
                <!-- Alternative Phone Number -->
                <b-col>
                    <label> Alt Phone Number </label>
                    <b-form-input v-model="form.alt_phone" placeholder="" type="tel">  </b-form-input>
                </b-col>
                <!-- Weekly Action -->
                <b-col>
                    <label> Weekly Action </label>
                    <b-form-select v-model="form.weekly_action" :options="weekly_action_options">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-col>
                <!-- Changes Action -->
                <b-col>
                    <label> Changes Action </label>
                    <b-form-input v-model="form.changes_action" placeholder="">  </b-form-input>
                </b-col>
                <!-- No Of Customers Supplied -->
                <b-col>
                    <!-- We want this value in the database but not so sure this is the right way to document it, or keep accurate -->
                    <label> No. of Customers Supplied </label> 
                    <b-form-input v-model="form.no_of_customers" placeholder="" type="number">  </b-form-input>
                </b-col>
            </b-row>
            <b-row class="row">
                <!-- Additional Info -->
                <b-col>
                    <label> Additional Info </label>
                    <b-form-textarea v-model="form.additional_info" placeholder="" :rows="3" :max-rows="6">  </b-form-textarea>
                </b-col>
            </b-row>
            <b-row class="row">
                <b-col></b-col>
                <b-col></b-col>
                <b-col>
                    <b-button type="submit" variant="primary"> Submit </b-button>
                </b-col>
                <b-col>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </b-col>
                <b-col></b-col>
                <b-col></b-col>
            </b-row>
        </b-form>

    </div>
</template>

<style lang="scss" scoped>

    #fruit-partner-form {
        font-weight: 400;
        .row {
            padding-top: 10px;
        }
    }

</style>

<script>

export default {

    data() {
        return {
            form: {
                name: '',
                email: '',
                telephone: '',
                url: '',
                primary_contact: '',
                secondary_contact: '',
                alt_phone: '',
                location: '',
                coordinates: '',
                weekly_action: null,
                changes_action: '',
                no_of_customers: '',
                additional_info: '',
            },
            show: true,
            weekly_action_options: ['Email Only', 'Email and Call', 'Email and Answerphone', 'Email and Text', 'Text Only', 'Call Only', 'Answerphone Only'],
        } // end of data return
    }, // end of data
    methods: {
        onSubmit (evt) {
          evt.preventDefault();
          let self = this;
          // alert(JSON.stringify(this.form));
          axios.post('/api/office-pantry/fruit_partners/add-new-fruitpartner', {
              fruit_partner: self.form,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
          }).then( response => {
              alert('Uploaded new fruit partner successfully!');
              // location.reload(true); // <-- Quick fix to update Fruit Partner list once I've made it.
              console.log(response.data);
          }).catch(error => console.log(error));
        },
        onReset(evt) {
            evt.preventDefault()
            /* Reset our form values */
            this.form.name = '',
            this.form.email = '',
            this.form.telephone = '',
            this.form.url = '',
            this.form.primary_contact = '',
            this.form.secondary_contact = '',
            this.form.alt_phone = '',
            this.form.location = '',
            this.form.coordinates = '',
            this.form.weekly_action = null,
            this.form.changes_action = '',
            this.form.no_of_customers = '',
            this.form.additional_info = '',
            /* Trick to reset/clear native browser form validation state */
            this.show = false
            this.$nextTick(() => {
              this.show = true
            })
        }
    } // end of methods
}

</script>