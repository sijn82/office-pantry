<template>

    <div id="milkboxes">
        <h3> Milk Boxes </h3>
        <div v-if="showinfo">

            <b-button variant="success" @click="showInfo()" style="margin-bottom:20px"> Hide Info </b-button>

            <div v-if="addnew">
                <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
                <add-new-milkbox :company="this.company" @refresh-data="refreshData($event)"></add-new-milkbox>
            </div>
            <div v-else class="add-new-close">
                <b-button variant="primary" @click="addNew()"> Add New Milkbox </b-button>
            </div>

            <div v-if="this.scheduled_milkboxes.length || this.milkboxes.length || this.paused_milkboxes.length || this.archived_milkboxes.length">

                <h4> Scheduled Boxes </h4>

                <div v-if="this.scheduled_milkboxes.length">  <!-- {{ milkbox.company_name }} -->

                    <milkbox    class="milkbox" 
                                v-for="milkbox in this.scheduled_milkboxes" 
                                :milkbox="milkbox" 
                                :key="milkbox.id" 
                                @refresh-data="refreshData($event)">
                    </milkbox>

                </div>
                <div v-else>
                    <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
                </div>

                <h4 class="margin-top-20"> Active Boxes </h4>

                <div v-if="this.milkboxes.length">  <!-- {{ milkbox.company_name }} -->

                    <milkbox    class="milkbox" 
                                v-for="milkbox in this.milkboxes" 
                                :milkbox="milkbox" 
                                :key="milkbox.id" 
                                @refresh-data="refreshData($event)">
                    </milkbox>

                </div>
                <div v-else>
                    <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
                </div>

                <h4 class="margin-top-20"> Paused Boxes </h4>

                <div v-if="this.paused_milkboxes.length">  <!-- {{ milkbox.company_name }} -->

                    <milkbox    class="milkbox" 
                                v-for="milkbox in this.paused_milkboxes" 
                                :milkbox="milkbox" 
                                :key="milkbox.id" 
                                @refresh-data="refreshData($event)">
                    </milkbox>

                </div>
                <div v-else>
                    <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
                </div>

                <h4 class="margin-top-20"> Archived Boxes (Awaiting Invoice) </h4>

                <div v-if="this.archived_milkboxes.length">  <!-- {{ milkbox.company_name }} -->

                    <milkbox    class="milkbox" 
                                v-for="milkbox in this.archived_milkboxes" 
                                :milkbox="milkbox" 
                                :key="milkbox.id" 
                                @refresh-data="refreshData($event)">
                    </milkbox>

                </div>
                <div v-else>
                    <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
                </div>
            </div>
            <div v-else> There are no associated milkboxes.  Please add one to begin. </div>
        </div>
        <div v-else><b-button variant="success" @click="showInfo()"> Show Info </b-button></div>
    </div>
</template>

<style lang="scss">

    #milkboxes {
        margin-top: 20px;
        text-align: center;

    }
    #milkboxes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
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
        padding-top: 20px;
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
    props: {    scheduled_milkboxes: Array, 
                milkboxes: Array, 
                paused_milkboxes: Array,
                archived_milkboxes: Array,
                company: Object
    },
    data () {
        return {

            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Fourth', 'Last'],
            editing: false,
            details: false,
            addnew: false,
            showinfo: false,
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
        showInfo() {
            if (this.showinfo == true) {
                this.showinfo = false;
            } else {
                this.showinfo = true;
            }
        },
        refreshData($event) {
            this.$emit('refresh-data', $event);
            // Added 14/01/20 to close add new panel on form completion.
            // This will close when an update (created/updated/deleted) is made to any fruitbox box but I don't see that being an issue.
            this.addnew = false;
        },

        changeName(name) {
            return this.fruitbox.fruit_partner_name = name;
        }
    },
    mounted() {
        // this.$store.commit('getFruitPartners');
        // console.log(this.milkboxes);
    }
}

</script>
