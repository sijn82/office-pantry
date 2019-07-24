<template lang="html">
    <div>
        <!-- <ul> -->
            <div id="edit-save-buttons">
                <h4> {{ archived_milkbox.delivery_day }}</h4> <p> {{ archived_milkbox.is_active }} </p>
                <b-button variant="primary" @click="showDetails()"> Details </b-button>
                <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                <b-button v-if="editing" class="btn btn-success" @click="updateMilkOrder(archived_milkbox)"> Save </b-button>
            </div>
            <div id="milk-details" v-show="details">
                <b-row id="top-details" :class="archived_milkbox.is_active" sm="12" class="b-row-padding">
                    <b-col>
                        <label><b> Milkbox ID </b></label>
                        <div>
                            <p> {{ archived_milkbox.id }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Company ID </b></label>
                        <div>
                            <p> {{ archived_milkbox.company_details_id }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Milkbox Fruit Partner </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_milkbox.fruit_partner_id"> 
                                <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                            </b-form-select>
                            <p> Selected: {{ archived_milkbox.fruit_partner_id }} </p>
                        </div>
                        <div v-else>
                            <p>  {{ archived_milkbox.fruit_partner_name }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Next Delivery </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.next_delivery" type="date"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.next_delivery }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Milkbox Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_milkbox.is_active">

                                <option value="Active"> Active </option>
                                <option value="Inactive"> Inactive </option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.is_active }} </p>
                        </div>
                    </b-col>

                    <b-col>
                        <label><b> Delivery Day </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_milkbox.delivery_day">
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.delivery_day }} </p>
                        </div>
                    </b-col>
                    
                    <b-col>
                        <label><b> Frequency </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_milkbox.frequency" :options="frequency" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.frequency }} </p>
                        </div>
                    </b-col>
                    
                    <b-col v-if="archived_milkbox.frequency == 'Monthly'">
                        <label><b> Week In Month </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_milkbox.week_in_month" :options="week_in_month" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.week_in_month }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Regular 2L Milk Options -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <h6> Regular 2l Milk Options </h6>
                    </b-col>
                </b-row>
                
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> 2L Semi Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.semi_skimmed_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.semi_skimmed_2l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 2L Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.skimmed_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.skimmed_2l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 2L Whole </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.whole_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.whole_2l }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Regular 1L Milk Options -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <h6> Regular 1l Milk Options </h6>
                    </b-col>
                </b-row>
                
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> 1L Semi Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.semi_skimmed_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.semi_skimmed_1l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 1L Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.skimmed_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.skimmed_1l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 1L Whole </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.whole_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.whole_1l }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Organic 2L Milk Options -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <h6> Organic 2l Milk Options </h6>
                    </b-col>
                </b-row>
                
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Organic 2L Semi Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_semi_skimmed_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_semi_skimmed_2l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Organic 2L Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_skimmed_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_skimmed_2l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Organic 2L Whole </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_whole_2l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_whole_2l }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Organic 1L Milk Options -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <h6> Organic 1l Milk Options </h6>
                    </b-col>
                </b-row>
                
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Organic 1L Semi Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_semi_skimmed_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_semi_skimmed_1l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Organic 1L Skimmed </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_skimmed_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_skimmed_1l }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Organic 1L Whole </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.organic_whole_1l" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.organic_whole_1l }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Alternative Milk Options -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <h6> Alternative 1l Milk Options </h6>
                    </b-col>
                </b-row>
                
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                         <label><b> 1L Milk Alt Coconut </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="archived_milkbox.milk_1l_alt_coconut" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ archived_milkbox.milk_1l_alt_coconut }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                         <label><b> 1L Unsweetened Almond </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="archived_milkbox.milk_1l_alt_unsweetened_almond" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ archived_milkbox.milk_1l_alt_unsweetened_almond }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 1L Almond </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_almond" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_almond }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Alternative Milk Options (Pt2) -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> 1L Unsweetened Soya </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_unsweetened_soya" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_unsweetened_soya }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> 1L Soya </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_soya" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_soya }} </p>
                        </div>
                    </b-col>
                    
                    <b-col class="col-sm-4">
                        <label><b> 1L Oat </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_oat" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_oat }} </p>
                        </div>
                    </b-col>
                </b-row>
                <!-- Alternative Milk Options (Pt3) -->
                <b-row :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    
                    <b-col class="col-sm-4">
                        <label><b> 1L Rice </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_rice" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_rice }} </p>
                        </div>
                    </b-col>
                    
                    <b-col class="col-sm-4">
                        <label><b> 1L Cashew </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_cashew" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_cashew }} </p>
                        </div>
                    </b-col>
                    
                    <b-col class="col-sm-4">
                        <label><b> 1L Lactose Free Semi </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_milkbox.milk_1l_alt_lactose_free_semi" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_milkbox.milk_1l_alt_lactose_free_semi }} </p>
                        </div>
                    </b-col>
                </b-row>
                <b-row id="bottom-details" :class="archived_milkbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Invoiced At </b></label>
                        <p> {{ archived_milkbox.invoiced_at }} </p> 
                    </b-col>
                    <b-col>
                        <label><b> Updated At </b></label>
                        <p> {{ archived_milkbox.updated_at }} </p> 
                    </b-col>
                </b-row>
            </div>
        <!-- </ul> -->
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>

