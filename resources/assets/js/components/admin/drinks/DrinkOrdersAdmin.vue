<template lang="html">
    <div id="drinkboxes">
        <h3> Drink Boxes </h3>
    
        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-drinkbox :company="this.company" @refresh-data="refreshData($event)"></add-new-drinkbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New DrinkBox </b-button>
        </div>
    
        <div v-if="Object.keys(this.drinkboxes).length"> 
    
            <drinkbox class="drinkbox" v-for="(drinkbox, key) in this.drinkboxes" :key="key" :drinkbox="drinkbox" :company="this.company" @refresh-data="refreshData($event)"> {{ key }} </drinkbox>
    
        </div>
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
            <!-- <ul><li style="list-style:none;"> {{ this.snackboxes }} </li></ul> -->
        </div>
    </div>
</template>

<style lang="scss" scoped>
    #drinkboxes {
        margin-top: 20px;
        text-align: center;

    }
    #drinkboxes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    .drinkbox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .drinkbox:after {
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
        props: ['drinkboxes', 'company'],
        data () {
            return {
                addnew: false,
            }
        },
        methods: {
            addNew() {
                if (this.addnew == false) {
                    this.addnew = true;
                } else {
                    this.addnew = false;
                }
            },
            refreshData($event) {
                this.$emit('refresh-data', $event);
            },
        }
    }
</script>