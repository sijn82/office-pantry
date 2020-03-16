<template>
    <div class="col-sm-12">
        <b-input-group id="company-searchbar" class="col-sm-12" size="lg" append=" Company Search">
            <b-form-input type="text" v-model.lazy="keywords"></b-form-input>
        </b-input-group>
        <b-list-group v-if="results.length > 0">
            <div class="col-sm-12" v-for="result in results">
                <b-list-group-item class="d-flex justify-content-between align-items-center" button :key="result.id" @click="officeData(result.id)"> {{ result.route_name }}
                    <b-badge variant="primary" pill> Get Data </b-badge>
                </b-list-group-item>
                <!-- <b-button :selected="result.id" @click="officeID(result.id)" type="submit" variant="success">Select</b-button> -->
                <!-- <b-button :selected="result.id" @click="officeData(result.id)" type="submit" variant="success">Get Data</b-button> -->
            </div>
        </b-list-group>
        <div v-if="this.company_data.company != null">
            <company-details-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company">
            </company-details-admin>
        </div>
        <div v-if="this.company_data.preferences != null">
            <preferences-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :preferences="this.company_data.preferences"
                :allergies="this.company_data.allergies"
                :additional_info="this.company_data.additional_info">
            </preferences-admin>
        </div>
        <!--    This v-if != null check is more about preventing the component building before data has been pulled through 
                - one check would suffice but eh, I have four for now -->
        <div v-if="this.company_data.scheduled_fruitboxes != null || this.company_data.fruitboxes != null || this.company_data.paused_fruitboxes != null || this.company_data.archived_fruitboxes != null">
            <fruit-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :scheduled_fruitboxes="this.company_data.scheduled_fruitboxes"
                :fruitboxes="this.company_data.fruitboxes"
                :paused_fruitboxes="this.company_data.paused_fruitboxes"
                :archived_fruitboxes="this.company_data.archived_fruitboxes">
            </fruit-orders-admin>
        </div>
        <div v-if="this.company_data.milkboxes != null">
            <milk-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :scheduled_milkboxes="this.company_data.scheduled_milkboxes"
                :milkboxes="this.company_data.milkboxes"
                :paused_milkboxes="this.company_data.paused_milkboxes"
                :archived_milkboxes="this.company_data.archived_milkboxes">
            </milk-orders-admin>
        </div>
        <div v-if="this.company_data.routes != null">
            <routes-admin
                @refresh-data="officeData($event.company_details_id)"
                :routes="this.company_data.routes">
            </routes-admin>
        </div>
        <div v-if="this.company_data.snackboxes != null">
            <snackboxes-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :snackboxes="this.company_data.snackboxes">
            </snackboxes-admin>
        </div>
        <div v-if="this.company_data.drinkboxes != null">
            <drink-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :drinkboxes="this.company_data.drinkboxes">
            </drink-orders-admin>
        </div>
        <div v-if="this.company_data.otherboxes != null">
            <other-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :otherboxes="this.company_data.otherboxes">
            </other-orders-admin>
        </div>

        <!-- Think I might remove this whole section and replace it with a box specific category -->
        <div class="archive-header" v-if="this.company_data.archived_fruitboxes != null || this.company_data.archived_milkboxes != null">
            <h2> Archived Orders (Awaiting Invoice) </h2>
        </div>

        <div v-if="this.company_data.archived_fruitboxes != null">
            <archived-fruit-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :archived_fruitboxes="this.company_data.archived_fruitboxes">
            </archived-fruit-orders-admin>
        </div>

        <div v-if="this.company_data.archived_milkboxes != null">
            <archived-milk-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :archived_milkboxes="this.company_data.archived_milkboxes">
            </archived-milk-orders-admin>
        </div>
        <div v-if="this.company_data.archived_snackboxes != null">
            <archived-snackboxes-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :archived_snackboxes="this.company_data.archived_snackboxes">
            </archived-snackboxes-admin>
        </div>
        <div v-if="this.company_data.archived_drinkboxes != null">
            <archived-drink-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :archived_drinkboxes="this.company_data.archived_drinkboxes">
            </archived-drink-orders-admin>
        </div>
        <div v-if="this.company_data.archived_otherboxes != null">
            <archived-other-orders-admin
                @refresh-data="officeData($event.company_details_id)"
                :company="this.company_data.company"
                :archived_otherboxes="this.company_data.archived_otherboxes">
            </archived-other-orders-admin>
        </div>
        <!-- <div v-else>
            <p>empty</p>
        </div> -->
    </div>
</template>

<style>
    .archive-header {
        text-align: center;
        font-weight: 400;
        margin: 20px 0;
    }
</style>

<script>
export default {
    // props: [], // Not sure I actually need to declare any of these here?
    data() {
        return {
            keywords: null,
            selected: '',
            selected_results: null,
            company_data: {
                fruitboxes: null,
                milkboxes: null,
                routes: null,
                companies: null,
                snackboxes: null,
            },
            results: []
        };
    },

    watch: {
        keywords(before, after) {
            this.fetch();
        },

    },

    methods: {
        fetch() {
            axios.get('/api/company/companies/search', { params: { keywords: this.keywords }})
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
                .then(response => { this.company_data = response.data, console.log(this.company_data ) })
                .catch(error => {});
        }
    },
    mounted() {
        console.log(this.company_data);
        console.log(this.company_data.snackboxes + ' = Snackbox Status');
    }
}
</script>
