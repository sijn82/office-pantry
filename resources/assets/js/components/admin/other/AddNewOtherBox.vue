<template lang="html">
    <div>
        <h3> Add New Other Box </h3>
        <div>
            <div id="build-otherbox">
                <b-row class="order-options">
                    <b-col>
                        <div class="build-order-button">
                            <h4> Build Order </h4>
                            <b-button v-model="createOtherbox" @click="creatingOtherbox()"> Create Otherbox </b-button>
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
                        <label> No. of Boxes </label>
                        <b-form-input type="number" v-model="no_of_boxes" size="sm"></b-form-input>
                    </b-col>
                    <b-col>
                        <label> Type </label>
                        <b-form-select v-model="type" :options="type_options" size="sm"></b-form-select>
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
                        <b-form-select v-model="week_in_month" :options="week_in_month_options" required>
                            <template slot="first">
                                    <option :value="null" disabled> Please select an option </option>
                            </template>
                        </b-form-select>
                    </b-col>
                </b-row>
            </div>
        </div>
        
        <div class="order-selections" v-if="createOtherbox">
            <b-row><b-col><h4> Product Name </h4></b-col><b-col><h4> Quantity </h4></b-col><b-col><h4> Price </h4></b-col><b-col>  </b-col></b-row>
            <div v-for="other in $store.state.otherbox ">
                 <b-row>
                     <b-col>
                         <p> {{ other.name }} </p>
                     </b-col>
                     <b-col>
                         <p> {{ other.quantity }} </p>
                     </b-col>
                     <b-col>
                         <p> {{ other.unit_price }} </p>
                     </b-col>
                     <b-col>
                         <b-button size="sm" variant="danger" @click="removeProduct(other.id)"> Remove </b-button>
                     </b-col>
                 </b-row>
             </div>    
        </div>
        
        <b-row class="margin-top-20"><b-col>  </b-col><b-col>  </b-col><b-col>  </b-col><b-col> <b-button size="sm" variant="success" @click="saveCompanyOtherbox()"> Save Otherbox </b-button> </b-col></b-row>
        
        <!-- This (products-list) is the parent component for products and pulls that component into this view as well.
        The button above changes the state of the createOtherbox data variable,
        offering an additional 'add to otherbox' button to each product. -->
        <div v-if="createOtherbox">
            <products-list v-on:addProduct="addProductToOrder($event)" :createOtherbox="createOtherbox"></products-list>
        </div>
        <div class="margin-top-20" v-else>
            <p><b> Click on the 'Create Otherbox' button above to add products. </b></p>
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
        //props:['addProductToOtherbox', 'product', 'quantity'], <-- I don't think I'm using this, so let's see if any errors get thrown further down the line.
        data () {
            return {
                createOtherbox: false,
                order: 'empty',
                company_details_id: 0, //  Not sure this is being used anymore?
                 // total_start: 0,
                delivered_by: null,
                // delivered_by_options: ['DPD', 'APC', 'OP'],
                delivery_day: null,
                type: null,
                type_options: [{value: null, text: 'Regular'}, {value: 'monthly-special', text: 'Monthly Special'}],
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
                //console.log(company.id),
                this.selected_company = company.id
                //alert(company.id);
            },
            creatingOtherbox() {
                if (this.createOtherbox == true) {
                  this.createOtherbox = false;
                } else {
                  this.createOtherbox = true;
                }
            },
            // addProductToOrder($event) {
            //     alert($event);
            //     this.order = 'one item';
            // },
            removeProduct(id) {
                console.log(id);
                this.$store.commit('removeFromOtherbox', id);
            },
            saveCompanyOtherbox() {
    
                axios.post('/api/otherboxes/save', {
                    details: { 
                        delivered_by_id: this.delivered_by, 
                        no_of_boxes: this.no_of_boxes,
                        type: this.type,
                        company_details_id: this.selected_company,
                        delivery_day: this.delivery_day, 
                        frequency: this.frequency, 
                        week_in_month: this.week_in_month, 
                        next_delivery_week: this.next_delivery_week 
                    },
                    order: this.$store.state.otherbox,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                }).then( function (response) {
                    alert('Uploaded new Company Otherbox successfully!');
                    // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                    // console.log(response.data);
                }).catch(error => console.log(error));
            },
        }
    }

</script>