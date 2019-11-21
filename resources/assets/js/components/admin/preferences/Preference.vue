<template>
    <div class="preference-item" v-if="quantity > 0">
        <b-row>
            <b-col>
                <p> {{ name }} </p>
            </b-col>
            <b-col>
                <p class="quantity"> = {{ quantity }} </p>
            </b-col>
            <b-col>
                <b-button v-if="name != 'None'" size="sm" @click="removePreference(id, column)"> Remove </b-button>
            </b-col>
        </b-row>
    </div>
    <div class="preference-item" v-else>
        <b-row>
            <b-col>
                <p> {{ name }} </p>
            </b-col>
            <b-col>
                <b-button v-if="name != 'None'" size="sm" @click="removePreference(id, column)"> Remove </b-button>
            </b-col>
        </b-row>
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
        props: ['name', 'column', 'quantity', 'id'],
        data() {
            return {

            }
        },
        methods: {
            removePreference(id, column) {
                console.log(id);
                this.$store.commit('removePreference', { id, column }); // If I continue to move this away from the Store, I'll need to rethink this line.

                axios.put('api/company/preferences/remove/' + id, { 
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
