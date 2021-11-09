<template>
    <div>
            <!-- <tr v-for="(fruitpartner, index) in " :key="index" >
                <td width="80%">{{ fruitpartner.name }}</td>
                <td>{{ fruitpartner.created_at }}</td>
            </tr> -->
        <b-button class="button-process" variant="primary" @click="createFruitPartnerJobs()"> Process Fruit Partner Orders </b-button>
        <b-progress v-if="count !== 0 && count < max" :value="count" :max="max" show-progress animated></b-progress>
        <b-button class="button-download" variant="success" v-if="remainingFruitPartners.length == 0" href="/api/office-pantry/fruit-partners-export/download-zip"> Download Fruit Partner Orders </b-button>
        <!-- <ul v-for="(fruitpartner, index) in remainingFruitPartners" :key="index" >
            <li width="80%"><b>{{ fruitpartner.name }}</b></li>
        </ul> -->
        <b-button class="button-download" variant="warning" href="/api/office-pantry/fruit-partners-export/download-zip"> Emergency Download Fruit Partner Orders </b-button>
</div>
</template>

<style lang="scss" scoped>

    .button-process {
        margin-bottom: 20px;
    }
    .button-download {
        margin-bottom: 20px;
    }
</style>

<script>
export default {
    //props: ['fruitpartners'],
    data() {
        return {
            //allJobs: this.jobs,
            remainingFruitPartners: [],
            count: 0,
            max: 1,
        }
    },
    methods: {
        createFruitPartnerJobs: function () {
            axios.get('/api/office-pantry/create-fruitpartner-export-jobs');
        },
        grabFruitPartners: function () {
            axios.get('/api/office-pantry/fruit-partner-deliveries/export').then( response => {
                this.fruitpartners = response.data,
                console.log(this.fruitpartners),
                this.remainingFruitPartners = this.fruitpartners,
                this.max = this.fruitpartners.length

            });
        }

    },
    created() {
        let vm = this

        vm.grabFruitPartners();
        // vm.refreshAllJobs = (e) => axios.get('jobs').then((e) => (vm.allJobs = e.data))
        // vm.allFruitPartners = (e) => axios.get('api/office-pantry/fruit-partners/select').then((e) => {vm.fruitpartners = e.data, console.log(vm.fruitpartners)})
        Echo.channel('fruitpartner-queue')
         //Echo.channel('office-pantry-development')
            .listen('.add', (e)  => { vm.refreshAllJobs(e), console.log(e) })
            .listen('.processed', (e) => {
                //console.log(e.fruitpartner.name)
                //vm.refreshAllJobs(e),
                var index = vm.remainingFruitPartners.map(x => {
                  return x.id;
              }).indexOf(e.fruitpartner.id);

                vm.remainingFruitPartners.splice(index, 1);
                vm.count++;
                // console.log(e.fruitpartner.name)
                // console.log(vm.count);
            })
        //console.log(vm.remainingFruitPartners)
    }
}
</script>
