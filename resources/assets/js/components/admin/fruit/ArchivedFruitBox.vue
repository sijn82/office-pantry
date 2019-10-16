<template lang="html">
    <div>
        <!-- <ul> -->
            <div id="edit-save-buttons">
                <h4> {{ archived_fruitbox.name }} </h4>
                <!-- <p class="date-delivered"> {{ archived_fruitbox.next_delivery }} </p> -->
                <h5> {{ archived_fruitbox.next_delivery }} </h5>
                <p> {{ archived_fruitbox.delivery_day }} - {{ archived_fruitbox.is_active }} </p>
                <b-button variant="primary" @click="showDetails()"> Details </b-button>
                <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                <b-button v-if="editing" class="btn btn-success" @click="updateArchivedFruitOrder(archived_fruitbox)"> Save </b-button>
                <b-button variant="danger" @click="deleteArchivedBox(archived_fruitbox)"> Delete </b-button>
            </div>
            <div id="fruit-details" v-show="details">
                <b-row id="top-details" :class="archived_fruitbox.is_active" sm="12" class="b-row-padding">
                    <b-col class="col-sm-4">
                        <label><b> Archived Fruitbox ID </b></label>
                        <div>
                            <p> {{ archived_fruitbox.id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <!-- <label><b> Company ID </b></label>
                        <div>
                            <p> {{ archived_fruitbox.company_details_id }} </p>
                        </div> -->
                        <label><b> Fruitbox Type </b></label>
                        <div>
                            <p> {{ archived_fruitbox.type }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Fruit Partner </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_fruitbox.fruit_partner_id">
                                <option v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                            </b-form-select>
                            <p> Selected: {{ archived_fruitbox.fruit_partner_id }} </p>
                        </div>
                        <div v-else>
                            <p>  {{ archived_fruitbox.fruit_partner_name }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Fruitbox Name </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.name"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.name }} </p>
                        </div>
                    </b-col>

                    <b-col>
                        <label><b> Archived Fruitbox Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_fruitbox.is_active">

                                <option value="Active"> Active </option>
                                <option value="Inactive"> Inactive </option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.is_active }} </p>
                        </div>
                    </b-col>

                    <b-col>
                        <label><b> Delivery Day </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_fruitbox.delivery_day">
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.delivery_day }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col>
                        <label><b> Frequency </b></label>
                        <div v-if="editing">
                            <b-form-select :options="frequency" v-model="archived_fruitbox.frequency" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.frequency }} </p>
                        </div>
                    </b-col>
                    <b-col v-if="archived_fruitbox.frequency == 'Monthly'">
                        <label><b> Week In Month </b></label>
                        <div v-if="editing">
                            <b-form-select :options="week_in_month" v-model="archived_fruitbox.week_in_month" type="number">
                                <template slot="first">
                                        <option :value="null" disabled>-- Please select an option --</option>
                                </template>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.week_in_month }} </p>
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Week Delivered </b></label> <!-- Changed title to reflect the value it really represents in this scenario -->
                        <div v-if="editing">
                            <b-form-input type="date" readonly v-model="archived_fruitbox.next_delivery"></b-form-input> <!-- I could change the db name etc... -->
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.next_delivery }} </p> <!-- ...but let's add it to the todo list for now. -->
                        </div>
                    </b-col>
                    <b-col>
                        <label><b> Fruitbox Total </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.fruitbox_total" type="number" min="0"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.fruitbox_total }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                         <label><b> Deliciously Red Apples </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="archived_fruitbox.deliciously_red_apples" type="number" min="0" max="100"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ archived_fruitbox.deliciously_red_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                         <label><b> Pink Lady Apples </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="archived_fruitbox.pink_lady_apples" type="number" min="0" max="100"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ archived_fruitbox.pink_lady_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Red Apples </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.red_apples" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.red_apples }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Green Apples </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.green_apples" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.green_apples }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Satsumas </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.satsumas" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.satsumas }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Pears </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.pears" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.pears }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Bananas </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.bananas" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.bananas }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Nectarines </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.nectarines" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.nectarines }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Limes </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.limes" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.limes }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Lemons </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.lemons" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.lemons }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Grapes </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.grapes" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.grapes }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Seasonal Berries </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.seasonal_berries" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.seasonal_berries }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Oranges </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.oranges" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.oranges }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Cucumbers </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.cucumbers" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.cucumbers }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Mint </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.mint" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.mint }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Organic Lemons </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.organic_lemons" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.organic_lemons }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Kiwis </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.kiwis" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.kiwis }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Grapefruits </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.grapefruits" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.grapefruits }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="archived_fruitbox.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Avocados </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.avocados" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.avocados }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Root Ginger </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.root_ginger" type="number" min="0" max="100"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.root_ginger }} </p>
                        </div>
                    </b-col>

                    <b-col class="col-sm-4">
                        <label><b> Tailoring Fee </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="archived_fruitbox.tailoring_fee" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.tailoring_fee }} </p>
                        </div>
                    </b-col>
                </b-row>
                <b-row id="bottom-details" :class="archived_fruitbox.is_active">
                    <b-col class="col-sm-4">
                        <label><b> Discount Multiple </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="archived_fruitbox.discount_multiple" :options="discountable_options"></b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ archived_fruitbox.discount_multiple }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Last Invoice Date </b></label>
                        <p> {{ archived_fruitbox.invoiced_at }} </p>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Last Updated Date </b></label>
                        <p> {{ archived_fruitbox.updated_at }} </p>
                    </b-col>
                </b-row>
            </div>
        <!-- </ul> -->
    </div>
