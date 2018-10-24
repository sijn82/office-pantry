<template>
    <div>
        <h3 class="section-header">Add New Company</h3>
        <b-form class="add-company-form" @submit="onSubmit" @reset="onReset" v-if="show">
                <!-- Company Names -->
                <b-form-group id="company-name-group"
                            label="Company Names"
                            label-for="company_names"
                            description="These names should match the Master Invoice Creator and Route entries.">
                        <b-form-input id="company_invoice"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.invoice_name"
                                    required
                                    placeholder="Enter invoice name">
                        </b-form-input>
                        <b-form-input id="company_route"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.route_name"
                                    required
                                    placeholder="Enter route name">
                        </b-form-input>
                </b-form-group>
                <!-- Fruit Boxes -->
                <b-input-group id="fruitbox-names"
                            label="Box Name(s)"
                            label-for="box-name-one">
                        <b-form-input id="box-name-one"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.box_names.box_one"
                                      required
                                      placeholder="Enter Fruit Box Name(s)">
                        </b-form-input>
                        <b-input-group-append class="box-appends">
                                <template v-if="!toggle">
                                    <b-btn :pressed.sync="toggle" variant="outline-success">Add 2nd Box</b-btn>
                                </template>
                                <template v-if="toggle && !toggle_two">
                                    <b-btn :pressed.sync="toggle_two" variant="outline-success">Add 3rd Box</b-btn>
                                </template>
                                <template v-if="toggle && toggle_two && !toggle_three">
                                    <b-btn :pressed.sync="toggle_three" variant="outline-success">Add 4th Box</b-btn>
                                </template>
                                <template v-if="toggle && toggle_two && toggle_three">
                                    <b-btn variant="outline-success">Current maximum reached!</b-btn>
                                </template>
                        </b-input-group-append>
                </b-input-group>
                <b-input-group>
                        <b-form-input v-if="toggle" id="box-name-two"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.box_names.box_two"
                                      required
                                      placeholder="Enter Second Fruit Box Name">
                        </b-form-input>
                        <b-input-group-append class="box-appends">
                                <template v-if="toggle">
                                        <b-btn :pressed.sync="toggle" variant="outline-success">Close 2nd box</b-btn>
                                </template>
                        </b-input-group-append>
                </b-input-group>
                <b-input-group>
                        <b-form-input v-if="toggle_two" id="box-name-three"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.box_names.box_three"
                                      required
                                      placeholder="Enter Third Fruit Box Name">
                        </b-form-input>
                        <b-input-group-append class="box-appends">
                                <template v-if="toggle_two">
                                        <b-btn :pressed.sync="toggle_two" variant="outline-success">Close 3rd box</b-btn>
                                </template>
                        </b-input-group-append>
                </b-input-group>
                <b-input-group>
                        <b-form-input v-if="toggle_three" id="box-name-four"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.box_names.box_four"
                                      required
                                      placeholder="Enter Fourth Fruit Box Name">
                        </b-form-input>
                        <b-input-group-append class="box-appends">
                                <template v-if="toggle_three">
                                        <b-btn :pressed.sync="toggle_three" variant="outline-success">Close 4th box</b-btn>
                                </template>
                        </b-input-group-append>
                </b-input-group>
                <!-- Output ready to be submitted - currently visible to spot errors! -->
                <div class="box_names">
                        <pre class="mt-3"> Box names (Ready for submission) : {{ form.box_names }} </pre>
                </div>
                <!-- Company Contact Information -->
                <b-form-group id="company-contacts-group"
                                label="Company Contacts"
                                label-for="company_contacts"
                                description="Add details for primary and (optional) secondary contacts, or any other relevent information.">
                          <b-form-input id="primary-contact-name"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.primary_contact"
                                      placeholder="Enter primary contact name">
                          </b-form-input>
                          <b-form-input id="primary-contact-email"
                                      class="company-inputs"
                                      type="email"
                                      v-model="form.primary_email"
                                      placeholder="Enter primary email">
                          </b-form-input>
                          <b-form-input id="secondary-contact-name"
                                      class="company-inputs"
                                      type="text"
                                      v-model="form.secondary_contact"
                                      placeholder="Enter secondary contact name">
                          </b-form-input>
                          <b-form-input id="secondary-contact-email"
                                      class="company-inputs"
                                      type="email"
                                      v-model="form.secondary_email"
                                      placeholder="Enter secondary email">
                          </b-form-input>
                </b-form-group>
                <!-- Company Delivery Information -->
                <b-form-group id="company-delivery-information"
                              label="Delivery Information"
                              label-for="delivery_information"
                              description="Add details for route and delivery information.">
                            <b-form-textarea id="delivery-information"
                                     v-model="form.delivery_information"
                                     placeholder="Enter some invaluable delivery information"
                                     :rows="3"
                                     :max-rows="6">
                            </b-form-textarea>
                            <pre class="mt-3">{{ form.delivery_information }}</pre>
                            <b-form-textarea id="route-summary-address"
                                     v-model="form.route_summary_address"
                                     placeholder="Enter the full route summary address here."
                                     :rows="3"
                                     :max-rows="6">
                            </b-form-textarea>
                            <pre class="mt-3">{{ form.route_summary_address }}</pre>
                </b-form-group>
                <!-- Company Invoice Information -->
                <b-form-group id="company-invoice-information"
                        label="Invoice Information"
                        label-for="invoice_information"
                        description="Add details for company invoicing.  This is currently misleading as the postcode is being used as the backup for pulling route information but you don't need to know that. :)">
                        <b-form-input id="address-line-1"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.address_line_1"
                                    required
                                    placeholder="Enter address line 1">
                        </b-form-input>
                        <b-form-input id="address-line-2"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.address_line_2"
                                    placeholder="Enter address line 2 (optional)">
                        </b-form-input>
                        <b-form-input id="address-city"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.city"
                                    required
                                    placeholder="Enter city">
                        </b-form-input>
                        <b-form-input id="address-region"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.region"
                                    placeholder="Enter region (optional)">
                        </b-form-input>
                        <b-form-input id="address-postcode"
                                    class="company-inputs"
                                    type="text"
                                    v-model="form.postcode"
                                    required
                                    placeholder="Enter postcode">
                        </b-form-input>
                </b-form-group>
                    <!-- Payment Options (Branding Theme) -->
                <b-form-group id="branding-theme"
                                    label="Branding theme"
                                    label-for="payment-options">
                        <b-form-select id="payment-options"
                                      :options="payment"
                                      required
                                      v-model="form.branding_theme">
                        </b-form-select>
                </b-form-group>
                <!-- Existing Suppliers -->
                <b-form-group id="existing-suppliers"
                                label="Suppliers"
                                label-for="supplier-options">
                        <b-form-select id="supplier-options"
                                      :options="suppliers"
                                      class="company-inputs"
                                      required
                                      v-model="form.supplier_options">
                        </b-form-select>
                        <!-- New Suppliers -->
                <b-input-group id="new-suppliers"
                                label="New Supplier">
                        <b-form-input id="new-supplier"
                                    class="company-inputs"
                                    type="text"
                                    placeholder="Enter new supplier"
                                    v-model="form.new_supplier">
                        </b-form-input>
                        <b-input-group-append class="box-appends">
                                <b-btn @click="addSupplier" variant="outline-success">Add Supplier</b-btn>
                        </b-input-group-append>
                </b-input-group>
                </b-form-group>

          <!-- <b-form-group id="exampleGroup4">
            <b-form-checkbox-group v-model="form.checked" id="exampleChecks">
              <b-form-checkbox value="me">Check me out</b-form-checkbox>
              <b-form-checkbox value="that">Check that out</b-form-checkbox>
            </b-form-checkbox-group>
          </b-form-group> -->

                <b-button type="submit" variant="success">Submit</b-button>
                <b-button type="reset" variant="danger">Reset</b-button>
        </b-form>
    </div>
