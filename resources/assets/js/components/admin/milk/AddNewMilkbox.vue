<template>
    <div>
        <b-container style="text-align: center;" sm="12">
            <h3> Add New Milkbox </h3>

            <b-form id="new-milkbox-form" @submit="onSubmit" @reset="onReset">
                <!-- Select Fruit Supplier -->
                <b-form-group>
                    <label> Fruit Partner </label>
                    <b-form-select v-model="form.fruit_partner_id">
                        <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                    </b-form-select>
                    <p> Selected: {{ form.fruit_partner_id }} </p>
                </b-form-group>
                <!-- Delivery Days -->
                <b-form-group id="milkbox-delivery-days" label="Select Delivery Day(s): ">
                    <b-form-checkbox-group v-model="form.delivery_day"><!-- Unable to use required here without writing some additional validation logic, allowing 1+ value(s) to be checked, instead of all -->
                        <b-form-checkbox value="Monday">Monday</b-form-checkbox>
                        <b-form-checkbox value="Tuesday">Tuesday</b-form-checkbox>
                        <b-form-checkbox value="Wednesday">Wednesday</b-form-checkbox>
                        <b-form-checkbox value="Thursday">Thursday</b-form-checkbox>
                        <b-form-checkbox value="Friday">Friday</b-form-checkbox>
                    </b-form-checkbox-group>
                </b-form-group>
                    <p> Selected Delivery Day(s): {{ form.delivery_day }} </p> <!-- According to the docs this must be an array reference, however it seems to me this is happening anyway?  Interesting... -->
                    <!-- Company Select -->
                <b-form-group id="milkbox-company-select" label="Select Company For Milkbox: ">
                    <b-form-select v-model="form.company_details_id" v-if="user_associated_companies != undefined" :options="user_associated_companies" required>
                        <template slot="first">
                            <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                    <b-form-select v-model="form.company_details_id" :options="company_selected" v-else required>
                        <template slot="first">
                            <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                    <p style="padding-top:10px;"> Selected Company: {{ form.company_details_id }} </p>
                </b-form-group>
                <!-- Week Start of First Delivery -->
                <b-form-group label="Week Start For First Delivery:" label-for="milkbox-first-delivery" description="Please enter the week start of the first delivery">
                    <b-form-input id="milkbox-first-delivery" type="date" v-model="form.first_delivery" placeholder="Enter milkbox first delivery week start" required></b-form-input>
                </b-form-group>
                <p style="padding-top:10px;"> Selected First Week Start Date: {{ form.first_delivery }} </p>
                <!-- Frequency Of Order -->
                <b-form-group id="milkbox-frequency" label="Select Milkbox Delivery Frequency: ">
                    <b-form-select v-model="form.frequency" :options="frequency" required>
                        <template slot="first">
                                <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                        <p style="padding-top:10px;"> Selected Box Delivery Frequency: {{ form.frequency }} </p>
                </b-form-group>
                <b-form-group v-if="form.frequency == 'Monthly'" id="milkbox-week-in-month" label="Select Milkbox Delivery Week (In Month): ">
                    <b-form-select v-model="form.week_in_month" :options="week_in_month" required>
                        <template slot="first">
                                <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                        <p style="padding-top:10px;"> Selected Box Delivery Week (In Month): {{ form.week_in_month }} </p>
                </b-form-group>
            <!-- Milk Breakdown - Begins with a container to define widths for column spacing -->
            <!-- <b-container style="text-align: center;" sm="12"> -->
                <b-form-group id="milkbox-breakdown" label="Milkbox Breakdown">
                    <b-row>
                        <b-col>
                            <h6> Regular 2l Milk Options </h6>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> 2L Semi-Skimmed </label>
                            <b-form-input v-model="form.semi_skimmed_2l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 2L Skimmed </label>
                            <b-form-input v-model="form.skimmed_2l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 2L Whole </label>
                            <b-form-input v-model="form.whole_2l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <h6> Regular 1l Milk Options </h6>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> 1L Semi-Skimmed </label>
                            <b-form-input v-model="form.semi_skimmed_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Skimmed </label>
                            <b-form-input v-model="form.skimmed_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Whole </label>
                            <b-form-input v-model="form.whole_1l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <h6> Organic 2l Milk Options </h6>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Organic 2L Semi-Skimmed </label>
                            <b-form-input v-model="form.organic_semi_skimmed_2l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Organic 2L Skimmed </label>
                            <b-form-input v-model="form.organic_skimmed_2l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Organic 2L Whole </label>
                            <b-form-input v-model="form.organic_whole_2l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <h6> Organic 1l Milk Options </h6>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Organic 1L Semi-Skimmed </label>
                            <b-form-input v-model="form.organic_semi_skimmed_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Organic 1L Skimmed </label>
                            <b-form-input v-model="form.organic_skimmed_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Organic 1L Whole </label>
                            <b-form-input v-model="form.organic_whole_1l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <h6> Alternative 1l Milk Options </h6>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> 1L Coconut </label>
                            <b-form-input v-model="form.coconut_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Unsweetened Almond </label>
                            <b-form-input v-model="form.unsweetened_almond_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Almond </label>
                            <b-form-input v-model="form.almond_1l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> 1L Unsweetened Soya </label>
                            <b-form-input v-model="form.unsweetened_soya_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Soya </label>
                            <b-form-input v-model="form.soya_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Oat </label>
                            <b-form-input v-model="form.oat_1l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> 1L Rice </label>
                            <b-form-input v-model="form.rice_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Cashew </label>
                            <b-form-input v-model="form.cashew_1l" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> 1L Lactose Free Semi-Skimmed </label>
                            <b-form-input v-model="form.lactose_free_semi_skimmed_1l" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                </b-form-group>
                <!-- </b-container> -->
                <div id="milkbox-buttons">
                    <b-button type="submit" variant="primary"> Submit </b-button>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </div>
            </b-form>
        </b-container>
    </div>
