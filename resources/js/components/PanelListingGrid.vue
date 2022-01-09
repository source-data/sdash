<template>
    <b-overlay :show="switchingPages" variant="dark">
        <h3 hidden aria-hidden ref="anchor">Panels</h3>
        <ul id="sd-panel-listing-grid" class="panel-grid list-unstyled">
            <panel-listing-grid-item
                v-for="panel in loadedPanels"
                :key="panel.id"
                :panel-id="panel.id"
            ></panel-listing-grid-item>
        </ul>

        <b-pagination
            v-if="paginate"
            v-model="currentPage"
            :total-rows="totalPanels"
            :per-page="pageSize"
            @change="scrollToTop"
            pills align="center"
            aria-controls="sd-panel-listing-grid"
        ></b-pagination>
    </b-overlay>
</template>

<script>

import { mapGetters, mapActions, mapMutations } from 'vuex'
import InfiniteLoading from 'vue-infinite-loading'
import PanelListingGridItem from './PanelListingGridItem'

export default {

    name: 'PanelListingGrid',
    components: { PanelListingGridItem, InfiniteLoading },
    props: ['list_root'],
    data(){
        return {
            switchingPages: false,
        }
    }, /* end of data */

    methods:{
        ...mapActions([
            'fetchPanelList',
        ]),
        ...mapMutations([
            'setCurrentPage',
            'setPanelLoadingState',
        ]),
        scrollToTop(page) {
            window.scrollTo({top: 0, left: 0, behavior: "smooth"});
        }
    },

    computed:{
        ...mapGetters([
            'loadedPanels',
            'pageSize',
            'paginate',
            'totalPanels',
        ]),
        currentPage: {
            set(page) {
                this.setCurrentPage(page);
                this.switchingPages = true;
                this.fetchPanelList().then(response => {
                    this.switchingPages = false;
                });
            },
            get() {
                return this.$store.getters.currentPage;
            }
        }

    }

}
</script>

<style lang="scss" scoped>
.panel-grid {
    position: relative;
    width: 100%;

    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
    column-gap: 16px;
    row-gap: 1.5rem;
}
</style>