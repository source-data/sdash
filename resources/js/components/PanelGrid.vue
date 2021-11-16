<template>
    <div :class="{ 'anonymous-user': !isLoggedIn }">
        <panel-drop-zone></panel-drop-zone>

        <panel-authors-edit-form></panel-authors-edit-form>

        <filter-bar></filter-bar>

        <header class="sd-view-title">
            <panel-action-bar v-if="isLoggedIn"></panel-action-bar>

            <section v-else id="sd-featured-jumbotron">
                <h1 class="text-xxl text-primary">
                    Share scientific results with your collaborators.
                </h1>

                <div class="text-lg">
                    Generate SmartFigures that link a scientific figure to the underlying source data and structured machine-readable metadata.
                    Share your SmartFigures with groups of colleagues or make them public to share with the whole scientific community.
                    Comment and discuss initiating an early scientific dissemination of results. 
                </div>
            </section>

            <h2 class="text-primary">
                <span v-if="isLoggedIn">My Dashboard</span>
                <span v-else>SmartFigures</span>
            </h2>
        
            <aside class="align-text-bottom text-right">
                {{ numLoadedPanels }} SmartFigures
            </aside>
        </header>

        <div class="sd-view-content">
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
                No Panels Found
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
import FilterBar from "./FilterBar";
import PanelActionBar from "./PanelActionBar";
import PanelAuthorsEditForm from "@/components/authors/PanelAuthorsEditForm";
import InfoFooter from "@/components/InfoFooter";
import PanelListingGrid from "./PanelListingGrid";
import Lightbox from 'vue-easy-lightbox';
import PanelDropZone from '@/components/helpers/PanelDropZone.vue';

export default {
    name: "PanelGrid",
    components: {
        FilterBar,
        InfoFooter,
        PanelActionBar,
        PanelAuthorsEditForm,
        PanelListingGrid,
        Lightbox,
        PanelDropZone,
    },
    data() {
        return {};
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
    },

    mounted() {
        store.commit("clearLoadedPanels");
        store.commit("setPagination", true);
        store.commit("clearSelectedPanels");
        store.commit("setSearchMode", "user");
        store.dispatch("fetchFileCategories");
        store.dispatch("fetchPanelList").catch(error => {
            this.$snotify.error(
                "We couldn't find any panels for you.",
                "Sorry!"
            );
        });
    },
};
</script>

<style lang="scss" scoped>
.no-panel-alert {
    max-width: 480px;
    margin: 0 auto;
}

.wrapper.anonymous-user {
    background-image: url("/images/landing-page-bg.jpg");
    background-repeat: no-repeat;
    background-size: contain;
}

#sd-featured-jumbotron {
    margin: 100px 20vw;
}
@media (max-width: 1200px) {
    #sd-featured-jumbotron {
        margin: 50px 10vw;
    }
}
</style>
