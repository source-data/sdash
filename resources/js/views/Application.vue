<template>
<div id="sdash-wrapper">
    <header>
        <top-bar home-url="/"></top-bar>
        <navigation-bar :user="currentUser"></navigation-bar>
    </header>
    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <!-- vue router mounts components here -->
    <router-view></router-view>

</div>

</template>

<script>
import TopBar from '@/components/TopBar'
import NavigationBar from '@/components/NavigationBar'
import { mapGetters, mapActions } from 'vuex';
import queryStringDehasher from '@/services/queryStringDehasher';

export default {

    name: 'Application',
    components: {TopBar, NavigationBar },
    props: [''],

    data(){

        return {

        }

    }, /* end of data */
    computed: {
        ...mapGetters([
            'currentUser',
            'isLoggedIn',
        ]),
    },
    methods: {
        ...mapActions(['fetchCurrentUser']),
    },
    created(){
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
        this.fetchCurrentUser()
            .then(() => {
                if (!this.currentUser.has_consented) {
                    this.showConsentModal();
                }
            })
            .catch((error) => {
                if(error.status === 401) {
                    console.log('No logged-in user found.');
                } else {
                    this.$snotify.error("We can't find your data. Please try again later.", "Sorry!")
                }
            });
    },


}
</script>

<style lang="scss">

</style>