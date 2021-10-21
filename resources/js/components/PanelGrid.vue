<template>
    <div>
        <vue-full-screen-file-drop
        @drop='uploadPanel'
        formFieldName="file"
        text="Please drop a JPG, PNG, GIF, TIF or PDF file"
        v-if="panelDropEnabled">
        </vue-full-screen-file-drop>
        <b-container fluid class="mt-3">
            <div id="wrapper" class="wrapper">
                <filter-bar
                    class="sidebar"
                    v-bind:class="{ collapsed: !isSidebarExpanded }"
                ></filter-bar>
                <div
                    id="content"
                    class="content"
                    v-bind:class="{ expanded: !isSidebarExpanded }"
                >
                    <panel-action-bar></panel-action-bar>
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
                    <div v-if="isLoadingPanels" class="text-center">
                        <b-spinner
                            variant="primary"
                            label="Spinning"
                            class="m-5"
                            style="width: 4rem; height: 4rem;"
                        ></b-spinner>
                    </div>
                    <div v-if="hasPanels">
                        <panel-listing-grid
                            list_root="user"
                        ></panel-listing-grid>
                    </div>
                    <div v-if="!hasPanels && !isLoadingPanels">
                        <b-alert show variant="danger" class="no-panel-alert"
                            >No Panels Found</b-alert
                        >
                    </div>
                </div>
            </div>
        </b-container>
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
import PanelListingGrid from "./PanelListingGrid";
import Lightbox from 'vue-easy-lightbox';
import VueFullScreenFileDrop from 'vue-full-screen-file-drop'

export default {
    name: "PanelGrid",
    components: {
        FilterBar,
        PanelActionBar,
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
            "hasPanels",
            "hasLoadedAllResults",
            "isLightboxOpen",
            "expandedPanel",
            "showAuthorSidebar",
            "currentGroup",
            "mayAddPanelToGroup",
            ]),
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
        showAuthorSidebarModal: {
            set(value){
                this.$store.commit('setAuthorSidebar',value)
            },
            get(){
                return this.showAuthorSidebar
            }
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
</style>
