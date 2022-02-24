<template>
    <div :class="{ 'anonymous-user': !isLoggedIn }">
        <panel-drop-zone></panel-drop-zone>

        <panel-authors-edit-modal></panel-authors-edit-modal>

        <filter-bar ref="filterBar"></filter-bar>

        <header class="sd-view-title">
            <panel-action-bar v-if="isLoggedIn"></panel-action-bar>

            <section v-else id="sd-featured-jumbotron">
                <h1 class="text-xl text-primary">
                    Figures at the heart of scientific exchange
                </h1>

                <div class="text-md">
                    Add SmartFigures to your dashboard, link them to the underlying source data and receive new ideas
                    from your peers. SDash is a pilot platform developed by SourceData at EMBO. Register now and let us
                    know what you think.
                </div>
            </section>

            <h2 class="text-primary">
                <span v-if="isLoggedIn">My Dashboard</span>
                <span v-else>SmartFigures</span>
            </h2>

            <div class="details-bar">
                <div class="selection-criteria">
                    <div class="search" v-if="searchQuery">
                        <font-awesome-icon icon="search" />
                        <div class="tag">
                            {{ searchQuery }}
                            <button type="button" class="close" @click="clearSearch">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="result-count">{{ numLoadedPanels }} SmartFigures</div>
            </div>
        </header>

        <div class="sd-view-content" ref="mainContent">
            <div v-if="isLoadingPanels" class="text-center">
                <b-spinner
                    variant="primary"
                    label="Spinning"
                    class="m-5 text-center"
                    style="width: 4rem; height: 4rem;"
                ></b-spinner>
            </div>

            <panel-listing-grid
                v-if="hasPanels"
                list_root="user"
            ></panel-listing-grid>

            <b-alert
                v-if="!hasPanels && !isLoadingPanels"
                show
                variant="danger"
                class="no-panel-alert"
            >
                No SmartFigures Found
            </b-alert>
        </div>

        <info-footer></info-footer>

        <lightbox
            :visible="isLightboxOpen"
            :imgs="'/panels/' + expandedPanel.id + '/image'"
            @hide="toggleLightbox"
        ></lightbox>
    </div>
</template>

<script>
import store from "@/stores/store";
import { mapGetters, mapActions } from "vuex";
import FilterBar from "@/components/FilterBar";
import PanelActionBar from "@/components/PanelActionBar";
import PanelAuthorsEditModal from "@/components/authors/PanelAuthorsEditModal";
import InfoFooter from "@/components/InfoFooter";
import PanelListingGrid from "@/components/panel-grid/PanelListingGrid";
import Lightbox from 'vue-easy-lightbox';
import PanelDropZone from '@/components/helpers/PanelDropZone.vue';

export default {
    name: "PanelGrid",

    components: {
        FilterBar,
        InfoFooter,
        PanelActionBar,
        PanelAuthorsEditModal,
        PanelListingGrid,
        Lightbox,
        PanelDropZone,
    },

    props: {
        query: String
    },

    data() {
        return {
            searchQuery: "",
        };
    },
    computed: {
        ...mapGetters([
            "isLoadingPanels",
            "isLoggedIn",
            "hasPanels",
            "loadedPanels",
            "isLightboxOpen",
            "expandedPanel",
            ]),
        numLoadedPanels() {
            return this.loadedPanels.length;
        },
    },

    methods: {
        ...mapActions([
            'toggleLightbox',
        ]),
        reloadPanels() {
            store.commit("clearLoadedPanels");
            store.commit("setPagination", true);
            store.commit("clearSelectedPanels");
            store.commit("setSearchMode", "user");

            // Click on main content block to hide open dropdowns
            this.$refs.mainContent.click();

            // Clear active filters
            this.$refs.filterBar.clearFilters();

            if (this.searchQuery) {
                store.dispatch("setSearchString", this.searchQuery);
            } else {
                store.dispatch("setSearchString", "");
            }

            store.dispatch("fetchPanelList").catch(error => {
                this.$snotify.error(
                    "We couldn't find any panels for you.",
                    "Sorry!"
                );
            });
        },
        clearSearch() {
            this.$router.push({
                name: 'dashboard',
            }).catch(err => {});
        }
    },

    mounted() {
        this.searchQuery = this.query;
        this.reloadPanels();
        store.dispatch("fetchFileCategories");
    },

    watch: {
        $route(to) {
            this.searchQuery = to.query.q || "";
            this.reloadPanels();
        }
    }
};
</script>

<style lang="scss" scoped>

.no-panel-alert {
    max-width: 480px;
    margin: 0 auto;
}


.anonymous-user {
    background-image: url("/images/landing-page-bg.jpg");
    background-repeat: no-repeat;
    background-size: contain;
}

#sd-featured-jumbotron {
    margin: 1.5rem 0;
    max-width: 1200px;
}

@media (min-width: 768px) {
    #sd-featured-jumbotron {
        margin: 2rem 10vw;
    }
}

// transparent images get a white background
::v-deep .vel-img {
    background-color: white;
}
</style>
