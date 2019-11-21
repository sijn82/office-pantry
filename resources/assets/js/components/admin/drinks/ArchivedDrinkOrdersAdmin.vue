<template >
    <div id="drinkboxes">
        <h3> Archived Drink Boxes </h3>
        <!-- v-if="Object.keys(this.archived_drinkboxes).length" -->
        <!-- This (Object.keys) has been replaced with !Array.isArray to tackle the issue of it returning as an array if empty rather than an object if populated by archived orders -->
        <div v-if="!Array.isArray(this.archived_drinkboxes)">
            <div v-for="(archived_drinkbox_week, key) in this.archived_drinkboxes">

                <h3> {{ key }} </h3>

                <archived-drinkbox class="drinkbox"
                    v-for="(archived_drinkbox, key) in archived_drinkbox_week"
                    :key="key"
                    :archived_drinkbox="archived_drinkbox"
                    :company="company" 
                    @refresh-data="refreshData($event)"> {{ key }}
                </archived-drinkbox>

            </div>
        </div>
        <div v-else>
            <ul><li style="list-style:none;">  ~ Nothing To See Here ~ </li></ul>
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
        props: ['archived_drinkboxes', 'company'],
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
            console.log(this.archived_drinkboxes);
        }
    }
</script>
