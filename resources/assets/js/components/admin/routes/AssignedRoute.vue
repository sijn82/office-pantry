<template >
    <div>
        <b-row class="padding-width-40 margin-height-5">
            <b-col class="font-weight-300"> <div v-if="editing"> <b-form-input v-model="assigned_route.name" type="text"></b-form-input> </div><div v-else> {{ assigned_route.name }} </div> </b-col>
            <b-col class="font-weight-300"> <div v-if="editing"> <b-form-select v-model="assigned_route.delivery_day" :options="days_of_week"></b-form-select> </div><div v-else> {{ assigned_route.delivery_day }} </div> </b-col>
            <b-col class="font-weight-300"> <div v-if="editing"> <b-form-input v-model="assigned_route.tab_order" type="number"></b-form-input> </div><div v-else> {{ assigned_route.tab_order }} </div> </b-col>
            <b-col>
                 <b-button variant="warning" size="sm" @click="edit()"> Edit </b-button>
                 <b-button variant="success" size="sm" v-if="editing" @click="update(assigned_route)"> Save </b-button>
                 <b-button variant="danger" size="sm" v-if="editing" @click="deleter(assigned_route.id)"> Delete </b-button>
            </b-col>
        </b-row>
    </div>
</template>


<style lang="scss" scoped>

    .padding-width-40 {
        padding: 0 40px;
    }
    .margin-height-5 {
        margin: 5px 0;
    }
    .font-weight-300 {
        font-weight: 300;
    }
    
</style>

<script>

export default {
    props: ['assigned_route'],
    data() {
        return {
            days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            editing: false
        }
    },
    
    methods: {
        edit() {
            if (this.editing == true) {
                this.editing = false;
            } else {
                this.editing = true;
            }
        },
        update(assigned_route) {
            axios.post('api/office-pantry/assigned-routes/update', {
                assigned_route: assigned_route,
            }).then ( response => {
                console.log(response);
            }).catch(error => console.log(error));
        },
        deleter(id) {
            this.$store.commit('removeAssignedRoute', { id });
            axios.put('api/office-pantry/assigned-route/' + id, { 
                id: id,
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        },
    }
}

</script>