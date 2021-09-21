<template>
<div id="sdash-wrapper">
    <header>
        <top-bar home-url="/dashboard"></top-bar>
        <navigation-bar :user="currentUser"></navigation-bar>
    </header>

    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <!-- drop uploader -->
    <vue-full-screen-file-drop
        @drop='uploadPanel'
        formFieldName="file"
        text="Please drop a JPG, PNG, GIF, TIF or PDF file"
        v-if="panelDropEnabled">
    </vue-full-screen-file-drop>


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

    <b-modal id="sd-consent-modal" ref="sd-consent-modal" size="lg">
        <template #modal-header>
            <h5 class="modal-title">Privacy Notification</h5>
        </template>
        <p>This site is designed to facilitate the submission and sharing of scientific figures.
            It collects Personally Identifiable Information such as the names, e-mail addresses,
            organizational affiliations, IP addresses, and Open Researcher and Contributor IDs (ORC ID)
            of those submitting content (the "PI Information").</p>
        <p>This PI Information is collected to facilitate communications regarding posted figures,
            to track/report on submissions and system usage. This PI information may be shared with
            the data controller (EMBO) and other registered users. The PI Information stored in relationship
            to a figure submission might be used in case of ethics concerns or violations.</p>
        <p>Please confirm the following:</p>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="confirmation-1" v-model="confCheckbox1">
            <label class="custom-control-label" for="confirmation-1">
                I agree with the conditions above and confirm my personal information is correct.</label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="confirmation-2" v-model="confCheckbox2">
            <label class="custom-control-label" for="confirmation-2">
                I will obtain consent from all persons and entities that may have intellectual property rights
                pertaining to the content I will post and share on this platform.</label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="confirmation-3" v-model="confCheckbox3">
            <label class="custom-control-label" for="confirmation-3">
                I will obtain permission from any relevant co-authors before publicly posting or sharing content.</label>
        </div>
        <template #modal-footer>
            <b-button variant="primary" size="sm" class="float-right" :disabled="!hasAcceptedTerms" @click="submitConsent">Accept</b-button>
        </template>
    </b-modal>
</div>

</template>

<script>

import Axios from "axios"
import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import PanelGrid from '@/components/PanelGrid'
import InfoBar from '@/components/InfoBar'
import VueFullScreenFileDrop from 'vue-full-screen-file-drop'
import Lightbox from 'vue-easy-lightbox'
import queryStringDehasher from '@/services/queryStringDehasher'
import FeedbackWidget from '@/components/FeedbackWidget'
import PanelAuthorsEditForm from "@/components/authors/PanelAuthorsEditForm"
import TopBar from '@/components/TopBar'
import NavigationBar from '@/components/NavigationBar'

export default {

    name: 'Dashboard',
    components: { PanelGrid, InfoBar, VueFullScreenFileDrop, Lightbox, FeedbackWidget, PanelAuthorsEditForm, TopBar, NavigationBar },
    props: [''],

    data() {

        return {
            confCheckbox1: false,
            confCheckbox2: false,
            confCheckbox3: false,
        }

    }, /* end of data */

    computed: {

        ...mapGetters([
            'currentUser',
            'currentGroup',
            'mayAddPanelToGroup',
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
        },
        hasAcceptedTerms() {
            return this.confCheckbox1 && this.confCheckbox2 && this.confCheckbox3;
        },
        panelDropEnabled() {
            // Disallow file dropping if the sidebar to edit a panel's authors is open.
            if (this.showAuthorSidebarModel) {
                return false;
            }
            // Disallow file dropping if we're on a group's page and not allowed to add panels to it.
            if (this.currentGroup && ! this.mayAddPanelToGroup) {
                return false;
            }
            return true;

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
        },
        submitConsent() {
            const user = this.currentUser;
            user.has_consented = 1;
            Axios.patch('/users/' + user.id + '/consent', {
                    has_consented: user.has_consented
                })
                .then(response => {
                    this.$store.commit('setCurrentUser', user)
                    this.hideConsentModal();
                });
        },
        showConsentModal() {
            this.$refs['sd-consent-modal'].show()
        },
        hideConsentModal() {
            this.$refs['sd-consent-modal'].hide()
        }
    },

    created(){
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
        store.dispatch('fetchCurrentUser')
            .then(() => {
                if (!this.currentUser.has_consented) {
                    this.showConsentModal();
                }
            })
            .catch((error) => {
                console.log(error)
                this.$snotify.error("We can't find your data. Please try again later.", "Sorry!")
            });
    },
}
</script>

<style lang="scss">
 main .b-sidebar > .b-sidebar-header {
     font-size:1rem;
 }
</style>