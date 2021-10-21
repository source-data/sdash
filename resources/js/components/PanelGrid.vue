<template>
    <b-container fluid class="wrapper bg-dark text-light">
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
</template>

<script>
import store from "@/stores/store";
import { mapGetters } from "vuex";
import FilterBar from "./FilterBar";
import PanelActionBar from "./PanelActionBar";
import PanelListingGrid from "./PanelListingGrid";

export default {
    name: "PanelGrid",
    components: {
        FilterBar,
        PanelActionBar,
        PanelListingGrid
    },
    props: [""],

    data() {
        return {
            isSidebarExpanded: true
        };
    } /* end of data */,

    computed: {
        ...mapGetters(["isLoadingPanels", "hasPanels", "hasLoadedAllResults"]),
        sidebarToggleText: function() {
            return this.isSidebarExpanded ? "Hide sidebar" : "Show sidebar";
        }
    },

    methods: {
        toggleSidebar() {
            this.isSidebarExpanded = !this.isSidebarExpanded;
        }
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
</style>
