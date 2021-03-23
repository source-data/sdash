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
            <div id="wrapper" class="wrapper">
                <filter-bar class="sidebar" v-bind:class="{collapsed: !isSidebarExpanded}"></filter-bar>
                <div id="content" class="content" v-bind:class="{expanded: !isSidebarExpanded}">
                    <ul class="toolbar" v-bind:class="{expanded: !isSidebarExpanded}">
                        <li class="sidebar-toggle">
                            <b-link href="#" @click="toggleSidebar" v-bind:title="sidebarToggleText">
                                <font-awesome-icon icon="chevron-left" v-if="isSidebarExpanded" />
                                <font-awesome-icon icon="chevron-right" v-if="!isSidebarExpanded" />
                            </b-link>
                        </li>
                        <li><font-awesome-icon icon="search" /></li>
                        <li><font-awesome-icon icon="filter" /></li>
                        <li><font-awesome-icon icon="users" /></li>
                    </ul>
                    <div v-if="isLoadingPanels" class="text-center">
                        <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
                    </div>
                    <div v-if="hasPanels">
                        <panel-listing-grid list_root="user"></panel-listing-grid>
                    </div>
                    <div v-if="!hasPanels && !isLoadingPanels">
                        <b-alert show variant="danger" class="no-panel-alert">No Panels Found</b-alert>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>

<script>

import store from '@/public_app/stores/store'
import { mapGetters } from 'vuex'
import FilterBar from './FilterBar'
import InfoBar from '@/components/InfoBar'
import PanelListingGrid from './PanelListingGrid'


export default {

    name: 'PanelGrid',
    components: {
        FilterBar,
        PanelListingGrid,
        InfoBar,
    },

    data() {
        return {
            isSidebarExpanded: true,
        }
    },

    computed: {
        ...mapGetters([
            'isLoadingPanels',
            'hasPanels',
            'hasLoadedAllResults',
        ]),
        sidebarToggleText: function() {
            return this.isSidebarExpanded ? 'Hide sidebar' : 'Show sidebar';
        }
    },
    
    methods: {
        toggleSidebar() {
            this.isSidebarExpanded = !this.isSidebarExpanded;
        }
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
        if (localStorage.getItem("isSidebarExpanded") !== null) {
            this.isSidebarExpanded = localStorage.getItem("isSidebarExpanded") === 'true'
        }
    },
    
    watch: {
        isSidebarExpanded(newStatus) {
            localStorage.setItem("isSidebarExpanded", newStatus)
        }
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
    transition: all .25s ease-in;
}

.sidebar.collapsed {
    transform: translateX(-100%);
}

.content {
    position: relative;
    transition: all .25s ease-in;
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
    background:#A6B2C6;
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