<template lang="html">
  <div class="">
      <b-row :class="">
          <b-col>
              <p> {{ archived_snackbox_item.name }} </p>
          </b-col>
          <b-col>
              <div v-if="edit">
                  <b-form-input v-model="archived_snackbox_item.quantity" type="number" min="0"></b-form-input>
              </div>
              <div v-else>
                  <p> {{ archived_snackbox_item.quantity }} </p>
              </div>
          </b-col>
          <b-col>
              <p v-if="archived_snackbox_item.type !== 'wholesale'"> {{ archived_snackbox_item.unit_price }} </p>
              <p v-else> {{ archived_snackbox_item.case_price }} </p>
          </b-col>
          <b-col>
              <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
              <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(archived_snackbox_item)"> Save </b-button>
              <b-button size="sm" variant="danger" @click="deleteSnackBoxItem(archived_snackbox_item)"> Remove </b-button>
          </b-col>
      </b-row>
  </div>
</template>

<style lang="scss" scoped>

</style>

<script>

    export default {
        props: ['archived_snackbox_item'],
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
            deleteSnackBoxItem(archived_snackbox_item) {
                axios.put('api/boxes/archived_snackbox/destroy/' + archived_snackbox_item.id, {
                    id: archived_snackbox_item.id,
                    archived_snackbox_id: archived_snackbox_item.archived_snackbox_id,
                }).then ( (response) => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    this.$emit('refresh-data', {company_details_id: archived_snackbox_item.company_details_id})
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(archived_snackbox_item) {
                axios.post('api/boxes/archived_snackbox/update', {
                    archived_snackbox_item_id: archived_snackbox_item.id,
                    archived_snackbox_item_quantity: archived_snackbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
