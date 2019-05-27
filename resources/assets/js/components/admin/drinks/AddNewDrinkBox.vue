<template lang="html">
    <div>
        <h3> Add New Drink Box </h3>
        <div>
            <div id="build-drinkbox">
                <b-row class="order-options">
                    <b-col>
                        <div class="build-order-button">
                            <h4> Build Order </h4>
                            <b-button v-model="createDrinkbox" @click="creatingDrinkbox()"> Create Drinkbox </b-button>
                        </div>
                    </b-col>
            
                    <b-col id="company-select">
                        <label> Select Company </label>
                        <!-- When the selected-company event is emitted from <select-company> component, 
                        the companySelected() method from this component is called, 
                        updating the selected_company prop. -->
                        <select-company v-on:selected-company="companySelected"></select-company>
                        <p> ID: {{ selected_company }} </p>
                    </b-col>
                </b-row>
                <b-row class="order-options">
                    <b-col>
                        <label> Type* </label>
                        <b-form-select v-model="type" size="sm" :options="type_options"></b-form-select>
                    </b-col>
                    <b-col>
                        <label> Next Delivery Week Start </label>
                        <b-form-input type="date" v-model="next_delivery_week" size="sm"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Delivered By </label>
                        <b-form-select v-model="delivered_by" size="sm">
                            <template slot="first">
                                    <option :value="null" disabled> Please select an option </option>
                            </template>
                            <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                        </b-form-select>
                    </b-col>
                    <b-col>
                        <label> Delivery Day </label>
                        <b-form-select v-model="delivery_day" :options="delivery_day_options" size="sm">
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
                        <b-form-select v-model="week_in_month" :options="week_in_month_options" size="sm" required>
                            <template slot="first">
                                    <option :value="null" disabled> Please select an option </option>
                            </template>
                        </b-form-select>
                    </b-col>
                    <b-form-text> * Use the 'unique' type to process coffee etc, as these will be pulled into the snacks column total when processing the routes export. </b-form-text>
                </b-row>
            </div>
        </div>
        
        <div class="order-selections" v-if="createDrinkbox">
            <b-row><b-col><h4> Product Name </h4></b-col><b-col><div v-if="type === 'Regular'"><h4> Quantity (Cases) </h4></div><div v-else><h4> Quantity (Units) </h4></div></b-col><b-col><h4> Price </h4></b-col><b-col>  </b-col></b-row>
            <div v-for="drink in $store.state.drinkbox">
                 <b-row>
                     <b-col>
                         <p> {{ drink.name }} </p>
                     </b-col>
                     <b-col>
                         <p> {{ drink.quantity }} </p>
                     </b-col>
                     <b-col>
                         <p> {{ drink.unit_price }} </p>
                     </b-col>
                     <b-col>
                         <b-button size="sm" variant="danger" @click="removeProduct(drink.id)"> Remove </b-button>
                     </b-col>
                 </b-row>
             </div>
        </div>
        
        <b-row class="margin-top-20"><b-col>  </b-col><b-col>  </b-col><b-col>  </b-col><b-col> <b-button size="sm" variant="success" @click="saveCompanyDrinkbox()"> Save Drinkbox </b-button> </b-col></b-row>
        
        <!-- This (products-list) is the parent component for products and pulls that component into this view as well.
        The button above changes the state of the createDrinkbox data variable,
        offering an additional 'add to drinkbox' button to each product. -->
        <div v-if="createDrinkbox">
            <products-list :createDrinkbox="createDrinkbox" :type="type"></products-list>
        </div>
        <div class="margin-top-20" v-else>
            <p><b> Click on the 'Create Drinkbox' button above to add products. </b></p>
        </div>
    </div>
</template>

<style lang="scss" scoped>

    #company-select {
        p {
            font-weight: 300;
        }
    }
    .order-selections {
        padding: 10px 40px;
        p {
            font-weight:300;
        }
    }
    .order-options {
        padding: 20px 40px;
    }
    .margin-top-20 {
        margin-top: 20px;
    }
    
</style>

<script>

    export default {
        //props:['addProductToDrinkbox', 'product', 'quantity'],
        data () {
            return {
                createDrinkbox: false,
                order: 'empty',
                company_details_id: 0,
                //total_start: 0,
                type: 'Regular',
                type_options: ['Regular', 'Unique', {value: 'monthly-special', text: 'Monthly Special'}],
                delivered_by: null,
                // delivered_by_options: ['DPD', 'APC', 'OP'], // This will be removed when I add the fruitpartners dropdown.
                delivery_day: null,
                delivery_day_options: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                frequency: null,
                frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
                week_in_month: null,
                week_in_month_options: ['First', 'Second', 'Third', 'Forth', 'Last'],
                no_of_boxes: 0,
                next_delivery_week: null,
                selected_company: 'none selected',
            }
        },
        methods: {
            companySelected(company) {
                console.log(company.id),
                this.selected_company = company.id
                //alert(company.id);
            },
            creatingDrinkbox() {
                if (this.createDrinkbox == true) {
                  this.createDrinkbox = false;
                } else {
                  this.createDrinkbox = true;
                }
            },
            removeProduct(id) {
                console.log(id);
                this.$store.commit('removeFromDrinkbox', id);
            },
            saveCompanyDrinkbox() {

                axios.post('/api/drinkboxes/save', {
                    details: { 
                        delivered_by_id: this.delivered_by, 
                        type: this.type, 
                        company_details_id: this.selected_company,
                        delivery_day: this.delivery_day, 
                        frequency: this.frequency, 
                        week_in_month: this.week_in_month, 
                        next_delivery_week: this.next_delivery_week 
                    },
                    order: this.$store.state.drinkbox,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                }).then( function (response) {
                    alert('Uploaded new Company Drinkbox successfully!');
                    // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                    // console.log(response.data);
                }).catch(error => console.log(error));
            },
        }
    }

</script>