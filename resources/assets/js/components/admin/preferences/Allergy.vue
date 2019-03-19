<template>
    <div class="allergy-item">
        <p> {{ name }} </p>
        <b-button v-if="name != 'None'" size="sm" @click="removeAllergy(id, column)"> Remove </b-button>
    </div>
</template>

<style lang="scss">
    .allergy-item {
        
        p {
            font-weight: 300;
            display: inline-block;
            float: left;
        }
        button {
            display: inline-block;
            float: right;
        }
        
    }
</style>

<script>
    export default {
        props: ['name', 'id', 'column'],
        data() {
            return {
            
            }
        },
        methods: {
            removeAllergy(id, column) {
                this.$store.commit('removePreference', { id, column });
                
                axios.put('api/allergies/' + id, { 
                    id: id,
                }).then (response => {
                    // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                    console.log(response);
                }).catch(error => console.log(error));
            },
        },
    }
</script>