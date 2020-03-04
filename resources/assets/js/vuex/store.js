import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        
        week_start: [], // This is a good use of the store as it's used in multiple places and only changes once a week, multiple concurrent users wouldn't be an issue.
        cron_data: [],
        allergies_list: [], // another good use of store, as it only populates a list of options which are equally useful to all users in real time.
        types_list: [], // another good use of store, as it only populates a list of options which are equally useful to all users in real time.
        fruit_partners_list: [], // another good use of store, as it only populates a list of options which are equally useful to all users in real time.
        assigned_routes_list: [], // another good use of store, as it only populates a list of options which are equally useful to all users in real time.
        snackbox: [], // so long as only one user is making a snackbox at a time, this would be fine, multiple users inputting snackboxes would break this currently. <-- IS THIS TRUE?
        otherbox: [], // so long as only one user is making a snackbox at a time, this would be fine, multiple users inputting snackboxes would break this currently.
        drinkbox: [], // so long as only one user is making a snackbox at a time, this would be fine, multiple users inputting snackboxes would break this currently.
        selectedCompany: {
            id: null,
        }, // This is main one that needs to be replaced with tradition props and custom events.
        selectedProduct: {
            id: null,
            code: null,
            brand: null,
            flavour: null,
            selling_case_price: null,
            selling_case_size: null,
            //unit_cost: null,
            selling_unit_price: null,
            stock_level: null,
            shortest_stock_date: null,
        }, // This one would also suffer if multiple users are selecting products for different orders etc...
        setPreferences: {
            snackbox_likes: [],
            snackbox_dislikes: [],
            snackbox_essentials: [],
            snackbox_essentials_quantity: [],
            allergies: [],
            additional_notes: [],
        }, // And this one for the same reason.

    },
    getters: {
        week_start: state => { return state.week_start[0] },
        //delivery_days: state => { return state.week_start[0].delivery_days }
    },
    mutations: {

        // Add items to store for frontend / backend syncronisity
        // and order building.
        addSnackboxToStore (state, product) {
            state.snackbox.push(product);
        },
        addOtherboxToStore (state, product) {
            state.otherbox.push(product);
        },
        addDrinkboxToStore (state, product) {
            state.drinkbox.push(product);
        },

        addPreferenceToStore (state, preference) {
            console.log(preference.product.brand + ' - ' + preference.product.flavour);
            if (preference.category == 'snackbox_likes') {
                preference.product.snackbox_likes = (preference.product.brand + ' - ' + preference.product.flavour);
                state.setPreferences.snackbox_likes.push(preference.product);
                console.log(state.setPreferences.snackbox_likes);
            } else if (preference.category == 'snackbox_dislikes') {
                preference.product.snackbox_dislikes = (preference.product.brand + ' - ' + preference.product.flavour);
                state.setPreferences.snackbox_dislikes.push(preference.product);
                console.log(state.setPreferences.snackbox_dislikes);
            } else if (preference.category == 'snackbox_essentials') {
                preference.product.snackbox_essentials = (preference.product.brand + ' - ' + preference.product.flavour);
                preference.product.snackbox_essentials_quantity = preference.product.quantity;
                state.setPreferences.snackbox_essentials.push(preference.product);
                console.log(state.setPreferences.snackbox_essentials);
            }
        },
        addAllergyToStore (state, allergy) {
            console.log(allergy);
            state.setPreferences.allergies.push(allergy);
        },
        addTypeToStore (state, type) {
            console.log(type);
            state.setPreferences.types.push(type);
        },
        addAdditionalInfoToStore (state, additional_info) {
            console.log(additional_info);
            state.setPreferences.additional_notes.push(additional_info);
        },

        // Add new allergy to list of options.
        addNewAllergyToStore (state, allergy) {
            state.allergies_list.push(allergy);
        },
        
        // Edit 04-03-20: Added a quick check that the value isn't already contained in the types_list array.
        // Adding it to the array if not, or console logging a message if it is.
        addNewTypeToStore (state, type) {

                var check = state.types_list.indexOf(type)

                if (check === -1) {
                    state.types_list.push(type);
                } else {
                    console.log(type + ' already in types list')
                }

        },

        addFruitPartnerToStore (state, fruit_partner) {
            console.log(fruit_partner);
            state.fruit_partners_list.push(fruit_partner);
        },

        // Remove from current store selection.
        removeFromSnackbox (state, id) {
            // first we need to find the product (object) index in array with the given id.
            // console.log(state.snackbox);
            let index = state.snackbox.map( product => {
                            return product.id;
                        }).indexOf(id);
                        // console.log(index);
            // then we can specify it in splice and remove that product form the list.
            state.snackbox.splice(index, 1);
        },
        removeFromOtherbox (state, id) {
            // first we need to find the product (object) index in array with the given id.
            // console.log(state.snackbox);
            let index = state.otherbox.map( product => {
                            return product.id;
                        }).indexOf(id);
                        // console.log(index);
            // then we can specify it in splice and remove that product form the list.
            state.otherbox.splice(index, 1);
        },
        removeFromDrinkbox (state, id) {
            // first we need to find the product (object) index in array with the given id.
            // console.log(state.snackbox);
            let index = state.drinkbox.map( product => {
                            return product.id;
                        }).indexOf(id);
                        // console.log(index);
            // then we can specify it in splice and remove that product form the list.
            state.drinkbox.splice(index, 1);
        },
        removePreference (state, preference) {

            if (preference.column == 'snackbox_likes') {
                let index = state.setPreferences.snackbox_likes.map( preference => {
                                return preference.id;
                            }).indexOf(preference.id);
                // then we can specify it in splice and remove that product form the list.
                state.setPreferences.snackbox_likes.splice(index, 1);

            } else if (preference.column == 'snackbox_dislikes') {
                let index = state.setPreferences.snackbox_dislikes.map( preference => {
                                return preference.id;
                            }).indexOf(preference.id);
                // then we can specify it in splice and remove that product form the list.
                state.setPreferences.snackbox_dislikes.splice(index, 1);

            } else if (preference.column == 'snackbox_essentials') {
                let index = state.setPreferences.snackbox_essentials.map( preference => {
                                return preference.id;
                            }).indexOf(preference.id);
                // then we can specify it in splice and remove that product form the list.
                state.setPreferences.snackbox_essentials.splice(index, 1);
            } else if (preference.column == 'allergies') {
                let index = state.setPreferences.allergies.map( preference => {
                                return preference.id;
                            }).indexOf(preference.id);
                // then we can specify it in splice and remove that product form the list.
                state.setPreferences.allergies.splice(index, 1);
            } else if (preference.column == 'additional_info') {
                let index = state.setPreferences.additional_notes.map( preference => {
                                return preference.id;
                            }).indexOf(preference.id);
                // then we can specify it in splice and remove that product form the list.
                state.setPreferences.additional_notes.splice(index, 1);
            }
        },
        removeAssignedRoute (state, id) {
            let index = state.assigned_routes_list.map( ass_route => {
                            return ass_route.id;
                        }).indexOf(id);
                        // console.log(index);
            // then we can specify it in splice and remove that product from the list.
            state.assigned_routes_list.splice(index, 1);
        },
        // Currently selected product/company variables for saving data to.
        selectedProduct (state, product) {
            console.log(product);
            // state.selectedProduct.case_size = product.case_price; // Wow, what a dumbass - I've followed the trail though and don't think this is used anywhere else.

            state.selectedProduct.id = product.id;
            state.selectedProduct.code = product.code;
            state.selectedProduct.brand = product.brand;
            state.selectedProduct.flavour = product.flavour;
            state.selectedProduct.selling_case_price = product.selling_case_price;
            state.selectedProduct.selling_case_size = product.selling_case_size;
            //state.selectedProduct.unit_cost = product.unit_cost;
            state.selectedProduct.selling_unit_price = product.selling_unit_price;
            state.selectedProduct.stock_level = product.stock_level;
            state.selectedProduct.shortest_stock_date = product.shortest_stock_date;

        },
        removeSelectedProductFromStore (state) {
            state.selectedProduct.id = null;
            state.selectedProduct.code = null;
            state.selectedProduct.brand = null;
            state.selectedProduct.flavour = null;
            state.selectedProduct.selling_case_price = null;
            state.selectedProduct.selling_case_size = null;
            //state.selectedProduct.unit_cost = null;
            state.selectedProduct.selling_unit_price = null;
            state.selectedProduct.stock_level = null;
            state.selectedProduct.shortest_stock_date = null;
        },
        selectedCompany (state, company) {
            console.log(company);
            state.selectedCompany.id = company.id;
        },
        // Grab db records for state to show the current company frontend.
        setPreferences (state, preferences) {
            console.log('look below');
            console.log(preferences);
            state.setPreferences.snackbox_likes = preferences.likes;
            state.setPreferences.snackbox_dislikes = preferences.dislikes;
            state.setPreferences.snackbox_essentials = preferences.essentials;
            state.setPreferences.allergies = preferences.allergies;
            state.setPreferences.additional_notes = preferences.additional_notes;
        },
        getAllergies (state) {
            axios.get('/api/company/allergies/select').then( response => {
                response.data.forEach( function (element) {
                    state.allergies_list.push(element.allergy);
                }),
                console.log(state.allergies_list)
            });
        },
        // This one has been tailored to work with an array, rather than an object
        // The var check no longer looks for a matching id but instead searches directly for the value.
        getTypes (state) {
            axios.get('/api/boxes/types/select').then( response => {
                response.data.forEach( element => {
                    //console.log(element);
                    var check = state.types_list.findIndex( list => list == element.type )
                    if (check === -1) {
                        state.types_list.push(element.type);
                    }
                }),
                console.log(state.types_list)
            });
        },
        getFruitPartners (state) {
            axios.get('/api/office-pantry/fruit-partners/select').then( response => {
                response.data.forEach( element => {
                    //console.log(element);
                    var check = state.fruit_partners_list.findIndex( list => list.id == element.id)
                    if (check === -1) {
                        state.fruit_partners_list.push(element);
                    }
                })
                //console.log(state.fruit_partners_list)
            });
        },
        getAssignedRoutes (state) {
            axios.get('/api/office-pantry/assigned-routes/select').then( response => {
                response.data.forEach( element => {
                    //console.log(element);
                    var check = state.assigned_routes_list.findIndex( list => list.id == element.id)
                    if (check === -1) {
                        state.assigned_routes_list.push(element);
                    }
                })
            });
        },

        getWeekStart (state) {
            axios.get('/api/office-pantry/week-start/select').then( response => {
                // response.data.forEach( element => {
                    //console.log(response);
                    var check = state.week_start.findIndex( list => list.id == response.data.id)
                    if (check === -1) {
                        state.week_start.push(response.data);
                    }
                // })
            });
        },

        getCronData (state) {
            axios.get('/api/office-pantry/cron-data/select').then(
                response => {
                    //console.log(response);
                    var check = state.cron_data.findIndex( list => list.id == response.data.id)
                    if (check === -1) {
                        state.cron_data.push(response.data);
                }
            });
        }
    },
    actions: {
        saveStandardSnackboxToDB (store, type) {
                // console.log(state.snackbox);
                console.log(store.state.snackbox);

            axios.post('/api/boxes/snackboxes/standard/update', {
                order: store.state.snackbox,
                type: type,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).then( function (response) {
                alert('Uploaded new standard snackbox successfully!');
                // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                console.log(response.data);
            }).catch(error => console.log(error));
        },
        saveCompanySnackboxToDB(store) {
            // console.log(store.state.snackbox);
            // console.log(store.state.selectedCompany.id);

            // axios.post('/api/snackboxes/save', {
            //     company_id: store.state.selectedCompany.id,
            //     order: store.state.snackbox,
            //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
            // }).then( function (response) {
            //     alert('Uploaded new Company snackbox successfully!');
            //     // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
            //     // console.log(response.data);
            // }).catch(error => console.log(error));
        }

    }
});
