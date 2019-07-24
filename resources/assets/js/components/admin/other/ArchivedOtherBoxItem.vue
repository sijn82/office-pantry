<template lang="html">
    <div>
        <b-row :class="">
            <b-col>
                <p> {{ archived_otherbox_item.name }} </p>
            </b-col>
            <b-col>
                <div v-if="edit">
                    <b-form-input type="number" v-model="archived_otherbox_item.quantity"></b-form-input>
                </div>
                <div v-else>
                    <p> {{ archived_otherbox_item.quantity }} </p>
                </div>
            </b-col>
            <b-col>
                <p> {{ archived_otherbox_item.unit_price }} </p>
            </b-col>
            <b-col>
                <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
                <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(archived_otherbox_item)"> Save </b-button>
                <b-button size="sm" variant="danger" @click="deleteOtherBoxItem(archived_otherbox_item)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        props: ['archived_otherbox_item'],
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
            deleteOtherBoxItem(archived_otherbox_item) {
                axios.put('api/boxes/archived-otherbox/destroy/' + archived_otherbox_item.id, { 
                    id: archived_otherbox_item.id,
                }).then ( (response) => {
                    this.$emit('refresh-data', {company_details_id: archived_otherbox_item.company_details_id})
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(otherbox_item) {
                axios.post('api/boxes/archived-otherbox/update', { 
                    otherbox_item_id: archived_otherbox_item.id,
                    otherbox_item_quantity: archived_otherbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>