</template>

<style lang="scss">

    .section-header {
        margin-top: 40px;
    }

    label#company-name-group__BV_label_,
    label#company-contacts-group__BV_label_,
    label#company-delivery-information__BV_label_,
    label#company-invoice-information__BV_label_,
    label#branding-theme__BV_label_,
    label#existing-suppliers__BV_label_,
    label#new-suppliers__BV_label_ {
        font-weight: bold;
    }

    #box-name-one.company-inputs, #box-name-two.company-inputs, #box-name-three.company-inputs, #new-supplier.company-inputs, .company-inputs, .box-appends {
        margin-bottom:5px;
    }

    .add-company-form {
        margin-left: 30px;
        margin-right: 30px;
    }

    .box_names {
        margin-top: 10px;
    }
    // .title-headers {
    //     margin-top: 30px;
    // }
    // .input-group label {
    //     margin-right: 30px;
    // }
    // form div {
    //     margin-top: 10px;
    //     margin-bottom: 10px;
    // }

</style>


<script>
export default {
  data () {
    return {
      form: {
        invoice_name: '',
        route_name: '',
        box_names: {
            box_one: '',
            box_two: '',
            box_three: '',
            box_four: '',
        },
        primary_contact: '',
        primary_email: '',
        secondary_contact: '',
        secondary_email: '',
        delivery_information: '',
        route_summary_address: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        region: '',
        postcode: '',
        branding_theme: null,
        supplier_options: null,
        new_supplier: null,
        // food: null,
        // checked: [],

      },
      payment: [
        { text: 'Select One', value: null },
        'BACS', 'GoCardless', 'GoCardless In Advance', 'Invoiced In Advance',
        'Monthly Invoice', 'Monthly Invoice GoCardless', 'Paypal (Stripe)', 'Paypal In Advance (Stripe)',
        'Standing Order', 'TBC'
      ],
      // Instead of hardcodeing these, I want to create a designated table in the database to pull all existing suppliers and allow the inclusion of a new one.
      suppliers: [
        { text: 'Select One', value: null },
        'Bill Weir Fruits', 'Fresh Produce Essex', 'Get Fresh and Fruity', 'Total Produce Bristol',
        'Anglia Produce', 'Dobsons', 'Oncore', 'Total Produce Cornwall', 'JR Holland', 'Mcdonald Fruit and Veg',
        'Capital Wholesalers', 'DJ Wright Dairies/GC Produce', 'Dannys Fruit and Veg', 'Bunched Carrot',
        'WR Bishop', 'DJ Perks', 'RK Harris', 'The Fruit Basket Nottingham', 'Total Produce Gloucester', 'Arnolds',
        'Tastables', 'Produce Warriors', 'Mikes of Sawbridgeworth', 'Total Produce Sheffield', 'Fruit and Veg Co',
        'Juicy Fresh', 'Par Pak Produce', 'Fresh from the Fields', 'Luv Fresh', 'P & M Round', 'TBC'
      ],

      toggle: false,
      toggle_two: false,
      toggle_three: false,
      new_supplier: '',
      show: true
    }
  },
  methods: {
      addSupplier () {
          console.log(this.form.new_supplier);
          let new_supplier = this.form.new_supplier;
          this.suppliers.unshift( new_supplier );
      },

    onSubmit (evt) {
      evt.preventDefault();
      let self = this;
      // alert(JSON.stringify(this.form));
      axios.post('/api/companies/add-new-company', {
          company_data: self.form,
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
          // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
      }).then(function (response) {
          alert('Uploaded new company successfully!');
          console.log(response.data);
      }).catch(error => console.log(error));
    },
    onReset (evt) {
      evt.preventDefault();
      /* Reset our form values */

      this.form.invoice_name = '';
      this.form.route_name = '';
      this.form.box_names = {};
          this.form.box_names.box_one = '';
          this.form.box_names.box_two = '';
          this.form.box_names.box_three = '';
          this.form.box_names.box_four = '';
      this.form.primary_contact = '';
      this.form.primary_email = '';
      this.form.secondary_contact = '';
      this.form.secondary_email = '';
      this.form.delivery_information = '';
      this.form.route_summary_address = '';
      this.form.address_line_1 = '';
      this.form.address_line_2 = '';
      this.form.city = '';
      this.form.region = '';
      this.form.postcode = '';
      this.form.branding_theme = '';
      this.supplier = '';


      this.form.email = '';
      this.form.name = '';
      this.form.box_names = {};
      this.form.payment = null;
      this.form.checked = [];
      /* Trick to reset/clear native browser form validation state */
      this.toggle = false;
      this.toggle_two = false;
      this.toggle_three = false;
      this.show = false;
      this.$nextTick(() => { this.show = true });
    }
  }
}
</script>
