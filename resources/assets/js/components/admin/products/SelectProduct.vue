<template>
    <div class="col-sm-12">
        <b-input-group id="product-searchbar-select" class="col-sm-12" size="sm" append=" Product Search">
            <b-form-input type="text" v-model.lazy="keywords"></b-form-input>
        </b-input-group>
        <b-list-group v-if="results.length > 0">
            <div class="col-sm-12" v-for="result in results">
                <b-list-group-item class="d-flex justify-content-between align-items-center" button :key="result.id" @click="productData(result.id)"> {{ result.name }}
                    <b-badge variant="primary" pill> Select Product </b-badge>
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

    methods: {
        fetch() {
            axios.get('/api/products/search', { params: { keywords: this.keywords }})
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
