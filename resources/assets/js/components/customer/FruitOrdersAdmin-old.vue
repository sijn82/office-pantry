<template>

    <div id="fruitboxes">
        <h3> Fruit Boxes </h3>
    
        <div v-if="this.fruitboxes.length"> {{ fruitbox.company_name }}
                
            <ul class="fruitbox" v-for="fruitbox in this.fruitboxes">
                <div id="edit-save-buttons">
                    <h4> {{ fruitbox.name }}</h4>
                    <b-button variant="primary" @click="showDetails()"> Details </b-button>
                    <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                    <b-button v-if="editing" class="btn btn-success" @click="updateFruitOrder(fruitbox)"> Save </b-button>
                </div>
                <div id="fruit-details" v-show="details">
                    <b-row id="top-details" :class="fruitbox.is_active" sm="12" class="b-row-padding">
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruitbox.id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruitbox.id }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox Company ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruitbox.company_id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruitbox.company_id }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox Route ID </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruitbox.route_id"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruitbox.route_id }} </p>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row :class="fruitbox.is_active" class="b-row-padding" sm="12">
                        <b-col class="col-sm-4">
                            <label><b> Fruitbox Name </b></label>
                            <div v-if="editing">
                                <b-form-input v-model="fruitbox.name"></b-form-input>
                            </div>
                            <div v-else>
                                <p> {{ fruitbox.name }} </p>
                            </div>
                        </b-col>
                        <b-col class="col-sm-4">
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

                        <b-col class="col-sm-4">
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

                    <b-row id="bottom-details" :class="fruitbox.is_active" class="b-row-padding" sm="12">
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
                </div>
            </ul>
        </div>
        <div v-else>
            <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
        </div>            
    </div>
</template>

<style lang="scss">

    #fruitboxes {
        margin-top: 20px;
        text-align: center;
        
    }
    #edit-save-buttons {
        padding-bottom: 10px;
    }
    #fruit-details {
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

    .Active {
        background-color: rgba(116, 244, 66, 0.5);
    }
    .Inactive {
        background-color: rgba(201, 16, 16, 0.5);
    }
    .fruitbox {
        padding-right: 40px;
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
            details: false,
        }
    },
    computed: {

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
        updateFruitOrder(fruitbox) {
            this.editing = false;
            console.log(fruitbox);
            console.log(fruitbox.id);
            axios.put('api/fruitbox/' + fruitbox.id, {
                id: fruitbox.id,
                is_active: fruitbox.is_active,
                name: fruitbox.name,
                company_id: fruitbox.company_id,
                route_id: fruitbox.route_id,
                delivery_day: fruitbox.delivery_day,
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
