<template>
    <div>
        <b-container style="text-align: center;" sm="12">

            <preferences v-if="show_preferences"></preferences>

            <!-- <b-button class="get-preferences" v-if="show_preferences" @click="getCompanyPreferences(selected_company)"> Refresh Company Preferences </b-button>
            <b-button class="get-preferences" v-else @click="getCompanyPreferences(selected_company)"> Get Company Preferences </b-button> -->

            <h3> Add New Preference </h3>
            <!-- <p> {{ $store.state.setPreferences }} </p> -->

            <b-form id="new-preference-form" @submit="onSubmit" @reset="onReset">

                <b-form-group>
                    <b-row>
                        <!-- Company Select -->
                        <b-col id="company-select">
                            <label> Selected Company </label>
                            <!-- <select-company v-on:selected-company="companySelected"></select-company> -->
                            <h4 class="font-weight-300"> {{ companySelected(this.company) }} </h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <!-- Select Product -->
                        <b-col id="product-select">
                            <label> Select Product </label>
                            <select-product></select-product>
                        </b-col>
                        <b-col id="add-to-buttons">
                            <p v-if="$store.state.selectedProduct.brand"> Selected Product: <b class="selected-product"> {{ $store.state.selectedProduct.brand }} - {{ $store.state.selectedProduct.flavour }} </b></p>
                            <p v-else> Selected Product: <b class="selected-product"> None Selected </b></p>
                            <b-button @click="addToPreferences('likes')"> Add To Likes </b-button>
                            <b-button @click="addToPreferences('dislikes')"> Add To Dislikes </b-button>
                            <div class="essentials">
                                <b-button @click="addToPreferences('essentials')"> Add To Essentials </b-button>
                                <b-input v-model="essential_quantity" type="number" placeholder="Quantity"></b-input>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row class="additional-padding">
                        <!-- <b-col>
                            <label> Select Allergy </label>
                            <b-form-select v-model="allergy" :options="this.$store.state.allergies_list" type="text" placeholder="Select Allergy">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                            <b-button @click="confirmAllergy(allergy)"> Save </b-button>
                        </b-col>
                        <b-col>
                            <label> Add New Allergy </label>
                            <b-form-input type="text" v-model="new_allergy" placeholder="Add a new allergy to list of options">  </b-form-input>
                            <b-button @click="addNewAllergy(new_allergy)"> Add </b-button>
                        </b-col> -->

                        <b-col>
                            <label><h4> Allergens </h4></label>
                            <b-form-group>
                                <b-form-checkbox v-for="allergen in allergens" v-model="selected_allergens" :key="allergen.value" :value="allergen.value"> {{ allergen.text }} </b-form-checkbox>
                            </b-form-group>
                            <b-button @click="confirmAllergens(selected_allergens)"> Save </b-button>
                        </b-col>

                        <b-col>
                            <label><h4> Dietary Requirements </h4></label>
                            <b-form-group>
                                <b-form-checkbox v-for="dietary_requirement in dietary_requirements" v-model="selected_dietary_requirements" :key="dietary_requirement.value" :value="dietary_requirement.value"> {{ dietary_requirement.text }} </b-form-checkbox>
                            </b-form-group>
                        </b-col>

                        <b-col>
                            <label><h4> Additional Notes </h4></label>
                            <b-form-textarea v-model="additional_info" :rows="3" :max-rows="6" placeholder="Enter unrealistic demands here..."></b-form-textarea>
                            <b-button @click="confirmAdditionalInfo(additional_info)"> Save </b-button>
                        </b-col>
                    </b-row>
                </b-form-group>

                <!-- <div id="product-buttons">
                    <b-button type="submit" variant="primary"> Submit </b-button>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </div> -->
            </b-form>
        </b-container>
    </div>
</template>

<style lang="scss">
    #new-preference-form::after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    #add-to-buttons {
        button {
            margin: 10px 0;
        }
        .essentials input {
            display: inline-block;
            max-width: 100px;
        }
        .selected-product {
            font-size: 1.5em;
        }
    }
    .additional-padding {
        padding-top: 30px;
    }
    .get-preferences {
        margin-bottom: 20px;
    }
    .font-weight-300, label, p {
        font-weight: 300;
    }
</style>

