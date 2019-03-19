<template>
    <div class="preference-item">
        <p> {{ name }} </p>
        <p class="quantity" v-if="quantity > 0"> = {{ quantity }} </p>
        <b-button v-if="name != 'None'" size="sm" @click="removePreference(id, column)"> Remove </b-button>
    </div>
</template>

<style lang="scss">
    .preference-item {
        p {
            font-weight: 300;
            display: inline-block;
            float: left;
        }
        button {
            display: inline-block;
            float: right;
        }
        .quantity {
            padding-left: 10px;
        }
        
    }
</style>

<script>
    export default {
        props: ['name', 'id', 'column', 'quantity'],
        data() {
            return {
            
            }
        },
        methods: {
            removePreference(id, column) {
                console.log(column);
                this.$store.commit('removePreference', { id, column });
                
                axios.put('api/preferences/' + id, { 
                    id: id,
                }).then (response => {
                    // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                    console.log(response);
                }).catch(error => console.log(error));
            },
        },
    }
</script>