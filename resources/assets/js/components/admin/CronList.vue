<template >
    <div class="col-md-12">
        <h1> Cron Data </h1>
        <h5> When orders were last advanced to 'next delivery' date and will next be advanced again. </h5>
        <b-row>
            <b-col>
                <p class="info"> 
                    This is used by Laravel (the system) to determine whether we're due to advance all orders forward to their next scheduled order date. 
                    Heroku (the server host) will check daily (at 12.00am) to see whether the current time is ahead of the 'Next Order Advance Date' and run the function if it is. 
                </p>
                <p class="info">
                    By default the 'Next Order Advance Date is then moved forward 1 week', however you are welcome to alter this date should you need.  The time is automatically set to be '00:00:00' on the day selected.
                </p>
            </b-col>
        </b-row>
        <b-row>
            <b-col> <h4> Command </h4> </b-col>
            <b-col> <h4> Next Order Advance Date </h4> </b-col>
            <b-col> <h4> Last Order Advance Date </h4> </b-col>
            <b-col> </b-col> <!-- Empty column to align headers with content below and allow space for buttons on the right. -->
        </b-row>
        <div v-for="cron_data in $store.state.cron_data">
            <b-row>
                <b-col><p><b> {{ cron_data.command }} </b></p></b-col>
                <!-- The only field it would be useful to edit manually (as an admin) would be the 'next_run', as changing the command would break everything and 'last_run' is otherwise irrelevant. -->
                <b-col> <div v-if="edit"><b-form-input type="date" readonly v-model="next_run"> </b-form-input></div> <div v-else> <p><b> {{ cron_data.next_run }} </b></p></div></b-col>
                <b-col> <p><b> {{ cron_data.last_run }} </b></p> </b-col>
                <b-col>
                    <b-button size="sm" variant="warning" @click="editCronData"> Edit </b-button> 
                    <b-button size="sm" variant="success" @click="saveCronData(cron_data.command)"> Save </b-button>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .info {
        margin: 40px 60px;
        font-weight: 300;
    }
</style>

<script>
    export default {
        data () {
            return {
                edit: false,
                next_run: '',
            }
        },
        methods: {
            editCronData: function () {
                if (this.edit === false) {
                    this.edit = true;
                } else {
                    this.edit = false;
                }
            },
            saveCronData: function (command) {
                console.log(command);
                axios.post('api/office-pantry/cron-data/update', { next_run: this.next_run, command: command }).then( response => {
                    alert('Updated Next Cron Run');
                    location.reload(true);
                }).catch( error => console.log( error ));
            }
        },
        mounted () {
            this.$store.commit('getCronData');
        }
    }
</script>