<script>
export default {
    // props: ['likes', 'dislikes', 'essentials', 'allergies', 'additional_notes'], old props, keeping a record but will remove if I carry on with the new plan.
    props: ['company'],
    data() {
        return {
            essential_quantity: null,
            allergy: null,
            new_allergy: '',
            // allergies: this.$store.state.allergies_list, //'Gluten', 'Dairy', 'Nuts', 'Work', 'Pollen', 'Sunshine'
            additional_info: '',
            // company_selected: null,
            preferences: {},
            show_preferences: false,
            // selected_company: 'none selected',
            selected_allergens: [],
            selected_dietary_requirements: [],
            allergens: [

                {text: 'Celery', value: 'celery'},
                {text: 'Gluten', value: 'gluten'},
                {text: 'Crustaceans', value: 'crustacians'},
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
            ],
        }
    },

    computed: {

        // selected_company: function (company) {
        //     console.log('yah, we got this ' . this.company);
        // }
        // getAllergies () {
        //     axios.get('/api/allergies/select').then( response => {
        //     let allergies = response.data;
        //     console.log(allergies);
        //     return allergies;
        //     }).catch(error => console.log(error));
        // },
    },

    methods: {
        // I need to do something about what the is held for backend purposes and what the user sees.
        companySelected(company) {
            console.log('Yah, we got this ' + company.route_name); // <-- changed this to route name, think I should do the same with all of this but I have another problem to solve first!
            this.selected_company = company.id;
            return this.selected_company_invoice_name = company.invoice_name;
            //alert(company.id);
        },

        confirmAllergens(selected_allergens) {
            axios.post('/api/company/allergies', {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                selected_allergens: this.selected_allergens,
                selected_dietary_requirements: this.selected_dietary_requirements,
                selected_company: this.company.id
            })
            .then( response => { alert('Selected company allergies saved.')})
            .catch( error => { alert(error)});
        },

        // addNewAllergy(new_allergy) {
        //     console.log(new_allergy);
        //
        //     this.$store.commit('addNewAllergyToStore', new_allergy);
        // },
        //
        // confirmAllergy(allergy) {
        //
        //     let company_details_id = this.selected_company;
        //     let name = allergy;
        //     axios.post('/api/company/allergies', {
        //         new_allergy: { name, company_details_id },
        //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        //     }).then( response => {
        //         alert('Uploaded new company allergy successfully!');
        //         this.$emit('refresh-data', {company_details_id: this.selected_company});
        //         // location.load(true);
        //         // console.log(response.data[0].id);
        //         // console.log(response.data[0].allergy);
        //         let allergy = response.data[0].allergy;
        //         let id = response.data[0].id
        //         this.$store.commit('addAllergyToStore', { allergy, id });
        //     }).catch(error => {
        //         console.log(error)
        //     });
        // },

        confirmAdditionalInfo(additional_info) {

            let company = this.selected_company;
            let info = additional_info;

            axios.post('/api/company/additional-info', {
                additional_info: { info, company },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).then( response => {
                alert('Uploaded new company additional info successfully!');
                this.$emit('refresh-data', {company_details_id: this.selected_company});
                let additional_info = response.data[0].additional_info;
                let id = response.data[0].id
                this.$store.commit('addAdditionalInfoToStore', { additional_info, id });
            }).catch(error => {
                console.log(error)
            });
        },

        addToPreferences(preference) {

            console.log(this.$store.state.selectedProduct.brand);

            let company_details_id = this.selected_company;
            let product_brand = this.$store.state.selectedProduct.brand;
            let product_flavour = this.$store.state.selectedProduct.flavour;
            let product_code = this.$store.state.selectedProduct.code;
            let product_quantity = this.essential_quantity;
            let preference_category = preference;

            // This is starting to become overly complicated as until we save it to the database we don't have an id to remove it.
            // We could maybe make another check but for now, let's sort out actually adding/deleting from database and then worrry about the front end.
            // this.$store.commit('addPreferenceToStore', { preference, product_name });

            axios.post('/api/company/preferences/add-new-preference', {
                preference: { company_details_id, product_brand, product_flavour, product_code, product_quantity, preference_category },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).then( response => {
                 alert('Uploaded new preference successfully!');
                 console.log('should be something below');
                 console.log(response.data.category);
                 console.log(response.data.preference);
                 let category = response.data.category;
                 let id = response.data.preference[0].id;
                 let name = response.data.preference[0][category];
                 let quantity = null;
                 if (category == 'snackbox_essentials') {
                     let quantity = response.data.preference[0].snackbox_essentials_quantity;
                 }
                 //console.log(name);

                 this.$emit('refresh-data', {company_details_id: this.selected_company});
                 this.$store.commit('addPreferenceToStore', { category, product:{ id, brand, flavour, quantity }});
                 // location.load(true);
            //     console.log(response.data);
            }).catch(error => console.log(error));

            // console.log(preference);
            // console.log(this.essential_quantity);
        },

        // getCompanyPreferences(company_details_id) {
        //
        //     let self = this;
        //
        //     axios.post('/api/preferences/selected', {
        //         id: company_details_id,
        //     }).then( response => {
        //         console.log(response.data);
        //         // self.preferences = response.data;
        //         self.$store.commit('setPreferences', response.data );
        //         // console.log(self.preferences);
        //         // console.log(self.preferences.likes);
        //         this.show_preferences = true;
        //
        //     }).catch(error => console.log(error));
        //
        // },

        onSubmit (evt) {
          evt.preventDefault();
          let self = this;
          // alert(JSON.stringify(this.form));
          // axios.post('/api/preferences/add-new-preference', {
          //     company_data: self.form,
          //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
          //     // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
          // }).then(function (response) {
          //     alert('Uploaded new preference successfully!');
          //     console.log(response.data);
          // }).catch(error => console.log(error));
        },

        onReset (evt) {
          evt.preventDefault();
          /* Reset our form values */
          // this.form.name = '';


        }
    },
    mounted() {

         this.$store.commit('getAllergies');
    }
}
</script>
