<template>
<div id="sdash-wrapper">
    <header>
        <top-bar home-url="/"></top-bar>
        <navigation-bar :user="currentUser"></navigation-bar>
    </header>
    <EmailConfirmationNotice v-if="showEmailConfirmationNotice"></EmailConfirmationNotice>
    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <!-- widget for providing feedback to us -->
    <feedback-widget></feedback-widget>
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

</div>

</template>

<script>
import TopBar from '@/components/TopBar'
import NavigationBar from '@/components/NavigationBar'
import EmailConfirmationNotice from '@/components/authentication/EmailConfirmationNotice'
import { mapGetters, mapActions, mapMutations } from 'vuex';
import queryStringDehasher from '@/services/queryStringDehasher';
import FeedbackWidget from '@/components/FeedbackWidget';

export default {

    name: 'Application',
    components: {TopBar, NavigationBar, EmailConfirmationNotice, FeedbackWidget, },
    computed: {
        ...mapGetters([
            'currentUser',
            'isLoggedIn',
            'applicationIsLoaded',
            'showEmailConfirmationNotice',
        ]),
    },
    methods: {
        ...mapActions(['fetchCurrentUser']),
        ...mapMutations(['setApplicationLoaded', 'setEmailConfirmationNotice']),
    },
    created(){
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
    },


}
</script>

<style lang="scss">

</style>