<template >
    <div class="routes">
        <!-- <ul> -->
            <div id="edit-save-buttons">
                <h4> {{ route.route_name }} </h4>
                <p> {{ route.delivery_day }} - {{ route.is_active }} </p>
                <b-button variant="primary" @click="showDetails()"> Details </b-button>
                <b-button variant="warning" @click="enableEdit()"> Edit </b-button>
                <b-button v-if="editing" class="btn btn-success" @click="updateRouteInfo(route)"> Save </b-button>
                <b-button v-if="editing" variant="danger" @click="deleteRoute(route)"> Delete </b-button>
            </div>
            <div id="route-details" v-show="details">
                <b-row id="top-details" :class="route.is_active" sm="12" class="b-row-padding">
                    <b-col class="col-sm-4">
                        <label><b> Route ID </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="route.id"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ route.id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Company ID </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="route.company_details_id"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ route.company_details_id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Route Status </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="route.is_active">
                                <option value="Active"> Active </option>
                                <option value="Inactive"> Inactive </option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ route.is_active }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="route.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Route Name </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="route.route_name"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ route.route_name }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                         <label><b> Fruit Crates </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="route.fruit_crates" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ route.fruit_crates }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                         <label><b> Fruit Boxes </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="route.fruit_boxes" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ route.fruit_boxes }} </p>
                        </div>
                    </b-col>
                </b-row>
                
                <b-row :class="route.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Snacks </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="route.snacks" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ route.snacks }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                         <label><b> Drinks </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="route.drinks" type="number"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ route.drinks }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                         <label><b> Other </b></label>
                         <div v-if="editing">
                             <b-form-textarea v-model="route.other" rows="3" max-rows="6"></b-form-textarea>
                         </div>
                         <div v-else>
                             <p> {{ route.other }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row :class="route.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                        <label><b> Delivery Day </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="route.delivery_day">
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ route.delivery_day }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Assigned Route </b></label>
                        <div v-if="editing">
                            <b-form-select v-model="route.assigned_route_id">
                                <option v-for="assigned_route in $store.state.assigned_routes_list" :value="assigned_route.id"> {{ assigned_route.name }} </option>
                            </b-form-select>
                        </div>
                        <div v-else>
                            <p> {{ route.assigned_route_id }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Position On Route </b></label>
                        <div v-if="editing">
                            <b-form-input v-model="route.position_on_route" type="number"></b-form-input>
                        </div>
                        <div v-else>
                            <p> {{ route.position_on_route }} </p>
                        </div>
                    </b-col>
                </b-row>

                <b-row id="bottom-details" :class="route.is_active" class="b-row-padding" sm="12">
                    <b-col class="col-sm-4">
                         <label><b> Postcode </b></label>
                         <div v-if="editing">
                             <b-form-input v-model="route.postcode" type="text"></b-form-input>
                         </div>
                         <div v-else>
                             <p> {{ route.postcode }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                         <label><b> Address </b></label>
                         <div v-if="editing">
                             <b-form-textarea v-model="route.address" type="text" rows="3" max-rows="6"></b-form-textarea>
                         </div>
                         <div v-else>
                             <p> {{ route.address }} </p>
                        </div>
                    </b-col>
                    <b-col class="col-sm-4">
                        <label><b> Delivery Information </b></label>
                        <div v-if="editing">
                            <b-form-textarea v-model="route.delivery_information" type="text" rows="3" max-rows="6"></b-form-textarea>
                        </div>
                        <div v-else>
                            <p> {{ route.delivery_information }} </p>
                        </div>
                    </b-col>
                </b-row>
            </div>
        <!-- </ul> -->
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>

export default {
    props: ['route'],
    data() {
        return {
            editing: false,
            details: false,
        }
    },
    methods: {
        enableEdit() {
            if (this.editing == false) {
                this.editing = true;
                this.details = true;
            } else {
                this.editing = false;
            }

        },
        showDetails() {
            if (this.details == true) {
                this.details = false;
            } else {
                this.details = true;
            }
        },
        updateRouteInfo(route) {
            this.editing = false;
            console.log(route);
            console.log(route.id);
            axios.put('api/company-route/update/' + route.id, {
                id: route.id,
                company_details_id: route.company_details_id,
                is_active: route.is_active,
                fruit_crates: route.fruit_crates,
                fruit_boxes: route.fruit_boxes,
                route_name: route.route_name,
                snacks: route.snacks,
                drinks: route.drinks,
                other: route.other,
                delivery_day: route.delivery_day,
                assigned_route_id: route.assigned_route_id,
                position_on_route: route.position_on_route,
                postcode: route.postcode,
                address: route.address,
                delivery_information: route.delivery_information,
            }).then (response => {
                console.log(response);
            }).catch(error => console.log(error));
        },
        deleteRoute(route) {
            axios.put('api/company-route/delete/' + route.id, { 
                id: route.id,
            }).then (response => {
                // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                console.log(response);
            }).catch(error => console.log(error));
        },
    },
    mounted() {
        
        // this.$store.commit('getAssignedRoutes'); // <-- Moved this up a component.
    }
}

</script>
