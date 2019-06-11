<template lang="html">
    <div id="preferences-admin">
        <h3> Preferences </h3>
        <!-- SHow/Hide Add New Preference -->
        <div v-if="addnew">
            <b-button variant="danger" @click="addNew()"> Close </b-button>
            <add-new-preference @refresh-data="refreshData($event)" :company="this.company"></add-new-preference>
        </div>
        <div v-else>
            <b-button variant="primary" @click="addNew()"> Add New Preference </b-button>
        </div>
        <!-- Show existing Preferences - Likes, Dislikes & Essentials -->
        <div class="preferences">
            <preferences 
                :company="this.company" 
                :preferences="this.preferences" 
                :allergies="this.allergies" 
                :additional_info="this.additional_info"
                @refresh-data="refreshData($event)">
            </preferences> <!-- I may move allergies & additional_info out of preferences and into their own section -->
        </div>
        <!-- Show Allergies & Select/Add New Allergy -->
        <div class="allergies">
            
        </div>
        <!-- Show Additional Information -->
        <div class="additional-info">
            
        </div>
        
    </div>
</template>

<style lang="scss" scoped>
    #preferences-admin {
        margin-top: 20px;
        text-align: center;
    }
    #preferences-admin::before {
        content: ""; /* This is necessary for the pseudo element to work. */
        display: block; /* This will put the pseudo element on its own line. */
        margin: 0 auto; /* This will center the border. */
        width: 70%; /* Change this to whatever width you want. */
        padding-bottom: 20px; /* This creates some space between the element and the border. */
        border-top: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
    }
    .preferences {
        margin-top: 20px;
    }

</style>

<script>
export default {
    props: ['company', 'preferences', 'allergies', 'additional_info'],
    data () {
        return {
            addnew: false,
            //company_details_id: null,
        }
    },
    methods: {
        addNew(){
            if (this.addnew === false) {
                this.addnew = true;
            } else {
                this.addnew = false;
            }
        },
        refreshData($event) {
            this.$emit('refresh-data', $event);
            console.log($event);
        },
        mounted() {
            console.log(this.preferences);
            console.log(this.company);
        }
    }
    
    
}
</script>