<template>
<div>
    <info-bar v-if="currentGroup">
        <template v-slot:above-title>
            <div v-if="isGroupAdmin" tabindex="0" class="sd-edit-icon sd-group-edit-link">
                <router-link :to="{path: '/group/' + currentGroup.id + '/edit'}" class="sd-edit-icon sd-group-edit-link">
                    <font-awesome-icon icon="edit" title="Edit group details" />
                    Edit group
                </router-link>
            </div>
            <group-title-icon></group-title-icon>
        </template>
        <template v-slot:title>
            <h1 class="pb-0 mb-0">{{ currentGroup.name }}</h1>
            <a :href="currentGroup.url" target="_blank" title="The project's homepage" class="pb-2">{{ currentGroup.url }}</a>
        </template>
        <template v-slot:text>
            {{ currentGroup.description }}
        </template>
        <template v-slot:footer>
            <div class="sd-group-users mt-3">
                <h5>Group Members</h5>
                <div class="sd-group-user-icons-wrapper">
                    <group-user-icon v-for="user in currentGroup.confirmed_users" :user="user" :role="user.pivot.role" :key="user.user_id"></group-user-icon>
                </div>
            </div>
            <p class="mt-2"><router-link :to="{name:'dashboard'}">&lt; Back to Dashboard</router-link></p>
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
            <b-col >
                <b-alert show variant="danger" class="no-panel-alert">No Panels Found</b-alert>
            </b-col>
        </b-row>
    </b-container>
</div>
</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import FilterBar from '../FilterBar'
import PanelListingGrid from '../PanelListingGrid'
import InfoBar from '../InfoBar'
import GroupTitleIcon from '../helpers/GroupTitleIcon'
import GroupUserIcon from '../helpers/GroupUserIcon'

export default {

    name: 'GroupListing',
    components: {
        PanelListingGrid,
        FilterBar,
        InfoBar,
        GroupTitleIcon,
        GroupUserIcon,
    },
    props: [
        'group_id'
    ],

    data(){

        return {

        }

    }, /* end of data */
    computed: {

        ...mapGetters([
            'userGroups',
            'currentGroup',
            'isLoadingPanels',
            'hasPanels',
            'hasLoadedAllResults',
            'isGroupAdmin',
            ])

    },
    methods:{
        fetchData(){
            this.$store.commit("clearLoadedPanels")
            this.$store.commit("updateExpandedPanelId")
            this.$store.commit("clearExpandedPanelDetail")
            this.$store.commit("clearSearchCriteria")
            this.$store.commit("setCurrentGroup", this.group_id)
            this.$store.commit("setPanelLoadingState", true)
            store.dispatch("fetchPanelList")
            .then((response) => {
                this.$snotify.success("OK, I found your panels", "Panels Loaded")
            })
            .catch((error) => { console.log(error)
                this.$snotify.error("We couldn't find any panels for you.", "Sorry!")
                this.$store.commit("setPanelLoadingState", false)
            })
        }
    },
    created() {
        this.fetchData()
    },
    watch: {
        $route(to, from){ this.fetchData() }
    }


}
</script>

<style lang="scss">
    .sd-group-user-icons-wrapper {
        display:flex;
        flex-wrap:wrap;
        justify-content: flex-start;
    }

    .sd-group-edit-link {
        font-size: 1rem;
        padding: 0.5rem 0;
        color: #459939;
    }
</style>