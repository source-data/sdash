<template>
    <div>
        <info-bar>
            <template v-slot:title>
                <h1>Published Panels</h1>
            </template>
        <template v-slot:text>
            Browse the latest panels that have been made public by their authors.
        </template>
        </info-bar>

        <b-container fluid class="mt-3">
            <b-row>
                <b-col cols="2" class="sd-filter-wrapper">
                <!--filter-bar></filter-bar-->Filter here
                </b-col>
                <b-col v-if="isLoadingPanels" class="text-center">
                    <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
                </b-col>
                <b-col v-if="hasPanels">
                    <panel-listing-grid list_root="user"></panel-listing-grid>
                </b-col>
                <b-col v-if="!hasPanels && !isLoadingPanels">
                    <b-alert show variant="danger" class="no-panel-alert">No Panels Found</b-alert>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>

import store from '@/public_app/stores/store'
import { mapGetters } from 'vuex'
// import FilterBar from '@/components//FilterBar'
import InfoBar from '@/components/InfoBar'
import PanelListingGrid from './PanelListingGrid'


export default {

    name: 'PanelGrid',
    components: {
        // FilterBar,
        PanelListingGrid,
        InfoBar,
      }, /* end of data */
    computed: {

        ...mapGetters([
            'isLoadingPanels',
            'hasPanels',
            'hasLoadedAllResults',
        ]),

    },
    mounted() {
        store.commit("clearLoadedPanels")
        store.commit("setPagination", true)
        // store.commit("clearSelectedPanels")
        store.commit("setSearchMode", "user")
        store.dispatch("fetchFileCategories")
        store.dispatch("fetchPanelList")
        .catch((error) => {
            this.$snotify.error("We couldn't find any panels for you.", "Sorry!")
        })
    }

}
</script>

<style lang="scss">
    .no-panel-alert {
        max-width:480px;
        margin: 0 auto;
    }

.sd-filter-wrapper {
    flex: 0 0 300px;
    max-width: 300px;
}
</style>