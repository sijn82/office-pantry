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
        <b-row>
            <b-col>
                <div id="buttonsToProcessSnackboxes">
                    <div class="title-headers">
                        <h3> Process Snackbox Orders into Picklists </h3>
                        <p><b> Each button will export a seperate file to your downloads folder. </b></p>
                    </div>
                    <div id="exportMultiCompanyButtons">
                        <b-button variant="outline-primary" href="export-snackbox-op-multicompany"> Export OP Multi Company </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-multicompany"> Export DPD Multi Company </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-multicompany"> Export APC Multi Company </b-button>
                        <b-form-text>
                            This first row of buttons will use the multi-company, single box templates.
                        </b-form-text> 
                    </div>
                    <div id="exportSingleCompanyButtons">
                        <b-button variant="outline-primary" href="export-snackbox-op-singlecompany"> Export OP Single Company </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-singlecompany"> Export DPD Single Company </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-singlecompany"> Export APC Single Company </b-button>
                        <b-form-text>
                            This second row uses the single company, multiple boxes template.
                        </b-form-text> 
                    </div>
                    <div>
                        <h4> Process Otherbox Orders into Checklists or Picklists </h4>
                        <b-button variant="outline-primary" href="export-otherbox-checklist-op"> Export OP Otherboxes (Checklist) </b-button>
                        <b-button variant="outline-primary" href="export-otherbox-checklist-op-weekly-total"> Export OP Otherboxes (Checklist) Weekly Total </b-button>
                        <b-button variant="outline-primary" href="export-otherbox-op-multicompany"> Export OP Otherboxes </b-button>
                        <b-form-text>
                            The checklist is for use when checking deliveries as they arrive, the other for adding to routes (pallets).
                        </b-form-text>
                    </div>
                    <!-- <div id="exportUniqueButtons">
                        <b-button variant="outline-primary" href="export-snackbox-op-unique"> Export OP Unique </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-dpd-unique"> Export DPD Unique </b-button>
                        <b-button variant="outline-primary" href="export-snackbox-apc-unique"> Export APC Unique </b-button>
                        <b-form-text>
                          This final row caters for unique orders, using the same multi-company template as the first row.
                        </b-form-text> 
                    </div> -->
                    <div id="exportWholesaleButtons">
                        <h4> Process Wholesale (Snackbox, Drinkbox & Otherbox) Orders into Picklists </h4>
                        <b-button variant="outline-primary" href="export-wholesale-snackbox-op-singlecompany"> Export OP Snackboxes </b-button>
                        <b-button variant="outline-primary" href="export-wholesale-drinkbox-op-multicompany"> Export OP Drinkboxes </b-button>
                        <b-button variant="outline-primary" href="export-wholesale-otherbox-op-multicompany"> Export OP Otherboxes </b-button>
                        <b-form-text>
                            I can't remember whether we need the wholesale otherbox option but keeping button here for now.
                        </b-form-text>
                    </div>
                </div>
            </b-col>
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
    #buttonsToProcessSnackboxes {
        margin-top: 20px;
        div {
            margin: 10px;
        
            #buttonsToProcessSnackboxes {
                
            }
            #exportMultiCompanyButtons {
            
            }
            #exportSingleCompanyButtons {
            
            }
            #exportUniqueButtons {
            
            }
            #exportWholesaleButtons {
            
            }
        }
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
