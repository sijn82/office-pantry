<template>
    <div>
        <!-- Title List -->
        <ul >
            <div id="edit-save-buttons">
                <b-row>
                    <b-col> <h4> {{ product.name }} </h4> </b-col>
                    <b-col> 
                        <div v-if="!createWholesaleSnackbox">
                            <h4 :class="{
                                            'none-in-stock' : matchNumberToColour(product.stock_level),
                                            'some-in-stock' : !matchNumberToColour(product.stock_level),
                                            'a-few-in-stock' : !matchNumberToColour(product.stock_level) && product.stock_level <= 20}">
                                            Units: {{ product.stock_level }} / {{ product.is_active }} 
                            </h4>
                        </div>
                        <div v-if="createWholesaleSnackbox">
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
                        <div v-if="createSnackbox || createDrinkbox || createOtherbox">
                            <b-input-group append="Unit(s)">
                                <b-form-input size="sm" v-model="quantity" class="quantity-input" type="number"></b-form-input>
                            </b-input-group>
                        </div>
                        <div v-else-if="createWholesaleSnackbox || createWholesaleDrinkbox || createWholesaleOtherbox">
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
                    <b-col class="col-sm-3">
                        <label><b> Shortest Stock Date </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.shortest_stock_date" type="date"></b-form-input>
                        </div>
                        <div>
                            <p> {{ product.shortest_stock_date }} </p>
                        </div>
                    </b-col>
                    
                    <b-col class="col-sm-3">
                        <label><b> Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.is_active" :options="status" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.is_active }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Name </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.name"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.name }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
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
                    <b-col class="col-sm-3">
                        <label><b> Case Price (£) </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.case_price" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.case_price }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Case Size </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.case_size" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.case_size }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Unit Purchase Cost (£) </b></label>
                        <div>
                            <p> {{ product.unit_cost = product.case_price / product.case_size }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Unit Sale Price (£) </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="product.unit_price" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ product.unit_price }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Bottom Row -->
                <b-row :class="product.is_active" sm="12" class="b-row-padding b-row-padding-extra">
                    <b-col class="col-sm-3">
                        <label><b> VAT? </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.vat" :options="vat" required></b-form-select>    
                        </div>
                        <div v-else>
                            <p> {{ product.vat }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Sales Nominal </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.sales_nominal" :options="sales_nominal" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.sales_nominal }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Cost Nominal </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="product.cost_nominal" :options="cost_nominal" required></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ product.cost_nominal }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-3">
                        <label><b> Profit Margin </b></label>
                        <div>
                            <p> {{ product.profit_margin = (product.unit_price - product.unit_cost) / product.unit_cost * 100 }} % </p>
                        </div>
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
    props: ['product', 'route', 'createSnackbox', 'createWholesaleSnackbox', 'createOtherbox', 'createDrinkbox'],
    data () {
        return {
            // product: {
            //     id: '',
            //     is_active: '',
            //     code: '',
            //     name: '',
            //     case_price: 0.00,
            //     case_size: 0,
            //     unit_cost: 0,
            //     unit_price: 0.00,
            //     vat: null,
            //     sales_nominal: null,
            //     cost_nominal: null,
            //     profit_margin: '',
            //     stock_level: 0,
            // },
            editing: false,
            details: false,
            quantity: 0,
            status: ['Active', 'Inactive'],
            vat: ['Yes', 'No'],
            sales_nominal: ['4010', '4020', '4040', '4050', '4090'],
            cost_nominal: ['5010', '5020', '5030', '5040'],
        }
    },
    
    computed: {
        // This little computed property converts the stock level from units, to cases but 
        // ignores any left over singles by rounding down to the nearest full case.
        number_of_cases: function () {
            
            return Math.floor(this.product.stock_level / this.product.case_size)
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
            axios.put('api/products/update/' + product.id, {
                id: product.id,
                is_active: product.is_active,
                name: product.name,
                code: product.code,
                case_price: product.case_price,
                case_size: product.case_size,
                unit_cost: product.unit_cost,
                unit_price: product.unit_price,
                vat: product.vat,
                sales_nominal: product.sales_nominal,
                cost_nominal: product.cost_nominal,
                profit_margin: product.profit_margin,
                stock_level: product.stock_level,
                shortest_stock_date: product.shortest_stock_date,
              
            }).then (response => {
              console.log(response);
            }).catch(error => console.log(error));
        },
        
        deleteProduct(product) {
            axios.put('api/products/destroy/' + product.id, { 
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
        //console.log(this.createSnackbox);
    }

}
</script>