<template>

    <div id="fruitboxes">
            <div v-for="fruitbox in this.fruitboxes"> {{ fruitbox.company_name }}
                <!-- <ul>
                    <div v-for="(fruit, key) in fruitbox">
                        <li>{{ key }} - {{ fruit }}</li>
                        <p> {{ fruitbox.name }} </p>
                    </div>
                </ul> -->
                 <ul class="fruitbox" v-for="(fruit, key) in fruitbox" v-if="fruit.id">
                    <div id="edit-save-buttons">
                        <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                        <b-button v-if="editing" class="btn btn-success" @click="updateFruitOrder(fruit)"> Save </b-button>
                    </div>

                    <b-row :class="fruit.is_active" sm="12">
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.id }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox Company ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.company_id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.company_id }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox Route ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.route_id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.route_id }} </p>
                            </div>
                        </b-col>
                    </b-row>

                     <b-row :class="fruit.is_active" sm="12">
                        <b-col class="col-sm-3">
                            <label><b> Fruitbox Name </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.name"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.name }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-3">
                            <label><b> Fruitbox Status </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="fruit.is_active">

                                    <option value="Active"> Active </option>
                                    <option value="Inactive"> Inactive </option>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ fruit.is_active }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-3">
                            <label><b> Delivery Day </b></label>
                            <div v-if="editing">
                                <b-form-select v-model="fruit.delivery_day">
                                    <option>Monday</option>
                                    <option>Tuesday</option>
                                    <option>Wednesday</option>
                                    <option>Thursday</option>
                                    <option>Friday</option>
                                </b-form-select>
                            </div>
                            <div v-else>
                                <p> {{ fruit.delivery_day }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-3">
                             <label><b> No. Of Fruitboxes </b></label>
                             <div v-if="editing">
                                 <b-form-input v-model="fruit.fruitbox_total" type="number"></b-form-input>
                             </div>
                             <div v-else>
                                 <p> {{ fruit.fruitbox_total }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row sm="12">
                        <b-col class="col-sm-4">
                             <label><b> Deliciously Red Apples </b></label>
                             <div v-if="editing">
                                 <b-form-input v-model="fruit.deliciously_red_apples" type="number"></b-form-input>
                             </div>
                             <div v-else>
                                 <p> {{ fruit.deliciously_red_apples }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                             <label><b> Pink Lady Apples </b></label>
                             <div v-if="editing">
                                 <b-form-input v-model="fruit.pink_lady_apples" type="number"></b-form-input>
                             </div>
                             <div v-else>
                                 <p> {{ fruit.pink_lady_apples }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Red Apples </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.red_apples" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.red_apples }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label><b> Green Apples </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.green_apples" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.green_apples }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Satsumas </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.satsumas" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.satsumas }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Pears </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.pears" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.pears }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label><b> Bananas </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.bananas" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.bananas }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Nectarines </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.nectarines" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.nectarines }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Limes </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.limes" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.limes }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label><b> Lemons </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.lemons" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.lemons }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Grapes </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.grapes" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.grapes }} </p>
                            </div>
                        </b-col>

                        <b-col class="col-sm-4">
                            <label><b> Seasonal Berries </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruit.seasonal_berries" type="number"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruit.seasonal_berries }} </p>
                            </div>
                        </b-col>
                    </b-row>
                 </ul><ul v-else-if="0 in fruitbox"><li style="list-style:none;"></li></ul><ul v-else><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
                 <!-- v-else-if="0 in fruitbox" is checking whether the wider collection (of fruitbox orders) contains a key of 0, meaning at least one order.
                        This is to discern a difference between an empty set of orders which have just had the company name injected,
                        and those where the injected company name is just the last item in the loop of orders -->
            </div>
    </div>
</template>

<style lang="scss" scoped>

    #edit-save-buttons {
        padding-bottom: 5px;
    }

    .Active {
        background-color: rgba(116, 244, 66, 0.5);
    }
    .Inactive {
        background-color: rgba(201, 16, 16, 0.5);
    }
    .fruitbox {
        //padding-right: 40px;
        li {
        list-style: none;
        }
    }
    .fruitbox:after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-top: 30px; /* This creates some space between the element and the border. */
        border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }

</style>

<script>
export default {
    props: ['fruitboxes'],
    data () {
        return {
            fruitbox: {
                fruit: {
                    id: '',
                    is_active: '',
                    name: '',
                    company_id: '',
                    route_id: '',
                    delivery_day: '',
                    fruitbox_total: '',
                    deliciously_red_apples: '',
                    pink_lady_apples: '',
                    red_apples: '',
                    green_apples: '',
                    satsumas: '',
                    pears: '',
                    bananas: '',
                    nectarines: '',
                    limes: '',
                    lemons: '',
                    grapes: '',
                    seasonal_berries: '',
                },
            },
            editing: false,
        }
    },
    computed: {

    },
    methods: {
        enableEdit() {
            this.editing = true;
        },
        updateFruitOrder(fruit) {
            this.editing = false;
            console.log(fruit);
            console.log(fruit.id);
            axios.put('api/fruitbox/' + fruit.id, {
                id: fruit.id,
                is_active: fruit.is_active,
                name: fruit.name,
                company_id: fruit.company_id,
                route_id: fruit.route_id,
                delivery_day: fruit.delivery_day,
                fruitbox_total: fruit.fruitbox_total,
                deliciously_red_apples: fruit.deliciously_red_apples,
                pink_lady_apples: fruit.pink_lady_apples,
                red_apples: fruit.red_apples,
                green_apples: fruit.green_apples,
                satsumas: fruit.satsumas,
                pears: fruit.pears,
                bananas: fruit.bananas,
                nectarines: fruit.nectarines,
                limes: fruit.limes,
                lemons: fruit.lemons,
                grapes: fruit.grapes,
                seasonal_berries: fruit.seasonal_berries,
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        }
    },
    mounted() {
        console.log(this.fruitboxes);
    }
}

</script>
