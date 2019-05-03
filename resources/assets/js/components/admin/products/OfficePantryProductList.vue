<template lang="html">
    <div class="office-pantry-product-list">
            <!-- Main List Page Title -->
            <b-row>
                <b-col>
                    <h4> Office Pantry Products (Pricing) </h4>
                </b-col>
            </b-row>
            <!-- List Headers -->
            <b-row>
                    <b-col><h6> Name </h6></b-col>
                    <b-col><h6> Price </h6></b-col>
                    <b-col><h6> Sales Nominal </h6></b-col>
                    <b-col><h6> Vat? </h6></b-col>
                    <b-col>  </b-col>
            </b-row>
            <!-- Show Product Entries In List -->
            <b-row>
                <b-col>
                    <office-pantry-product v-for="product in this.products" :product="product" :key="product.id"></office-pantry-product>
                </b-col>
            </b-row>

    </div>
</template>

<style lang="scss" scoped>

    label, p {
        font-weight: 300;
    }
    .office-pantry-product-list {
        margin: 10px 40px;
    }
    .margin-height-20 {
        margin: 20px 0;
    }

</style>

<script>
    export default {
        data() {
            return {
                // I think I'll be pulling the data through as a prop but adding these here for now so as not to throw an undefined error.
                form: {
                    milk_1l: 0,
                    milk_2l: 0,
                    milk_1l_alt: 0,
                    milk_1l_org: 0,
                    milk_2l_org: 0,
                    fruit_standard: 0,
                    fruit_standard_2: 0,
                    fruit_standard_3: 0,
                    fruit_standard_4: 0,
                    fruit_standard_7: 0,
                    fruit_punnet: 0,
                },
                products: {},
            }
        },
        methods: {

            onSubmit (evt) {
              evt.preventDefault();
              let self = this;
              // alert(JSON.stringify(this.form));
              axios.post('/api/products/office-pantry-products/update', {
                  company_data: self.form,
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
                  // user_id: self.userData.id // This hasn't been setup yet so probably won't work yet?!
              }).then(function (response) {
                  alert('Changed Office Pantry Product Pricing Successfully!');
                 // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                  console.log(response.data);
              }).catch(error => {
                   console.log(error),
                   // Sadly it fails while trying to add empty fields to the database, this never gets called... dumb error catcher.
                   // Sorry error catcher I know the fault ultimately lies with my understanding of the situation, not you.
                   alert('Nope you done it wrong - ' . error);
              });
            },

            onReset (evt) {
              evt.preventDefault();
              /* Reset our form values */

              this.form.milk_1l = 0.00;
              this.form.milk_2l = 0.00;
              this.form.milk_1l_alt = 0.00;
              this.form.milk_1l_org = 0.00;
              this.form.milk_2l_org = 0.00;
              this.form.fruit_standard = 0.00;
              this.form.fruit_standard_2 = 0.00;
              this.form.fruit_standard_3 = 0.00;
              this.form.fruit_standard_4 = 0.00;
              this.form.fruit_standard_7 = 0.00;
              this.form.fruit_punnet = 0.00;

            }
        },
        mounted() {
            axios.get('api/products/office-pantry-products/show').then( response => this.products = response.data)
        }
    }
</script>
