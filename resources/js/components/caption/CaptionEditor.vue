<template>
    <div>
        <b-form-textarea
            id="sd-edit-caption-textarea"
            v-model="captionText"
            placeholder="Enter a description..."
            rows="3"
            max-rows="9"></b-form-textarea>

        <div class="sd-caption-edit-actions-wrapper">
            <span class="sd-caption-edit-smart-wrapper mr-4">
                <label for="smart-tag-toggle-switch">
                    Suggest SmartTags?
                </label>

                <toggle-button
                    id="smart-tag-toggle-switch"
                    :sync="true"
                    v-model="smartTagToggle"
                    :labels="{checked:'Yes', unchecked:'No'}"
                    :width="55"
                    :font-size="14" />
            </span>

            <b-button variant="success" small @click="saveCaptionChanges" :disabled="disableSave">
                Save
            </b-button>

            <b-button variant="light" small @click="cancelCaptionChanges">
                Cancel
            </b-button>
        </div>
    </div>
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
                })

            }

            let updatedPanel = Object.assign({}, this.expandedPanel)
            updatedPanel.caption = this.captionText
            this.$store.dispatch("updatePanel", updatedPanel).then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Update succeeded")
                    this.$store.commit("toggleEditingCaption", false)
                }).catch(error => {
                    this.$snotify.error(error.message, "SmartFigure Update Failed")
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

<style lang="scss" scoped>
    .sd-caption-edit-actions-wrapper {
        text-align:right;
        padding-top: 0.5rem;
    }
</style>