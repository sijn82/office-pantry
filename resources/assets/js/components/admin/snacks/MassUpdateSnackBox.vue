<template lang="html">
    <div class="mass-update-snackbox">
        <h1> Mass Update Snackbox (By Type) </h1>
        <b-row>
            <b-col></b-col>
            <b-col>
                <label class="select-type-label"> Select Type </label>
                <b-form-group description="Select the 'Type', from existing list of options to mass update.">
                    <b-form-select v-model="type" :options="this.$store.state.types_list" size="sm" required>
                        <template slot="first">
                            <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-form-group>
            </b-col>
            <b-col></b-col>
        </b-row>
        <div class="order-selections">
            <b-row><b-col><h4> Product Name </h4></b-col><b-col><h4> Quantity </h4></b-col><b-col><h4> Price </h4></b-col><b-col>  </b-col></b-row>
            <div v-for="snack in $store.state.snackbox ">
                <b-row>
                    <b-col>
                        <p> {{ snack.name }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ snack.quantity }} </p>
                    </b-col>
                    <b-col>
                        <p> {{ snack.unit_price }} </p>
                    </b-col>
                    <b-col>
                        <b-button size="sm" variant="danger" @click="removeProduct(snack.id)"> Remove </b-button>
                    </b-col>
                </b-row>
            </div>
            <b-row>
                <b-col>  </b-col>
                <b-col>  </b-col>
                <b-col><p> Total: £{{ total }} </p></b-col>
                <b-col>
                    <b-button size="sm" variant="warning" @click="saveStandardSnackbox()"> Save as New Standard Box </b-button>
                    <b-form-text class="save-info"> This option will <b> update all boxes of the 'type' selected </b> in the input option above.  </b-form-text>
                </b-col>
            </b-row>
        </div>
        <div>
            <products-list v-on:addProduct="addProductToOrder($event)" :createSnackbox="createSnackbox"></products-list>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .mass-update-snackbox {
        margin-top: 40px;
        .select-type-label {
            font-weight: 400;
            font-size: 1.2em;
        }
        .order-selections {
            padding: 10px 40px;
            p {
                font-weight:300;
            }
            .save-info {
                margin: 10px 30px;
            }    
        }

    }
</style>

<script>
export default {
    props:['addProductToSnackbox', 'product', 'quantity'],
    data () {
        return {
            createSnackbox: true,
            type: null,
            order: 'empty',
        }
    },
    computed: {
        total() {
            // Even though this value is immediately replaced and not used, it still needs to be declared.
            let total_cost = 0;

            // This function checks each entry in the current snackbox list and creates a running total of the unit price multiplied by the quantity.
            let sum = function(snackbox, cost, quantity){

                return snackbox.reduce( function(a, b) {
                    
                    return parseFloat(a) + ( parseFloat(b[cost]) * parseFloat(b[quantity]) );
                }, 0);
            };
            // Now we use the function by passing in the snackbox array, and the two (or 3 for wholesale) properties we need to multiply - saving it as the current total cost.
            total_cost = sum(this.$store.state.snackbox, 'unit_price', 'quantity');
            console.log(total_cost);

            // console.log(total_cost);
            return total_cost;
        },
    },
    methods: {
        removeProduct(id) {
            console.log(id);
            this.$store.commit('removeFromSnackbox', id);
        },
        saveStandardSnackbox() {
            this.$store.dispatch('saveStandardSnackboxToDB', this.type );

        },
    },
    mounted () {
        this.$store.commit('getTypes');
    }
}
</script>