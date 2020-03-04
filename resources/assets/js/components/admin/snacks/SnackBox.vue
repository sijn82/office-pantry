<template >
    <div>

        <div id="edit-save-buttons">
            <h4> {{ snackbox.snackbox_id }} </h4>
            <h5> {{ snackbox.next_delivery_week }} </h5>
            <p> {{ snackbox.delivery_day }} - {{ snackbox.is_active }} </p>
            <!-- <p><b> {{ this.company }} </b></p> -->
            <b-button variant="primary" @click="showDetails()"> Details </b-button>
            <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
            <b-button v-if="editing" class="btn btn-success" @click="updateDetails(snackbox)"> Save </b-button>
            <b-button variant="danger" @click="deleteSnackBox(snackbox)"> Delete </b-button>
        </div>

        <div class="snackbox-details" v-if="details">
            <b-row id="top-details" :class="snackbox.is_active">
                <b-col>
                    <label><b> Snackbox Id </b></label>
                    <div>
                        <p> {{ snackbox.snackbox_id }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Snackbox Status </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.is_active">
                            <option value="Active"> Active </option>
                            <option value="Inactive"> Inactive </option>
                            <option value="Paused"> Paused </option>
                        </b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.is_active }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label> Name </label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox.name"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.name }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivered By </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.delivered_by" :options="delivered_by_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.delivered_by }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> No. Of Boxes </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox.no_of_boxes" type="number" min="0"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.no_of_boxes }} </p>
                    </div>
                </b-col>
                <b-col v-if="snackbox.type !== 'wholesale'">
                    <label><b> Snack Cap </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox.snack_cap" type="number" min="0"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.snack_cap }} </p>
                    </div>
                </b-col>
            </b-row>

            <b-row :class="snackbox.is_active" class="padding-top-10">
                <b-col>
                    <label><b> Type </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.type" :options="this.$store.state.types_list"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.type }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Delivery Day </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.delivery_day" :options="days_of_week"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.delivery_day }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Frequency </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.frequency" :options="frequency_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.frequency }} </p>
                    </div>
                </b-col>
            </b-row>

            <b-row id="bottom-details" :class="snackbox.is_active" class="padding-top-10">
                <b-col v-if="snackbox.frequency === 'Monthly'">
                    <label><b> Week In Month </b></label>
                    <div v-if="editing">
                        <b-form-select v-model="snackbox.week_in_month" :options="week_in_month_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.week_in_month }} </p>
                    </div>
                </b-col>
                <b-col v-if="snackbox.previous_delivery_week !== null">
                    <label><b> Previous Delivery Date </b></label>
                    <div>
                        <p> {{ snackbox.previous_delivery_week }} </p>
                    </div>
                </b-col>
                <b-col>
                    <label><b> Last Invoiced At </b></label>
                    <p> {{ snackbox.invoiced_at }} </p>
                </b-col>
                <b-col>
                    <label><b> Next Delivery Week </b></label>
                    <div v-if="editing">
                        <b-form-input v-model="snackbox.next_delivery_week" type="date"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ snackbox.next_delivery_week }} </p>
                    </div>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <div class="allergies">
                        <h4> Allergies  </h4>
                        <!-- start of alternative approach of existing allergies for review -->
                        <div v-if="editing">
                            <b-form-group>
                                <b-form-checkbox    inline
                                                    v-for="allergen in allergens"
                                                    v-model="selected_allergens"
                                                    :key="allergen.value"
                                                    :value="allergen.value">
                                                    <b> {{ allergen.text }} </b>
                                </b-form-checkbox>
                            </b-form-group>
                        </div>
                        <div v-else>
                            <b-form-group v-if="snackbox.allergies">
                                <b-form-checkbox    inline
                                                    v-for="allergen in snackbox.allergies"
                                                    v-model="selected_allergens"
                                                    :key="allergen"
                                                    :value="allergen">
                                                    <b> {{ allergen }} </b>
                                </b-form-checkbox>
                            </b-form-group>
                            <b-form-group v-else>
                                <!-- Umm, why am I showing this (snackbox[0]) if there aren't any allergies associated with the box? -->
                                <!-- Switching it back out for 'No allergens selected' which makes much more sense!  -->
                                <!-- <p> {{ snackbox[0] }}</p> -->
                                <p> No box specific allergens selected </p>
                            </b-form-group>
                        </div>
                        <!-- end of alternative approach for review -->
                    </div>
                </b-col>
            </b-row>
            <!-- I felt bad doing this the first time, now that i've copy/pasted it somewhere else I feel even worse about it!  This can't be best practise!!! -->
            <!-- Mind you it does work?  No, no you're right that's not the point. -->
            <b-row>
                <b-col>
                    <p hidden><b> {{ currently_selected_allergens }} </b></p>
                    <p hidden><b> {{ currently_selected_dietary_requirements }} </b></p>
                </b-col>
            </b-row>

            <h4> Order Breakdown </h4>
            <div v-if="add_product">
                <b-button variant="danger" @click="addProduct()"> Close </b-button>
            </div>
            <div v-else>
                <b-button variant="primary" @click="addProduct()"> Add a Product </b-button>
            </div>
            <div v-if="add_product">
                <b-row class="margin-top-10">
                    <select-product></select-product>
                </b-row>
                <b-row class="margin-top-10">
                    <b-col>
                        <h4> Add To Snackbox </h4>
                    </b-col>
                </b-row>
                <b-row class="margin-top-10">
                    <b-col>
                        <h4> Product Brand </h4>
                    </b-col>
                    <b-col>
                        <h4> Product Flavour </h4>
                    </b-col>
                    <b-col>
                        <h4> Stock Level </h4>
                    </b-col>
                    <b-col>
                        <h4> Shortest Stock Date </h4>
                    </b-col>
                    <b-col>
                        <h4 v-if="snackbox.type !== 'wholesale'"> Unit Price </h4> <!-- This whole order breakdown section is well served for mixed snackboxes BUT NOT WHOLESALE!!!! - I NEED TO DO SOMETHING WITH 'snackbox[0].type' TO DETERMINE WHAT TO DO!! -->
                        <h4 v-else> Case Price </h4>
                    </b-col>
                    <b-col>
                        <h4> Quantity </h4>
                    </b-col>
                    <b-col>
                        <!-- A place holder column to allow room for the edit/remove buttons on each item -->
                    </b-col>
                </b-row>
                <b-row v-if="this.$store.state.selectedProduct.id">
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.brand }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.flavour }} </p>
                    </b-col>
                    <b-col>
                        <p v-if="snackbox.type !== 'wholesale'"> {{ (this.$store.state.selectedProduct.stock_level - quantity) }} </p>
                        <p v-else> {{ (case_stock_level - quantity) }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ this.$store.state.selectedProduct.shortest_stock_date }} </p>
                    </b-col>
                    <b-col>
                        <p v-if="snackbox.type !== 'wholesale'"> {{ this.$store.state.selectedProduct.selling_unit_price }} </p>
                        <p v-else> {{ this.$store.state.selectedProduct.selling_case_price }} </p>
                    </b-col>
                    <b-col>
                        <b-form-input v-model="quantity" type="number"></b-form-input>
                    </b-col>
                    <b-col>
                        <b-button variant="success" @click="saveProductToBox(snackbox)"> Add </b-button>
                    </b-col>
                </b-row>
            </div>
            <b-row class="margin-top-10">
                <b-col><h4> Current Snackbox Contents </h4></b-col>
            </b-row>
            <b-row class="margin-top-10">
                <b-col>
                    <p><b> Product Brand </b></p>
                </b-col>
                <b-col>
                    <p><b> Product Flavour </b></p>
                </b-col>
                <b-col>
                    <p><b> Quantity In Box </b></p>
                </b-col>
                <b-col>
                    <p v-if="snackbox.type !== 'wholesale'"><b> Unit Price </b></p>
                    <p v-else><b> Case Price </b></p>
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
            through a button revealable section, select a new product(s) and attach it to the current snackbox_id + details.
            This will need refreshing to update, so holding them in the store might be a good option -->

            <snackbox-item id="snackbox-products" v-for="snackbox_item in snackbox.box_items" :snackbox_item="snackbox_item" :company_details_id="company.id" :key="snackbox_item.id" @refresh-data="refreshData($event)"></snackbox-item>

             <h3 class="border-top"> Current Total: £{{ snackbox_total }} </h3> <!-- This needs some work now I've changed the snackbox composition and processes. -->
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .margin-top-10 {
        margin-top: 10px;
    }
    .padding-top-10 {
        padding-top: 10px;
    }
    .border-top {
        border-top: 2px solid black;
        padding-top: 20px;
    }
