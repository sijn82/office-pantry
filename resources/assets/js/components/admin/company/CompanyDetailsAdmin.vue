<template lang="html">
    <div class="companybox">
        <b-container class="text-align-center margin-top-20">
            <h3> Company Details </h3>
            <b-row class="margin-top-20">
                <b-col>
                    <div v-if="edit">
                        <b-button type="submit" variant="success" @click="updateCompanyDetails(company)"> Update </b-button>
                        <b-button class="margin-left-10" variant="warning" @click="editing()"> Cancel </b-button>
                    </div>
                    <div v-else>
                        <b-button variant="warning" @click="editing()"> Edit </b-button>
                    </div>
                </b-col>
            </b-row>
            <b-row class="margin-top-20">
                <b-col>
                    <h4> Company Names </h4>
                    <p> * denotes a required field. </p>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <label> Invoice Name* </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_name" required></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_name }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Status </label>
                    <div v-if="edit">
                        <b-form-select v-model="company.is_active" :options="status"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ company.is_active }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route Name* </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_name" required></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_name }} </p>
                    </div>
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
                    <div v-if="edit">
                        <b-form-input v-model="company.primary_contact"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.primary_contact }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Primary Contact Job Title </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.primary_contact_job_title"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.primary_contact_job_title }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Primary Contact Email </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.primary_email"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.primary_email }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Primary Contact Telephone </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.primary_tel"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.primary_tel }} </p>
                    </div>
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
                    <div v-if="edit">
                        <b-form-input v-model="company.secondary_contact"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.secondary_contact }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Secondary Contact Job Title </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.secondary_contact_job_title"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.secondary_contact_job_title }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Secondary Contact Email </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.secondary_email"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.secondary_email }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Secondary Contact Telephone </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.secondary_tel"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.secondary_tel }} </p>
                    </div>
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
                    <div v-if="edit">
                        <b-form-textarea v-model="company.delivery_information" :rows="3" :max-rows="6"></b-form-textarea>
                    </div>
                    <div v-else>
                        <p> {{ company.delivery_information }} </p>
                    </div>
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
                    <div v-if="edit">
                        <b-form-input v-model="company.route_address_line_1" required></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_address_line_1 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route Address Line 2 </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_address_line_2"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_address_line_2 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route Address Line 3 </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_address_line_3"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_address_line_3 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route City* </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_city" required></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_city }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route Region </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_region"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_region }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Route Postcode* </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.route_postcode" required></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.route_postcode }} </p>
                    </div>
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
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_address_line_1"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_address_line_1 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Invoice Address Line 2 </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_address_line_2"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_address_line_2 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Invoice Address Line 3 </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_address_line_3"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_address_line_3 }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Invoice City </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_city"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_city }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Invoice Region </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_region"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_region }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Invoice Postcode </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.invoice_postcode"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_postcode }} </p>
                    </div>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <label> Invoice Email </label>
                    <div v-if="edit">
                        <b-form-input type="email" v-model="company.invoice_email"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.invoice_email }} </p>
                    </div>
                </b-col>
            </b-row>
            <b-row class="margin-top-20">
                <b-col>
                    <label> Branding Theme </label>
                    <div v-if="edit">
                        <b-form-select v-model="company.branding_theme" :options="payment_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ company.branding_theme }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Surcharge (%) </label>
                    <div v-if="edit">
                        <b-form-input v-model="company.surcharge" type="number"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.surcharge }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Delivered By </label>
                    <div v-if="edit">
                        <b-form-select v-model="company.supplier_id">
                            <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                            </template>
                            <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ company.supplier_id }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Model </label>
                    <div v-if="edit">
                        <b-form-select v-model="company.model" :options="model_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ company.model }} </p>
                    </div>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <label> Monthly Surprise </label>
                    <div v-if="edit">
                        <b-form-radio-group v-model="company.monthly_surprise" :options="monthly_surprise_options"></b-form-radio-group>
                    </div>
                    <div v-else>
                        <p> {{ company.monthly_surprise }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Number of (Surprised) Staff </label>
                    <div v-if="edit">
                        <b-form-input type="number" v-model="company.no_of_surprises"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ company.no_of_surprises }} </p>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<style lang="scss" scoped>
    label {
        font-weight: bold;
    }
    .text-align-center {
        text-align: center;
    }
    .margin-top-20 {
        margin-top: 20px;
    }
    .margin-left-10 {
        margin-left: 10px;
    }
    .companybox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .companybox:after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-top: 20px; /* This creates some space between the element and the border. */
        border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    
</style>

<script>
export default {
    props: ['company'],
    data () {
        return {
            edit: false,
            payment_options: [
              { text: 'Please select an option', value: null },
              'BACS', 'GoCardless', 'GoCardless In Advance', 'Invoiced In Advance',
              'Monthly Invoice', 'Monthly Invoice GoCardless', 'Paypal (Stripe)', 'Paypal In Advance (Stripe)',
              'Weekly Standing Order', 'Monthly Standing Order', 'Weekly Standing Order In Advance', 
              'Monthly Standing Order In Advance', 'Eden Branding Theme', 'TBC'
            ],
            model_options: ['Free', 'Honesty Box'],
            status: ['Active', 'Inactive'],
            monthly_surprise_options: [
                { text: 'Yes', value:'yes' }, 
                { text: 'No', value:'no' }, 
                { text: 'TBC', value: null }
            ],
        }
    },
    methods: {
        editing () {
            if (this.edit === true) {
                this.edit = false;
            } else {
                this.edit = true;
            }
        },
        updateCompanyDetails (company) {
            this.edit = false;
            axios.put('api/company/company-details/update/' + company.id, {
                company_details: {
                    is_active: company.is_active,
                    // Company Name(s)
                    invoice_name: company.invoice_name,
                    route_name: company.route_name,
                    // Contact Details
                    primary_contact: company.primary_contact,
                    primary_contact_job_title: company.primary_contact_job_title,
                    primary_email: company.primary_email,
                    primary_tel: company.primary_tel,
                    secondary_contact: company.secondary_contact,
                    secondary_contact_job_title: company.secondary_contact_job_title,
                    secondary_email: company.secondary_email,
                    secondary_tel: company.secondary_tel,
                    delivery_information: company.delivery_information,
                    // Route Address
                    route_address_line_1: company.route_address_line_1,
                    route_address_line_2: company.route_address_line_2,
                    route_address_line_3: company.route_address_line_3,
                    route_city: company.route_city,
                    route_region: company.route_region,
                    route_postcode: company.route_postcode,
                    // Invoice Address
                    invoice_address_line_1: company.invoice_address_line_1,
                    invoice_address_line_2: company.invoice_address_line_2,
                    invoice_address_line_3: company.invoice_address_line_3,
                    invoice_city: company.invoice_city,
                    invoice_region: company.invoice_region,
                    invoice_postcode: company.invoice_postcode,
                    // Billing and Delivery
                    branding_theme: company.branding_theme,
                    surcharge: company.surcharge,
                    supplier_id: company.supplier_id,
                    model: company.model,
                    monthly_surprise: company.monthly_surprise,
                    no_of_surprises: company.no_of_surprises,
                }
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        }
    },
    mounted () {
        this.$store.commit('getFruitPartners'); // The company must exist to add boxes to their account.  So this component seems a good place to make the call? This and add new company are now the only calls.
    }
}
</script>