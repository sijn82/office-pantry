<template lang="html">
    <div class="">
        <input type="text" name="" value="Lookup Invoice Name">
    </div>
</template>

<!-- Below is a reference to a similar component I've already made but it will still need its own improved way of working -->

<!-- <b-input-group id="company-searchbar" class="col-sm-12" size="lg" append=" Company Search">
    <b-form-input type="text" v-model.lazy="keywords"></b-form-input>
</b-input-group>
<b-list-group v-if="results.length > 0">
    <div class="col-sm-12" v-for="result in results">
        <b-list-group-item class="d-flex justify-content-between align-items-center" button :key="result.id" @click="officeData(result.id)"> {{ result.route_name }}
            <b-badge variant="primary" pill> Get Data </b-badge>
        </b-list-group-item>
    </div>
</b-list-group> -->

<style lang="scss" scoped>

</style>

<script>
export default {
        props:[],
        data() {

            return {

            }
        }
}
</script>
