<template>
    <div>
        <info-bar>
            <template v-slot:title>
                <h1>My Dashboard</h1>
            </template>
        </info-bar>

        <b-container fluid class="mt-3">
            <b-row v-if="isLoadingPanels">
                <b-col class="text-center">
                    <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
                </b-col>
            </b-row>
            <b-row v-if="hasPanels">
                <b-col cols="2" class="sd-filter-wrapper"><filter-bar></filter-bar></b-col>
                <b-col>
                    <panel-listing-grid list_root="user"></panel-listing-grid>
                </b-col>
            </b-row>
            <b-row v-if="!hasPanels && !isLoadingPanels">
                <b-col cols="2" class="sd-filter-wrapper"><filter-bar></filter-bar></b-col>
                <b-col >
                    <b-alert show variant="danger" class="no-panel-alert">No Panels Found</b-alert>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>

import store from '@/stores/store'
import { mapGetters } from 'vuex'
import FilterBar from './FilterBar'
import InfoBar from './InfoBar'
import PanelListingGrid from './PanelListingGrid'


export default {

    name: 'PanelGrid',
    components: {
        FilterBar,
        PanelListingGrid,
        InfoBar,
      },
    props: [''],

    data(){

        return {

        }

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
        store.commit("clearSelectedPanels")
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