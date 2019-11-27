<template>
    <div id="preferences">
         <b-row>
            <b-col>
                <div class="likes">
                    <h4> Likes </h4>
                    <!-- <div v-if="$store.state.setPreferences.snackbox_likes != 'None'">
                        <div v-for="like in $store.state.setPreferences.snackbox_likes"> -->
                        <!-- <div v-if="this.preferences.snackbox_likes != null"> -->
                        <div  v-for="preference in this.preferences">
                            <!-- <p > {{ like.snackbox_likes }} </p> -->
                            <preference v-if="preference.snackbox_likes != null"
                                        :name="preference.snackbox_likes"
                                        :key="preference.id"
                                        :id="preference.id"
                                        :column="'snackbox_likes'"
                                        @refresh-data="refreshData(preference.company_details_id)">
                            </preference>
                        </div>
                    <!-- </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_likes"></preference>
                    </div> -->
                </div>
            </b-col>
            <b-col>
                <div class="dislikes">
                    <h4> Dislikes </h4>
                    <div  v-for="preference in this.preferences">
                        <!-- <p > {{ like.snackbox_likes }} </p> -->
                        <preference v-if="preference.snackbox_dislikes != null"
                                    :name="preference.snackbox_dislikes"
                                    :key="preference.id"
                                    :id="preference.id"
                                    :column="'snackbox_dislikes'"
                                    @refresh-data="refreshData(preference.company_details_id)">
                        </preference>
                    </div>
                    <!-- <div v-if="$store.state.setPreferences.snackbox_dislikes != 'None'">
                        <div v-for="dislike in $store.state.setPreferences.snackbox_dislikes">
                            <preference :name="dislike.snackbox_dislikes"
                                        :id="dislike.id"
                                        :column="'snackbox_dislikes'">
                            </preference>
                        </div>
                    </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_dislikes"></preference>
                    </div> -->
                </div>
            </b-col>
            <b-col>
                <div class="essentials">
                    <h4> Essentials </h4>
                    <div  v-for="preference in this.preferences">
                        <!-- <p > {{ like.snackbox_likes }} </p> -->
                        <preference v-if="preference.snackbox_essentials != null"
                                    :name="preference.snackbox_essentials"
                                    :quantity="preference.snackbox_essentials_quantity"
                                    :key="preference.id"
                                    :id="preference.id"
                                    :column="'snackbox_essentials'"
                                    @refresh-data="refreshData(preference.company_details_id)">
                        </preference>
                    </div>
                    <!-- <div v-if="$store.state.setPreferences.snackbox_essentials != 'None'">
                        <div v-for="essential in $store.state.setPreferences.snackbox_essentials">
                            <b-row>
                                <b-col>
                                    <preference :name="essential.snackbox_essentials"
                                                :quantity="essential.snackbox_essentials_quantity"
                                                :id="essential.id"
                                                :column="'snackbox_essentials'">
                                    </preference>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_essentials"></preference>
                    </div> -->
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <div class="allergies">
                    <h4> Allergies </h4>
                    <!-- Existing approach of existing allergies associated with the company -->
                    <!-- Commenting oout for now to test the new approach without duplicate info - will need to update the destroy function for either approach. -->
                    <!-- <div v-if="allergies.length > 0" >
                        <div v-for="allergy in this.allergies[0].allergy">
                            <allergy v-if="allergy != null"
                                        :name="allergy"
                                        :column="'allergies'"
                                        @refresh-data="refreshData(this.allergies[0].company_details_id)">
                            </allergy>
                        </div>
                    </div>
                    <div v-else>
                        <p> No allergies selected </p>
                    </div> -->

                    <!-- start of alternative approach of existing allergies for review -->
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
                        <b-form-group v-if="this.allergies.length > 0">
                            <b-form-checkbox    inline
                                                v-for="allergen in this.allergies[0].allergy"
                                                v-model="selected_allergens"
                                                :key="allergen"
                                                :value="allergen">
                                                <b> {{ allergen }} </b>
                            </b-form-checkbox>
                        </b-form-group>
                        <b-form-group v-else>
                            <p> No allergens selected </p>
                        </b-form-group>
                    </div>
                    <!-- end of alternative approach for review -->
                </div>
            </b-col>
            <b-col>
                <div class="dietary_requirements">
                    <h4> Dietary Requirements </h4>
                    <div v-if="allergies.dietary_requirements">
                        <div v-for="dietary_requirement in this.allergies[0].dietary_requirements">
                            <!-- <p > {{ like.snackbox_likes }} </p> -->
                            <allergy v-if="dietary_requirement != null"
                                        :name="dietary_requirement"
                                        :column="'dietary_requirements'"
                                        @refresh-data="refreshData(this.allergies[0].company_details_id)">
                            </allergy>
                        </div>
                    </div>
                    <div v-else>
                        <p> No dietary requirements selected </p>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="additional_notes">
                    <h4> Additional Notes </h4>
                    <div  v-for="info in this.additional_info">
                        <!-- <p > {{ like.snackbox_likes }} </p> -->
                        <additional-info v-if="info != null"
                                    :name="info.additional_info"
                                    :key="info.id"
                                    :id="info.id"
                                    :column="'additional_info'"
                                    @refresh-data="refreshData(info.company_details_id)">
                        </additional-info>
                    </div>
                    <!-- <div v-if="$store.state.setPreferences.additional_notes != 'None'">
                        <div v-for="note in $store.state.setPreferences.additional_notes">
                            <b-row>
                                <b-col>
                                    <additional-info    :name="note.additional_info"
                                                        :id="note.id"
                                                        :column="'additional_info'">
                                    </additional-info>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <additional-info :name="$store.state.setPreferences.additional_notes"></additional-info>
                    </div> -->
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <p hidden><b> {{ currently_selected_allergens }} </b></p>
                <p hidden><b> {{ currently_selected_dietary_requirements }} </b></p>

            </b-col>
        </b-row>
    </div>
</template>

<style>
    #preferences {

    }
</style>

<script>
export default {
    props:['preferences', 'likes', 'dislikes', 'essentials', 'allergies', 'additional_info'],
    data() {
        return {
            editing: false,
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
        currently_selected_allergens: function () {
            if (this.allergies.length > 0) {
                return this.selected_allergens = this.allergies[0].allergy
            }

        },
        currently_selected_dietary_requirements: function (dietary_requirements) {
            if (this.allergies.length > 0) {
                return this.selected_dietary_requirements = this.allergies[0].dietary_requirements
            }

        },
    },
    methods: {
        refreshData(company_details_id){
            this.$emit('refresh-data', {company_details_id: company_details_id});
        }
        // Pretty sure I'm not using this anymore so commenting it out to see if anything breaks.

        // removePreference(id){
        //      console.log(id);
        //     // console.log(preference);
        //     axios.post('api/preferences/remove', {
        //         // id: key,
        //         // preference: preference,
        //         // column: column,
        //     }).then(response => {
        //         console.log(response);
        //         // location.reload(true);
        //     }).catch(error => console.log(error));
        // }
    },
    mounted() {
        console.log(this.additional_info);
        console.log(this.preferences);
        console.log('allergies below')
        console.log(this.allergies);
    }

}
</script>
