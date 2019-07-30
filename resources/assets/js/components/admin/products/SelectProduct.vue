<template>
    <div class="col-sm-12">
        <b-input-group id="product-searchbar-select" class="col-sm-12" size="sm" append=" Product Search">
            <b-form-input type="text" v-model.lazy="keywords"></b-form-input>
        </b-input-group>
        <b-list-group v-if="results.length > 0">
            <div class="col-sm-12" v-for="result in results">
                <b-list-group-item :variant="stock_level(result.stock_level)" class="d-flex justify-content-between align-items-center" button :key="result.id" @click="productData(result.id)"> {{ result.name }}
                    <b-badge variant="primary" pill> £{{ result.unit_price }} </b-badge>
                </b-list-group-item>

            </div>
        </b-list-group>

        <!-- Selected Product: {{ this.product_data.product.name }}  I'm pretty sure this isn't used for anything but commenting out for now just in case.   -->
        <!-- Selected Product: {{ this.$store.state.selectedProduct }} This isn't necessary here outside of debugging. -->

    </div>
</template>

<style>

</style>

<script>
export default {
    props: [],
    data() {
        return {
            keywords: null,
            selected: '',
            selected_results: null,
            product_data: {
                product: {
                    name: '',
                }
            },
            results: []
        };
    },

    watch: {
        keywords(after, before) {
            this.fetch();
        },

    },
    
    computed: {
        // stock_level() {
        // 
        // //    let apply_variant = function (stock_count) {
        //         if (stock_count < 20) {
        //             return 'Warning'
        //         } else {
        //             return 'Primary'
        //         }
        //     }
        // 
        // //    $count = apply_variant(this.result.stock_level)
        //     return $count
        // 
        // }
    },

    methods: {
        stock_level(stock_count) {
                console.log(stock_count)
                if (stock_count <= 0) {
                    return "danger"
                } else if (stock_count < 20) {
                    return "warning"
                } else {
                    return "primary"
                }
        },
        
        fetch() {
            axios.get('/api/office-pantry/products/search', { params: { keywords: this.keywords }})
                .then(response => this.results = response.data)
                .catch(error => {});
        },
        // officeID(selected) {
        //     axios.post('/api/companies/selected', { params: { company: selected }})
        //         .then(response => console.log(selected))
        //         .catch(error => {});
        //
        // },
        productData(id) {
             axios.get('/api/products/' + id)
                .then(response => {
                    this.product = response.data.product;
                    this.$store.commit('selectedProduct', this.product)
                }).catch(error => {});
                // 
        },

    },
    mounted() {
        console.log('Product Select component mounted');
    }
}
</script>
