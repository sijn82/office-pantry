<template>
    <div class="">
        <b-button v-if="!add_product" variant="primary" @click="addProduct()"> Add New Office Pantry Product </b-button>
        <b-button v-if="add_product" variant="danger" @click="addProduct()"> Close </b-button>
        <b-form v-if="add_product">
            <b-row>
                <b-col>
                    <label> Name </label>
                    <b-form-input type="text" v-model="form.name"> </b-form-input>
                </b-col>
                <b-col>
                    <label> Price </label>
                    <b-form-input type="number" v-model="form.price"> </b-form-input>
                </b-col>
                <b-col>
                    <label> Sales Nominal </label>
                    <b-form-select v-model="form.sales_nominal" :options="sales_nominal_options">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-col>
                <b-col>
                    <label> Vat? </label>
                    <b-form-select v-model="form.vat" :options="vat_options">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-col>
                <b-col class="margin-auto">
                    <b-button variant="success" @click="saveProduct(form)"> Save </b-button>
                </b-col>
            </b-row>
        </b-form>
    </div>
</template>

<style lang="scss" scoped>
    .margin-auto {
        margin: auto;
    }
</style>

<script>
export default {
    data() {
        return {
            add_product: false,
            form: {
                name: null,
                price: null,
                sales_nominal: null,
                vat: null,
            },
            sales_nominal_options: [
                '4010',
                '4020',
                '4040',
                '4050',
                '4090',
                '4100',
            ],
            vat_options: [
                'Yes',
                'No',
            ],
        }
    },
    methods: {
        addProduct() {
            if (this.add_product == false) {
                this.add_product = true
            } else {
                this.add_product = false
            }
        },
        saveProduct() {
            this.add_product = false;
            axios.put('/api/office-pantry/office-pantry-products/new', {
                name: this.form.name,
                price: this.form.price,
                sales_nominal: this.form.sales_nominal,
                vat: this.form.vat,
            }).then (response => {
                alert('Saved Office Pantry Product Successfully!');
                this.$emit('refresh-data');
                console.log(response);
            }).catch(error => console.log(error));
        }
    }
}
</script>