export default {
    props: ['archived_milkbox'],
    data() {
        return {
            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Forth', 'Last'],
            editing: false,
            details: false,
            addnew: false,
            skip_archive: 'false',
        }
    },
    methods: {

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
        updateMilkOrder(archived_milkbox) {
            this.editing = false;
            console.log(archived_milkbox);
            console.log(archived_milkbox.id);
            axios.put('api/boxes/archived_milkbox/' + archived_milkbox.id, {
                id: archived_milkbox.id,
                is_active: archived_milkbox.is_active,
                fruit_partner_id: archived_milkbox.fruit_partner_id,
                company_details_id: archived_milkbox.company_details_id,
                route_id: archived_milkbox.route_id,
                next_delivery: archived_milkbox.next_delivery,
                delivery_day: archived_milkbox.delivery_day,
                frequency: archived_milkbox.frequency,
                week_in_month: archived_milkbox.week_in_month,
                semi_skimmed_2l: archived_milkbox.semi_skimmed_2l,
                skimmed_2l: archived_milkbox.skimmed_2l,
                whole_2l: archived_milkbox.whole_2l,
                semi_skimmed_1l: archived_milkbox.semi_skimmed_1l,
                skimmed_1l: archived_milkbox.skimmed_1l,
                whole_1l: archived_milkbox.whole_1l,
                organic_semi_skimmed_2l: archived_milkbox.organic_semi_skimmed_2l,
                organic_skimmed_2l: archived_milkbox.organic_skimmed_2l,
                organic_whole_2l: archived_milkbox.organic_whole_2l,
                organic_semi_skimmed_1l: archived_milkbox.organic_semi_skimmed_1l,
                organic_skimmed_1l: archived_milkbox.organic_skimmed_1l,
                organic_whole_1l: archived_milkbox.organic_whole_1l,
                milk_1l_alt_coconut: archived_milkbox.milk_1l_alt_coconut,
                milk_1l_alt_unsweetened_almond: archived_milkbox.milk_1l_alt_unsweetened_almond,
                milk_1l_alt_almond: archived_milkbox.milk_1l_alt_almond,
                milk_1l_alt_unsweetened_soya: archived_milkbox.milk_1l_alt_unsweetened_soya,
                milk_1l_alt_soya: archived_milkbox.milk_1l_alt_soya,
                milk_1l_alt_oat: archived_milkbox.milk_1l_alt_oat,
                milk_1l_alt_rice: archived_milkbox.milk_1l_alt_rice,
                milk_1l_alt_cashew: archived_milkbox.milk_1l_alt_cashew,
                milk_1l_alt_lactose_free_semi: archived_milkbox.milk_1l_alt_lactose_free_semi,
                invoiced_at: archived_milkbox.invoiced_at,
                updated_at: archived_milkbox.updated_at,
                skip_archive: this.skip_archive,
                
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        },
        changeName(name) {
            return this.fruitbox.fruit_partner_name = name;
        }
    },
    mounted() {
        this.$store.commit('getFruitPartners');
    }
}

</script>