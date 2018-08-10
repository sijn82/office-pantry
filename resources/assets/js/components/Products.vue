<template>
  <div class="products-container">

  <!-- Looks pretty but unless I can filter ul li's dynamically, it's no good to me right now. -->
    <!-- <div>
      <v-select label="name" :options="products"></v-select>
    </div> -->
  <!-- End of rainy day code. -->


    <ul v-for="product in products">
      <!-- <div v-if="product.name == inputtedSearchQuery"> -->
        <li> <strong> {{ product.name }} </strong> </li>
        <li class="no-list-style">
          <strong> Unit Price: </strong> {{ product.unit_price }} -
          <strong> Unit Cost: </strong> {{ product.unit_cost }}
        </li>
        <li :class="{
          'none-in-stock' : matchNumberToColour(product.stock_level),
          'some-in-stock' : !matchNumberToColour(product.stock_level)}">
          <strong> Stock Level: {{ product.stock_level }} </strong>
        </li>
      <!-- </div> -->
    </ul>
  </div>
</template>

<style>
  .flex-center {
    display: block;
  }
  .no-list-style {
    list-style: none;
  }
  .none-in-stock {
    color: red;
  }
  .some-in-stock {
    color: green;
  }
</style>

<script>

import axios from 'axios';

export default {
  data() {
    return {
        products: [],

      // product: {
      //   id: '',
      //   is_active: '',
      //   code: '',
      //   name: '',
      //   case_price: '',
      //   case_size: '',
      //   unit_cost: '',
      //   vat: '',
      //   sales_nominal: '',
      //   cost_nominal: '',
      // }

    }
  },

  methods: {
    productSearch: function() {
        var self=this;
        if (products.product.name == this.product) {
          console.log(this.product);
          return this.product;
          };
    },
    matchNumberToColour(number) {
        if (number < 1) {
          return true;
        }
    }
  },

  filters: {
    productFilter() {

    }

  },

  created() {
    let self = this;
    axios.get('/api/products').then( response => {
    // console.log(response);
    self.products = response.data;
    console.log(self.products)
            }).catch(error => console.log(error));
  },

  mounted() {
    console.log('Component Products Mounted');
  }
}

</script>
