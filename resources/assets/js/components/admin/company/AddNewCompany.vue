<template lang="html">
    <div>
        <h3> Add New Company </h3>
        <div class="form-margin-40">
            <b-form @submit="onSubmit" @reset="onReset" v-if="show">
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Company Names </h4>
                        <p> * denotes a required field. </p>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Invoice Name* </label>
                        <b-form-input v-model="form.invoice_name" required></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Route Name* </label>
                        <b-form-input v-model="form.route_name" required></b-form-input>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Primary Contact </h4>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Primary Contact Name </label>
                        <b-form-input v-model="form.primary_contact_name"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Primary Contact Job Title </label>
                        <b-form-input v-model="form.primary_contact_job_title"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Primary Contact Email </label>
                        <b-form-input type="email" v-model="form.primary_contact_email"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Primary Contact Telephone </label>
                        <b-form-input v-model="form.primary_contact_telephone"></b-form-input>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Secondary Contact </h4>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Secondary Contact Name </label>
                        <b-form-input v-model="form.secondary_contact_name"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Secondary Contact Job Title </label>
                        <b-form-input v-model="form.secondary_contact_job_title"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Secondary Contact Email </label>
                        <b-form-input type="email" v-model="form.secondary_contact_email"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Secondary Contact Telephone </label>
                        <b-form-input v-model="form.secondary_contact_telephone"></b-form-input>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Delivery Info </h4>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Delivery Information </label>
                        <b-form-textarea v-model="form.delivery_information" :rows="3" :max-rows="6"></b-form-textarea>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Route Address Info </h4>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Route Address Line 1* </label>
                        <b-form-input v-model="form.route_address_line_1" required></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Route Address Line 2 </label>
                        <b-form-input v-model="form.route_address_line_2"></b-form-input>
                    </b-col>
                    
                    <!-- Initially this seemed like a good idea, 
                    then we thought xero wouldn't want this field so was going to scrap it 
                    but it turns out xero can accept a 3rd address line so commenting it out 
                    but keeping it here for if/when we want it later -->
                    
                    <!-- <b-col>
                        <label> Route Address Line 3 </label>
                        <b-form-input v-model="form.route_address_line_3"></b-form-input>
                    </b-col> -->
                    <!-- what the heck is this 'h' doing here? I'll make sure it's a typo before deleting and forgetting all about it. -->
                    <b-col>
                        <label> Route City* </label>
                        <b-form-input v-model="form.route_address_city" required></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Route Region </label>
                        <b-form-input v-model="form.route_address_region"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Route Postcode* </label>
                        <b-form-input v-model="form.route_address_postcode" required></b-form-input>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <h4> Invoice Address Info </h4>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label> Invoice Address Line 1 </label>
                        <b-form-input v-model="form.invoice_address_line_1"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Invoice Address Line 2 </label>
                        <b-form-input v-model="form.invoice_address_line_2"></b-form-input>
                    </b-col>
                    
                    <!-- Initially this seemed like a good idea, 
                    then we thought xero wouldn't want this field so was going to scrap it 
                    but it turns out xero can accept a 3rd address line so commenting it out 
                    but keeping it here for if/when we want it later -->
                    
                    <!-- <b-col>
                        <label> Invoice Address Line 3 </label>
                        <b-form-input v-model="form.invoice_address_line_3"></b-form-input>
                    </b-col> -->
                    
                    <b-col>
                        <label> Invoice City </label>
                        <b-form-input v-model="form.invoice_address_city"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Invoice Region </label>
                        <b-form-input v-model="form.invoice_address_region"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Invoice Postcode </label>
                        <b-form-input v-model="form.invoice_address_postcode"></b-form-input>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col> 
                        <label> Invoice Email </label> 
                        <b-form-input type="email" v-model="form.invoice_email"></b-form-input>
                    </b-col>
                </b-row>
                
                <b-row class="margin-top-20">
                    <b-col>
                        <label> Branding Theme </label>
                        <b-form-select v-model="form.branding_theme" :options="payment_options"></b-form-select>
                    </b-col>
                    <b-col>
                        <label> Surcharge (%) </label>
                        <b-form-input v-model="form.surcharge" type="number"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Delivered By </label>
                        <b-form-select v-model="form.supplier_id">
                            <template slot="first">
                                    <option :value="null" disabled> Please select an option </option>
                            </template>
                            <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                        </b-form-select>
                    </b-col>
                    <b-col>
                        <label> Model </label>
                        <b-form-select v-model="form.model" :options="model_options"></b-form-select>
                    </b-col>
                </b-row>
                <b-row class="margin-top-20">
                    <b-col>
                        <b-button type="submit" variant="success"> Submit </b-button>
                        <b-button type="reset" variant="danger"> Reset </b-button>
                    </b-col>
                </b-row>
            </b-form>
        </div>
    </div>
</template>

<style lang="scss" scoped>

    label, p {
        font-weight: 300;
    }
    .form-margin-40 {
        margin: 40px;
    }
    .margin-top-20 {
        margin-top: 20px;
    }

