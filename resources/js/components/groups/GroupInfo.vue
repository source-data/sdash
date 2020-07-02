<template>

<div v-if="hasUserGroups">
    <group-listing :group_id="group_id"></group-listing>
</div>

</template>

<script>
import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import InfoBar from '../InfoBar'
import GroupListing from './GroupListing'

export default {

    name: 'GroupInfo',
    props: ['group_id'],
    components: {
        InfoBar,
        GroupListing,
    },
    computed: {

        ...mapGetters([
            'userGroups',
            'confirmedUserGroups',
            'currentGroup',
            'isLoadingPanels',
            'hasPanels',
            'hasLoadedAllResults',
            ]),
        hasUserGroups () {
            return this.confirmedUserGroups.length > 0
        }

    },
    created () {
        store.commit("setSearchMode", "group")
    }

}
</script>

<style lang="scss">

</style>