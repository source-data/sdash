<template>
    <section>
        <div class="sd-caption-edit-smart-wrapper">
            <label for="smart-tag-toggle-switch">Suggest SmartTags?</label>    <toggle-button id="smart-tag-toggle-switch" :sync="true" v-model="smartTagToggle" :labels="{checked:'Yes', unchecked:'No'}" :width="55" :font-size="14"/>
        </div>
        <b-form-textarea
            id="sd-edit-caption-textarea"
            v-model="captionText"
            placeholder="Enter a caption..."
            rows="3"
            max-rows="3"
        >
        </b-form-textarea>
        <div class="sd-caption-edit-actions-wrapper">
            <b-button variant="success" small @click="saveCaptionChanges" :disabled="disableSave">Save</b-button>
            <b-button variant="light" small @click="cancelCaptionChanges">Cancel</b-button>
        </div>
    </section>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'

export default {

    name: 'CaptionEditor',

    data(){

        return {
            captionText: "",
            smartTagToggle: true,

        }

    }, /* end of data */
    computed:{
        ...mapGetters(['expandedPanel', 'editingCaption']),
        disableSave(){
            return this.captionText === this.expandedPanel.caption && this.smartTagToggle===false
        },
    },
    methods:{ //run as event handlers, for example

        saveCaptionChanges(){
            if(this.smartTagToggle) {

                this.smartTagToggle = false

                this.$store.dispatch("fetchSmartTags", this.captionText).then(response => {
                    if(response.length < 1){
                        this.$snotify.info("SmartTags found no auto-taggable entities", "No tags found")
                    } else {
                        this.$store.commit("suggestedSmartTags", response)
                        this.$snotify.success("SmartTags generated", "SmartTags Succeeded")
                    }


                }).catch(error => {
                    this.$snotify.error("The SmartTag service failed to process this request", "Unavailable")
                    console.log(error)
                })

            }

            let updatedPanel = Object.assign({}, this.expandedPanel)
            updatedPanel.caption = this.captionText
            this.$store.dispatch("updatePanel", updatedPanel).then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Update succeeded")
                    this.$store.commit("toggleEditingCaption", false)
                }).catch(error => {
                    console.log(error)
                    this.$snotify.error(error.message, "Panel Update Failed")
                })

        },
        cancelCaptionChanges(){
            this.$store.commit("toggleEditingCaption")
            this.captionText = this.expandedPanel.caption
        }

    },
    mounted(){
        this.captionText = this.expandedPanel.caption
    }

}
</script>

<style lang="scss">
    .sd-caption-edit-actions-wrapper {
        text-align:right;
        padding: 6px 0;
    }

    .sd-caption-edit-smart-wrapper {
        text-align:right;

    }

</style>