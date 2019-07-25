<template>
    <div>
        <h3 class="title-headers"> Berry Picklists </h3>
        
        <b-container class="current-values">
            <b-row>
                <b-col>
                    <b v-for="week_start in $store.state.week_start"> Week Start: {{ week_start.current }} </b>
                </b-col>
                <b-col>
                    <b-button variant="primary" href="/api/seasonal-berries-breakdown"> Export Berry Picklists </b-button>
                </b-col>
                <b-col>
                    <b v-for="week_start in $store.state.week_start"> Delivery Days: {{ week_start.delivery_days }} </b>
                </b-col>
            </b-row>
        </b-container>
    </div>
    
</template>

<style>

.title-headers {
    margin-top: 30px;
}
.current-values {
    margin-top: 20px;
    margin-bottom: 30px;
}

</style>

<script>
import axios from 'axios';
export default {
    data () {
        return {
            // week_start: '',
            // selected_delivery_days: this.delivery_days,
        }
    },
    props: ['week_started', 'delivery_days'],
    mounted() {
            console.log('Component Berry Picklist mounted.');
        }
}

</script>