<template lang="html">
    <div class="office-pantry-product">
        <b-container>
            <b-row class="margin-height-10">
                <b-col>
                    <div v-if="editing">
                        <b-form-input v-model="product.name"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ product.name }} </p>
                    </div>
                </b-col>
                <b-col>
                    <div v-if="editing">
                        <b-form-input v-model="product.price"></b-form-input>
                    </div>
                    <div v-else>
                        <p> {{ product.price }} </p>
                    </div>
                </b-col>
                <b-col>
                    <div v-if="editing">
                        <b-form-select v-model="product.sales_nominal" :options="sales_nominal_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ product.sales_nominal }} </p>
                    </div>
                </b-col>
                <b-col>
                    <div v-if="editing">
                        <b-form-select v-model="product.vat" :options="vat_options"></b-form-select>
                    </div>
                    <div v-else>
                        <p> {{ product.vat }} </p>
                    </div>
                </b-col>
                <b-col>
                    <div v-if="editing">
                        <b-button variant="success" @click="saveChange(product)" size="sm"> Save </b-button>
                    </div>
                    <div v-else>
                        <b-button variant="warning" @click="enableEdit()" size="sm"> Edit </b-button>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<style lang="scss" scoped>
    label, p {
        font-weight: 300;
    }
    .margin-height-10 {
        margin: 10px 0;
    }
</style>

<script>
    export default {
        props: ['product'],
        data () {
            return {
                editing: false,
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
            enableEdit() {
                if (this.editing == false) {
                    this.editing = true;
                    this.details = true;
                } else {
                    this.editing = false;
                }
            },
            saveChange(product) {
                this.editing = false;
                axios.put('api/office-pantry/office-pantry-products/update/' + product.id, {
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    sales_nominal: product.sales_nominal,
                    vat: product.vat,
                }).then (response => {
                    alert('Updated Office Pantry Product Successfully!');
                    console.log(response);
                }).catch(error => console.log(error));
            }
        }
    }
</script>
