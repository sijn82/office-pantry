<template >
    <div class="invoice-options">
        <b-row class="margin-top-20 border-top">
            <h4 class="margin-top-20 margin-left-right-auto"> Invoice Options </h4>
        </b-row>
        <b-row>
            <b-col>
                <b-button href="/api/office-pantry/weekly-invoicing" variant="outline-primary"> Export Weekly Invoicing </b-button>
                <b-form-text class="margin-left-right-top-20"> All active orders for companies using these branding themes ['BACS', 'GoCardless', 'Paypal (Stripe)', 'Weekly Standing Order', 'Eden Branding Theme']. </b-form-text>
            </b-col>
            <b-col>
                <b-button href="/api/office-pantry/confirm-weekly-invoicing" variant="outline-primary"> Confirm Weekly Invoicing </b-button>
                <b-form-text class="margin-left-right-top-20"> If you're happy with the exported invoice, click here to confirm and add an invoiced_at date to each entry. </b-form-text>
            </b-col>
        </b-row>
        <b-row class="margin-top-20">
            <p>
                While you don't need to confirm the invoicing immediately, I'd still suggest not leaving it too long.  
                It calls the same query, checking all active companies with a matching branding theme, 
                then loops through each current box/archive looking for orders which match the current week start. 
                So make sure the week start is the same one you selected when exporting the invoices!
            </p>
        </b-row>
        <b-row class="margin-top-20 border-top">
            <h4 class="margin-top-20 margin-left-right-auto"> Advance Order Date </h4>
        </b-row>
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
    .margin-left-right-auto {
        margin-left: auto;
        margin-right: auto;
    }
    .margin-left-right-top-20 {
        margin-top: 20px;
        margin-left: 20px;
        margin-right: 20px;
    }
    .border-top {
        border-top: solid 2px #636b6f;
        margin-left: 40px;
        margin-right: 40px;
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
