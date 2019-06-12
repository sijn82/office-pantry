<template lang="html">
    <div id="otherboxes">
        <h3> Other Boxes </h3>
  
        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-otherbox :company="this.company" @refresh-data="refreshData($event)"></add-new-otherbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New OtherBox </b-button>
        </div>
  
        <div v-if="Object.keys(this.otherboxes).length"> 
  
            <otherbox class="otherbox" v-for="(otherbox, key) in this.otherboxes" :key="key" :otherbox="otherbox" :company="this.company" @refresh-data="refreshData($event)"> {{ key }} </otherbox>
  
        </div>
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
            <!-- <ul><li style="list-style:none;"> {{ this.snackboxes }} </li></ul> -->
        </div>
    </div>
</template>

<style lang="scss" scoped>
    #otherboxes {
        margin-top: 20px;
        text-align: center;

    }
    #otherboxes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    .otherbox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .otherbox:after {
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
        props: ['otherboxes', 'company'],
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
