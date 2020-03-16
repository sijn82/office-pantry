<template>
    <div id="routes">
        <h3> Routes </h3>

        <div v-if="showinfo">

            <b-button variant="success" @click="showInfo()" style="margin-bottom:20px"> Hide Info </b-button>

            <div v-if="this.routes.length"> {{ routes.company_name }}

                <company-route class="routebox" v-for="route in this.routes" :route="route" :key="route.id" @refresh-data="refreshData($event)"></company-route>

            </div>
            <div v-else>
                <ul><li style="list-style:none;"> There are no associated routes.  Add an order to auto generate one. </li></ul>
            </div>

        </div>
        <div v-else><b-button variant="success" @click="showInfo()"> Show Info </b-button></div>
    </div>
</template>

<style lang="scss">

    #routes {
        margin-top: 20px;
        text-align: center;

    }
    #routes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
     /* named box for clarity as it's offering the same styling as the fruit, milk, snack, drink and otherboxes. */
    .routebox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .routes {
    padding-left: 0;
    }
    .routes:after {
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

    props: ['routes'],

    data () {
        return {
            showinfo: false,
        }
    },
    methods: {
        refreshData($event) {
            this.$emit('refresh-data', $event);
        },
        showInfo() {
            if (this.showinfo == true) {
                this.showinfo = false;
            } else {
                this.showinfo = true;
            }
        },
    },

    mounted() {
        console.log(this.routes);
        this.$store.commit('getAssignedRoutes'); // Can I move calls up a component?
    }
}
</script>
