<template >
  <div class="">
      <b-row :class="">
          <b-col>
              <p> {{ snackbox_item.product.brand }} </p>
          </b-col>
          <b-col>
              <p> {{ snackbox_item.product.flavour }} </p>
          </b-col>
          <b-col>
              <div v-if="edit">
                  <b-form-input v-model="snackbox_item.quantity" type="number" min="0"></b-form-input>
              </div>
              <div v-else>
                  <p> {{ snackbox_item.quantity }} </p>
              </div>
          </b-col>
          <b-col>
              <p v-if="snackbox_item.type !== 'wholesale'"> {{ snackbox_item.product.selling_unit_price }} </p>
              <p v-else> {{ snackbox_item.product.selling_case_price }} </p>
          </b-col>
          <b-col>
              <b-button size="sm" variant="warning" @click="editor()"> Edit </b-button>
              <b-button v-if="edit" size="sm" variant="success" @click="editQuantity(snackbox_item)"> Save </b-button>
              <b-button size="sm" variant="danger" @click="deleteSnackBoxItem(snackbox_item)"> Remove </b-button>
          </b-col>
      </b-row>
  </div>
</template>

<style lang="scss" scoped>

</style>

<script>

    export default {
        props: ['snackbox_item', 'company_details_id'],
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
            deleteSnackBoxItem(snackbox_item) {
                axios.put('api/boxes/snackbox/remove-product/' + snackbox_item.id, {
                    id: snackbox_item.id,
                    //snackbox_id: snackbox_item.snackbox_id, // EDIT: 20/01/20 - I'll not need this anymore, if I need to destroy the box as well, I have to do this another way.
                }).then ( (response) => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    this.$emit('refresh-data', {company_details_id: this.company_details_id})
                    console.log(response);
                }).catch(error => console.log(error));
            },
            editQuantity(snackbox_item) {
                axios.post('api/boxes/snackbox/update-item-quantity', {
                    item_id: snackbox_item.id,
                    item_quantity: snackbox_item.quantity,
                }).then (response => {
                    //location.reload(true); // What am I doing with the store on this one?  Will I need this?
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
