<template>
<div id="sdash-wrapper">
    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <!-- drop uploader -->
    <vue-full-screen-file-drop @drop='uploadPanel' formFieldName="file" text="Please drop a JPG, PNG, GIF, TIF or PDF file" v-if="!showAuthorSidebarModel"></vue-full-screen-file-drop>

    <action-bar-top v-if="isLoggedIn"></action-bar-top>

    <router-view></router-view>

    <b-container v-if="!isLoggedIn">
        <b-row>
            <b-col class="text-center">
                <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
            </b-col>
        </b-row>
    </b-container >

        <lightbox
            :visible="isLightboxOpen"
            :imgs="'/panels/' + expandedPanel.id + '/image'"
            @hide="toggleLightbox"
        ></lightbox>


    <feedback-widget></feedback-widget>
    <b-sidebar
        id="author-edit-sidebar"
        right
        shadow
        lazy
        title="Edit the list of authors"
        width="420px"
        bg-variant="dark"
        text-variant="light"
        v-model="showAuthorSidebarModel"
    >
        <panel-authors-edit-form></panel-authors-edit-form>
    </b-sidebar>
</div>

</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import ActionBarTop from '@/components/ActionBarTop'
import PanelGrid from '@/components/PanelGrid'
import InfoBar from '@/components/InfoBar'
import VueFullScreenFileDrop from 'vue-full-screen-file-drop'
import Lightbox from 'vue-easy-lightbox'
import queryStringDehasher from '@/services/queryStringDehasher'
import FeedbackWidget from '@/components/FeedbackWidget'
import PanelAuthorsEditForm from "@/components/authors/PanelAuthorsEditForm";

export default {

    name: 'Dashboard',
    components: { ActionBarTop, PanelGrid, InfoBar, VueFullScreenFileDrop, Lightbox, FeedbackWidget, PanelAuthorsEditForm },
    props: [''],

    data(){

        return {
            //name: value //accessed as this.name internally or name in template

        }

    }, /* end of data */

    computed: {

        ...mapGetters([
            'currentUser',
            'currentGroup',
            'isLoggedIn',
            'isLightboxOpen',
            'expandedPanel',
            'showAuthorSidebar'
        ]),
        showAuthorSidebarModel: {
            set(value){
                this.$store.commit('setAuthorSidebar',value)
            },
            get(){
                return this.showAuthorSidebar
            }
        }


    }, /* end of computed properties */

    methods:{ //run as event handlers, for example
        ...mapActions([
            'uploadNewPanel',
            'toggleLightbox',
            'addSelectedPanelsToGroup',
        ]),
        methodName(){
            //do stuff here
        },
        uploadPanel(formData, files){
            this.uploadNewPanel(formData)
            .then(response => {
                this.$snotify.success("New panel created", "Uploaded")
                if(this.currentGroup) {
                    this.$store.commit("clearSelectedPanels")
                    this.$store.commit("addPanelToSelections", response.data.DATA.id)
                    this.addSelectedPanelsToGroup(this.currentGroup.id)
                      .then(response => {
                          this.$snotify.success("Panel added to this group", "Group updated")
                      })
                      .catch(error => {
                          console.log(error)
                          this.$snotify.error("Cannot add panel to this group", "Update failed")
                      })

                }
                })
                .catch(error => {
                    this.$snotify.error(error.data.errors.file[0], "Upload failed")
                })
        }

    },

    created(){
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
        store.dispatch('fetchCurrentUser')
        .catch((error) => {
            console.log(error)
            this.$snotify.error("We can't find your data. Please try again later.", "Sorry!")
        })

    },

    mounted(){

    }

}
</script>

<style lang="scss">
 main .b-sidebar > .b-sidebar-header {
     font-size:1rem;
 }
</style>