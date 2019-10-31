<template>
    <div>
        <!-- Title List -->
        <ul >
            <div id="edit-save-buttons">
                <b-row>
                    <b-col> <h4> {{ product.brand }} </h4> </b-col>
                    <b-col> <h4> {{ product.flavour }} </h4> </b-col>
                    <b-col>
                        <div v-if="!createWholesaleSnackbox && !createDrinkbox || createDrinkbox && type === 'Unique' || createDrinkbox && type === 'monthly-special'">
                            <h4 :class="{
                                            'none-in-stock' : matchNumberToColour(product.stock_level),
                                            'some-in-stock' : !matchNumberToColour(product.stock_level),
                                            'a-few-in-stock' : !matchNumberToColour(product.stock_level) && product.stock_level <= 20}">
                                            Units: {{ computed_quantity_units }} / {{ product.is_active }}
                            </h4>
                        </div>
                        <div v-if="createWholesaleSnackbox || createDrinkbox && type === 'Regular'">
                            <h4 :class="{
                                            'none-in-stock' : matchNumberToColour(product.stock_level),
                                            'some-in-stock' : !matchNumberToColour(product.stock_level),
                                            'a-few-in-stock' : !matchNumberToColour(product.stock_level) && product.stock_level <= 20}">
                                            Cases: {{ number_of_cases }} / {{ product.is_active }}
                            </h4>
                        </div>
                    </b-col>
                    <b-col>
                        <b-button size="sm" v-if="!createSnackbox && !createWholesaleSnackbox && !createDrinkbox && !createOtherbox" variant="primary" @click="showDetails()"> Details </b-button>
                        <b-button size="sm" v-if="!createSnackbox && !createWholesaleSnackbox && !createDrinkbox && !createOtherbox" variant="warning" @click="enableEdit()"> Edit </b-button>
                        <b-button size="sm" v-if="editing && !createSnackbox && !createWholesaleSnackbox && !createDrinkbox && !createOtherbox" variant="success" @click="updateProduct(product)"> Save </b-button>
                        <b-button size="sm" v-if="!createSnackbox && !createWholesaleSnackbox && !createDrinkbox && !createOtherbox" variant="danger" @click="deleteProduct(product)"> Delete </b-button>
                        <b-button size="sm" v-if="createSnackbox" variant="outline-success" @click="addProductToSnackbox(product, quantity)"> Add To Snackbox </b-button>
                        <b-button size="sm" v-if="createWholesaleSnackbox" variant="outline-success" @click="addProductToSnackbox(product, quantity)"> Add To Wholesale Snackbox </b-button>
                        <b-button size="sm" v-if="createOtherbox" variant="outline-success" @click="addProductToOtherbox(product, quantity)"> Add To Otherbox </b-button>
                        <b-button size="sm" v-if="createDrinkbox" variant="outline-success" @click="addProductToDrinkbox(product, quantity)"> Add To Drinkbox </b-button>
                        <div v-if="createSnackbox || createOtherbox || createDrinkbox && type === 'Unique' || createDrinkbox && type === 'monthly-special'">
                            <b-input-group append="Unit(s)">
                                <b-form-input size="sm" v-model="quantity" class="quantity-input" type="number"></b-form-input>
                            </b-input-group>
                        </div>
                        <div v-else-if="createWholesaleSnackbox || createDrinkbox && type === 'Regular' || createWholesaleOtherbox">
                            <b-input-group append="Case(s)">
                                <b-form-input size="sm" v-model="quantity" class="quantity-input" type="number"></b-form-input>
                            </b-input-group>
                        </div>
                    </b-col>
                </b-row>
            </div>
            <!-- Breakdown Details -->
            <!-- Top Row -->
            <div id="product-details" v-if="details">
                <b-row id="top-details" sm="12" class="b-row-padding b-row-padding-extra">
                    <b-col>
                        <label><b> Shortest Stock Date </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.shortest_stock_date" type="date"></b-form-input>
                        </div>
                        <div>
                            <p> {{ product.shortest_stock_date }} </p>
                        </div>
                    </b-col>

                    <b-col>
                        <label><b> Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.is_active" :options="status" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.is_active }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Brand </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.brand"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.brand }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Flavour </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.flavour"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.flavour }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Code </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.code"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.code }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Middle Row -->
                <b-row :class="product.is_active" sm="12" class="b-row-padding b-row-padding-extra">
                    <b-col>
                        <label><b> Buying Case Cost (£) </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.buying_case_cost" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.buying_case_cost }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Selling Case Price (£) </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.selling_case_price" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.selling_case_price }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Buying Case Size </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.buying_case_size" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.buying_case_size }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Selling Case Size </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.selling_case_size" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.selling_case_size }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Buying Unit Cost (£) </b></label>
                        <div>
                            <p> {{ product.buying_unit_cost = product.buying_case_cost / product.buying_case_size }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Selling Unit Price (£) </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.selling_unit_price" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.selling_unit_price }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Bottom Row -->
                <b-row :class="product.is_active" sm="12" class="b-row-padding b-row-padding-extra">
                    <b-col>
                        <label><b> VAT? </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.vat" :options="vat" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.vat }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Supplier </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.supplier" :options="supplier" required>
                                <!-- <template slot="first">
                                        <option :value="product.supplier"> {{ product.supplier }} </option>
                                </template> -->
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p class="selected-option"> Selected Supplier: {{ product.supplier }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Sales Nominal </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.sales_nominal" :options="sales_nominal" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.sales_nominal }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Profit Margin </b></label>
                        <div>
                            <p> {{ product.profit_margin = (product.selling_unit_price - product.buying_unit_cost) / product.selling_unit_price * 100 }} % </p>
                        </div>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <label><b> Allergen Information </b></label>
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
                            <b-form-group>
                                <b-form-checkbox    inline
                                                    v-for="allergen in product.allergen_info"
                                                    v-model="product.allergen_info"
                                                    :key="allergen"
                                                    :value="allergen">
                                                    <b> {{ allergen }} </b>
                                </b-form-checkbox>
                            </b-form-group>
                        </div>
                    </b-col>
                </b-row>
                <b-row>
                    <!-- <b-col>
                        <p><b> {{ selected_allergens }} </b></p>
                    </b-col> -->
                    <b-col>
                        <p hidden><b> {{ currently_selected_allergens }} </b></p>
                    </b-col>
                </b-row>
            </div>
        </ul>
    </div>
</template>

<style lang="scss">
    ul {
        padding-right: 40px
    }
    .b-row-padding-extra {
        padding-top: 8px;
        padding-bottom: 7px;
    }
    #product-details {
        p {
            font-weight: 300;
        }
    }
    .quantity-input {
        width: 50px;
        display: inline-block;
    }
</style>

<script>
export default {
    props: ['product', 'route', 'createSnackbox', 'createWholesaleSnackbox', 'createOtherbox', 'createWholesaleOtherbox', 'createDrinkbox', 'type'],
    data () {
        return {
            editing: false,
            details: false,
            quantity: 0,
            status: ['Active', 'Inactive'],
            vat: ['Yes', 'No'],
            supplier: [
                'Booker',
                'Epicurium',
                'Kingdom Coffee',
                'Supermarket',
                'Craft Drink Co',
                'Direct',
                'Holley\'s Fine Foods',
                'Essential Trading',
                'Templeton Drinks',
                'Majestic Wines',
                'Enotria & Coe',
                'LWC',
                'Euroffice',
                'Other',
            ],
            sales_nominal: ['4010', '4020', '4040', '4050', '4090'],
            selected_allergens: [],
            allergens: [
                {text: 'Vegetarian', value: 'vegetarian'},
                {text: 'Vegan', value: 'vegan'},
                {text: 'Contains Nuts', value: 'contains-nuts'},
                {text: 'Gluten Free', value: 'gluten-free'},
                {text: 'Dairy Free', value: 'dairy-free'},
                {text: 'Soy Free', value: 'soy-free'},
                {text: 'High Protein', value: 'high-protein'},
                {text: 'Sweet', value: 'sweet'},
                {text: 'Savoury', value: 'savory'},
                {text: 'Eco-friendly Packaging', value: 'eco-friendly-packaging'},
            ],
        }
    },

    computed: {
        // This little computed property converts the stock level from units, to cases but
        // ignores any left over singles by rounding down to the nearest full case.
        number_of_cases: function () {

            return Math.floor(this.product.stock_level / this.product.selling_case_size)
        },
        computed_quantity_units: function () {
            return this.product.stock_level - this.quantity
        },
        currently_selected_allergens: function (allergen_info) {
            return this.selected_allergens = this.product.allergen_info
        }

    },

    methods: {

        productSearch: function() {
            var self=this;
            if (product.name == this.product) {
            console.log(this.product);
            return this.product;
            };
        },

        matchNumberToColour(number) {
            if (number < 1) {
            return true;
            }
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

        updateProduct(product) {
            this.editing = false;
            console.log(product);
            console.log(product.id);
            axios.put('/api/office-pantry/products/update/' + product.id, {
                id: product.id,
                is_active: product.is_active,
                brand: product.brand,
                flavour: product.flavour,
                code: product.code,
                buying_case_cost: product.buying_case_cost,
                selling_case_price: product.selling_case_price,
                buying_case_size: product.buying_case_size,
                selling_case_size: product.selling_case_size,
                buying_unit_cost: product.buying_unit_cost,
                selling_unit_price: product.selling_unit_price,
                vat: product.vat,
                supplier: product.supplier,
                sales_nominal: product.sales_nominal,
                profit_margin: product.profit_margin,
                stock_level: product.stock_level,
                selected_allergens: this.selected_allergens,
                shortest_stock_date: product.shortest_stock_date,

            }).then (response => {
                location.reload(true);
                console.log(response);
            }).catch(error => console.log(error));
        },

        deleteProduct(product) {
            axios.put('api/office-pantry/products/destroy/' + product.id, {
                id: product.id,
            }).then (response => {
                location.reload(true); // If I stored the current products in the store rather than like this, I wouldn't need to reload the page to update the view.
                console.log(response);
            }).catch(error => console.log(error));
        },

        // addProductToSnackbox(product, quantity) {
        //     this.$emit('addProduct', [product, quantity]);
        //     console.log(this.$emit('addProduct', [product, quantity]));
        // }

        addProductToSnackbox(product, quantity) {
             // var $snackbox = [];
            product.quantity = quantity;
            // $snackbox.push({product: product, quantity: quantity});
            // $snackbox.push({product: product});
            this.$store.commit('addSnackboxToStore', product);
        },
        addProductToOtherbox(product, quantity) {
             // var $snackbox = [];
            product.quantity = quantity;
            // $snackbox.push({product: product, quantity: quantity});
            // $snackbox.push({product: product});
            this.$store.commit('addOtherboxToStore', product);
        },
        addProductToDrinkbox(product, quantity) {
             // var $snackbox = [];
            product.quantity = quantity;
            // $snackbox.push({product: product, quantity: quantity});
            // $snackbox.push({product: product});
            this.$store.commit('addDrinkboxToStore', product);
        }
    },

    mounted() {
        console.log('Components Product Mounted');
        //console.log(this.product.allergen_info);
    }

}
</script>
