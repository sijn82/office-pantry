<template>
    <div class="col-sm-12">
        <b-input-group  id="company-searchbar-select" class="col-sm-12" size="sm">
                        <!-- append=" Company Search" - stripped this from the company searchbar to increase input entry space -->

            <b-form-input type="text" v-model.lazy="keywords" placeholder="Search Companies..."></b-form-input>
        </b-input-group>
        <b-list-group v-if="results.length > 0">
            <div class="col-sm-12" v-for="result in results">
                <b-list-group-item class="d-flex justify-content-between align-items-center" button :key="result.id" @click="officeData(result.id)"> {{ result.invoice_name }}
                    <b-badge variant="primary" pill> Get Data </b-badge>
                </b-list-group-item>
            </div>
        </b-list-group>

            <p class="selected-company"> Selected Company: {{ company.invoice_name }} </p>

    </div>
</template>

<style>
    .selected-company {
        font-weight: 300;
        margin-bottom: 0;
    }
</style>

<script>
export default {
    props: [],
    data() {
        return {
            keywords: null,
            selected: '',
            selected_results: null,
            company: {
                id: null,
                invoice_name: '',
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
            axios.get('/api/companies/search', { params: { keywords: this.keywords }})
                .then(response => this.results = response.data)
                .catch(error => {});
        },
        // officeID(selected) {
        //     axios.post('/api/companies/selected', { params: { company: selected }})
        //         .then(response => console.log(selected))
        //         .catch(error => {});
        //
        // },
        officeData(id) {
            axios.get('/api/companies/' + id)
                .then(response => {
                    this.company = response.data.company,
                    // this.$store.commit('selectedCompany', this.company) <-- this commit saves the value to store, however on reflection I'm going to pass it via props and custom events instead. 
                    this.$emit('selected-company', this.company) // <-- Pass this.company value to parent component as a custom event. 
                }).catch(error => {});
        }
    },
    mounted() {
        console.log('Company Select component mounted');
    }
}
</script>
