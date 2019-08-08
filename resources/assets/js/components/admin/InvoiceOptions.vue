<template lang="html">
    <div class="invoice-options">
        <h4> Invoice Options </h4>
        <b-row>
            <b-col>
                <b-button href="/api/office-pantry/weekly-invoicing" variant="outline-primary"> Export Weekly Invoicing </b-button>
            </b-col>
            <b-col>
                <b-button href="/api/office-pantry/confirm-weekly-invoicing" variant="outline-primary"> Confirm Weekly Invoicing </b-button>
                <b-form-text> If you're happy with the exported invoice, click here to confirm and add an invoiced_at date to each entry. </b-form-text>
            </b-col>
        </b-row>
        <h4 class="margin-top-20"> Advance Order Date </h4>
        <p> 
            While this process was set up to run once a week automatically, I'm going to deactivate that feature for now.
            Instead you're going to need to <b> remember to advance it yourselves </b>, when it suits your workflow.
        </p>
        <p>
            As well as advancing orders to their next delivery date (if the current date set on the box has past), it will also archive the pre-existing orders, so we have a record of them.
        </p>
        <p> 
            If the order in question hasn't been invoiced yet, it will become an active archive and appear in the company dashboard (under archives), otherwise it'll be a deactivated archive.
        </p>
        <p>
            I'm yet to write the ability to search through these inactive orders but I will.  For now I'm hoping out of sight, out of mind.
        </p>
        <b-button variant="danger" href="/api/office-pantry/carbon"> Advance Orders! </b-button>
    </div>
</template>

<style lang="scss">
    .flex-center {
        display: block;
    }
    p {
        font-weight: 300;
        margin-left: 30px;
        margin-right: 30px;
    }
    .margin-top-20 {
        margin-top: 20px;
    }
    .invoice-options {
        margin-bottom: 40px;
    }
</style>

<script>
    export default {
        data() {
            return {
            
            }
        },

    }
</script>
