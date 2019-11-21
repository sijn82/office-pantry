<template >
    <div>
    <table class="table">
        <tbody>
            <tr v-for="(job, index) in allJobs" :key="index" v-bind:class="{success: job.run, danger: !job.run}">
                <td width="80%">{{ job.payload }}</td>
                <td>{{ job.created_at }}</td>
            </tr>
        </tbody>
    </table>
</div>
</template>

<style lang="scss" scoped>
</style>

<script>
export default {
    props: ['jobs'],
    data() {
        return {allJobs: this.jobs}
    },
    created() {
        let vm = this
        vm.refreshAllJobs = (e) => axios.get('jobs').then((e) => (vm.allJobs = e.data))
        Echo.channel('fruitpartner-queue')
        // Echo.channel('office-pantry-development')
            .listen('.add', (e)  => { vm.refreshAllJobs(e), console.log(e) })
            .listen('.processed', (e) => vm.refreshAllJobs(e))
        console.log(this.jobs)
    }
}
</script>
