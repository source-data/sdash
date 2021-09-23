<template>
    <div>
        <b-container fluid class="mt-3" style="overflow: hidden;">
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
                    <info-bar v-if="currentGroup">
                        <template v-slot:title>
                            <h1 class="pb-0 mb-0">
                                {{ currentGroup.name }}
                                <font-awesome-icon
                                    v-if="currentGroup.is_public"
                                    icon="lock-open"
                                    size="xs"
                                />
                            </h1>
                            <a
                                :href="currentGroup.url"
                                target="_blank"
                                title="The project's homepage"
                                class="pb-2"
                                >{{ currentGroup.url }}</a
                            >
                        </template>
                        <template v-slot:text>
                            {{ currentGroup.description }}
                        </template>
                        <template v-slot:extra-content>
                            <div class="group-actions" v-if="isGroupAdmin">
                                <b-button
                                    variant="outline-success"
                                    :to="{
                                        path:
                                            '/group/' +
                                            currentGroup.id +
                                            '/edit'
                                    }"
                                    v-b-tooltip.hover.top
                                    title="Edit the group details"
                                >
                                    <font-awesome-icon icon="edit" />
                                    Edit
                                </b-button>
                                <b-button
                                    variant="outline-danger"
                                    @click.prevent
                                    id="sd-delete-group"
                                    type="submit"
                                    v-b-tooltip.hover.top
                                    title="Delete the group"
                                >
                                    <font-awesome-icon icon="trash-alt" />
                                    Delete
                                </b-button>
                                <b-popover
                                    ref="delete-group-popover"
                                    target="sd-delete-group"
                                    triggers="click"
                                    placement="bottom"
                                    selector="sd-delete-group"
                                    :key="currentGroup.id"
                                >
                                    <template v-slot:title>
                                        Are you sure?
                                        <b-button
                                            @click="closeDeleteGroupPopover"
                                            class="close"
                                            aria-label="Close"
                                        >
                                            <span
                                                class="d-inline-block"
                                                aria-hidden="true"
                                                >&times;</span
                                            >
                                        </b-button>
                                    </template>
                                    <div class="confirm-delete-content">
                                        <p>
                                            Delete the user group? Panels will
                                            not be deleted from the system but
                                            will no longer be shared with the
                                            group.
                                        </p>
                                        <div class="delete-buttons">
                                            <b-button
                                                variant="danger"
                                                small
                                                @click="deleteGroup"
                                                >Delete Group</b-button
                                            >
                                            <b-button
                                                variant="outline-dark"
                                                small
                                                @click="closeDeleteGroupPopover"
                                                >Cancel</b-button
                                            >
                                        </div>
                                    </div>
                                </b-popover>
                            </div>
                            <div class="group-actions" v-if="isGroupMember && !isGroupOwner">
                                <b-button
                                    variant="outline-danger"
                                    @click.prevent
                                    id="sd-quit-group"
                                    type="submit"
                                    v-b-tooltip.hover.top
                                    title="Leave the group"
                                >
                                    <font-awesome-icon icon="sign-out-alt" />
                                    Leave
                                </b-button>
                                <b-popover
                                    ref="quit-group-popover"
                                    target="sd-quit-group"
                                    triggers="click"
                                    placement="bottom"
                                    selector="sd-quit-group"
                                    :key="currentGroup.id"
                                >
                                    <template v-slot:title>
                                        Are you sure?
                                        <b-button
                                            @click="closeQuitGroupPopover"
                                            class="close"
                                            aria-label="Close"
                                        >
                                            <span
                                                class="d-inline-block"
                                                aria-hidden="true"
                                                >&times;</span
                                            >
                                        </b-button>
                                    </template>
                                    <div class="confirm-delete-content">
                                        <p>
                                            Remove yourself and your panels from
                                            the group?
                                        </p>
                                        <div class="delete-buttons">
                                            <b-button
                                                variant="danger"
                                                small
                                                @click="quitGroup"
                                                >Remove Me</b-button
                                            >
                                            <b-button
                                                variant="outline-dark"
                                                small
                                                @click="closeQuitGroupPopover"
                                                >Cancel</b-button
                                            >
                                        </div>
                                    </div>
                                </b-popover>
                            </div>
                        </template>
                        <template v-slot:footer>
                            <div class="sd-group-users mt-3">
                                <h5 v-if="isGroupMember">Group Members</h5>
                                <div v-if="isGroupMember" class="sd-group-user-icons-wrapper">
                                    <group-user-icon
                                        v-for="user in currentGroup.confirmed_users"
                                        :user="user"
                                        :role="user.pivot.role"
                                        :key="user.user_id"
                                    ></group-user-icon>
                                </div>
                                <b-button
                                    v-if="!isGroupMember && !hasRequestedMembership"
                                    variant="outline-secondary"
                                    class="my-2"
                                    :disabled="sendingMembershipRequest"
                                    @click="applyToJoin"
                                >
                                    <font-awesome-icon icon="plus" />
                                    Apply to Join
                                </b-button>
                                <b-alert show v-if="hasRequestedMembership" variant="primary">
                                    Your request to join is pending approval
                                </b-alert>
                            </div>
                        </template>
                    </info-bar>
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
                        <b-alert show variant="danger" class="no-panel-alert mt-3"
                            >No Panels Found</b-alert
                        >
                    </div>
                </div>
            </div>
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
import GroupMemberRequestIcon from '../helpers/GroupMemberRequestIcon'
import GroupUserIcon from '../helpers/GroupUserIcon'

