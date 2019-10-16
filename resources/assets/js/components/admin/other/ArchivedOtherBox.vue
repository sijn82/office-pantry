<template lang="html">
    <div>
        <div id="edit-save-buttons">
            <h4> {{ archived_otherbox[0].next_delivery_week }} </h4>
            <p> {{ archived_otherbox[0].delivery_day }} - {{ archived_otherbox[0].is_active }} </p>
            <b-button variant="primary" @click="showDetails()"> Details </b-button>
            <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
            <b-button v-if="editing" class="btn btn-success" @click="updateDetails(archived_otherbox[0])"> Save </b-button>
            <b-button variant="danger" @click="deleteOtherBox(archived_otherbox[0])"> Delete </b-button>
        </div>

        <div class="otherbox-details" v-if="details">
            <b-row id="top-details" :class="archived_otherbox[0].is_active">
                <b-col>
                    <label><b> Otherbox Id </b></label>
                    <div>
                        <p> {{ archived_otherbox[0].otherbox_id }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Otherbox Status </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="archived_otherbox[0].is_active">
                            <option value="Active"> Active </option>
                            <option value="Inactive"> Inactive </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].is_active }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivered By </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="archived_otherbox[0].delivered_by_id" size="sm">
                            <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].fruit_partner_name }} </p>
                    </div>
                </b-col>
            </b-row>

            <b-row :class="archived_otherbox[0].is_active">

                <b-col>
                    <label><b> Delivery Day </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="archived_otherbox[0].delivery_day" :options="days_of_week"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].delivery_day }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Frequency </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="archived_otherbox[0].frequency" :options="frequency_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].frequency }} </p>
                    </div>
                </b-col>
                <b-col v-if="archived_otherbox[0].frequency === 'Monthly'">
                    <label><b> Week In Month </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="archived_otherbox[0].week_in_month" :options="week_in_month_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].week_in_month }} </p>
                    </div>
                </b-col>
            </b-row>

            <b-row class="bottom-details" :class="archived_otherbox[0].is_active">

                <b-col v-if="archived_otherbox[0].previous_delivery_week !== null">
                    <label><b> Previous Delivery Date </b></label>
                    <div>
                        <p> {{ archived_otherbox[0].previous_delivery_week }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Week Delivered </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="archived_otherbox[0].next_delivery_week" type="date"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ archived_otherbox[0].next_delivery_week }} </p>
                    </div>
                </b-col>
                <b-col>
                    <b-row>
                        <label><b> Last Invoiced At </b></label>
                        <b-col> {{ archived_otherbox[0].invoiced_at }} </b-col>
                    </b-row>
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
                        <b-button variant="success" @click="saveProductToBox(archived_otherbox[0])"> Add </b-button>
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
                </b-col>
            </b-row>

            <archived-otherbox-item id="otherbox-products" v-for="archived_otherbox_item in archived_otherbox" v-if="archived_otherbox_item.product_id !== 0" :archived_otherbox_item="archived_otherbox_item" :key="archived_otherbox_item.id" @refresh-data="refreshData($event)"></archived-otherbox-item>

            <h3 class="border-top"> Current Total: £{{ archived_otherbox_total }} </h3>
        </div>
    </div>
</template>


<style lang="scss" scoped>
    .margin-top-10 {
        margin-top: 10px;
    }
    .border-top {
        border-top: 2px solid black;
        padding-top: 20px;
    }
    .bottom-details {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
</style>

<script>
    export default {
        props: ['archived_otherbox'],
        data () {
            return {
                add_product: false,
                quantity: 0,
                editing: false,
                details: false,
                days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
                week_in_month_options: ['First', 'Second', 'Third', 'Forth', 'Last'],
            }
        },
        computed: {
            archived_otherbox_total() {

                let $otherbox_total = 0

                // This function checks each entry in the current snackbox list and creates a running total (a) of the unit price (b[cost]) multiplied by the quantity (b[quantity]).
                let sum = function(otherbox, cost, quantity){
                    // console.log(snackbox);
                    return otherbox.reduce( function(a, b) {
                        if (b[cost] !== null) {
                            // Then this is a regular entry, we just need to total it up
                            return parseFloat(a) + ( parseFloat(b[cost]) * parseFloat(b[quantity]) );
                        } else {
                            // Then chances are this is the initial row; where it was either saved without product data, or had it stripped out when archived.
                            // Let's set the values to 0 so that the accumulator doesn't have a hissy fit about NaN.
                            b[cost] = 0;
                            b[quantity] = 0;
                            return parseFloat(a) + ( parseFloat(b[cost]) * parseFloat(b[quantity]) );
                        }
                    }, 0);
                };

                $otherbox_total = sum(this.archived_otherbox, 'unit_price', 'quantity');

                return $otherbox_total;
            }
        },
        methods: {
            refreshData($event) {
                this.$emit('refresh-data', $event);
            },
            addProduct() {
                if (this.add_product === false) {
                    this.add_product = true;
                } else {
                    this.add_product = false;
                }
            },
            saveProductToBox(archived_otherbox) {
                axios.post('api/boxes/archived-otherbox/add-product', {
                    product: {
                        id: this.$store.state.selectedProduct.id,
                        name: this.$store.state.selectedProduct.name,
                        code: this.$store.state.selectedProduct.code,
                        quantity: this.quantity,
                        unit_price: this.$store.state.selectedProduct.unit_price,
                    },
                    archived_otherbox_details: archived_otherbox,

                }).then (response => {
                    this.$emit('refresh-data', {company_details_id: archived_otherbox.company_details_id})
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
            updateDetails(archived_otherbox) {
                axios.post('api/boxes/archived-otherbox/details', {
                    archived_otherbox_details: archived_otherbox,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            deleteOtherBox(archived_otherbox) {
                let self = this;
                axios.put('api/boxes/archived-otherbox/destroy-box/' + archived_otherbox.otherbox_id, {
                    archived_otherbox_id: archived_otherbox.otherbox_id,
                }).then ( (response) => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                    console.log(archived_otherbox.company_details_id);
                    self.$emit('refresh-data', {company_details_id: archived_otherbox.company_details_id})
                }).catch(error => console.log(error));
            },
        },
    }
</script>
