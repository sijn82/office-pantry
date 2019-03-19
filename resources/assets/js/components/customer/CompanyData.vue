<template>
    <div>
        <h3 class="section-header">Company Details</h3>
        <p>User Id: {{ this.userid }} </p>
    
        <div id="company-data" v-for="company in this.companies"> {{ company.company_name }} 
            
            <!-- <ul v-for="(details, key) in company" v-if="details"> -->
                <!-- This revealed something to ponder over, now I have relations sorted between models these will be outputted as well if I don't specify fields!  -->
                <!-- <li> {{ key }} : {{ details }} </li> --> 
                
                <li><b> Company ID: </b> {{ company.id }} </li>
                <li><b> Company Status: </b> {{ company.is_active }} </li>
                <li><b> Company Invoice Name: </b> {{ company.invoice_name }} </li>
                <li><b> Company Route Name: </b> {{ company.route_name }} </li>
                <li><b> Company Primary Contact: </b> {{ company.primary_contact }} </li>
                <li><b> Company Primary Email: </b> {{ company.primary_email }} </li>
                <li><b> Company Secondary Contact: </b> {{ company.secondary_contact }} </li>
                <li v-if="company.secondary_email"><b> Company Secondary Email: </b> {{ company.secondary_email }} </li>
                <li><b> Company Delivery Information: </b> {{ company.delivery_information }} </li>
                <li><b> Company Route Summary Address: </b> {{ company.route_summary_address }} </li>
                <li><b> Company Address Line 1: </b> {{ company.address_line_1 }} </li>
                <li><b> Company Address Line 2: </b> {{ company.address_line_2 }} </li>
                <li><b> Company City: </b> {{ company.city }} </li>
                <li><b> Company Postcode: </b> {{ company.postcode }} </li>
                <li><b> Company Branding Theme: </b> {{ company.branding_theme }} </li>
                <li><b> Company Supplier: </b> {{ company.supplier }} </li>

             </ul></br>
        </div>
    
        <!-- <div v-for="fruitbox in this.fruitboxes"> {{ fruitbox.company_name }} 
            <ul v-for="fruit in fruitbox" v-if="fruit.id">
                 <li><b> Fruitbox ID: </b> {{ fruit.id }} </li>
                 <li><b> Fruitbox Status: </b> {{ fruit.is_active }} </li>
                 <li><b> Fruitbox Name: </b> {{ fruit.name }} </li>
                 <li><b> Fruitbox Company ID: </b> {{ fruit.company_id }} </li>
                 <li><b> Fruitbox Route ID: </b> {{ fruit.route_id }} </li>
                 <li><b> Fruitbox Delivery Day: </b> {{ fruit.delivery_day }} </li>
                 <li><b> Deliciously Red Apples: </b> {{ fruit.deliciously_red_apples }} </li>
                 <li><b> Pink Lady Apples: </b> {{ fruit.pink_lady_apples }} </li>
                 <li><b> Red Apples: </b> {{ fruit.red_apples }} </li>
                 <li><b> Green Apples: </b> {{ fruit.green_apples }} </li>
                 <li><b> Satsumas: </b> {{ fruit.satsumas }} </li>
                 <li><b> Pears: </b> {{ fruit.pears }} </li>
                 <li><b> Bananas: </b> {{ fruit.bananas }} </li>
                 <li><b> Nectarines: </b> {{ fruit.nectarines }} </li>
                 <li><b> Limes: </b> {{ fruit.limes }} </li>
                 <li><b> Lemons: </b> {{ fruit.lemons }} </li>
                 <li><b> Grapes: </b> {{ fruit.grapes }} </li>
                 <li><b> Seasonal Berries: </b> {{ fruit.seasonal_berries }} </li>
             </ul></br>
        </div> -->
        
         <!-- @submit="onSubmit" @reset="onReset" v-if="show"  -->
         <b-form class="add-company-form">
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
                
                <!-- I'm thinking about inputting this information in a different way, so assigning a name is done at the same time as creating the fruitbox order.
                
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
                 Output ready to be submitted - currently visible to spot errors!
                <div class="box_names">
                        <pre class="mt-3"> Box names (Ready for submission) : {{ form.box_names }} </pre>
                </div>
                
                This is where the fruitbox name section ends.  -->
                
                
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
                
                <!-- This section is not for customer eyes!  Should we rely on conditional rendering to hide this from view unless visited by OP office users, or take this functionality to somewhere else entirely? -->
                
                        <!-- Existing Suppliers -->
                <!--
                <b-form-group id="existing-suppliers"
                                label="Suppliers"
                                label-for="supplier-options">
                        <b-form-select id="supplier-options"
                                      :options="suppliers"
                                      class="company-inputs"
                                      required
                                      v-model="form.supplier_options">
                        </b-form-select> -->
                        
                        <!-- New Suppliers -->
                        <!--
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
                </b-form-group> -->
                
                <!-- This is the end of the supplier information -->

                <b-button type="submit" variant="success">Submit</b-button>
                <b-button type="reset" variant="danger">Reset</b-button>
        </b-form>
    </div>
</template>

<style lang="scss">
    #company-data {
        li {
             list-style: none;
            b {
                font-size: 12px;
            }
        }
    }
</style>


<script>
import axios from 'axios';
    export default {
        props: {
            userid: {
                type: Number
            },
            companies: {},
            fruitboxes: {
                type: Array,
                id: '',
                is_active: '',
                name: '',
                company_id: '',
                route_id: '',
                delivery_day: '',
                deliciously_red_apples: '',
                pink_lady_apples: '',
                red_apples: '',
                green_apples: '',
                satsumas: '',
                pears: '',
                bananas: '',
                nectarines: '',
                limes: '',
                lemons: '',
                grapes: '',
                seasonal_berries: '',
                created_at: '',
                updated_at: '',
                
            }
        },
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
                },
                payment: [
                  { text: 'Select One', value: null },
                  'BACS', 'GoCardless', 'GoCardless In Advance', 'Invoiced In Advance',
                  'Monthly Invoice', 'Monthly Invoice GoCardless', 'Paypal (Stripe)', 'Paypal In Advance (Stripe)',
                  'Standing Order', 'TBC'
                ],
            }
        },

        mounted() {
                
                console.log('Component Company Data mounted.');
                
            }
    }
</script>