<template lang="html">
    <div>
        <!-- We only need to access these properties once per snackbox -->
        <!-- <h4> Snackbox Id: {{ snackbox[0].snackbox_id }} </h4>
        <h4> Delivered By: {{ snackbox[0].delivered_by }} </h4>
        <h4> No. Of Boxes: {{ snackbox[0].no_of_boxes }} </h4>
        <h4> Type: {{ snackbox[0].type }} </h4>
        <h4> Delivery Day: {{ snackbox[0].delivery_day }} </h4>
        <h4> Frequency: {{ snackbox[0].frequency }} </h4>
        <h4 v-if="snackbox[0].frequency === 'Monthly'"> Week In Month: {{ snackbox[0].week_in_month }} </h4>
        <h4 v-if="snackbox[0].previous_delivery_week !== null"> Previous Delivery Date: {{ snackbox[0].previous_delivery_week }} </h4>
        <h4> Next Delivery Week: {{ snackbox[0].next_delivery_week }} </h4> -->
        
        <div id="edit-save-buttons">
            <h4> {{ snackbox[0].snackbox_id }} </h4>
            <p> {{ snackbox[0].delivery_day }} - {{ snackbox[0].is_active }} </p>
            <b-button variant="primary" @click="showDetails()"> Details </b-button>
            <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
            <b-button v-if="editing" class="btn btn-success" @click="updateDetails(snackbox[0])"> Save </b-button>
            <b-button variant="danger" @click="deleteSnackBox(snackbox[0])"> Delete </b-button>
        </div>
        
        <div class="snackbox-details" v-if="details">
            <b-row id="top-details" :class="snackbox[0].is_active">
                <b-col>
                    <label><b> Snackbox Id </b></label>
                    <div>
                        <p> {{ snackbox[0].snackbox_id }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Snackbox Status </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].is_active">
                            <option value="Active"> Active </option>
                            <option value="Inactive"> Inactive </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].is_active }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivered By </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].delivered_by" :options="delivered_by_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].delivered_by }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> No. Of Boxes </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox[0].no_of_boxes" type="number"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].no_of_boxes }} </p>
                    </div>
                </b-col>
                <b-col v-if="snackbox[0].type !== 'wholesale'">
                    <label><b> Snack Cap </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox[0].snack_cap" type="number"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].snack_cap }} </p>
                    </div>
                </b-col>
            </b-row>
            
            <b-row :class="snackbox[0].is_active">
                <b-col>
                    <label><b> Type </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].type" :options="this.$store.state.types_list"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].type }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivery Day </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].delivery_day" :options="days_of_week"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].delivery_day }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Frequency </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].frequency" :options="frequency_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].frequency }} </p>
                    </div>
                </b-col>
            </b-row>
            
            <b-row id="bottom-details" :class="snackbox[0].is_active">
                <b-col v-if="snackbox[0].frequency === 'Monthly'">
                    <label><b> Week In Month </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox[0].week_in_month" :options="week_in_month_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].week_in_month }} </p>
                    </div>
                </b-col>
                <b-col v-if="snackbox[0].previous_delivery_week !== null">
                    <label><b> Previous Delivery Date </b></label>
                    <div>
                        <p> {{ snackbox[0].previous_delivery_week }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Next Delivery Week </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox[0].next_delivery_week" type="date"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox[0].next_delivery_week }} </p>
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
                        <!-- A place holder column to allow room for the edit/remove buttons on each item -->
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
                        <b-button variant="success" @click="saveProductToBox(snackbox[0])"> Add </b-button>
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
                    <p><b> Unit Price </b></p>
                </b-col>
                <b-col>
                    <!-- A place holder column to allow room for the edit/remove buttons on each item -->
                </b-col>
            </b-row>
            
            <!-- Now lets loop through the products contained in the box, 
            I might need to put these in their own component to have them editable as their own instance.
        
            On edit I also need to change the input somehow into (potentially) a filterable searchbar so a new product can be selected
            although I still need to consider some other options on how best to go about this 
        
            Actually I think a better idea is just to allow the user to delete a product or, 
            though a button revealable section, select a new product(s) and attach it to the current snackbox_id + details.
            This will need refreshing to update, so holding them in the store might be a good option -->
            
            <snackbox-item id="snackbox-products" v-for="snackbox_item in snackbox" :snackbox_item="snackbox_item" :key="snackbox_item.id"></snackbox-item>
            
            <!-- <p> {{ snackbox_total }} </p>  This needs some work to generate an accurate and useful total, it's on my todo list but not a priority right now. -->
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
    props: ['snackbox'],
    data () {
        return {
            add_product: false,
            quantity: 0,
            editing: false,
            details: false,
            delivered_by_options: ['DPD', 'APC', 'OP'],
            days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month_options: ['First', 'Second', 'Third', 'Forth', 'Last'],
        }
    },
    computed: {
        // snackbox_total(snackbox) {
        //     let $snackbox_total = 0
        //     for (unit_price in this.snackbox) {
        //         $snackbox_total += snackbox_item[unit_price]
        //     }
        //     return $snackbox_total;
        // 
        // }
    },
    methods: {
        
        addProduct() {
            if (this.add_product === false) {
                this.add_product = true;
            } else {
                this.add_product = false;
            }
        },
        saveProductToBox(snackbox) {
            axios.post('api/snackbox/add-product', {
                product: {
                    id: this.$store.state.selectedProduct.id,
                    name: this.$store.state.selectedProduct.name,
                    code: this.$store.state.selectedProduct.code,
                    quantity: this.quantity,
                    unit_price: this.$store.state.selectedProduct.unit_price,
                },
                snackbox_details: snackbox, 
                    
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
        updateDetails(snackbox) {
            axios.post('api/snackbox/details', {
                snackbox_details: snackbox,
            }).then (response => {
                //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                console.log(response);
            }).catch(error => console.log(error));
        },
        deleteSnackBox(snackbox) {
            axios.put('api/snackbox/destroy-box/' + snackbox.snackbox_id, { 
                snackbox_id: snackbox.snackbox_id,
            }).then (response => {
                //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                console.log(response);
            }).catch(error => console.log(error));
        },
    },
    mounted() {
         this.$store.commit('getTypes');
    }
}
</script>