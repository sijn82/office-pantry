<template>
    <div class="products-container">
        <h1> Product List </h1>
        
        <b-row>
            <b-col> <h3> Filters </h3> </b-col>
        </b-row>

        <b-row class="filters">
            <b-col>
                <h3> Search </h3>
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
            <b-col><h3> Name </h3></b-col>
            <b-col><h3> Stock Level and Status </h3></b-col>
            <b-col><h3> Options </h3></b-col>
        </b-row>

            <div class="products" v-for="product in products">
                <div v-if="product.vat == vat_select || vat_select == null">
                    <div v-if="product.sales_nominal == sales_nominal_select || sales_nominal_select == null">
                        <!-- This stock level thingy isn't a great plan, better would be to write a function and then get that to output a usable result. -->
                        <!-- <div v-if="product.stock_level || stock_level_select == null"> -->
                            <product    v-on:addProduct="addProductToOrder($event)"
                                        :createSnackbox="createSnackbox"
                                        :createWholesaleSnackbox="createWholesaleSnackbox"
                                        :createOtherbox="createOtherbox" 
                                        :createDrinkbox="createDrinkbox"
                                        :type="type" 
                                        :product="product">
                            </product>
                        <!-- </div> -->
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
</style>

<script>

import axios from 'axios';

export default {
    props: ['route', 'createSnackbox', 'createWholesaleSnackbox', 'createOtherbox', 'createDrinkbox', 'type', 'product', 'quantity'],
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
            sales_nominal: ['4010', '4020', '4040', '4050', '4090'],
            stock_level_select: null,
            stock_level: [
                { value: "<= 0", text: 'Less than Zero'},
                { value: "> 0 && < 20", text: 'Less than 20 Units'},
                { value: ">= 20 && <= 100", text: 'Between 20 & 100 Units'},
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
            axios.get('/api/products/search', { params: { keywords: this.keywords }})
                .then(response => self.products = response.data)
                .catch(error => {});
        },
        addProductToOrder($event) {
            // alert($event);
            this.$emit('addProduct', [product, quantity]);
            this.order = 'one item';
        },
        test() {
            // store.commit('increment'); This is the example in the documentation.
            this.$store.commit('increment'); // This is the only way I could get it to work?  What's the difference?  Why is nothing straightforward?
            // console.log(store.state.count); This is the example in the documentation.
            console.log(this.$store.state.count); // This is the only way I could get it to work?
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
        let self = this;
        axios.get('/api/products').then( response => {
            // console.log(response);
            self.products = response.data;
            console.log(self.products)
        }).catch(error => console.log(error));
    },

    mounted() {
        console.log('Component Product Lists Mounted');
        console.log(this.route);
    }
}

</script>
