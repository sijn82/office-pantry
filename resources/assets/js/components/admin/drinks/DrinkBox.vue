<template lang="html">
    <div>
        <div id="edit-save-buttons">
            <h4> {{ drinkbox[0].drinkbox_id }} </h4>
            <p> {{ drinkbox[0].delivery_day }} - {{ drinkbox[0].is_active }} </p>
            <b-button variant="primary" @click="showDetails()"> Details </b-button>
            <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
            <b-button v-if="editing" class="btn btn-success" @click="updateDetails(drinkbox[0])"> Save </b-button>
        </div>
        
        <div class="drinkbox-details" v-if="details">
            <b-row id="top-details" :class="drinkbox[0].is_active">
                <b-col>
                    <label><b> Drinkbox Id </b></label>
                    <div>
                        <p> {{ drinkbox[0].drinkbox_id }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Drinkbox Status </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].is_active">
                            <option value="Active"> Active </option>
                            <option value="Inactive"> Inactive </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].is_active }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivered By </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].delivered_by_id" size="sm">
                            <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].fruit_partner_name }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Type </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].type" :options="type_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].type }} </p>
                    </div>
                </b-col>
            </b-row>
            
            <b-row :class="drinkbox[0].is_active">
            
                <b-col>
                    <label><b> Delivery Day </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].delivery_day" :options="days_of_week"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].delivery_day }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Frequency </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].frequency" :options="frequency_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].frequency }} </p>
                    </div>
                </b-col>
            </b-row>
            
            <b-row id="bottom-details" :class="drinkbox[0].is_active">
                <b-col v-if="drinkbox[0].frequency === 'Monthly'">
                    <label><b> Week In Month </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="drinkbox[0].week_in_month" :options="week_in_month_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].week_in_month }} </p>
                    </div>
                </b-col>
                <b-col v-if="drinkbox[0].previous_delivery_week !== null">
                    <label><b> Previous Delivery Date </b></label>
                    <div>
                        <p> {{ drinkbox[0].previous_delivery_week }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Next Delivery Week </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="drinkbox[0].next_delivery_week" type="date"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ drinkbox[0].next_delivery_week }} </p>
                    </div>
                </b-col>
            </b-row>
            
            <h4> Order Breakdown </h4>
            <b-button variant="primary" @click="addProduct()"> Add a Product </b-button>
            <div v-if="add_product">
                <b-row class="margin-top-10">
                    <select-product></select-product>
                </b-row>
                <b-row class="margin-top-10">
                    <b-col>
                        <h4> Product Name </h4>
                    </b-col>
                    <b-col>
                        <h4> Stock Level </h4>
                    </b-col>
                    <b-col>
                        <h4> Shortest Stock Date </h4>
                    </b-col>
                    <b-col>
                        <h4> Unit Price </h4>
                    </b-col>
                    <b-col>
                        <h4> Quantity </h4>
                    </b-col>
                    <b-col>
                        
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.name }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.stock_level }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.shortest_stock_date }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.unit_price }} </p>
                    </b-col>
                    <b-col>
                        <b-form-input v-model="quantity" type="number"></b-form-input>
                    </b-col>
                    <b-col>
                        <b-button variant="success" @click="saveProductToBox(drinkbox[0])"> Add </b-button>
                    </b-col>
                </b-row>
            </div>
            <b-row class="margin-top-10">
                <b-col>
                    <p><b> Product Name </b></p>
                </b-col>
                <b-col>
                    <p><b> Quantity In Box </b></p>
                </b-col>
                <b-col>
                </b-col>
            </b-row>
            
            <drinkbox-item id="drinkbox-products" v-for="drinkbox_item in drinkbox" :drinkbox_item="drinkbox_item" :key="drinkbox_item.id"></drinkbox-item>
            
        </div>
    </div>
</template>


<style lang="scss" scoped>
    .margin-top-10 {
        margin-top: 10px;
    }
</style>

<script>
    export default {
        props: ['drinkbox'],
        data () {
            return {
                add_product: false,
                quantity: 0,
                editing: false,
                details: false,
                type_options: ['Regular', 'Unique', {value: 'monthly-special', text: 'Monthly Special'}],
                days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
                week_in_month_options: ['First', 'Second', 'Third', 'Forth', 'Last'],
            }
        }, 
        methods: {
            
            addProduct() {
                if (this.add_product === false) {
                    this.add_product = true;
                } else {
                    this.add_product = false;
                }
            },
            saveProductToBox(drinkbox) {
                axios.post('api/drinkbox/add-product', {
                    product: {
                        id: this.$store.state.selectedProduct.id,
                        name: this.$store.state.selectedProduct.name,
                        code: this.$store.state.selectedProduct.code,
                        quantity: this.quantity,
                        unit_price: this.$store.state.selectedProduct.unit_price,
                    },
                    drinkbox_details: drinkbox, 
                        
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            enableEdit() {
                if (this.editing == false) {
                    this.editing = true;
                    this.details = true;
                } else {
                    this.editing = false;
                }
            },
            showDetails() {
                if (this.details == true) {
                    this.details = false;
                } else {
                    this.details = true;
                }
            },
            updateDetails(drinkbox) {
                axios.post('api/drinkbox/details', {
                    drinkbox_details: drinkbox,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        },
    }
</script>