<template>
    <div>
        <b-button class="button-addnew" variant="primary" size="lg" @click="addProduct()"> Add New Product </b-button>
        <b-container v-if="add_product" style="text-align: center;" sm="12">
            <b-form id="new-product-form" @submit="onSubmit" @reset="onReset">

                <b-form-group>
                    <b-row sm="12">
                        <!-- Product Brand -->
                        <b-col class="col-sm-4" id="product-name">
                            <label> Brand </label>
                            <b-form-input id="product-brand" type="text" v-model="form.brand" placeholder="Enter product brand" required></b-form-input>
                        </b-col>
                        <!-- Product Flavour -->
                        <b-col class="col-sm-4" id="product-name">
                            <label> Flavour </label>
                            <b-form-input id="product-flavour" type="text" v-model="form.flavour" placeholder="Enter product flavour" required></b-form-input>
                        </b-col>
                        <!-- Product Code -->
                        <b-col class="col-sm-4" id="product-code">
                            <label> Product Code </label>
                            <b-form-input id="product-code" type="text" v-model="form.code" placeholder="Enter unique product code" required></b-form-input>
                        </b-col>
                    </b-row>
                </b-form-group>

                <b-form-group>
                    <b-row sm="12">
                        <b-col class="col-sm-4" id="product-case-cost">
                            <!-- Product Case Cost -->
                            <label> Buying Case Cost </label>
                            <b-form-input v-model="form.buying_case_cost" type="number" step="any"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4" id="product-case-price">
                            <!-- Product Case Price -->
                            <label> Selling Case Price </label>
                            <!-- This needs to be formula linked - selling_case_price = (selling_unit_price * selling_case_size) -->
                            <b-form-input v-model="form.selling_case_price" type="number" step="any"></b-form-input>
                            <p class="selected-option"> Selling Case Price: {{ form.selling_case_price = form.selling_unit_price * form.selling_case_size }} </p>
                        </b-col>
                        <b-col class="col-sm-4" id="buying-case-size">
                            <!-- Buying Case Size -->
                            <label> Buying Case Size </label>
                            <b-form-input v-model="form.buying_case_size" type="number" step="any"></b-form-input>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col class="col-sm-4" id="selling-case-size">
                            <!-- Selling Case Size -->
                            <label> Selling Case Size </label>
                            <b-form-input v-model="form.selling_case_size" type="number" step="any"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4" id="product-unit-price">
                            <!-- Product Unit Price -->
                            <label> Selling Unit Price </label>
                            <b-form-input v-model="form.selling_unit_price" type="number" step="any"></b-form-input>
                            <p class="selected-option"> Buying Unit Cost: {{ form.buying_unit_cost = form.buying_case_cost / form.buying_case_size }} </p>
                        </b-col>
                        <!-- Product Stock Level -->
                        <b-col class="col-sm-4" id="product-case-stock-level">
                            <label> Stock Level (Singles) </label>
                            <b-form-input v-model="form.stock_level" type="number" step="any"></b-form-input>
                            <p class="selected-option"> Product Profit Margin: {{ form.profit_margin = (form.selling_unit_price - form.buying_unit_cost) / form.selling_unit_price * 100 }} % </p>
                        </b-col>
                    </b-row>
                </b-form-group>

                <b-form-group id="product-vat-nominals">
                    <b-row sm="12">
                        <!-- Product Vat? -->
                        <b-col class="col-sm-4">
                            <label> VAT? </label>
                            <b-form-select v-model="form.vat" :options="vat" required>
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected VAT Option: {{ form.vat }} </p>
                        </b-col>
                        <!-- Supplier -->
                        <b-col class="col-sm-4">
                            <label> Supplier </label>
                            <b-form-select v-model="form.supplier" :options="supplier" required>
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected Supplier: {{ form.supplier }} </p>
                        </b-col>
                        <!-- Sales Nominal -->
                        <b-col class="col-sm-4">
                            <label> Product Sales Nominal </label>
                            <b-form-select v-model="form.sales_nominal" :options="sales_nominal" required>
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected Sales Nominal: {{ form.sales_nominal }} </p>
                        </b-col>

                    </b-row>
                </b-form-group>

                <b-row>
                    <b-col>
                        <label><b> Allergens </b></label>
                        <b-form-group>
                            <b-form-checkbox inline v-for="allergen in allergens" v-model="form.selected_allergens" :key="allergen.value" :value="allergen.value"> {{ allergen.text }} </b-form-checkbox>
                        </b-form-group>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col>
                        <label><b> Dietary Requirements </b></label>
                        <b-form-group>
                            <b-form-checkbox inline v-for="dietary_requirement in dietary_requirements" v-model="form.selected_dietary_requirements" :key="dietary_requirement.value" :value="dietary_requirement.value"> {{ dietary_requirement.text }} </b-form-checkbox>
                        </b-form-group>
                    </b-col>
                </b-row>

                <div id="product-buttons">
                    <b-button type="submit" variant="primary"> Submit </b-button>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </div>
            </b-form>
        </b-container>
    </div>
