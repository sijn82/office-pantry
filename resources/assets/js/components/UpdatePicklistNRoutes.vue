<!-- HTML/PHP -->

<template>

  <div>
      <h2 @click="showOptions(selected)">Update Picklists and Routes</h2>
      <ul v-if="selected" id="update-picklist-n-routes-menu">
            <li><a href="api/import-fod">(i): Import FOD</a></li>
            <li><a href="api/picklists-vs-fod">(ii): Update Picklists with FOD</a></li>
            <li><a href="api/update-routing">(iii): Update Routes with FOD</a></li>
            <li><a href="api/import-drinks-n-snacks">(iv): Add Snacks and Drinks to Routes</a></li>
            <li><a href="api/export-routing">(v): Export Routing</a></li>
            <li><a href="api/rejig-routing">(vi): Import Updated Route Order</a></li>
            <li><a href="api/update-picklists-with-routes">(vii): Update Picklist with Rejigged Routes</a></li>
            <li><a href="api/export-picklists">(viii): Export New Picklists</a></li>
            <li><a href="api/export-routing">(ix): Export New Routing</a></li>
      </ul>
  </div>

</template>

<!-- SASS -->

<style lang="scss">

// .flex-center {
//   display: block;
// }

ul#update-picklist-n-routes-menu {
  list-style: none;
  li {
    display: inline-flex;
    padding: 20px;
    a {
      text-decoration: none;
      font-size: 30px;
      font-weight: bold;
      color: #636b6f;
    }
  }
}

</style>

<!-- Javascript -->

<script>
export default {

  data () {
    return {
      selected: false,
    }
  }, // End of data variables and values

  methods: {
    showOptions: function(selected) {
      if (this.selected == false) {
        console.log(selected);
        return this.selected = true;
      } console.log(selected); return this.selected = false;
    }
  }, // End of methods

  mounted() {
      console.log('Component Picklist N Routes mounted')
  } // End of mounted

} //End of export default

</script>
