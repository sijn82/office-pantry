<template lang="html">
    <div>
        <div class="snackbox-recommended-procedure">
            <h1> Snackbox Process </h1>
            <b-row>
                <b-col>
                    <h4> Step 1: </h4>
                    <p>
                        Go into Company Admin Panel and create any new company snackboxes.  
                        Leave them empty if you wish to mass update them by 'type' (at Step 2) later on.
                    </p>
                </b-col>
                <b-col>
                    <h4> Step 2: </h4>
                    <p>
                        Mass Update Snackboxes (By Type).
                    </p>
                    <h4> Step 3: </h4>
                    <p>
                        Manually update the rest, including any tweaks needed to the mass updated boxes, on a per company basis.
                    </p>
                </b-col>
                <b-col>
                    <h4> Step 4: </h4>
                    <p>
                        Now the boxes are tailored and ready to go, run picklists, 
                        routes and any other stuff you need that needs this data.
                    </p>
                </b-col>
                <b-col>
                    <h4> Step 5: </h4>
                    <p>
                        In an ideal world, run invoicing for all orders this week.  
                        Measures are in place to allow this to be done after Step 6 (fingers crossed).
                    </p>
                </b-col>
                <b-col>
                    <h4> Step 6: </h4>
                    <p>
                        Archive and empty the snackboxes, ready to repeat the process.
                    </p>
                </b-col>
            </b-row>
        </div>
        <div class="archive-and-empty">
            <h1> Archive And Empty Snackboxes (Drinkboxes & Otherboxes) </h1>
            <b-row>
                <b-col>
                    <p>  
                        This button will <b> archive the contents of all 'active' snackboxes. </b>  
                        If the box has an 'invoiced_at' date the box is saved to the archives as 'inactive'.  
                        We only need it for our records.  However, if the box is missing an invoice date it will be saved as 'active'.  
                    </p>
                    <p>
                        This is to ensure it gets pulled into the next invoicing run for that branding theme and can be viewed in the company archives section of the admin panel.
                    </p>
                </b-col>
                <b-col>
                    <p>
                        Next it reuses the box data, i.e snackbox_id, type, frequency etc and builds an empty box. 
                        This box is then ready for when its 'type' is mass updated, 
                        or the box is altered manually through the company admin panel.
                    </p>
                    <p>
                        <b> If you're happy to archive and empty these boxes (and no longer need them for picklists, or routes) - push the button. </b>
                    </p>
                </b-col>
                <b-col class="archive-and-empty-button-group">
                    <b-button size="lg" href="archive-and-empty" variant="danger"> Archive And Empty Snackboxes </b-button>
                    <b-button size="lg" href="/office/drinkboxes/archive-and-empty" variant="danger"> Archive And Empty Drinkboxes </b-button>
                    <b-button size="lg" href="/office/otherboxes/archive-and-empty" variant="danger"> Archive And Empty Otherboxes </b-button>
                </b-col>
            </b-row>
        </div>
        <div class="mass-update-snackbox">
            <h1> Mass Update Snackbox (By Type) </h1>
            <b-row>
                <b-col>
                    <p> 
                        If you've <b> already archived and emptied the existing snackboxes from last week, you're ready to mass update any selected 'type' with products. </b>
                        Should a company dislike an item in the box you create (by listing it in their 'Preferences' under 'Dislike'), 
                        it will attempt to pick a different item (in stock) from their listed likes.  
                        Increasing, or decreasing the quantity to match the replaced product value as closely as possible.
                        This is a work in progress but if the company doesn't have anything listed in their likes section, 
                        it will default to picking something from else from general stock.  
                        In the future we'll also be able to autopopulate boxes with a companies listed 'Essentials', 
                        however it's only useful at the moment to keep a record of what they really want for manual replacement.
                    </p>
                    <p>
                        As a side note, should you populate a 'type' with products and then add a new box of that 'type', 
                        it will not be autopoulated with products (until the mass update for that 'type' is next run - 
                        <b> Do not just re-run it, archive and stock level chaos will ensue! </b>).  
                        Instead you'll have to do it manually in the company admin panel.
                    </p>
                </b-col>
            </b-row>
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
    </div>
</template>

<style lang="scss" scoped>
    .snackbox-recommended-procedure {
        margin: 40px 60px;
        p {
            font-weight: 300;
            font-size: 1.2em;
            margin: 20px 20px;
            b {
            font-size: 1.3em;
            }
        }
    }
    .archive-and-empty {
        margin: 40px 60px;
        p {
            font-weight: 300;
            font-size: 1.2em;
            margin: 20px 80px;
            b {
            font-size: 1.3em;
            }
        }
        .archive-and-empty-button-group {
            display: flex;
            align-items: center;
            justify-content: center;
            a {
                margin: 10px;
            }
        }
    }
    .mass-update-snackbox {
        margin-top: 40px;
        p {
            font-weight: 300;
            font-size: 1.2em;
            margin: 20px 80px;
            b {
            font-size: 1.3em;
            }
        }
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