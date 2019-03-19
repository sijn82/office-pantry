<template lang="html">
  <div class="margin-30">
      <h3> Import and Export Stuff </h3>
      <b-row class="padding-top-20">
          <b-col></b-col>
          <b-col>
              <b-button href="api/export-fruitbox-picklists" variant="info"> Export Fruitbox Picklists </b-button>
          </b-col>
          <b-col>
              <b-button href="api/export-routing" variant="info"> Export Routes </b-button>
          </b-col>
          <b-col>
              <b-button href="#" variant="info"> Import Rejigged Routes (Unwritten) </b-button>
          </b-col>
          <b-col></b-col>
      </b-row> 
  </div>
</template>

<style lang="scss" scoped>
    .margin-30 {
        margin: 30px;
    }
    .padding-top-20 {
        padding-top: 20px;
    }
</style>

<script>
export default {
    data () {
        return {
            
        }
    }
}
</script>