</template>

<style lang="scss">

#new-product-form {
    label {
        font-weight: 400;
    }
    .form-control {
        font-weight: 300;
    }
}

    .button-addnew {
        margin-bottom: 20px;
    }

#new-product-form:after {
    content: ""; /* This is necessary for the pseudo element to work. */
    display: block; /* This will put the pseudo element on its own line. */
    margin: 0 auto; /* This will center the border. */
    width: 70%; /* Change this to whatever width you want. */
    padding-top: 30px; /* This creates some space between the element and the border. */
    margin-bottom: 30px; /*  */
    border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
}
.selected-option {
    padding-top: 10px;
    font-weight: 300;
}
</style>

<script>

export default {
    data () {
        return {

            form: {
                is_active: 'Active',
                code: '',
                brand: '',
                flavour: '',
                buying_case_cost: 0.00,
                selling_case_price:0.00,
                buying_case_size: 0,
                selling_case_size: 0,
                buying_unit_cost: 0,
                selling_unit_price: 0.00,
                vat: null,
                supplier: null,
                sales_nominal: null,
                profit_margin: '',
                stock_level: 0,
                selected_allergens: [],
                selected_dietary_requirements: [],
            },
            vat: ['Yes', 'No'],
            sales_nominal: ['4010', '4020', '4021', '4022', '4023', '4024', '4040', '4050', '4090'],
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
                'Other'
            ],
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
            add_product: false,
        }
    },
    methods: {
        addProduct() {
            if (this.add_product == false) {
                this.add_product = true
            } else {
                this.add_product = false
            }
        },
        onSubmit (evt) {
          evt.preventDefault();
          let self = this;
          // alert(JSON.stringify(this.form));
          axios.post('/api/office-pantry/products/add-new-product', {
              product_data: self.form,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              // user_id: self.userData.id // This hasn't been setup yet so probably won't work yet?!
          }).then(function (response) {
              alert('Uploaded new product successfully!');
              //location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
              console.log(response.data);
             // self.$emit('update-products'); <-- started this but got no further passing the command to the product list component. Add it to the todo list, for now just reload the page.
          }).catch(error => console.log(error));
        },

        onReset (evt) {
          evt.preventDefault();
          /* Reset our form values */

          this.form.brand = '';
          this.form.flavour = '';
          this.form.code = '';
          this.form.buying_case_cost = 0.00;
          this.form.selling_case_price = 0.00;
          this.form.buying_case_size = 0;
          this.form.selling_case_size = 0;
          this.form.selling_unit_price = 0.00;
          this.form.buying_unit_cost = 0.00;

          this.form.vat = null;
          this.form.sales_nominal = null;
          this.form.profit_margin = '';
          this.form.stock_level = 0;

        }
    },
}

</script>
