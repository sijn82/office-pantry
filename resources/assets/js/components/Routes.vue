<template>

    <div>
        <h2 @click="showOptions(selected)">Routing</h2>
        <ul v-if="selected" id="routes-menu">
              <li><a href="api/importFruitOrderingDocument">Import FOD</a></li>
              <li><a href="#">Update Routes</a></li>
              <li><a href="display-routes">Display Routes</a></li>
              <li><a href="#">Export Routes</a></li>
        </ul>
    </div>

</template>

<style lang="scss">

    ul#routes-menu  {
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

  mounted() {
    console.log('Component Routes Mounted')
  }

}
</script>