export default {
    name: "GroupListing",
    components: {
        PanelListingGrid,
        FilterBar,
        InfoBar,
        GroupTitleIcon,
        GroupUserIcon,
        GroupMemberRequestIcon,
    },
    props: ["group_id"],

    data() {
        return {
            isSidebarExpanded: true,
            sendingMembershipRequest: false,
        };
    } /* end of data */,
    computed: {
        ...mapGetters([
            "currentUser",
            'currentGroup',
            'isLoadingPanels',
            'hasPanels',
            'hasLoadedAllResults',
            'isGroupAdmin',
            "isGroupOwner",
            "isGroupMember",
            "hasRequestedMembership",
            ]),
        shouldShowMembershipRequest(){
            return (this.currentGroup && this.isGroupAdmin && this.currentGroup.requested_users_count)
        },
        membershipRequestCaption(){
            if(!this.shouldShowMembershipRequest) return '';
            if(this.currentGroup.requested_users_count === 1) return '1 member request';
            return this.currentGroup.requested_users_count + ' member requests';
        },
        sidebarToggleText() {
            return this.isSidebarExpanded ? "Hide sidebar" : "Show sidebar";
        }
    },
    methods: {
        ...mapActions([
            "removeUserFromGroup",
            "deleteUserGroup",
            "fetchCurrentGroup",    
        ]),
        fetchPanels() {
            store
                .dispatch("fetchPanelList")
                .then(response => {
                    this.$snotify.success(
                        "OK, I found your panels",
                        "Panels Loaded"
                    );
                })
                .catch(error => {
                    this.$snotify.error(
                        "We couldn't find any panels for you.",
                        "Sorry!"
                    );
                    this.$store.commit("setPanelLoadingState", false);
                });
        },
        closeQuitGroupPopover() {
            if (this.$refs["quit-group-popover"]) {
                this.$refs["quit-group-popover"].$emit("close");
            }
        },
        closeDeleteGroupPopover() {
            if (this.$refs["delete-group-popover"]) {
                this.$refs["delete-group-popover"].$emit("close");
            }
        },
        applyToJoin() {
            this.sendingMembershipRequest = true;
            this.$store.dispatch("applyToJoin", {groupId: this.group_id})
            .then(response => {
                this.sendingMembershipRequest = false;
                this.$snotify.success("Application Sent!", "OK")
            }).catch(err => {
                this.$snotify.error("Could not complete the action", "Sorry!")
            })
        },
        quitGroup() {
            this.removeUserFromGroup(this.currentUser.id)
                .then(response => {
                    this.closeQuitGroupPopover();
                    this.$router.push({ name: "dashboard" });
                    this.$snotify.success(response.data.MESSAGE, "Success");
                })
                .catch(error => {
                    this.$snotify.error(error.data.MESSAGE, "Update Failed!");
                    this.closeQuitGroupPopover();
                });
        },
        deleteGroup() {
            this.deleteUserGroup()
                .then(response => {
                    this.closeDeleteGroupPopover();
                    this.$router.push({ name: "dashboard" });
                    this.$snotify.success(response.data.MESSAGE, "Success");
                })
                .catch(error => {
                    this.$snotify.error(error.data.MESSAGE, "Delete Failed!");
                    this.closeDeleteGroupPopover();
                });
        },
        toggleSidebar() {
            this.isSidebarExpanded = !this.isSidebarExpanded;
        }
    },
    created() {
        this.$store.commit("clearLoadedPanels");
        this.$store.commit("updateExpandedPanelId");
        this.$store.commit("clearExpandedPanelDetail");
        this.$store.commit("clearSearchCriteria");
        this.$store.commit("setSearchMode", "group");
        if (this.$route.query.search)
            this.$store.commit("setSearchString", this.$route.query.search);
        this.$store.commit("setPanelLoadingState", true);
        this.fetchCurrentGroup(this.group_id);
    },

    destroyed() {
        // Make sure the dashboard or another group's detail view doesn't start out displaying this group's panels
        this.$store.commit("clearLoadedPanels");
        // Make sure another group's detail view doesn't start out displaying this group's info
        this.$store.commit("setCurrentGroup", null);
    },

    mounted() {
        if (localStorage.getItem("isSidebarExpanded") !== null) {
            this.isSidebarExpanded =
                localStorage.getItem("isSidebarExpanded") === "true";
        }
    },

    watch: {
        isSidebarExpanded(newStatus) {
            localStorage.setItem("isSidebarExpanded", newStatus);
        },
        currentGroup() {
            this.fetchPanels();
        }
    }
};
</script>

<style lang="scss" scoped>
.sd-group-user-icons-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.sd-filter-wrapper {
    flex: 0 0 300px;
    max-width: 300px;
}

.group-actions button {
    margin: 0;
    margin-right: 1em;
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
