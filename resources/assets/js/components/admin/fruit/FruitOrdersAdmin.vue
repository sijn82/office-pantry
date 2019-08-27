<template>

    <div id="fruitboxes">
        <h3> Fruit Boxes </h3>

        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <!-- <transition name="show-add-new"> -->
                <add-new-fruitbox :company="this.company" @refresh-data="refreshData($event)"></add-new-fruitbox>
            <!-- </transition> -->
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New Fruitbox </b-button>
        </div>

        <div v-if="this.fruitboxes.length"> {{ fruitbox.company_name }}

            <fruitbox class="fruitbox" v-for="fruitbox in this.fruitboxes" :key="fruitbox.id" :fruitbox="fruitbox" @refresh-data="refreshData($event)"></fruitbox>

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
    #fruitboxes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    #edit-save-buttons {
        padding-bottom: 10px;
        h4 {
            display: inline;
        }
    }
    .add-new-close {
        margin-bottom: 20px;
    }
    .fruitbox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
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
        // padding-right: 40px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .fruitbox:after {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-top: 20px; /* This creates some space between the element and the border. */
        border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    // .show-add-new-enter-active, .show-add-new-leave-active {
    //     transition: opacity .5s;
    // }
    // .show-add-new-enter, .show-add-new-leave-to /* .fade-leave-active below version 2.1.8 */ {
    //     opacity: 0;
    // }

</style>

<script>
export default {
    props: ['fruitboxes', 'company'],
    data () {
        return {
            fruitbox: {
                fruit: {
                    id: '',
                    is_active: '',
                    fruit_partner_id: '',
                    name: '',
                    company_id: '',
                    route_id: '',
                    delivery_day: '',
                    frequency: '',
                    week_in_month: '',
                    next_delivery: '',
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
                    oranges: '',
                    cucumbers: '',
                    mint: '',
                    organic_lemons: '',
                    kiwis: '',
                    grapefruits: '',
                    avocados: '',
                    root_ginger: '',
                    tbc: '',
                },
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
        // updated_fruit_partner: function () {
        //      fruit_partner_id_to_name_converter(fruitbox.fruit_partner_id);
        // }
    },
    methods: {
        refreshData($event) {
            this.$emit('refresh-data', $event);
        },
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
                company_id: fruitbox.company_id,
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
        // this.$store.commit('getFruitPartners');
        // console.log(this.fruitboxes);
    }
}

</script>
