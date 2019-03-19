<template lang="html">
    <div>
        <h3 class="margin-top-40"> Assigned Routes List </h3>
        
        <div>
            <h4 class="margin-height-20"> Monday & Tuesday Routes </h4>
            <div v-for="assigned_route in $store.state.assigned_routes_list">
                <div v-if="assigned_route.delivery_day == 'Monday' || assigned_route.delivery_day == 'Tuesday'">
                    <assigned-route :assigned_route="assigned_route"></assigned-route>
                </div>
            </div>
        </div>
        <div class="margin-bottom-30">
            <h4 class="margin-height-20"> Wednesday, Thursday & Friday Routes </h4>
            <div v-for="assigned_route in $store.state.assigned_routes_list">
                <div v-if="assigned_route.delivery_day == 'Wednesday' || assigned_route.delivery_day == 'Thursday' ||  assigned_route.delivery_day == 'Friday'">
                    <assigned-route :assigned_route="assigned_route"></assigned-route>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

    .margin-top-40 {
        margin-top: 40px;
    }
    .margin-height-20 {
        margin: 20px 0;
    }
    .margin-bottom-30 {
        margin-bottom: 30px;
    }
    
</style>

<script>

export default {
    data() {
        return {
            
        }
    },
    
    methods: {
        
    },
    
    mounted() {
        this.$store.commit('getAssignedRoutes');
    }
}

</script>