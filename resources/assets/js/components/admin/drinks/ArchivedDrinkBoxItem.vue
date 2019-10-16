<template lang="html">
    <div>
        <b-row :class="">
            <b-col>
                <p> {{ archived_drinkbox_item.name }} </p>
            </b-col>
            <b-col>
                <div v-if="edit">
                    <b-form-input v-model="archived_drinkbox_item.quantity" type="number" min="0" max="100"></b-form-input>
                </div>
                <div v-else>
                    <p> {{ archived_drinkbox_item.quantity }} </p>
                </div>
            </b-col>
            <b-col>
                <p> {{ archived_drinkbox_item.unit_price }} </p>
            </b-col>
            <b-col>
                <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
                <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(archived_drinkbox_item)"> Save </b-button>
                <b-button size="sm" variant="danger" @click="deleteDrinkBoxItem(archived_drinkbox_item)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        props: ['archived_drinkbox_item'],
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
            deleteDrinkBoxItem(archived_drinkbox_item) {
                axios.put('api/boxes/archived_drinkbox/destroy/' + archived_drinkbox_item.id, {
                    id: archived_drinkbox_item.id,
                }).then ( (response) => {
                    this.$emit('refresh-data', {company_details_id: archived_drinkbox_item.company_details_id})
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(archived_drinkbox_item) {
                axios.post('api/boxes/archived_drinkbox/update', {
                    archived_drinkbox_item_id: archived_drinkbox_item.id,
                    archived_drinkbox_item_quantity: archived_drinkbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
