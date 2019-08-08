<template lang="html">
    <div class="">
        <b-row>
            <!-- Name -->
            <b-col>
                <div class="" v-if="editing">
                    <label> Fruit Partner Name </label>
                    <b-form-input v-model="fruitpartner.name" placeholder="" type="text" required>  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.name }} </p>
                </div>
            </b-col>
            <b-col>
                <b-button variant="primary" size="sm" @click="showDetails"> Details </b-button>
                <b-button variant="warning" size="sm" @click="editDetails"> Edit </b-button>
                <b-button v-if="editing" variant="success" size="sm" @click="saveDetails(fruitpartner)"> Save </b-button>
                <b-button v-if="editing" variant="danger" size="sm" @click="deleteFruitPartner(fruitpartner)"> Delete </b-button>
            </b-col>
        </b-row>
        <b-row class="margin-top-10" v-if="details">
            <!-- Email -->
            <b-col>
                <label> Email </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.email" placeholder="" type="email">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.email }} </p>
                </div>
            </b-col>
            <!-- Telephone -->
            <b-col>
                <label> Telephone </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.telephone" placeholder="" type="tel">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.telephone }} </p>
                </div>
            </b-col>
            <!-- Url -->
            <b-col>
                <label> Url </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.url" placeholder="" type="url">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.url }} </p>
                </div>
            </b-col>
        </b-row>
        <b-row class="margin-top-10" v-if="details">
            <!-- Primary Contact -->
            <b-col>
                <label> Primary Contact First Name </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.primary_contact_first_name" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.primary_contact_first_name }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Primary Contact Surname </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.primary_contact_surname" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.primary_contact_surname }} </p>
                </div>
            </b-col>
            <!-- Secondary Contact -->
            <b-col>
                <label> Secondary Contact First Name </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.secondary_contact_first_name" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.secondary_contact_first_name }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Secondary Contact Surname </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.secondary_contact_surname" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.secondary_contact_surname }} </p>
                </div>
            </b-col>
        </b-row>
        <b-row class="margin-top-10" v-if="details">
            
            <!-- Location -->
            <b-col>
                <label> Address Line 1 </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.address_line_1" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.address_line_1 }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Address Line 2 </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.address_line_2" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.address_line_2 }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Address Line 3 </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.address_line_3" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.address_line_3 }} </p>
                </div>
            </b-col>
        </b-row>
        <b-row class="margin-top-10" v-if="details">
            <b-col>
                <label> City </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.city" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.city }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Region </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.region" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.region }} </p>
                </div>
            </b-col>
            <b-col>
                <label> Postcode </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.postcode" placeholder="" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.postcode }} </p>
                </div>
            </b-col>
        </b-row>
        <b-row class="margin-top-10" v-if="details">
            <!-- Alternative Phone Number -->
            <b-col>
                <label> Alt Phone Number </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.alt_phone" placeholder="" type="tel">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.alt_phone }} </p>
                </div>
            </b-col>
            <!-- Weekly Action -->
            <b-col>
                <label> Weekly Action </label>
                <div class="" v-if="editing">
                    <b-form-select v-model="fruitpartner.weekly_action" :options="weekly_action_options">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.weekly_action }} </p>
                </div>
            </b-col>
            <!-- Changes Action -->
            <b-col>
                <label> Changes Action </label>
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.changes_action" placeholder="">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.changes_action }} </p>
                </div>
            </b-col>
        </b-row>
        <b-row class="margin-top-10 margin-bottom-20" v-if="details">
            <b-col>
                <label> Status </label>
                <!-- I think this should be a dropdown of options rather than a free for all -->
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.status" type="text">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.status }} </p>
                </div>
            </b-col>
            <!-- No Of Customers Supplied -->
            <b-col>
                <!-- We want this value in the database but not so sure this is the right way to document it, or keep accurate -->
                <label> No. of Customers Supplied </label> 
                <div class="" v-if="editing">
                    <b-form-input v-model="fruitpartner.no_of_customers" placeholder="" type="number">  </b-form-input>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.no_of_customers }} </p>
                </div>
            </b-col>
            <!-- Use OP Boxes -->
            <b-col>
                <label> Use OP Boxes </label>
                <div class="" v-if="editing">
                    <b-form-select v-model="fruitpartner.use_op_boxes" :options="yes_no">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.use_op_boxes }} </p>
                </div>
            </b-col>
            <!-- Additional Info -->
            <b-col>
                <label> Additional Info </label>
                <div class="" v-if="editing">
                    <b-form-textarea v-model="fruitpartner.additional_info" placeholder="" :rows="3" :max-rows="6">  </b-form-textarea>
                </div>
                <div class="" v-else>
                    <p> {{ fruitpartner.additional_info }} </p>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>
.margin-bottom-20 {
    margin-bottom: 20px;
}
.margin-top-10 {
    margin-top: 10px;
}
</style>

<script>
export default {
    props: ['fruitpartner'],
    data () {
        return {
            editing: false,
            details: false,
            yes_no: ['Yes', 'No'],
            weekly_action_options: ['Email Only', 'Email and Call', 'Email and Answerphone', 'Email and Text', 'Text Only', 'Call Only', 'Answerphone Only'],
        }
    },
    methods: {
        showDetails() {
            if (this.details == true) {
                this.details = false;
            } else {
                this.details = true;
            }
        },
        // These probably need to go into the fruitpartner template rather than this admin/list.  Will likely move this in a sec.
        editDetails() {
            if (this.editing == false) {
                this.editing = true;
                this.details = true;
            } else {
                this.editing = false;
            }
        },
        saveDetails(fruitpartner) {
            axios.put('/api/office-pantry/fruit-partners/update/' + fruitpartner.id, {
                id: fruitpartner.id,
                name: fruitpartner.name,
                email: fruitpartner.email,
                telephone: fruitpartner.telephone,
                url: fruitpartner.url,
                primary_contact_first_name: fruitpartner.primary_contact_first_name,
                primary_contact_surname: fruitpartner.primary_contact_surname,
                secondary_contact_first_name: fruitpartner.secondary_contact_first_name,
                secondary_contact_surname: fruitpartner.secondary_contact_surname,
                address_line_1: fruitpartner.address_line_1,
                address_line_2: fruitpartner.address_line_2,
                address_line_3: fruitpartner.address_line_3,
                city: fruitpartner.city,
                region: fruitpartner.region,
                postcode: fruitpartner.postcode,
                alternative_telephone: fruitpartner.alternative_telephone,
                weekly_action: fruitpartner.weekly_action,
                changes_action: fruitpartner.changes_action,
                status: fruitpartner.status,
                no_of_customers: fruitpartner.no_of_customers,
                use_op_boxes: fruitpartner.use_op_boxes,
                additional_info: fruitpartner.additional_info,
                
            });
        },
        deleteFruitPartner(fruitpartner) {
            axios.put('/api/office-pantry/fruit-partners/destroy/' + fruitpartner.id, {
                id: fruitpartner.id,
            });
        },
    }
    
}
</script>