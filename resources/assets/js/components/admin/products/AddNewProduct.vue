<template>
    <div>
        <b-container style="text-align: center;" sm="12">
            <h3> Add New Product </h3>
            
            <b-form id="new-product-form" @submit="onSubmit" @reset="onReset">
                
                <b-form-group>
                    <b-row sm="12">
                        <!-- Product Name -->
                        <b-col class="col-sm-4" id="product-name">
                            <label> Name </label>
                            <b-form-input id="product-name" type="text" v-model="form.name" placeholder="Enter unique product name" required></b-form-input>
                        </b-col>
                        <!-- Product Code -->
                        <b-col class="col-sm-4" id="product-code">
                            <label> Code </label>
                            <b-form-input id="product-code" type="text" v-model="form.code" placeholder="Enter unique product code" required></b-form-input>
                        </b-col>
                        <!-- Product Stock Level -->
                        <b-col class="col-sm-4" id="product-case-stock-level">
                            <label> Stock Level (Singles) </label>
                            <b-form-input v-model="form.stock_level" type="number"></b-form-input>
                        </b-col>
                    </b-row>
                </b-form-group>
                
                <b-form-group>
                    <b-row sm="12">
                        <b-col class="col-sm-4" id="product-case-price">
                            <!-- Product Case Price -->
                            <label> Product Case Price </label>
                            <b-form-input v-model="form.case_price" type="number"></b-form-input>
                        </b-col>
                        <b-col class="col-sm-4" id="product-case-size">
                            <!-- Product Case Size -->
                            <label> Product Case Size </label>
                            <b-form-input v-model="form.case_size" type="number"></b-form-input>
                            <p class="selected-option"> Product Profit Margin: {{ form.profit_margin = (form.unit_price - form.unit_cost) / form.unit_price * 100 }} % </p>
                        </b-col>
                        <b-col class="col-sm-4" id="product-unit-price">
                            <!-- Product Unit Price -->
                            <label> Product Unit Price </label>
                            <b-form-input v-model="form.unit_price" type="number"></b-form-input>
                            <p class="selected-option"> Product Unit Cost: {{ form.unit_cost = form.case_price / form.case_size }} </p>
                        </b-col>
                    </b-row>        
                </b-form-group>
                <!-- Product Vat? -->
                <b-form-group id="product-vat-nominals">
                    <b-row sm="12">
                        <b-col class="col-sm-4">
                            <label> VAT? </label>
                            <b-form-select v-model="form.vat" :options="vat" required>
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected VAT Option: {{ form.vat }} </p>
                        </b-col>
                        <!-- Sales Nominal -->
                        <b-col class="col-sm-4">
                            <label> Product Sales Nominal </label>
                            <b-form-select v-model="form.sales_nominal" :options="sales_nominal" required>
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected Sales Nominal: {{ form.sales_nominal }} </p>
                        </b-col>
                        <!-- Cost Nominal -->
                        <!-- <b-col class="col-sm-4">
                            <label> Product Cost Nominal </label>
                            <b-form-select v-model="form.cost_nominal" :options="cost_nominal" >
                                <template slot="first">
                                        <option :value="null" disabled> Please select an option </option>
                                </template>
                            </b-form-select>
                                <p class="selected-option"> Selected Cost Nominal: {{ form.cost_nominal }} </p>
                        </b-col> -->
                    </b-row>
                </b-form-group>
                
                <div id="product-buttons">
                    <b-button type="submit" variant="primary"> Submit </b-button>
                    <b-button type="reset" variant="danger"> Reset </b-button>
                </div>
            </b-form>
        </b-container>
    </div>
</template>

<style lang="scss">

#new-product-form {
    label {
        font-weight: 400;
    }
    .form-control {
        font-weight: 300;
    }
}

#new-product-form:after {
    content: ""; /* This is necessary for the pseudo element to work. */
    display: block; /* This will put the pseudo element on its own line. */
    margin: 0 auto; /* This will center the border. */
    width: 70%; /* Change this to whatever width you want. */
    padding-top: 30px; /* This creates some space between the element and the border. */
    margin-bottom: 30px; /*  */
    border-bottom: 1px solid #636b6f; /* This creates the border. Replace black with whatever color you want. */
}
.selected-option {
    padding-top: 10px;
    font-weight: 300;
}
</style>

<script>

export default {
    data () {
        return {

            form: {
                is_active: 'Active',
                code: '',
                name: '',
                case_price: 0.00,
                case_size: 0,
                unit_cost: 0,
                unit_price: 0.00,
                vat: null,
                sales_nominal: null,
                cost_nominal: null,
                profit_margin: '',
                stock_level: 0,
            },
            vat: ['Yes', 'No'],
            sales_nominal: ['4010', '4020', '4040', '4050', '4090'],
            cost_nominal: ['5010', '5020', '5030', '5040'],
        }
    },
    methods: {

        onSubmit (evt) {
          evt.preventDefault();
          let self = this;
          // alert(JSON.stringify(this.form));
          axios.post('/api/products/add-new-product', {
              company_data: self.form,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
              // user_id: self.userData.id // This hasn't been setup yet so probably won't work yet?!
          }).then(function (response) {
              alert('Uploaded new product successfully!');
              location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
              console.log(response.data);
             // self.$emit('update-products'); <-- started this but got no further passing the command to the product list component. Add it to the todo list, for now just reload the page.
          }).catch(error => console.log(error));
        },

        onReset (evt) {
          evt.preventDefault();
          /* Reset our form values */
         
          this.form.code = '';
          this.form.name = '';
          this.form.case_price = 0.00;
          this.form.case_size = 0;
          this.form.unit_cost = 0.00;
          this.form.unit_price = 0.00;
          this.form.vat = null;
          this.form.sales_nominal = null;
          this.form.cost_nominal = null;
          this.form.profit_margin = '';
          this.form.stock_level = 0;
        
        }
    },
}

</script>