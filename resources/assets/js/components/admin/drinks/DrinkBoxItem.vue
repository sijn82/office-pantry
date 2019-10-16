<template lang="html">
    <div>
        <b-row :class="">
            <b-col>
                <p> {{ drinkbox_item.name }} </p>
            </b-col>
            <b-col>
                <div v-if="edit">
                    <b-form-input v-model="drinkbox_item.quantity" type="number" min="0" max="100"></b-form-input>
                </div>
                <div v-else>
                    <p> {{ drinkbox_item.quantity }} </p>
                </div>
            </b-col>
            <b-col>
                <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
                <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(drinkbox_item)"> Save </b-button>
                <b-button size="sm" variant="danger" @click="deleteDrinkBoxItem(drinkbox_item)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        props: ['drinkbox_item'],
        data () {
            return {
                edit: false,
            }
        },
        methods: {
            editor() {
                if (this.edit === false) {
                    this.edit = true;
                } else {
                    this.edit = false;
                }
            },
            deleteDrinkBoxItem(drinkbox_item) {
                axios.put('api/boxes/drinkbox/destroy/' + drinkbox_item.id, {
                    id: drinkbox_item.id,
                }).then ( (response) => {
                    this.$emit('refresh-data', {company_details_id: drinkbox_item.company_details_id})
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(drinkbox_item) {
                axios.post('api/boxes/drinkbox/update', {
                    drinkbox_item_id: drinkbox_item.id,
                    drinkbox_item_quantity: drinkbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