</style>

<script>

export default {
    data () {
        return {
            form: {
                invoice_name: '',
                route_name: '',
                primary_contact_name: '',
                primary_contact_job_title: '',
                primary_contact_email: '',
                primary_contact_telephone: '',
                secondary_contact_name: '',
                secondary_contact_job_title: '',
                secondary_contact_email: '',
                secondary_contact_telephone: '',
                delivery_information: '',
                route_address_line_1: '',
                route_address_line_2: '',
                route_address_line_3: '',
                route_address_city: '',
                route_address_region: '',
                route_address_postcode: '',
                invoice_address_line_1: '',
                invoice_address_line_2: '',
                invoice_address_line_3: '',
                invoice_address_city: '',
                invoice_address_region: '',
                invoice_address_postcode: '',
                invoice_email: '',
                branding_theme: null,
                surcharge: null,
                supplier_id: null,
                model: 'Free',
            },
            show: true,
            payment_options: [
              { text: 'Please select an option', value: null },
              'BACS', 'GoCardless', 'GoCardless In Advance', 'Invoiced In Advance',
              'Monthly Invoice', 'Monthly Invoice GoCardless', 'Paypal (Stripe)', 'Paypal In Advance (Stripe)',
              'Weekly Standing Order', 'Monthly Standing Order', 'Weekly Standing Order In Advance', 'Monthly Standing Order In Advance', 'TBC'
            ],
            model_options: ['Free', 'Honesty Box'],
        }
    },
    methods: {
        onSubmit (evt) {
            evt.preventDefault();
            let self = this;
            // alert(JSON.stringify(this.form));
            axios.post('/api/company-details/add-new-company', {
              company_details: {

                  invoice_name: self.form.invoice_name,
                  route_name: self.form.route_name,
                  primary_contact_name: self.form.primary_contact_name,
                  primary_contact_job_title: self.form.primary_contact_job_title,
                  primary_contact_email: self.form.primary_contact_email,
                  primary_contact_telephone: self.form.primary_contact_telephone,
                  secondary_contact_name: self.form.secondary_contact_name,
                  secondary_contact_job_title: self.form.secondary_contact_job_title,
                  secondary_contact_email: self.form.secondary_contact_email,
                  secondary_contact_telephone: self.form.secondary_contact_telephone,
                  delivery_information: self.form.delivery_information,
                  route_address_line_1: self.form.route_address_line_1,
                  route_address_line_2: self.form.route_address_line_2,
                  route_address_line_3: self.form.route_address_line_3,
                  route_address_city: self.form.route_address_city,
                  route_address_region: self.form.route_address_region,
                  route_address_postcode: self.form.route_address_postcode,
                  invoice_address_line_1: self.form.invoice_address_line_1,
                  invoice_address_line_2: self.form.invoice_address_line_2,
                  invoice_address_line_3: self.form.invoice_address_line_3,
                  invoice_address_city: self.form.invoice_address_city,
                  invoice_address_region: self.form.invoice_address_region,
                  invoice_address_postcode: self.form.invoice_address_postcode,
                  invoice_email: self.form.invoice_email,
                  branding_theme: self.form.branding_theme,
                  surcharge: self.form.surcharge,
                  supplier_id: self.form.supplier_id,
                  model: self.form.model,

              },
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
              // user_id: self.userData.id // This hasn't been setup yet so probably won't work, ...yet?!
            }).then(function (response) {
              alert('Uploaded new company successfully!');
              console.log(response.data);
            }).catch(error => console.log(error));
        },
        onReset (evt) {
            evt.preventDefault();
            /* Reset our form values */

            this.form.invoice_name = '';
            this.form.route_name = '';
            this.form.primary_contact_name = '';
            this.form.primary_contact_job_title = '';
            this.form.primary_contact_email = '';
            this.form.primary_contact_job_title = '',
            this.form.primary_contact_email = '',
            this.form.primary_contact_telephone = '',
            this.form.secondary_contact_name = '',
            this.form.secondary_contact_job_title = '',
            this.form.secondary_contact_email = '',
            this.form.secondary_contact_telephone = '',
            this.form.delivery_information = '',
            this.form.route_address_line_1 = '',
            this.form.route_address_line_2 = '',
            this.form.route_address_line_3 = '',
            this.form.route_address_city = '',
            this.form.route_address_region = '',
            this.form.route_address_postcode = '',
            this.form.invoice_address_line_1 = '',
            this.form.invoice_address_line_2 = '',
            this.form.invoice_address_line_3 = '',
            this.form.invoice_address_city = '',
            this.form.invoice_address_region = '',
            this.form.invoice_address_postcode = '',
            this.form.invoice_email = '',
            this.form.branding_theme = null,
            this.form.surcharge = null,
            this.form.supplier_id = null,
            this.form.model = 'Free',
            /* Trick to reset/clear native browser form validation state */
            this.show = false;
            this.$nextTick(() => { this.show = true });
        }
    },
    mounted () {
        this.$store.commit('getFruitPartners');
    }
}

</script>
