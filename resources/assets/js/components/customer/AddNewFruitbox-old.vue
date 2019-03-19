<template>
    <div>
        <b-container style="text-align: center;" sm="12">
            <h3> Add New Fruitbox </h3>
            
            <b-form id="new-fruitbox-form" @submit="onSubmit" @reset="onReset">
                <!-- Fruitbox Name -->
                <b-form-group label="Name:" label-for="fruitbox-name" description="Add a unique name to bind with this order">
                    <b-form-input id="fruitbox-name" type="text" v-model="form.name" placeholder="Enter fruitbox name" required></b-form-input>
                </b-form-group>
                <!-- Delivery Days -->
                <b-form-group id="fruitbox-delivery-days" label="Select Delivery Day(s): ">
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
                <b-form-group id="fruitbox-company-select" label="Select Company For Fruitbox: ">
                    <b-form-select v-model="form.company_id" v-if="user_associated_companies != undefined" :options="user_associated_companies" required>
                        <template slot="first">
                            <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                    <b-form-select v-model="form.company_id" :options="company_selected" v-else required>
                        <template slot="first">
                            <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                    <p style="padding-top:10px;"> Selected Company: {{ form.company_id }} </p>
                </b-form-group>
                <!-- Type of Box -->
                <b-form-group id="fruitbox-type" label="Select Fruitbox Type: ">
                    <b-form-select v-model="form.type" :options="types" required>
                        <template slot="first">
                            <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                    <p style="padding-top:10px;"> Selected Box Type: {{ form.type }} </p>
                </b-form-group>
                <!-- Week Start of First Delivery -->
                <b-form-group label="Week Start For First Delivery:" label-for="fruitbox-first-delivery" description="Please enter the week start of the first delivery">
                    <b-form-input id="fruitbox-first-delivery" type="date" v-model="form.first_delivery" placeholder="Enter fruitbox first delivery week start" required></b-form-input>
                </b-form-group>
                <p style="padding-top:10px;"> Selected First Week Start Date: {{ form.first_delivery }} </p>
                <!-- Frequency Of Order -->
                <b-form-group id="fruitbox-frequency" label="Select Fruitbox Delivery Frequency: ">
                    <b-form-select v-model="form.frequency" :options="frequency" required>
                        <template slot="first">
                                <option :value="null" disabled>-- Please select an option --</option>
                        </template>
                    </b-form-select>
                        <p style="padding-top:10px;"> Selected Box Delivery Frequency: {{ form.frequency }} </p>
                </b-form-group>

            <!-- Fruit Breakdown - Begins with a container to define widths for column spacing -->
            <!-- <b-container style="text-align: center;" sm="12"> -->
                <b-form-group id="fruitbox-breakdown" label="Fruitbox Breakdown">
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Deliciously Red Apples </label>
                            <b-form-input v-model="form.deliciously_red_apples" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Pink Lady Apples </label>
                            <b-form-input v-model="form.pink_lady_apples" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Red Apples </label>
                            <b-form-input v-model="form.red_apples" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Green Apples </label>
                            <b-form-input v-model="form.green_apples" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Satsumas </label>
                            <b-form-input v-model="form.satsumas" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Pears </label>
                            <b-form-input v-model="form.pears" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Bananas </label>
                            <b-form-input v-model="form.bananas" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Nectarines </label>
                            <b-form-input v-model="form.nectarines" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Limes </label>
                            <b-form-input v-model="form.limes" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> Lemons </label>
                            <b-form-input v-model="form.lemons" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Grapes </label>
                            <b-form-input v-model="form.grapes" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label> Seasonal Berries </label>
                            <b-form-input v-model="form.seasonal_berries" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                </b-form-group>
                <!-- </b-container> -->
                <div id="fruitbox-buttons">
                    <b-button type="submit" variant="primary"> Submit </b-button>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </div>
            </b-form>
        </b-container>
    </div>
</template>

<style>

    #fruitbox-breakdown label {
        padding-top: 10px;
    }
    #new-fruitbox-form:after {
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
                    name: '',
                    company_id: null, // If this is created by Frosh, how are they going to select the company to attach the order to?  A typed filter of a long list may be the best way.
                    route_id: null, // This will also need a way to filter from all possible routes, however once a company has been confirmed, the options could easily fit on a dropdown.
                    delivery_day: '',  // According to the docs this must be an array reference, however it seems to me this is happening anyway?  Interesting...
                    type: null, // Whilst not currently in use, this will determine between standard, berries and tailored, with tailored being the only one which can be edited (probably).
                    first_delivery: null, // This will be necessary to get the first delivery scheduled and for the frequency field to begin incrementing.
                    frequency: null, // Also new to the system, this will determine between daily, weekly, fortnightly and monthly, or bespoke.  This may then be connected to the cron/events when checking if a company is due for orders.
                    deliciously_red_apples: 0,
                    pink_lady_apples: 0,
                    red_apples: 0,
                    green_apples: 0,
                    satsumas: 0,
                    pears: 0,
                    bananas: 0,
                    nectarines: 0,
                    limes: 0,
                    lemons: 0,
                    grapes: 0,
                    seasonal_berries: 0,
                },
                types: ['Standard', 'Berry', 'Tailored'],
                frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
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
              axios.post('/api/fruitbox/add-new-fruitbox', {
                  company_data: self.form,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
              }).then(function (response) {
                  alert('Uploaded new fruitbox successfully!');
                  console.log(response.data);
              }).catch(error => console.log(error));
            },

            onReset (evt) {
              evt.preventDefault();
              /* Reset our form values */
              this.form.name = '';
              this.form.company_id = null;
              this.form.route_id = null;
              this.form.delivery_day = '';
              this.form.type = null;
              this.form.frequency = '';
              this.form.deliciously_red_apples = 0;
              this.form.pink_lady_apples = 0;
              this.form.red_apples = 0;
              this.form.green_apples = 0;
              this.form.satsumas = 0;
              this.form.pears = 0;
              this.form.bananas = 0;
              this.form.nectarines = 0;
              this.form.limes = 0;
              this.form.lemons = 0;
              this.form.grapes = 0;
              this.form.seasonal_berries = 0;

            }
        },
        mounted() {
            console.log(this.company.id);
        }
}
</script>
<!--
 id": 3, - auto increment
"is_active": "Active", - default active
"name": "Dodgy Seasonal Box",
"company_id": 899, - pulled from user, except if the user is known by more than one company, how do we auto assign?  Can we create a dropdown that only includes companies registered to that user?
"route_id": 857, - route id assigned by looking for an existing route for day/company combo.
"delivery_day": "Friday", this works fine for a fruitbox delivered once a week but what about daily deliveries?
"deliciously_red_apples": 0,
"pink_lady_apples": 0,
"red_apples": 6,
"green_apples": 3,
"satsumas": 6,
"pears": 3,
"bananas": 10,
"nectarines": 12,
"limes": 0,
"lemons": 0,
"grapes": 0,
"seasonal_berries": 2,
"created_at": null, - auto created
"updated_at - auto updated
-->
