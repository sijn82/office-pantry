<template >
    <div>
        <b-row :class="">
            <b-col>
                <p> {{ otherbox_item.name }} </p>
            </b-col>
            <b-col>
                <div v-if="edit">
                    <b-form-input v-model="otherbox_item.quantity" type="number" min="0" max="100"></b-form-input>
                </div>
                <div v-else>
                    <p> {{ otherbox_item.quantity }} </p>
                </div>
            </b-col>
            <b-col>
                <p> {{ otherbox_item.unit_price }} </p>
            </b-col>
            <b-col>
                <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
                <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(otherbox_item)"> Save </b-button>
                <b-button size="sm" variant="danger" @click="deleteOtherBoxItem(otherbox_item)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        props: ['otherbox_item'],
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
            deleteOtherBoxItem(otherbox_item) {
                axios.put('api/boxes/otherbox/destroy/' + otherbox_item.id, {
                    id: otherbox_item.id,
                }).then ( (response) => {
                    this.$emit('refresh-data', {company_details_id: otherbox_item.company_details_id})
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(otherbox_item) {
                axios.post('api/boxes/otherbox/update', {
                    otherbox_item_id: otherbox_item.id,
                    otherbox_item_quantity: otherbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