</template>

<style>
    h6 {
        margin-top: 16px;
        font-weight: bold;
    }
    #milkbox-breakdown label {
        padding-top: 10px;
    }
    #new-milkbox-form:after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-top: 30px; /* This creates some space between the element and the border. */
        margin-bottom: 30px; /*  */
        border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }


</style>

<script>

export default {

        props: ['user_associated_companies', 'company'],
        data () {
            return {
                form: {
                    is_active: 'Active',
                    fruit_partner_id: 1,
                    company_details_id: null, // If this is created by Frosh, how are they going to select the company to attach the order to?  A typed filter of a long list may be the best way.
                    delivery_day: '',  // According to the docs this must be an array reference, however it seems to me this is happening anyway?  Interesting...
                    type: null, // Whilst not currently in use, this will determine between standard, berries and tailored, with tailored being the only one which can be edited (probably).
                    first_delivery: null, // This will be necessary to get the first delivery scheduled and for the frequency field to begin incrementing.
                    frequency: null, // Also new to the system, this will determine between daily, weekly, fortnightly and monthly, or bespoke.  This may then be connected to the cron/events when checking if a company is due for orders.
                    week_in_month: null,
                    // Regular 2l milk
                    semi_skimmed_2l: 0,
                    skimmed_2l: 0,
                    whole_2l: 0,
                    // Regular 1l milk
                    semi_skimmed_1l: 0,
                    skimmed_1l: 0,
                    whole_1l: 0,
                    // Organic 2l milk
                    organic_semi_skimmed_2l: 0,
                    organic_skimmed_2l: 0,
                    organic_whole_2l: 0,
                    // Organic 1l milk
                    organic_semi_skimmed_1l: 0,
                    organic_skimmed_1l: 0,
                    organic_whole_1l: 0,
                    // Alternative milk
                    coconut_1l: 0,
                    unsweetened_almond_1l: 0,
                    almond_1l: 0,
                    // Alt pt2
                    unsweetened_soya_1l: 0,
                    soya_1l: 0,
                    oat_1l: 0,
                    // Alt pt3
                    rice_1l: 0,
                    cashew_1l: 0,
                    lactose_free_semi_skimmed_1l: 0,
                },
                frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
                week_in_month: ['First', 'Second', 'Third', 'Forth', 'Last'],
                company_selected: [{
                    value: this.company.id,
                    text: this.company.route_name
                }]
            }
        },

        methods: {

            onSubmit (evt) {
              evt.preventDefault();
              let self = this;
              // alert(JSON.stringify(this.form));
              axios.post('/api/boxes/milkbox/add-new-milkbox', {
                  company_data: self.form,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
              }).then(function (response) {
                  alert('Uploaded new milkbox successfully!');
                  self.$emit('refresh-data', {company_details_id: self.form.company_details_id});
                  console.log(response.data);
              }).catch(error => console.log(error));
            },

            onReset (evt) {
              evt.preventDefault();
              /* Reset our form values */

              this.form.company_details_id = null;
              this.form.route_id = null;
              this.form.delivery_day = '';
              this.form.frequency = '';
              this.form.week_in_month = null,
              this.form.semi_skimmed_2l = 0;
              this.form.skimmed_2l = 0;
              this.form.whole_2l = 0;
              this.form.semi_skimmed_1l = 0;
              this.form.skimmed_1l = 0;
              this.form.whole_1l = 0;
              this.form.organic_semi_skimmed_2l = 0;
              this.form.organic_skimmed_2l = 0;
              this.form.organic_whole_2l = 0;
              this.form.organic_semi_skimmed_1l = 0;
              this.form.organic_skimmed_1l = 0;
              this.form.organic_whole_1l = 0;
              this.form.coconut_1l = 0;
              this.form.unsweetened_almond_1l = 0;
              this.form.almond_1l = 0;
              this.form.unsweetened_soya_1l = 0;
              this.form.soya_1l = 0;
              this.form.oat_1l = 0;
              this.form.rice_1l = 0;
              this.form.cashew_1l = 0;
              this.form.lactose_free_semi_skimmed_1l = 0;

            }
        },
        mounted() {
            console.log(this.company.id);
        }
}
</script>
