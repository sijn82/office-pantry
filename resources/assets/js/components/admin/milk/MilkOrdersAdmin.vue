<template>

    <div id="milkboxes">
        <h3> Milk Boxes </h3>

        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-milkbox :company="this.company" @refresh-data="refreshData($event)"></add-new-milkbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New Milkbox </b-button>
        </div>

        <div v-if="this.milkboxes.length">
            <!-- {{ milkbox.company_name }} -->

            <milkbox class="milkbox" v-for="milkbox in this.milkboxes" :milkbox="milkbox" :key="milkbox.id" @refresh-data="refreshData($event)"></milkbox>

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
    props: ['milkboxes', 'company'],
    data () {
        return {

            fruit_partner_name: '',
            frequency: ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'],
            week_in_month: ['First', 'Second', 'Third', 'Fourth', 'Last'],
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
        refreshData($event) {
            this.$emit('refresh-data', $event);
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