</style>

<script>
export default {
    props: ['snackbox', 'company'],
    data () {
        return {
            add_product: false,
            quantity: 0,
            editing: false,
            details: false,
            delivered_by_options: ['DPD', 'APC', 'OP'],
            days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            frequency_options: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month_options: ['First', 'Second', 'Third', 'Fourth', 'Last'],
            selected_allergens: [],
            selected_dietary_requirements: [],
            allergens: [

                {text: 'Celery', value: 'celery'},
                {text: 'Gluten', value: 'gluten'},
                {text: 'Crustaceans', value: 'crustaceans'},
                {text: 'Eggs', value: 'eggs'},
                {text: 'Fish', value: 'fish'},
                {text: 'Lupin', value: 'lupin'},
                {text: 'Milk', value: 'milk'},
                {text: 'Molluscs', value: 'molluscs'},
                {text: 'Mustard', value: 'mustard'},
                {text: 'Tree Nuts', value: 'tree-nuts'},
                {text: 'Peanuts', value:'peanuts'},
                {text: 'Sesame', value:'sesame'},
                {text: 'Soya', value: 'soya'},
                {text: 'Sulphites', value: 'sulphites'},
            ],
            dietary_requirements: [

                {text: 'Vegetarian', value: 'vegetarian'},
                {text: 'Vegan', value: 'vegan'},
                {text: 'High Protein', value: 'high-protein'},
                {text: 'Sweet', value: 'sweet'},
                {text: 'Savoury', value: 'savory'},
                {text: 'Low Salt', value: 'low-salt'},
                {text: 'Eco-friendly Packaging', value: 'eco-friendly-packaging'},
                {text: 'Gluten Free', value: 'gluten-free'},
                {text: 'Organic', value: 'organic'},
            ],
        }
    },
    computed: {
        case_stock_level() {
            let stock_level = Math.floor(this.$store.state.selectedProduct.stock_level / this.$store.state.selectedProduct.selling_case_size)
            return ( Number.isNaN(stock_level) ? '' : stock_level)
        },
        // EDIT: 20/01/20 - snackbox_total has now been amended to work with the new snackbox system.
        snackbox_total() {

            let $snackbox_total = 0

            // This function checks each entry in the current snackbox list and creates a running total (a) of the unit price (b[cost]) multiplied by the quantity (b[quantity]).
            let sum = function(snackbox_items, cost, quantity){
                 console.log(snackbox_items);
                 console.log(cost);
                 console.log(quantity);
                return snackbox_items.reduce( function(a, b) {
                    if (b.product[cost] !== null) {
                        console.log(b.product[cost]);
                        // Then this is a regular entry, we just need to total it up
                        return parseFloat(a) + ( parseFloat(b.product[cost]) * parseFloat(b[quantity]) );
                    } else {
                        // Then chances are this is the initial row; where it was either saved without product data, or had it stripped out when archived.
                        // Let's set the values to 0 so that the accumulator doesn't have a hissy fit about NaN.
                        b.product[cost] = 0;
                        b[quantity] = 0;
                        return parseFloat(a) + ( parseFloat(b.product[cost]) * parseFloat(b[quantity]) );
                    }
                }, 0);
            };

            // Now we use the function by passing in the snackbox array, and the two properties we need to multiply - saving it as the current total cost.
            // First a quick check, on whether we need to tally up case prices for wholesale, or unit prices for regular snackboxes.
            (this.snackbox.type === 'wholesale') ? $snackbox_total = sum(this.snackbox.box_items, 'selling_case_price', 'quantity') : $snackbox_total = sum(this.snackbox.box_items, 'selling_unit_price', 'quantity');

            return $snackbox_total;
        },
        currently_selected_allergens: function () {
            if (this.snackbox.allergies) {
                return this.selected_allergens = this.snackbox.allergies
            }

        },
        currently_selected_dietary_requirements: function (dietary_requirements) {
            if (this.snackbox.dietary_requirements) {
                return this.selected_dietary_requirements = this.snackbox.dietary_requirements
            }

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
        saveProductToBox(snackbox) {
            axios.post('api/boxes/snackbox/add-product', {
                product: {
                    id: this.$store.state.selectedProduct.id,
                    code: this.$store.state.selectedProduct.code,
                    brand: this.$store.state.selectedProduct.brand,
                    flavour: this.$store.state.selectedProduct.flavour,
                    quantity: this.quantity,
                    selling_unit_price: this.$store.state.selectedProduct.selling_unit_price,
                    selling_case_price: this.$store.state.selectedProduct.selling_case_price,
                },
                // snackbox_details: snackbox, // commenting out while I try to make the controller function reusable for snacks, drinks and other boxes.
                box_details: snackbox,
                box_type: 'SnackBox',
            }).then ((response) => {
                //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                this.$emit('refresh-data', {company_details_id: snackbox.company_details_id})
                this.$store.commit('removeSelectedProductFromStore');
                this.quantity = 0;
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
            axios.post('api/boxes/snackbox/details', {
                snackbox_details: snackbox,
                snackbox_selected_allergens: this.selected_allergens
            }).then (response => {
                alert('Snackbox Updated Successfully!');
                //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                console.log(response);
            }).catch(error => console.log(error));
        },
        deleteSnackBox(snackbox) {
            let self = this;
            axios.put('api/boxes/snackbox/destroy-box/' + snackbox.snackbox_id, {
                snackbox_id: snackbox.snackbox_id,
            }).then ( (response) => {
                alert('Snackbox Deleted!');
                //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                console.log(response);
                console.log(snackbox.company_details_id);
                self.$emit('refresh-data', {company_details_id: snackbox.company_details_id})
            }).catch(error => console.log(error));
        },
    },
    mounted() {
         this.$store.commit('getTypes');
         console.log(this.company); // Is coming back undefined?  But in add-new-snackbox it's working fine! I'm just using snackbox instead but it's still confusing.
    }
}
</script>
