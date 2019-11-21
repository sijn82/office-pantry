<template >
    <div>
        <h4> Add New Assigned Route </h4>
        <b-form class="padding-width-40" id="assigned-route-form" @submit="onSubmit" @reset="onReset" v-if="show">
            <b-row>
                <b-col>
                    <label> Route Name </label>
                    <b-form-input v-model="form.name" type="text"></b-form-input>
                </b-col>
                <b-col>
                    <label> Delivery Day </label>
                    <b-form-select v-model="form.delivery_day" :options="days_of_week">
                        <template slot="first">
                                <option :value="null" disabled> Please select an option </option>
                        </template>
                    </b-form-select>
                </b-col>
                <b-col>
                    <label> Tab Order (Export) </label>
                    <b-form-input v-model="form.tab_order" type="number"></b-form-input>
                </b-col>
            </b-row>
            <b-row class="margin-top-20">
                <b-col> {{ form.name }} </b-col>
                <b-col> {{ form.delivery_day }} </b-col>
                <b-col> {{ form.tab_order }} </b-col>
            </b-row>
            <b-button class="margin-top-20" type="submit" variant="primary"> Submit </b-button>
            <b-button class="margin-top-20" type="reset" variant="danger"> Reset </b-button>
        </b-form>
    </div>
</template>

<style lang="scss" scoped>
    .padding-width-40 {
        padding: 0 40px;
    }
    .margin-top-20 {
        margin-top: 20px;
    }
    #assigned-route-form {
        font-weight: 300;
    }
</style>

<script>
export default {
    data () {
        return {
            form: {
                name: '',
                delivery_day: null,
                tab_order: '',
            },
            show: true,
            days_of_week: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        }
    },
    
    methods: {
        onSubmit(evt) {
        evt.preventDefault();
        
        let self = this;
        
        axios.post('/api/office-pantry/assigned-route/add-new-assigned-route', {
            assigned_route: self.form,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Content-Type': 'text/csv'},
            // user_id: self.userData.id // This hasn't been setup yet so proabably won't work yet?!
        }).then(function (response) {
            alert('Uploaded new assigned route successfully!');
            location.reload(true); // <-- Quick fix to update the assigned routes list after submitting new route.
            console.log(response.data);
        }).catch(error => console.log(error));
      },
      onReset(evt) {
        evt.preventDefault()
        /* Reset our form values */
        this.form.name = '';
        this.form.delivery_day = null;
        this.form.tab_order = '';
        /* Trick to reset/clear native browser form validation state */
        this.show = false;
        this.$nextTick(() => {
          this.show = true;
        })
      }
    },
    
    mounted() {
    
    }
    
}
</script>