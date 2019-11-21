<template >
    <div class="">
        <!-- This is the searchbar to try and find a matching invoice if one exists in the db. -->
        <input v-model.lazy="invoice_name_lookup" type="text" name="" value="Lookup Invoice Name">
        <!--
            If it does, the possible matches will appear in the list below.
            However we don't want multiple matches if the invoice name is already in use for multiple sites.
            So just grab first match.
        -->
        <!-- I don't want this to dominate the page, so I'm going to try and make it a simple list.  If the list item is clicked, a method is called. -->
        <ul>
            <li></li>
        </ul>
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

<!-- But what do I really want it to look like? Is it a searchbar or a dropdown.  Can it be both?
    User Steps
    1. Enters invoice name to check for an existing match.
    2. a) Possible matches appear in dropdown or b) exact match needs to be entered and search commences on 'search' button click?
    3. If a) Click on the invoice name that matches and associated data gets pulled into the relevant fields - form of auto complete.
       If b) Exact match returns and populates the relevant fields (auto complete), or alert pops up to say match wasn't found.
 -->

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
