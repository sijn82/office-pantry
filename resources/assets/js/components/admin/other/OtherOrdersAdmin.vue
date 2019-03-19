<template lang="html">
    <div id="otherboxes">
        <h3> Other Boxes </h3>
  
        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-otherbox></add-new-otherbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New OtherBox </b-button>
        </div>
  
        <div v-if="Object.keys(this.otherboxes).length"> 
  
            <otherbox class="otherbox" v-for="(otherbox, key) in this.otherboxes" :key="key" :otherbox="otherbox"> {{ key }} </otherbox>
  
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
</style>

<script>
    export default {
        props: ['otherboxes'],
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
