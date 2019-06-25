<template lang="html">
    <div id="otherboxes">
        <h3> Archived Other Boxes </h3>
        <!-- v-if="Object.keys(this.otherboxes).length" -->
        <div v-if="!Array.isArray(this.archived_otherboxes)">
            <div v-for="(archived_otherbox_week, key) in this.archived_otherboxes"> 
      
                <archived-otherbox 
                    class="otherbox" 
                    v-for="(archived_otherbox, key) in archived_otherbox_week" 
                    :key="key" :archived_otherbox="archived_otherbox" 
                    :company="this.company" 
                    @refresh-data="refreshData($event)"> {{ key }} 
                </archived-otherbox>
      
            </div>
        </div>
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
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
        props: ['archived_otherboxes', 'company'],
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
        },
        mounted() {
            console.log('look below for archived otherboxes')
            console.log(this.archived_otherboxes);
        }
    }
</script>
