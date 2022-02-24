<template>
    <b-overlay :show="switchingPages" variant="dark">
        <h3 hidden aria-hidden ref="anchor">Panels</h3>

        <ul id="sd-panel-listing-grid" class="panel-grid list-unstyled">
            <panel-listing-grid-item
                v-for="panel in loadedPanels"
                :key="panel.id"
                :panel="panel"
                :batchSelectDisabled="batchSelectDisabled"
                @show-panel-detail="showPanelDetail"
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

        <guided-tour v-if="showGuidedTour"></guided-tour>

        <b-modal
            :id="idPanelDetailModal"
            hide-header hide-footer
            static lazy
        >
            <div class="sd-grid-extra">
                <button
                    type="button"
                    aria-label="Close"
                    class="close sd-grid-extra--close text-light"
                    @click="hidePanelDetail"
                >
                    <span aria-hidden="true">&#10005;</span>
                </button>

                <b-row v-if="!hasPanelDetail">
                    <b-col class="text-center">
                        <b-spinner
                            variant="primary"
                            label="Spinning"
                            class="m-5"
                            style="width: 4rem; height: 4rem;"
                        ></b-spinner>
                    </b-col>
                </b-row>

                <panel-detail v-if="hasPanelDetail"></panel-detail>
            </div>
        </b-modal>
    </b-overlay>
</template>

<script>

import { mapGetters, mapActions, mapMutations } from 'vuex'
import PanelDetail from "@/components/PanelDetail";
import PanelListingGridItem from '@/components/panel-grid/PanelListingGridItem'
import GuidedTour from '@/components/helpers/GuidedTour';
import {getShowGuidedTour} from '@/services/GuidedTourService';

export default {

    name: 'PanelListingGrid',
    components: {
        PanelDetail,
        PanelListingGridItem,
        GuidedTour,
    },
    props: {
        // the details of the panel with this id will be opened when loading the page
        idPanel: {
            type: Number,
            default: null,
        },
        batchSelectDisabled: {
            type: Boolean,
            default: false,
        },
    },
    data(){
        return {
            idPanelDetailModal: 'sd-panel-detail-modal',
            switchingPages: false,
            showGuidedTour: false,
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
        },
        showPanelDetail(panelId) {
            this.$store.dispatch("expandPanel", panelId);
            this.$store.dispatch("loadPanelDetail", panelId);
            this.$bvModal.show(this.idPanelDetailModal);
        },
        hidePanelDetail() {
            this.$bvModal.hide(this.idPanelDetailModal)
            this.$store.dispatch("closeExpandedPanels");
        },
    },

    computed:{
        ...mapGetters([
            'hasPanelDetail',
            'isLoggedIn',
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
        },
    },
    mounted() {
        this.showGuidedTour = (getShowGuidedTour() && this.isLoggedIn);
        if (this.idPanel) {
            this.showPanelDetail(this.idPanel)
        }
    }

}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.panel-grid {
    position: relative;
    width: 100%;

    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
    column-gap: 16px;
    row-gap: 1.5rem;
}

#sd-panel-detail-modal {
    .sd-grid-extra {
        width: 100%;
        background-color: $very-dark-desaturated-blue;
    }
    .sd-grid-extra--close {
        position: absolute;
        font-size: 1.5em;
        top: 1rem;
        right: 2vw;
        opacity: 1;
    }
}

::v-deep #sd-panel-detail-modal .modal-dialog {
    max-width: initial;
    margin: 5rem 0;
}
@media (min-width: 768px) {
    ::v-deep #sd-panel-detail-modal .modal-dialog {
        margin: 5rem 1rem;
    }
}
::v-deep #sd-panel-detail-modal .modal-body {
    padding: 0;
}
</style>