<template>

    <div id="milkboxes">
        <h3> Milk Boxes </h3>

        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-milkbox :company="this.company"></add-new-milkbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New Milkbox </b-button>
        </div>

        <div v-if="this.milkboxes.length"> {{ milkbox.company_name }}

            <milkbox v-for="milkbox in this.milkboxes" :milkbox="milkbox" :key="milkbox.id"></milkbox>

            <!-- <ul class="milkbox" v-for="milkbox in this.milkboxes">
                <div id="edit-save-buttons">
                    <h4> {{ milkbox.delivery_day }}</h4> <p> {{ milkbox.is_active }} </p>
                    <b-button variant="primary" @click="showDetails()"> Details </b-button>
                    <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                    <b-button v-if="editing" class="btn btn-success" @click="updateMilkOrder(milkbox)"> Save </b-button>
                </div>
                <div id="milk-details" v-show="details">
                    <b-row id="top-details" :class="milkbox.is_active" sm="12" class="b-row-padding">
                        <b-col>
                            <label><b> Milkbox ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.id }} </p>
                            </div>
                        </b-col>
                        <b-col>
                            <label><b> Company ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.company_id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.company_id }} </p>
                            </div>
                        </b-col>
                        <b-col>
                            <label><b> Milkbox Fruit Partner </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="milkbox.fruit_partner_id">
                                    <template slot="first">
                                        <option :value="milkbox.fruit_partner_id" disabled> {{ milkbox.fruit_partner_name }} </option>
                                    </template>
                                    <option @click.once="changeName(fruit_partner.name)" v-for="fruit_partner in $store.state.fruit_partners_list" :value="fruit_partner.id"> {{ fruit_partner.name }} </option>
                                </b-form-select>
                                <p> Selected: {{ milkbox.fruit_partner_id }} </p>
                            </div>
                            <div v-else>
                                <p>  {{ milkbox.fruit_partner_name }} </p>
                            </div>
                        </b-col>
                        <b-col>
                            <label><b> Next Delivery </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.next_delivery_week_start" type="date"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.next_delivery_week_start }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row :class="milkbox.is_active" class="b-row-padding" sm="12">
                        <b-col>
                            <label><b> Milkbox Status </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="milkbox.is_active">

                                    <option value="Active"> Active </option>
                                    <option value="Inactive"> Inactive </option>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.is_active }} </p>
                            </div>
                        </b-col>

                        <b-col>
                            <label><b> Delivery Day </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="milkbox.delivery_day">
                                    <option>Monday</option>
                                    <option>Tuesday</option>
                                    <option>Wednesday</option>
                                    <option>Thursday</option>
                                    <option>Friday</option>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.delivery_day }} </p>
                            </div>
                        </b-col>

                        <b-col>
                            <label><b> Frequency </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="milkbox.frequency" :options="frequency" type="number">
                                    <template slot="first">
                                            <option :value="null" disabled>-- Please select an option --</option>
                                    </template>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.frequency }} </p>
                            </div>
                        </b-col>

                        <b-col v-if="milkbox.frequency == 'Monthly'">
                            <label><b> Week In Month </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="milkbox.week_in_month" :options="week_in_month" type="number">
                                    <template slot="first">
                                            <option :value="null" disabled>-- Please select an option --</option>
                                    </template>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.week_in_month }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row :class="milkbox.is_active" class="b-row-padding" sm="12">
                        <b-col class="col-sm-4">
                             <label><b> 1L Milk Alt Coconut </b></label>
                             <div v-if="editing">
                                 <b-form-input v-model="milkbox.milk_1l_alt_coconut" type="number"></b-form-input>
                             </div>
                             <div v-else>
                                 <p> {{ milkbox.milk_1l_alt_coconut }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                             <label><b> 1L Unsweetened Almond </b></label>
                             <div v-if="editing">
                                 <b-form-input v-model="milkbox.milk_1l_alt_unsweetened_almond" type="number"></b-form-input>
                             </div>
                             <div v-else>
                                 <p> {{ milkbox.milk_1l_alt_unsweetened_almond }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 1L Almond </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.milk_1l_alt_almond" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.milk_1l_alt_almond }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row :class="milkbox.is_active" class="b-row-padding" sm="12">
                        <b-col class="col-sm-4">
                            <label><b> 1L Unsweetened Soya </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.milk_1l_alt_unsweetened_soya" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.milk_1l_alt_unsweetened_soya }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 1L Soya </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.milk_1l_alt_soya" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.milk_1l_alt_soya }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 1L Lactose Free Semi </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.milk_1l_alt_lactose_free_semi" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.milk_1l_alt_lactose_free_semi }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row :class="milkbox.is_active" class="b-row-padding" sm="12">
                        <b-col class="col-sm-4">
                            <label><b> 2L Semi Skimmed </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.semi_skimmed_2l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.semi_skimmed_2l }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 2L Skimmed </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.skimmed_2l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.skimmed_2l }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 2L Whole </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.whole_2l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.whole_2l }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row id="bottom-details" :class="milkbox.is_active" class="b-row-padding" sm="12">
                        <b-col class="col-sm-4">
                            <label><b> 1L Semi Skimmed </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.semi_skimmed_1l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.semi_skimmed_1l }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 1L Skimmed </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.skimmed_1l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.skimmed_1l }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> 1L Whole </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="milkbox.whole_1l" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ milkbox.whole_1l }} </p>
                            </div>
                        </b-col>
                    </b-row>
                </div>
            </ul> -->
        </div>
        <div v-else>
            <ul class="milkbox"><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
        </div>
    </div>
