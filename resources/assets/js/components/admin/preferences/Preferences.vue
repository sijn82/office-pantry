<template>
    <div id="preferences">
         <b-row>
            <b-col>
                <div class="likes">
                    <h4> Likes </h4>
                    <div v-if="$store.state.setPreferences.snackbox_likes != 'None'">
                        <div v-for="like in $store.state.setPreferences.snackbox_likes">
                            <!-- <p > {{ like.snackbox_likes }} </p> -->
                            <preference :name="like.snackbox_likes" 
                                        :id="like.id" 
                                        :column="'snackbox_likes'">
                            </preference>
                        </div>
                    </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_likes"></preference>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="dislikes">
                    <h4> Dislikes </h4>
                    <div v-if="$store.state.setPreferences.snackbox_dislikes != 'None'">
                        <div v-for="dislike in $store.state.setPreferences.snackbox_dislikes">
                            <preference :name="dislike.snackbox_dislikes"
                                        :id="dislike.id" 
                                        :column="'snackbox_dislikes'">
                            </preference>
                        </div>
                    </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_dislikes"></preference>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="essentials">
                    <h4> Essentials </h4>
                    <div v-if="$store.state.setPreferences.snackbox_essentials != 'None'">
                        <div v-for="essential in $store.state.setPreferences.snackbox_essentials">
                            <b-row>
                                <b-col>
                                    <preference :name="essential.snackbox_essentials" 
                                                :quantity="essential.snackbox_essentials_quantity" 
                                                :id="essential.id" 
                                                :column="'snackbox_essentials'">
                                    </preference>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <preference :name="$store.state.setPreferences.snackbox_essentials"></preference>
                    </div>
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <div class="allergies">
                    <h4> Allergies </h4>
                    <div v-if="$store.state.setPreferences.allergies != 'None'">
                        <div v-for="allergy in $store.state.setPreferences.allergies">
                            <b-row>
                                <b-col>
                                    <allergy    :name="allergy.allergy" 
                                                :id="allergy.id" 
                                                :column="'allergies'">
                                    </allergy>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <allergy :name="$store.state.setPreferences.allergies"></allergy>
                    </div>
                </div>
            </b-col>
            <b-col>
                <div class="additional_notes">
                    <h4> Additional Notes </h4>
                    <div v-if="$store.state.setPreferences.additional_notes != 'None'">
                        <div v-for="note in $store.state.setPreferences.additional_notes">
                            <b-row>
                                <b-col>
                                    <additional-info    :name="note.additional_info"
                                                        :id="note.id" 
                                                        :column="'additional_info'">
                                    </additional-info> 
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                    <div v-else>
                        <additional-info :name="$store.state.setPreferences.additional_notes"></additional-info>   
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<style>
    #preferences {
    
    }
</style>

<script>
export default {
    // props:['likes', 'dislikes', 'essentials', 'allergies', 'additional_notes'],
    data() {
        return {
            
        }
    },
    methods: {
        
        // Pretty sure I'm not using this anymore so commenting it out to see if anything breaks.
        
        // removePreference(id){
        //      console.log(id);
        //     // console.log(preference);
        //     axios.post('api/preferences/remove', {
        //         // id: key,
        //         // preference: preference,
        //         // column: column,
        //     }).then(response => {
        //         console.log(response);
        //         // location.reload(true);
        //     }).catch(error => console.log(error));
        // }
    },
    mounted() {
        console.log(this.likes);
    }

}
</script>