<template>
    <div id="new-snackbox">
        <b-row class="order-options">
            <!-- <b-col>
                <div class="build-order-button">
                    <h4> Build Order </h4>
                    <b-button variant="primary" v-model="createSnackbox" @click="creatingSnackbox()"> Create Snackbox </b-button>
                    <b-button variant="info" class="margin-top-20" v-model="createWholesaleSnackbox" @click="creatingWholesaleSnackbox()"> Create Wholesale Snackbox </b-button>
                </div>
            </b-col> -->
            <b-col id="company-select">
                <label> Selected Company </label>
                <!-- <select-company v-on:selected-company="companySelected"></select-company>
                <p> ID: {{ selected_company }} </p> -->
                <h4 class="font-weight-300"> {{ companySelected(this.company) }} </h4>
            </b-col>
            <b-col>
                <label> Select Type </label>
                <b-form-group description="This is a required field.">
                    <b-form-select v-model="type" :options="this.$store.state.types_list" size="sm" required>
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-form-group>
            </b-col>
            <b-col>
                <label> Add New Type </label>
                <b-input-group>
                    <b-form-input type="text" v-model="new_type" placeholder="Add a new type to list of options" size="sm">  </b-form-input>
                    <b-input-group-append>
                        <b-button @click="addNewType(new_type)" size="sm"> Add </b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
        </b-row>
        <!-- This should split the select company and snack cap onto it's own row. -->
        <b-row>
            <b-col>
                <label> Name </label>
                <b-form-input v-model="name" size="sm" type="text"></b-form-input>
            </b-col>
            <b-col v-if="!createWholesaleSnackbox">
                <label> No. of Boxes </label>
                <b-form-input v-model="no_of_boxes" size="sm" type="number" min="0"></b-form-input>
            </b-col>
            <b-col v-if="!createWholesaleSnackbox">
                <label> Snack Cap </label>
                <b-input type="number" v-model="snack_cap" size="sm" placeholder="Please enter a snack cap..." :state="snack_cap_state"></b-input>
                <!-- ':state' and 'b-form-invalid-feedback' has been an interesting diversion but without 'required' to enforce it, kinda pointless for now. -->
                <b-form-invalid-feedback> This is a required field, £0 would make the box free! </b-form-invalid-feedback>
            </b-col>
            <b-col>
                <label> Next Delivery Week Start </label>
                <b-form-input type="date" v-model="delivery_week" size="sm"></b-form-input>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <!-- Delivery Days -->
                <b-form-group id="fruitbox-delivery-days" label="Select Delivery Day(s): ">
                    <b-form-checkbox-group v-model="delivery_day"><!-- Unable to use required here without writing some additional validation logic, allowing 1+ value(s) to be checked, instead of all -->
                        <b-form-checkbox value="Monday">Monday</b-form-checkbox>
                        <b-form-checkbox value="Tuesday">Tuesday</b-form-checkbox>
                        <b-form-checkbox value="Wednesday">Wednesday</b-form-checkbox>
                        <b-form-checkbox value="Thursday">Thursday</b-form-checkbox>
                        <b-form-checkbox value="Friday">Friday</b-form-checkbox>
                    </b-form-checkbox-group>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <label> Delivered By </label>
                <b-form-select v-model="delivered_by" :options="delivered_by_options" size="sm">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-col>
                <label> Frequency </label>
                <b-form-select v-model="frequency" :options="frequency_options" size="sm">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-col v-if="frequency == 'Monthly'">
                <label> Week In Month </label>
                <b-form-select v-model="week_in_month" :options="week_in_month_options" required>
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
        </b-row>

        <b-row class="margin-top">
            <b-col>
                <div id="snackbox-buttons">
                    <b-button type="submit" variant="primary" @click="saveCompanySnackbox()"> Submit </b-button>
                    <b-button type="reset" variant="danger" @click="onReset()"> Reset </b-button>
                </div>
            </b-col>
        </b-row>

    </div>
</template>

