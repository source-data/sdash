<template>
    <div>
        <vue-full-screen-file-drop
            @drop='uploadPanel'
            formFieldName="file"
            text="Please drop a JPG, PNG, GIF, TIF or PDF file"
            v-if="panelDropEnabled">
        </vue-full-screen-file-drop>

        <b-sidebar
            id="author-edit-sidebar"
            right
            shadow
            lazy
            title="Edit the list of authors"
            width="420px"
            bg-variant="dark"
            text-variant="light"
            v-model="showAuthorSidebarModel"
        >
            <panel-authors-edit-form></panel-authors-edit-form>
        </b-sidebar>

        <b-container fluid class="wrapper bg-dark text-light" :class="{ 'anonymous-user': !isLoggedIn }">
            <filter-bar
                class="sidebar"
                v-bind:class="{ collapsed: !isSidebarExpanded }"
            ></filter-bar>
            
            <div
                id="content"
                class="content"
                v-bind:class="{ expanded: !isSidebarExpanded }"
            >
                <ul
                    class="toolbar"
                    v-bind:class="{ expanded: !isSidebarExpanded }"
                >
                    <li class="sidebar-toggle">
                        <b-link
                            href="#"
                            @click="toggleSidebar"
                            v-bind:title="sidebarToggleText"
                        >
                            <font-awesome-icon
                                icon="chevron-left"
                                v-if="isSidebarExpanded"
                            />
                            <font-awesome-icon
                                icon="chevron-right"
                                v-if="!isSidebarExpanded"
                            />
                        </b-link>
                    </li>
                    <li><font-awesome-icon icon="search" /></li>
                    <li><font-awesome-icon icon="filter" /></li>
                    <li><font-awesome-icon icon="users" /></li>
                </ul>
        
                <header id="sd-panel-grid-header">
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
        </b-container>

        <panel-grid-footer></panel-grid-footer>

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
import PanelGridFooter from "@/components/PanelGridFooter";
import PanelListingGrid from "./PanelListingGrid";
import Lightbox from 'vue-easy-lightbox';
import VueFullScreenFileDrop from 'vue-full-screen-file-drop'

export default {
    name: "PanelGrid",
    components: {
        FilterBar,
        PanelActionBar,
        PanelAuthorsEditForm,
        PanelGridFooter,
        PanelListingGrid,
        Lightbox,
        VueFullScreenFileDrop,
    },
    data() {
        return {
            isSidebarExpanded: true
        };
    } /* end of data */,

    computed: {
        ...mapGetters([
            "isLoadingPanels",
            "isLoggedIn",
            "hasPanels",
            "loadedPanels",
            "hasLoadedAllResults",
            "isLightboxOpen",
            "expandedPanel",
            "showAuthorSidebar",
            "currentGroup",
            "mayAddPanelToGroup",
            ]),
        showAuthorSidebarModel: {
            set(value) {
                this.$store.commit('setAuthorSidebar', value)
            },
            get() {
                return this.showAuthorSidebar
            }
        },
        sidebarToggleText: function() {
            return this.isSidebarExpanded ? "Hide sidebar" : "Show sidebar";
        },
        panelDropEnabled() {
            // Disallow file dropping if the sidebar to edit a panel's authors is open.
            if (this.showAuthorSidebarModal) {
                return false;
            }
            // Disallow file dropping if we're on a group's page and not allowed to add panels to it.
            if (this.currentGroup && ! this.mayAddPanelToGroup) {
                return false;
            }
            return true;

        },
        numLoadedPanels() {
            return this.loadedPanels.length;
        },
    },

    methods: {
        ...mapActions([
            'uploadNewPanel',
            'toggleLightbox',
            'addSelectedPanelsToGroup',
        ]),
        toggleSidebar() {
            this.isSidebarExpanded = !this.isSidebarExpanded;
        },
        uploadPanel(formData, files){
            this.uploadNewPanel(formData)
            .then(response => {
                this.$snotify.success("New panel created", "Uploaded")
                if(this.currentGroup) {
                    this.$store.commit("clearSelectedPanels")
                    this.$store.commit("addPanelToSelections", response.data.DATA.id)
                    this.addSelectedPanelsToGroup(this.currentGroup.id)
                      .then(response => {
                          this.$snotify.success("Panel added to this group", "Group updated")
                      })
                      .catch(error => {
                          console.log(error)
                          this.$snotify.error("Cannot add panel to this group", "Update failed")
                      })

                }
                })
                .catch(error => {
                    this.$snotify.error(error.data.errors.file[0], "Upload failed")
                })
        },
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
        if (localStorage.getItem("isSidebarExpanded") !== null) {
            this.isSidebarExpanded =
                localStorage.getItem("isSidebarExpanded") === "true";
        }
    },

    watch: {
        isSidebarExpanded(newStatus) {
            localStorage.setItem("isSidebarExpanded", newStatus);
        }
    }
};
</script>

<style lang="scss" scoped>
.no-panel-alert {
    max-width: 480px;
    margin: 0 auto;
}

.sd-filter-wrapper {
    flex: 0 0 300px;
    max-width: 300px;
}

.wrapper {
    display: flex;
    height: 100%;
    padding-top: 2rem;
}
.wrapper.anonymous-user {
    background-image: url("/images/landing-page-bg.jpg");
    background-repeat: no-repeat;
    background-size: contain;
}

.sidebar,
.content {
    min-height: 100%;
}

.sidebar {
    flex: 0 0 300px;
    padding-right: 15px;
    transition: all 0.25s ease-in;
}

.sidebar.collapsed {
    transform: translateX(-100%);
}

.content {
    flex: auto;
    position: relative;
    transition: all 0.25s ease-in;
    width: 100%;
}

.content.expanded {
    margin-left: -300px;
}

.toolbar {
    position: absolute;
    top: 40px;
    left: -17px;
    width: 40px;
    margin: 0;
    padding: 0;
    list-style: none;
    background: #a6b2c6;
    li,
    li a {
        color: white;
    }
    li {
        padding: 2px;
        text-align: center;
        font-size: 20px;
        opacity: 0.2;
    }
    li.sidebar-toggle {
        opacity: 1;
        font-size: 24px;
    }
}

.b-sidebar > .b-sidebar-header {
    font-size:1rem;
}

#sd-panel-grid-header {
    margin: 0 30px;
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
