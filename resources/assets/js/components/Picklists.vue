<template>

    <div >
        <h2 class="top-border"><button type="button" class="btn btn-info" @click="showOptions(selected)">Picklists</button></h2>
        <ul v-if="selected" id="picklist-menu">
              <li><a href="api/importFruitOrderingDocument">Import FOD</a></li>
              <li><a href="api/importPicklist">Manually Import Picklists (Optional)</a></li>
              <li><a href="api/picklists-vs-fod">Update Picklists</a></li>
              <li><a href="api/export-picklists">Export Picklists</a></li>
        </ul>
    </div>

</template>

<style lang="scss">

.top-border {
  // border-top: 1px solid #636b6f;
  // margin: auto;
}
ul#picklist-menu  {
  list-style: none;
  padding-left: 0px;
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

<script>

export default {

  data () {
    return {
      selected: false,
    }
  },

methods: {
  showOptions: function(selected) {
    if (this.selected == false) {
      console.log(selected);
      return this.selected = true;
    } console.log(selected); return this.selected = false;
  }
},

}

</script>
