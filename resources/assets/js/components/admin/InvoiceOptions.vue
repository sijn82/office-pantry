<template lang="html">
    <div class="">
        <h4> Invoice Options </h4>
        <b-row>
            <b-col>
                <b-button href="api/weekly-invoicing" variant="outline-primary"> Export Weekly Invoicing </b-button>
            </b-col>
            <b-col>
                <b-button href="api/confirm-weekly-invoicing" variant="outline-primary"> Confirm Weekly Invoicing </b-button>
                <b-form-text> If you're happy with the exported invoice, click here to confirm and add an invoiced_at date to each entry. </b-form-text>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    export default {
        
    }
</script>