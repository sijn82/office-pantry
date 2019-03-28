<template lang="html">
    <div>
        <!-- <ul> -->
            <div id="edit-save-buttons">
                <h4> {{ fruitbox.name }} </h4>
                <p> {{ fruitbox.delivery_day }} - {{ fruitbox.is_active }} </p>
                <b-button variant="primary" @click="showDetails()"> Details </b-button>
                <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                <b-button v-if="editing" class="btn btn-success" @click="updateFruitOrder(fruitbox)"> Save </b-button>
            </div>
            <div id="fruit-details" v-show="details">
                <b-row id="top-details" :class="fruitbox.is_active" sm="12" class="b-row-padding">
                    <b-col class="col-sm-4">
                        <label><b> Fruitbox ID </b></label>
                        <div>
                            <p> {{ fruitbox.id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Fruitbox Company ID </b></label>
                        <div>
                            <p> {{ fruitbox.company_details_id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Fruitbox Fruit Partner </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="fruitbox.fruit_partner_id"> 
                                <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                            </b-form-select>
                            <p> Selected: {{ fruitbox.fruit_partner_id }} </p>
                        </div>
                        <div v-else>
                            <p>  {{ fruitbox.fruit_partner_name }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Fruitbox Name </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.name"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.name }} </p>
                        </div>
                    </b-col>
                    
                    <b-col>
                        <label><b> Fruitbox Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="fruitbox.is_active">

                                <option value="Active"> Active </option>
                                <option value="Inactive"> Inactive </option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.is_active }} </p>
                        </div>
                    </b-col>

                    <b-col>
                        <label><b> Delivery Day </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="fruitbox.delivery_day">
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.delivery_day }} </p>
                        </div>
                    </b-col>
                </b-row>
                
                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Frequency </b></label>
                        <div v-if="editing">
                            <b-form-select :options="frequency" v-model="fruitbox.frequency" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.frequency }} </p>
                        </div>
                    </b-col>
                    <b-col v-if="fruitbox.frequency == 'Monthly'">
                        <label><b> Week In Month </b></label>
                        <div v-if="editing">
                            <b-form-select :options="week_in_month" v-model="fruitbox.week_in_month" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.week_in_month }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Next Delivery </b></label>
                        <div v-if="editing">
                            <b-form-input type="date" v-model="fruitbox.next_delivery"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.next_delivery }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Fruitbox Total </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.fruitbox_total" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.fruitbox_total }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                         <label><b> Deliciously Red Apples </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="fruitbox.deliciously_red_apples" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ fruitbox.deliciously_red_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                         <label><b> Pink Lady Apples </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="fruitbox.pink_lady_apples" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ fruitbox.pink_lady_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Red Apples </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.red_apples" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.red_apples }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Green Apples </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.green_apples" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.green_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Satsumas </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.satsumas" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.satsumas }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Pears </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.pears" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.pears }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Bananas </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.bananas" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.bananas }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Nectarines </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.nectarines" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.nectarines }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Limes </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.limes" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.limes }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Lemons </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.lemons" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.lemons }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Grapes </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.grapes" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.grapes }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Seasonal Berries </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.seasonal_berries" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.seasonal_berries }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Oranges </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.oranges" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.oranges }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Cucumbers </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.cucumbers" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.cucumbers }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Mint </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.mint" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.mint }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Organic Lemons </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.organic_lemons" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.organic_lemons }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Kiwis </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.kiwis" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.kiwis }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Grapefruits </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.grapefruits" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.grapefruits }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row id="bottom-details" :class="fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Avocados </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.avocados" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.avocados }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Root Ginger </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.root_ginger" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.root_ginger }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> TBC </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="fruitbox.tbc" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ fruitbox.tbc }} </p>
                        </div>
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
    props: ['fruitbox'],
    data() {
        return {
            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Forth', 'Last'],
            editing: false,
            details: false,
            addnew: false,
        }
    },
    
    methods: {
        addNew() {
            if (this.addnew == false) {
                this.addnew = true;
            } else {
                this.addnew = false;
            }
        },
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
        updateFruitOrder(fruitbox) {
            this.editing = false;
            console.log(fruitbox);
            console.log(fruitbox.id);
            axios.put('api/fruitbox/' + fruitbox.id, {
                id: fruitbox.id,
                is_active: fruitbox.is_active,
                fruit_partner_id: fruitbox.fruit_partner_id,
                name: fruitbox.name,
                company_details_id: fruitbox.company_details_id,
                route_id: fruitbox.route_id,
                delivery_day: fruitbox.delivery_day,
                frequency: fruitbox.frequency,
                week_in_month: fruitbox.week_in_month,
                next_delivery: fruitbox.next_delivery,
                fruitbox_total: fruitbox.fruitbox_total,
                deliciously_red_apples: fruitbox.deliciously_red_apples,
                pink_lady_apples: fruitbox.pink_lady_apples,
                red_apples: fruitbox.red_apples,
                green_apples: fruitbox.green_apples,
                satsumas: fruitbox.satsumas,
                pears: fruitbox.pears,
                bananas: fruitbox.bananas,
                nectarines: fruitbox.nectarines,
                limes: fruitbox.limes,
                lemons: fruitbox.lemons,
                grapes: fruitbox.grapes,
                seasonal_berries: fruitbox.seasonal_berries,
                oranges: fruitbox.oranges,
                cucumbers: fruitbox.cucumbers,
                mint: fruitbox.mint,
                organic_lemons: fruitbox.organic_lemons,
                kiwis: fruitbox.kiwis,
                grapefruits: fruitbox.grapefruits,
                avocados: fruitbox.avocados,
                root_ginger: fruitbox.root_ginger,
                tbc: fruitbox.tbc,
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        },
        fruit_partner_id_to_name_converter(id) {
            console.log(id);
            axios.get('/api/fruit_partners/' + id)
                        .then( response => {
                            console.log(response);
                            this.fruitpartner = response.data[0];
                            this.fruit_partner_name = this.fruitpartner.name;
                            console.log(this.fruitpartner.name);
                        });
                    
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