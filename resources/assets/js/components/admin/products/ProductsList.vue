<template>
    <div class="products-container">
        <h1> Product List </h1>

        <b-row>
            <b-col> <h3> Filters </h3> </b-col>
        </b-row>

        <b-row class="filters">
            <b-col>
                <h3> Search (Brand) </h3>
                <b-form-input type="text" v-model="keywords"></b-form-input>
            </b-col>
            <b-col>
                <h4> Vat? </h4>
                <b-form-select v-model="vat_select" :options="vat" required>
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-col>
                <h4> Sales Nominal </h4>
                <b-form-select v-model="sales_nominal_select" :options="sales_nominal" required>
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-col>
                <h4> Dietary Requirement </h4>
                <b-form-select v-model="dietary_requirement" :options="dietary_requirements">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
                <h4 v-if="dietary_requirement !== null" class="additional_filter"> Additional Dietary Requirement </h4>
                <b-form-select v-if="dietary_requirement !== null" v-model="additional_dietary_requirement" :options="dietary_requirements">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-col>
                <h4> Allergens </h4>
                <b-form-select v-model="allergen" :options="allergens">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
                <h4 v-if="allergen !== null" class="additional_filter"> Additional Allergen </h4>
                <b-form-select v-if="allergen !== null" v-model="additional_allergen" :options="allergens">
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <!-- Why ask to ignore when we can just comment it out for now! -->
            <!-- <b-col>
                <h4> Stock Level * </h4>
                <b-form-select v-model="stock_level_select" :options="stock_level" required>
                    <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                    </template>
                </b-form-select>
            </b-col>
            <b-form-text> * Ignore this filter for now, it's not actually built to do anything yet! </b-form-text> -->
        </b-row>

        <b-row class="product-list-headers">
            <b-col><h3> Brand </h3></b-col>
            <b-col><h3> Flavour </h3></b-col>
            <b-col><h3> Stock Level and Status </h3></b-col>
            <b-col><h3> Options </h3></b-col>
        </b-row>

        <!-- Wow this has really got out of hand, it works but I should find out if this is bad practise and replace it with a better solution! -->
        <div class="products" v-for="product in products">
            <div v-if="product.vat == vat_select || vat_select == null">
                <div v-if="product.sales_nominal == sales_nominal_select || sales_nominal_select == null">
                    <!-- <div v-if="product.dietary_requirements.includes(dietary_requirement) || dietary_requirement == null">
                        <div v-if="product.dietary_requirements.includes(additional_dietary_requirement) || additional_dietary_requirement == null">
                            <div v-if="!product.allergen_info.includes(allergen) || allergen == null">
                                <div v-if="!product.allergen_info.includes(additional_allergen) || additional_allergen == null"> -->

                                    <product    v-on:addProduct="addProductToOrder($event)"
                                                @refresh-data="grabProductList()"
                                                :createSnackbox="createSnackbox"
                                                :createWholesaleSnackbox="createWholesaleSnackbox"
                                                :createOtherbox="createOtherbox"
                                                :createDrinkbox="createDrinkbox"
                                                :type="type"
                                                :product="product">
                                    </product>

                                <!-- </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

    </div>
</template>

<style>
    .flex-center {
      display: block;
    }
    .no-list-style {
      list-style: none;
    }
    .none-in-stock {
      color: red;
    }
    .some-in-stock {
      color: green;
    }
    .a-few-in-stock {
      color: orange;
    }
    #edit-save-buttons h4 {
      font-size: 1.15em;
    }
    .product-list-headers {
      padding-bottom: 20px;
    }
    .filters {
        padding: 20px 40px;
    }
    .additional_filter {
        margin-top: 10px;
    }
</style>

<script>

import axios from 'axios';

export default {
    props: [
        'route',
        'createSnackbox',
        'createWholesaleSnackbox',
        'createOtherbox',
        'createDrinkbox',
        'type',
        'product', //  I think this is historic, or actually is it to do with the product passed through in the emitted event to add the selected product to the box?
        'quantity' // same with this one, i'm passing a product and quantity to the store.
    ],
    data() {
        return {
            products: [],
            search: '',
            keywords: '',
            // createSnackbox: false,
            order: 'nothing to see here',
            vat_select: null,
            vat: ['Yes', 'No'],
            sales_nominal_select: null,
            sales_nominal: ['4010', '4020', '4021', '4022', '4023', '4024', '4040', '4050', '4090'],
            stock_level_select: null,
            stock_level: [
                { value: "<= 0", text: 'Less than Zero'},
                { value: "> 0 && < 20", text: 'Less than 20 Units'},
                { value: ">= 20 && <= 100", text: 'Between 20 & 100 Units'},
            ],
            allergen: null,
            additional_allergen: null,
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
            dietary_requirement: null,
            additional_dietary_requirement: null,
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

    watch: {
        keywords(after, before) {
            this.fetch();
        },

    },

    methods: {
        fetch() {
            let self = this;
            axios.get('/api/office-pantry/products/search', { params: { keywords_brand: this.keywords }})
                .then(response => self.products = response.data)
                .catch(error => {});
        },
        addProductToOrder($event) {
            // alert($event);
            this.$emit('addProduct', [product, quantity]);
            this.order = 'one item';
        },

        grabProductList() {
            let self = this;
            axios.get('/api/office-pantry/products').then( response => {
                // console.log(response);
                self.products = response.data;
                console.log(self.products)
            }).catch(error => console.log(error));
        }
        // stockLevel() {
        //     if (product.stock_level < 0) {
        //         return
        //     }
        // }
    },

    computed: {
        users() {
            return this.$store.state.users;
        }

        // Goddamn, filters not working for my usecase, going to need to find another GENIOUS solution!

        // filteredProducts: function () {
        //     return self.products.filter(
        //         (product) => {
        //             return product.name.match(this.search);
        //         }
        //     )
        // }
    },

    created() {
        this.grabProductList();

    },

    mounted() {
        console.log('Component Product Lists Mounted');
        //console.log(this.route);
    }
}

</script>
