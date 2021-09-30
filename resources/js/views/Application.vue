<template>
<div id="sdash-wrapper">
    <header>
        <top-bar home-url="/"></top-bar>
        <navigation-bar v-if="isLoggedIn" :user="currentUser"></navigation-bar>
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
    methods:{ //run as event handlers, for example

        methodName(){
            //do stuff here
        }

    }

}
</script>

<style lang="scss">

</style>