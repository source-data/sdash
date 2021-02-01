<template>
<div id="sdash-wrapper">
    <!-- utility component for notifications-->
    <vue-snotify></vue-snotify>
    <action-bar-top></action-bar-top>
    <router-view></router-view>

        <lightbox
            :visible="isLightboxOpen"
            :imgs="'/public-api/panels/' + expandedPanel.id + '/image'"
            @hide="toggleLightbox"
        ></lightbox>

</div>

</template>

<script>

import store from '@/public_app/stores/store'
import { mapGetters, mapActions } from 'vuex'
import PanelGrid from '@/public_app/components/PanelGrid'
import ActionBarTop from '@/public_app/components/ActionBarTop'
import Lightbox from 'vue-easy-lightbox'
import queryStringDehasher from '@/services/queryStringDehasher'

export default {

    name: 'Dashboard',
    components: {
        PanelGrid,
        Lightbox,
        ActionBarTop,
        },
    computed: {

        ...mapGetters([
            'currentGroup',
            'isLightboxOpen',
            'expandedPanel'
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

    methods:{
        ...mapActions([
            'toggleLightbox'
        ])

    },
    created(){
        let query = queryStringDehasher(this.$route)
        if(query) this.$store.commit("setSearchString", query)
    }
}
</script>

<style lang="scss">
 #sdash-wrapper .b-sidebar > .b-sidebar-header {
     font-size:1rem;
 }
</style>