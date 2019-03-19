<template lang="html">
    <div id="drinkboxes">
        <h3> Drink Boxes </h3>
    
        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-drinkbox></add-new-drinkbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New DrinkBox </b-button>
        </div>
    
        <div v-if="Object.keys(this.drinkboxes).length"> 
    
            <drinkbox class="drinkbox" v-for="(drinkbox, key) in this.drinkboxes" :key="key" :drinkbox="drinkbox"> {{ key }} </drinkbox>
    
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
</style>

<script>
    export default {
        props: ['drinkboxes'],
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
        }
    }
</script>