<style lang="scss">
#new-snackbox {
    label {
        font-weight: 400;
    }
    .build-order-button {
        padding-bottom: 20px;
    }
    .order-selections {
        padding: 10px 40px;
        p {
            font-weight:300;
        }
    }
    #company-select {
        p {
            font-weight: 300;
        }
    }
    .order-options {
        padding: 20px 40px;
    }
    .margin-top {
        margin-top: 10px;
    }
    .margin-top-20 {
        margin-top: 20px;
    }
}
#new-snackbox:after {
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
    props:{
        company: Object,
    },
    data() {
        return {
            createSnackbox: false,
            createWholesaleSnackbox: false,
            //order: 'empty', edit 16/03/2020: if we're not adding products here, we won't need this.
            //company_id: 0, edit 16/03/2020: i don't think this is being used anywhere either.
            name: this.company.route_name,
            snack_cap: null,
            delivered_by: null,
            delivered_by_options: ['DPD', 'APC', 'OP'],
            type: null,
            new_type: null,
            delivery_day: [],
            delivery_day_options: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            frequency: null,
            frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: null,
            week_in_month_options: ['First', 'Second', 'Third', 'Fourth', 'Last'],
            no_of_boxes: 0,
            delivery_week: null,
            selected_company: 'none selected',
        }
    },
    computed: {
        total() {
            // Even though this value is immediately replaced and not used, it still needs to be declared.
            let total_cost = 0;

            // This function checks each entry in the current snackbox list and creates a running total of the unit price multiplied by the quantity.
            let sum = function(snackbox, createWholesaleSnackbox, cost, quantity, case_size){

                return snackbox.reduce( function(a, b) {
                    // If we're here then the box we're building is (hopefully!) a wholesale snackbox order so we want to display the cost of a case, rather than the cost of single unit.
                    if (createWholesaleSnackbox) {
                        // The return is very similar, it just needs to include multiplying the unit price by the case size, before multiplying again by quantity ordered.
                        return parseFloat(a) + ( ( parseFloat(b[cost]) * parseFloat(b[case_size]) ) * parseFloat(b[quantity]) );
                    }
                    // Otherwise it's just an ordinary snackbox so case size is irrelevant.
                    return parseFloat(a) + ( parseFloat(b[cost]) * parseFloat(b[quantity]) );
                }, 0);
            };
            // Now we use the function by passing in the snackbox array, and the two (or 3 for wholesale) properties we need to multiply - saving it as the current total cost.
            total_cost = sum(this.$store.state.snackbox, this.createWholesaleSnackbox, 'unit_price', 'quantity', 'case_size');
            console.log(total_cost);

            // console.log(total_cost);
            return total_cost;
        },
        snack_cap_state() {
            return this.snack_cap !== null ? true : false
        }
    },
    methods: {
        companySelected(company) {
            // console.log('Yah, we got this ' + company.invoice_name);
            this.selected_company = company.id;
            return this.selected_company_invoice_name = company.invoice_name;
            //alert(company.id);
        },
        creatingSnackbox() {
            if (this.createSnackbox == true) {
              this.createSnackbox = false;
            } else {
              this.createSnackbox = true;
              this.createWholesaleSnackbox = false;
              // if the wholesale box (button) was pressed prior to this one,
              // a type would have been entered without the user necessarily realising it,
              // so let's strip it out for them too.
              // If it was anything else, they entered it, so they can sort it. <--- UX at its very best!
              if (this.type === 'wholesale') {
                  this.type = null;
              }
            }
        },
        creatingWholesaleSnackbox() {
            if (this.createWholesaleSnackbox == true) {
              this.createWholesaleSnackbox = false;
              this.type = null;
            } else {
              this.createWholesaleSnackbox = true;
              this.createSnackbox = false;
              this.addNewType('wholesale');
              this.type = 'wholesale';
            }
        },
        // removeProduct(id) {
        //     console.log(id);
        //     this.$store.commit('removeFromSnackbox', id);
        // },
        saveCompanySnackbox() {

            axios.post('/api/boxes/snackboxes/save', {
                company_details_id: this.selected_company,
                // details: {
                    name: this.name,
                    delivered_by: this.delivered_by,
                    no_of_boxes: this.no_of_boxes,
                    snack_cap: this.snack_cap,
                    type: this.type,
                    delivery_day: this.delivery_day,
                    frequency: this.frequency,
                    week_in_month: this.week_in_month,
                    delivery_week: this.delivery_week,
               // },
                //order: this.$store.state.snackbox,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).then( (response) => {
                alert('Uploaded new Company snackbox successfully!');
                this.$emit('refresh-data', {company_details_id: this.selected_company});
                // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                // console.log(response.data);
            }).catch(error => console.log(error));
        },

        onReset () {
            
            /* Reset our form values */
            this.name = this.company.route_name,
            this.type = null,
            this.no_of_boxes = 0,
            this.snack_cap = null,
            this.delivery_week = null,
            this.delivered_by = null,
            this.delivery_day = null,
            this.frequency = null,
            this.week_in_month = null

        },

        // saveStandardSnackbox() {
        //     this.$store.dispatch('saveStandardSnackboxToDB', this.type );
        //
        // },
        addNewType(new_type) {
            console.log(new_type);
            this.$store.commit('addNewTypeToStore', new_type);
        },
        // Not sure this will work now the url's have changed - 7/8/19 need to check, it might also have been replaced by something else.
        confirmType(type) {

            let company = this.selected_company;
            let name = type;
            axios.post('/api/types', {
                new_type: { name, company },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).then( response => {
                alert('Uploaded new snackbox type successfully!');
                // location.load(true);
                // console.log(response.data[0].id);
                // console.log(response.data[0].allergy);
                let type = response.data[0].type;
                let id = response.data[0].id
                this.$store.commit('addTypeToStore', { type, id });
            }).catch(error => {
                console.log(error)
            });
        },
    },
    mounted() {
        console.log(this.company);
         this.$store.commit('getTypes');
    }
}
</script>
