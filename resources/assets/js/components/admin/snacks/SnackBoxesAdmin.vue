<template>
    <!-- Fields to show/edit -->
    <!-- Id, Snackbox Id, Delivered By, No of Boxes, Type, Company Id, Delivery Day, Frequency, Prev Delivery Week, Next Delivery Week, Code, Name, Quantity -->
    <div id="snackboxes">
        <h3> Snack Boxes </h3>

        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-snackbox></add-new-snackbox>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New SnackBox </b-button>
        </div>

        <div v-if="Object.keys(this.snackboxes).length"> 

            <snackbox class="snackbox" v-for="(snackbox, key) in this.snackboxes" :key="key" :snackbox="snackbox"> {{ key }} </snackbox>

        </div>
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
            <!-- <ul><li style="list-style:none;"> {{ this.snackboxes }} </li></ul> -->
        </div>
    </div>
</template>

<style lang="scss">
    #snackboxes {
        margin-top: 20px;
        text-align: center;

    }
    #edit-save-buttons {
        padding-bottom: 10px;
        h4 {
            display: inline;
        }
    }
    .Active {
        background-color: rgba(116, 244, 66, 0.5);
    }
    .Inactive {
        background-color: rgba(201, 16, 16, 0.5);
    }
    .snackbox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .snackbox:after {
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
        props: ['snackboxes'],
        data () {
            return {
                snackbox: {
                    
                },
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