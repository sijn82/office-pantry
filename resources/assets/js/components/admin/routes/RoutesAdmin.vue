<template>
    <div id="routes">
        <h3> Routes </h3>
    
        <div v-if="this.routes.length"> {{ routes.company_name }}
                
            <company-route class="routebox" v-for="route in this.routes" :route="route" :key="route.id"></company-route>
            
        </div>
        <div v-else>
            <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
        </div>            
    </div>
</template>

<style>
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

        }
    },

    mounted() {
        console.log(this.routes);
        this.$store.commit('getAssignedRoutes'); // Can I move calls up a component?
    }
}
</script>