</template>

<style lang="scss" scoped>
    .date-delivered {
        margin-bottom: 0px;
        font-size: 20px;
    }

</style>

<script>

export default {
    props: ['archived_fruitbox'],
    data() {
        return {
            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Forth', 'Last'],
            discountable_options: ['Yes', 'No'],
            editing: false,
            details: false,
            addnew: false,
        }
    },

    methods: {
        // addNew() {
        //     if (this.addnew == false) {
        //         this.addnew = true;
        //     } else {
        //         this.addnew = false;
        //     }
        // },
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
        deleteArchivedBox(archived_fruitbox) {
            axios.put('api/boxes/archived-fruitbox/destroy/' + archived_fruitbox.id, {
                id: archived_fruitbox.id,
            }).then (response => {
                // location.reload(true); // If I stored the current products in the store rather than like this, I wouldn't need to reload the page to update the view.
                console.log(response);
            }).catch(error => console.log(error));
        },
        updateArchivedFruitOrder(archived_fruitbox) {
            this.editing = false;
            console.log(archived_fruitbox);
            console.log(archived_fruitbox.id);
            axios.put('api/boxes/archived-fruitbox/update/' + archived_fruitbox.id, {
                id: archived_fruitbox.id,
                is_active: archived_fruitbox.is_active,
                fruit_partner_id: archived_fruitbox.fruit_partner_id,
                name: archived_fruitbox.name,
                company_details_id: archived_fruitbox.company_details_id,
                route_id: archived_fruitbox.route_id,
                delivery_day: archived_fruitbox.delivery_day,
                frequency: archived_fruitbox.frequency,
                week_in_month: archived_fruitbox.week_in_month,
                next_delivery: archived_fruitbox.next_delivery,
                fruitbox_total: archived_fruitbox.fruitbox_total,
                deliciously_red_apples: archived_fruitbox.deliciously_red_apples,
                pink_lady_apples: archived_fruitbox.pink_lady_apples,
                red_apples: archived_fruitbox.red_apples,
                green_apples: archived_fruitbox.green_apples,
                satsumas: archived_fruitbox.satsumas,
                pears: archived_fruitbox.pears,
                bananas: archived_fruitbox.bananas,
                nectarines: archived_fruitbox.nectarines,
                limes: archived_fruitbox.limes,
                lemons: archived_fruitbox.lemons,
                grapes: archived_fruitbox.grapes,
                seasonal_berries: archived_fruitbox.seasonal_berries,
                oranges: archived_fruitbox.oranges,
                cucumbers: archived_fruitbox.cucumbers,
                mint: archived_fruitbox.mint,
                organic_lemons: archived_fruitbox.organic_lemons,
                kiwis: archived_fruitbox.kiwis,
                grapefruits: archived_fruitbox.grapefruits,
                avocados: archived_fruitbox.avocados,
                root_ginger: archived_fruitbox.root_ginger,
                tailoring_fee: archived_fruitbox.tailoring_fee,
                discount_multiple: archived_fruitbox.discount_multiple,
                // invoiced_at: archived_fruitbox.invoiced_at, // I don't feel these will need editing but we'll see...
                // updated_at: archived_fruitbox.updated_at,
            }).then (response => {
                console.log(response);
                alert('Updated Archived Fruitbox Sucessfully');
            }).catch(error => { console.log(error); alert(error); });
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
            return this.archived_fruitbox.fruit_partner_name = name;
        }
    },

    mounted() {
        // this.$store.commit('getFruitPartners');
    }
}

</script>
