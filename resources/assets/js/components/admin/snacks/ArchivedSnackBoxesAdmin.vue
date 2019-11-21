<template>
    <!-- Fields to show/edit -->
    <!-- Id, Snackbox Id, Delivered By, No of Boxes, Type, Company Id, Delivery Day, Frequency, Prev Delivery Week, Next Delivery Week, Code, Name, Quantity -->
    <div id="archived_snackboxes">
        <h3> Archived Snack Boxes </h3>
        <!-- v-if="Object.keys(this.archived_snackboxes).length" --> <!-- THIS CHECK NOW THROWS ERRORS, NEED ALTERNATIVE -->
         <!-- v-if="Array.isArray(this.archived_snackboxes)" -->
        <div v-if="!Array.isArray(this.archived_snackboxes)">
            <div v-for="(archived_snackbox_week, key) in this.archived_snackboxes">

                <h3> {{ key }} </h3>

                     <archived-snackbox
                        class="archived_snackbox"
                        v-for="(archived_snackbox, key) in archived_snackbox_week"
                        :key="key"
                        :archived_snackbox="archived_snackbox"
                        :company="company"
                        @refresh-data="refreshData($event)"> {{ key }}
                    </archived-snackbox>
            </div>
        </div>
         <!-- - Without an initial v-if ( we have orders ), we can't use the v-else ('nothing to see here') until I find a replacement. -->
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
        </div>
    </div>
</template>

<style lang="scss">
    #archived_snackboxes {
        margin-top: 20px;
        text-align: center;

    }
    #archived_snackboxes::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
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
    .archived_snackbox {
        padding-top: 20px;
        padding-left: 0;
        li {
        list-style: none;
        }
    }
    .archived_snackbox:after {
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
        props: ['archived_snackboxes', 'company'],
        data () {
            return {
                archived_snackbox: {

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
            refreshData($event) {
                this.$emit('refresh-data', $event);
            },
        },
        mounted() {
            console.log('look below for achived snackboxes')
            console.log(this.archived_snackboxes)
        }
    }
</script>
