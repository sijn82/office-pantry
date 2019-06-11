<template>
    <div class="additional-info-item">
        <b-row>
            <b-col>
                <p> {{ name }} </p>
                <b-button v-if="name != 'None'" size="sm" @click="removeAdditionalInfo(id, column)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<style lang="scss">
    .additional-info-item {
        
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
            removeAdditionalInfo(id, column) {
                this.$store.commit('removePreference', { id, column }); // Again, if i'm moving from Store, I need to find a different way to update the view.
                
                axios.put('api/additional-info/' + id, { 
                    id: id,
                }).then (response => {
                    this.$emit('refresh-data');
                    // location.reload(true); // This refreshes the browser and pulls the updated variables from the database into the vue component.
                    console.log(response);
                }).catch(error => console.log(error));
            },
        },
    }
</script>