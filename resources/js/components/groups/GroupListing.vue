<template>
    <div v-if="currentGroup">
        <panel-drop-zone></panel-drop-zone>

        <filter-bar></filter-bar>

        <header class="sd-view-title">
            <h2 class="text-primary">
                {{ currentGroup.name }}

                <font-awesome-icon
                    v-if="currentGroup.is_public"
                    icon="lock-open"
                    size="xs"
                />
            </h2>

            <div class="group-header-image">
                <img src="/images/group_cover_thumbnail.jpg" alt="The group header image">

                <div class="apply-to-join">
                    <b-button
                        v-if="!isGroupMember && !hasRequestedMembership"
                        variant="primary"
                        :disabled="sendingMembershipRequest"
                        @click="applyToJoin"
                    >
                        <font-awesome-icon icon="plus" />
                        Apply to join this group
                    </b-button>

                    <b-alert show v-if="hasRequestedMembership" variant="primary">
                        Your request to join this group is pending approval
                    </b-alert>
                </div>
            </div>

            <div class="group-description text-lg">
                {{ currentGroup.description }}
            </div>

            <b-container fluid>
                <b-row>
                    <b-col cols="1">
                        <font-awesome-icon icon="user-cog" size="lg" />
                    </b-col>

                    <ul class="col-11 group-members-list text-xs list-unstyled">
                        <li
                            v-for="user in groupAdministrators"
                            :key="user.user_id"
                            class="group-member bg-light text-dark"
                        >
                            <div class="group-member-name">
                                <router-link :to="{name: 'user', params: {user_id: user.id}}">
                                    {{ user.firstname + ' ' + user.surname }}
                                </router-link>
                            </div>

                            <div class="group-member-organisation">
                                {{ user.institution_name }}
                            </div>
                        </li>
                    </ul>
                </b-row>
            </b-container>

            <b-container v-if="groupMembers.length" fluid>
                <b-row>
                    <b-col cols="1">
                        <font-awesome-icon icon="users" size="lg" />
                    </b-col>

                    <ul class="col-11 group-members-list text-xs list-unstyled">
                        <li
                            v-for="user in groupMembers"
                            :key="user.user_id"
                            class="group-member bg-light text-dark"
                        >
                            <div class="group-member-name">
                                <router-link :to="{name: 'user', params: {user_id: user.id}}">
                                    {{ user.firstname + ' ' + user.surname }}
                                </router-link>
                            </div>

                            <div class="group-member-organisation">
                                {{ user.institution_name }}
                            </div>
                        </li>
                    </ul>
                </b-row>
            </b-container>

            <div>
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
            </div>
        </header>

        <div class="sd-view-content">
            <div v-if="isLoadingPanels" class="text-center">
                <b-spinner
                    variant="primary"
                    label="Spinning"
                    class="m-5"
                    style="width: 4rem; height: 4rem;"
                ></b-spinner>
            </div>

            <panel-listing-grid v-if="hasPanels" list_root="user"></panel-listing-grid>
            
            <b-alert v-if="!hasPanels && !isLoadingPanels" show variant="danger" class="no-panel-alert mt-3">
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
import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import FilterBar from '../FilterBar'
import PanelListingGrid from '../PanelListingGrid'
import InfoFooter from '@/components/InfoFooter'
import GroupTitleIcon from '../helpers/GroupTitleIcon'
import GroupMemberRequestIcon from '../helpers/GroupMemberRequestIcon'
import GroupUserIcon from '../helpers/GroupUserIcon'
import Lightbox from 'vue-easy-lightbox';
import PanelDropZone from '@/components/helpers/PanelDropZone.vue';

export default {
    name: "GroupListing",
    components: {
        PanelListingGrid,
        FilterBar,
        InfoFooter,
        GroupTitleIcon,
        GroupUserIcon,
        GroupMemberRequestIcon,
        Lightbox,
        PanelDropZone,
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
            "isLightboxOpen",
            "expandedPanel",
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
        },
        groupAdministrators() {
            if (this.currentGroup.administrators) {
            return this.currentGroup.administrators;
            }
            if (this.currentGroup.confirmed_users) {
                return this.currentGroup.confirmed_users.filter(
                    user => user.pivot.role == "admin"
                );
            }
            return []
        },
        groupMembers() {
            if (!this.currentGroup.confirmed_users) {
                return []
            }
            return this.currentGroup.confirmed_users.filter(
                user => user.pivot.role != "admin"
            )
        }
    },
    methods: {
        ...mapActions([
            "removeUserFromGroup",
            "deleteUserGroup",
            "fetchCurrentGroup",
            'toggleLightbox',
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
        this.$store.commit("clearSearchCriteria");
        this.$store.commit("setSearchMode", "group");
        if (this.$route.query.search)
            this.$store.commit("setSearchString", this.$route.query.search);
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
        group_id() {
            this.fetchCurrentGroup(this.group_id);
        },
        currentGroup() {
            this.$store.commit("clearLoadedPanels");
            this.$store.commit("updateExpandedPanelId");
            this.$store.commit("clearExpandedPanelDetail");
            this.$store.commit("setPanelLoadingState", true);
            this.fetchPanels();
        }
    }
};
</script>

<style lang="scss" scoped>
header {
    padding-top: 6rem;

    > * {
        margin-bottom: 2rem;
    }

    .group-header-image {
        height: 20rem;
        margin-bottom: 2rem;
        overflow: hidden;
        position: relative;
        width: 100%;

        img {
            width: 100%;
        }

        .apply-to-join {
            position: absolute;
            right: 20px;
            top: 20px;

            button {
                border-radius: 1rem;
                padding: 1rem 2rem;
            }
        }
    }

    .group-members-list {
        display: flex;
        flex-wrap: wrap;
        margin: -0.25rem;

        .group-member {
            border-radius: 0.5rem;
            margin: 0.25rem;
            padding: 0.1rem 0.35rem;
        }
        .group-member-name {
            font-weight: bolder;
            text-decoration: underline;
        }
    }
}


.sd-filter-wrapper {
    flex: 0 0 300px;
    max-width: 300px;
}

.group-actions button {
    margin: 0;
    margin-right: 1em;
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
