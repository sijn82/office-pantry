<template lang="html">

    <div id="fruitpartners">
        <h3> List of Existing Fruit Partners </h3>

        <div v-if="addnew">
            <b-button class="add-new-close" variant="danger" @click="addNew()"> Close </b-button>
            <add-new-fruitpartner @refresh-data="refreshData($event)"></add-new-fruitpartner>
        </div>
        <div v-else class="add-new-close">
            <b-button variant="primary" @click="addNew()"> Add New FruitPartner </b-button>
        </div>

        <div class="fruitpartner-list">

            <fruitpartner class="fruitpartner" v-for="fruitpartner in $store.state.fruit_partners_list" :key="fruitpartner.id" :fruitpartner="fruitpartner" @refresh-data="refreshData($event)"></fruitpartner>

        </div>
        <!-- <div v-else>
            <ul><li style="list-style:none;"> ~ Nothing To See Here ~ </li></ul>
        </div> -->
    </div>
    
</template>

<style lang="scss">

.flex-center {
    display: block;
}
label {
    font-weight: bold;
}
p {
    font-weight: 300;
}
.fruitpartner-list {
    margin: 20px;
    
}

</style>

<script>
export default {
    //props: ['fruitpartners'], // Guessing i'll be sending this through as a prop. <-- ACTUALLY GOING WITH STORE.
    data () {
        return {
            addnew: false,
        }
    },
    methods: {
        refreshData($event) {
            this.$emit('refresh-data', $event);
        },
        addNew() {
            if (this.addnew == false) {
                this.addnew = true;
            } else {
                this.addnew = false;
            }
        },
        
    },
    mounted () {
        console.log('hello?');
        // console.log(this.$store.commit('getFruitPartners'));
        this.$store.commit('getFruitPartners');
    }
}
</script>