</template>

<style lang="scss">

    #milkboxes {
        margin-top: 20px;
        text-align: center;

    }
    #edit-save-buttons {
        padding-bottom: 10px;
    }
    #milk-details {
        .b-row-padding {
            padding-bottom: 5px;
        }
    }
    #top-details {
        padding-top: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    #bottom-details {
        padding-bottom: 30px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .add-new-close {
        margin-bottom: 20px;
    }
    .Active {
        background-color: rgba(116, 244, 66, 0.5);
    }
    .Inactive {
        background-color: rgba(201, 16, 16, 0.5);
    }
    .milkbox {
        // padding-right: 40px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .milkbox:after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-top: 20px; /* This creates some space between the element and the border. */
        border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }

</style>

<script>
export default {
    props: ['milkboxes', 'company'],
    data () {
        return {
            milkbox: {
                id: '',
                is_active: '',
                fruit_partner_id: '',
                company_id: '',
                route_id: '',
                next_delivery_week_start: '',
                delivery_day: '',
                frequency: '',
                week_in_month: '',
                milk_1l_alt_coconut: '',
                milk_1l_alt_unsweetened_almond: '',
                milk_1l_alt_almond: '',
                milk_1l_alt_unsweetened_soya: '',
                milk_1l_alt_soya: '',
                milk_1l_alt_lactose_free_semi: '',
                semi_skimmed_2l: '',
                skimmed_2l: '',
                whole_2l: '',
                semi_skimmed_1l: '',
                skimmed_1l: '',
                whole_1l: '',
                // pint_semi_skimmed: '',
                // pint_whole: '',
                // organic_semi_skimmed_1l: '',
                // organic_skimmed_1l: '',
            },
            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Forth', 'Last'],
            editing: false,
            details: false,
            addnew: false,
        }
    },
    computed: {

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
        updateMilkOrder(milkbox) {
            this.editing = false;
            console.log(milkbox);
            console.log(milkbox.id);
            axios.put('api/milkbox/' + milkbox.id, {
                id: milkbox.id,
                is_active: milkbox.is_active,
                fruit_partner_id: milkbox.fruit_partner_id,
                company_id: milkbox.company_id,
                route_id: milkbox.route_id,
                next_delivery_week_start: milkbox.next_delivery_week_start,
                delivery_day: milkbox.delivery_day,
                frequency: milkbox.frequency,
                week_in_month: milkbox.week_in_month,
                milk_1l_alt_coconut: milkbox.milk_1l_alt_coconut,
                milk_1l_alt_unsweetened_almond: milkbox.milk_1l_alt_unsweetened_almond,
                milk_1l_alt_almond: milkbox.milk_1l_alt_almond,
                milk_1l_alt_unsweetened_soya: milkbox.milk_1l_alt_unsweetened_soya,
                milk_1l_alt_soya: milkbox.milk_1l_alt_soya,
                milk_1l_alt_lactose_free_semi: milkbox.milk_1l_alt_lactose_free_semi,
                semi_skimmed_2l: milkbox.semi_skimmed_2l,
                skimmed_2l: milkbox.skimmed_2l,
                whole_2l: milkbox.whole_2l,
                semi_skimmed_1l: milkbox.semi_skimmed_1l,
                skimmed_1l: milkbox.skimmed_1l,
                whole_1l: milkbox.whole_1l,
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
        console.log(this.milkboxes);
    }
}

</script>
