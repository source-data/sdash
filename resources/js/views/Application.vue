<template>
<!--
  Having :key=user.id triggers a full reload of this component and all its children - i.e. the whole application -
  every time the user changes. This resets, for example, all the panels that are being shown. See this question
  for details on using :key like this: https://stackoverflow.com/a/54367510/3385618
 -->
<div id="sdash-wrapper" :key="currentUser.id">
    <header>
        <navigation-bar :user="currentUser"></navigation-bar>
    </header>
    <EmailConfirmationNotice v-if="showEmailConfirmationNotice"></EmailConfirmationNotice>
    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <!-- widget for providing feedback to us -->
    <feedback-widget v-if="isLoggedIn"></feedback-widget>
    <!-- loading placeholder while checking for login -->
    <div v-if="!applicationIsLoaded" class="text-center">
        <b-spinner
            variant="primary"
            label="Spinning"
            class="m-5"
            style="width: 4rem; height: 4rem;"
        ></b-spinner>
    </div>
    <!-- vue router mounts components here -->
    <router-view v-if="applicationIsLoaded"></router-view>

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
import NavigationBar from '@/components/NavigationBar'
import EmailConfirmationNotice from '@/components/authentication/EmailConfirmationNotice'
import { mapGetters, mapActions, mapMutations } from 'vuex';
import queryStringDehasher from '@/services/queryStringDehasher';
import FeedbackWidget from '@/components/FeedbackWidget';

export default {

    name: 'Application',
    components: {NavigationBar, EmailConfirmationNotice, FeedbackWidget, },
    data() {
        return {
            confCheckbox1: false,
            confCheckbox2: false,
            confCheckbox3: false,
        }
    },
    computed: {
        ...mapGetters([
            'currentUser',
            'isLoggedIn',
            'applicationIsLoaded',
            'hasVerifiedEmail',
        ]),
        showEmailConfirmationNotice(){
            return this.isLoggedIn && !this.hasVerifiedEmail;
        },
        hasAcceptedTerms() {
            return this.confCheckbox1 && this.confCheckbox2 && this.confCheckbox3;
        },
    },
    methods: {
        ...mapActions(['fetchCurrentUser']),
        ...mapMutations(['setApplicationLoaded']),
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
        },
    },
    created() {
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
    },
    mounted() {
        if (this.isLoggedIn && !this.currentUser.has_consented) {
            this.showConsentModal();
        }
    },
}
</script>

<style lang="scss">

</style>