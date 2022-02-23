<template>
    <div v-if="currentGroup">
        <panel-drop-zone v-if="!showImageUploadDialog"></panel-drop-zone>

        <panel-authors-edit-modal></panel-authors-edit-modal>

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

            <image-uploader
                field="cover_photo"
                ref="coverPhotoUploader"
                @crop-upload-success="coverPhotoUploadSuccess"
                v-model="showImageUploadDialog"
                :width="800"
                :height="400"
                :url="coverPhotoUploadUrl"
                :method="requestMethod"
                :headers="requestHeaders"
                :params="requestParams"
                :no-circle="true"
                lang-type="en"
                img-format="jpg"
            ></image-uploader>

            <div class="group-header-image">
                <b-overlay :show="changingCoverPhoto" variant="dark">
                    <img
                        :src="coverPhotoUrl"
                        :alt="'Cover photo of ' + currentGroup.name"
                        draggable="false"
                    />

                    <div v-if="isGroupAdmin && currentGroup.cover_photo">
                        <button
                            id="sd-delete-cover-photo-button"
                            class="delete-cover-photo text-xxs"
                            @click="toggleCoverPhotoDeleteDialog"
                            title="Delete cover photo"
                        >
                            <font-awesome-icon icon="trash-alt" />
                        </button>

                        <b-popover
                            custom-class="sd-custom-popover"
                            placement="top"
                            show.sync="showCoverPhotoDeleteDialog"
                            target="sd-delete-cover-photo-button"
                            triggers="click blur"
                        >
                            <div>
                                <p>
                                    Do you really want to delete this group's cover photo?
                                </p>

                                <div class="delete-buttons">
                                    <b-button
                                        id="sd-delete-cover-photo-confirm-button"
                                        variant="primary"
                                        small
                                        @click="deleteGroupCoverPhoto"
                                    >
                                        Delete it!
                                    </b-button>

                                    <b-button
                                        variant="outline-dark"
                                        small
                                        @click="toggleCoverPhotoDeleteDialog"
                                    >
                                        Cancel
                                    </b-button>
                                </div>
                            </div>
                        </b-popover>
                    </div>

                    <button
                        class="edit text-xxs"
                        v-if="isGroupAdmin"
                        @click="toggleCoverPhotoUploadDialog"
                        title="Change cover photo"
                    >
                        <font-awesome-icon icon="pen" />
                    </button>

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

                        <b-alert
                            show
                            v-if="hasRequestedMembership"
                            variant="primary"
                        >
                            Your request to join this group is pending approval
                        </b-alert>
                    </div>
                </b-overlay>
            </div>

            <div class="group-description text-lg">
                {{ currentGroup.description }}
            </div>

            <div class="group-members">
                <div class="group-members-icon">
                    <font-awesome-icon icon="user-cog" size="lg" />
                </div>

                <ul class="group-members-list text-xxs list-unstyled">
                    <li
                        v-for="user in groupAdministrators"
                        :key="user.user_id"
                        class="group-member bg-light text-dark"
                    >
                        <div class="group-member-name">
                            <router-link
                                :to="{
                                    name: 'user',
                                    params: { user_id: user.id }
                                }"
                            >
                                {{ user.firstname + " " + user.surname }}
                            </router-link>
                        </div>

                        <div class="group-member-organisation">
                            {{ user.institution_name }}
                        </div>
                    </li>
                </ul>
            </div>

            <div v-if="groupMembers.length" class="group-members">
                <div class="group-members-icon">
                    <font-awesome-icon icon="users" size="lg" />
                </div>

                <ul class="group-members-list text-xxs list-unstyled">
                    <li
                        v-for="user in groupMembers"
                        :key="user.user_id"
                        class="group-member bg-light text-dark"
                    >
                        <div class="group-member-name">
                            <router-link
                                :to="{
                                    name: 'user',
                                    params: { user_id: user.id }
                                }"
                            >
                                {{ user.firstname + " " + user.surname }}
                            </router-link>
                        </div>

                        <div class="group-member-organisation">
                            {{ user.institution_name }}
                        </div>
                    </li>
                </ul>
            </div>

            <div>
                <div class="group-actions" v-if="isGroupAdmin">
                    <b-button
                        variant="outline-success"
                        :to="{
                            path: '/group/' + currentGroup.id + '/edit'
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
                        custom-class="sd-custom-popover"
                    >
                        <template v-slot:title>
                            Are you sure?
                            <b-button
                                @click="closeDeleteGroupPopover"
                                class="close"
                                aria-label="Close"
                            >
                                <span class="d-inline-block" aria-hidden="true"
                                    >&times;</span
                                >
                            </b-button>
                        </template>
                        <div class="confirm-delete-content">
                            <p>
                                Delete the user group? Panels will not be
                                deleted from the system but will no longer be
                                shared with the group.
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

                <div class="group-actions mt-2" v-if="isGroupMember">
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
                        custom-class="sd-custom-popover"
                    >
                        <template v-slot:title>
                            Are you sure?
                            <b-button
                                @click="closeQuitGroupPopover"
                                class="close"
                                aria-label="Close"
                            >
                                <span class="d-inline-block" aria-hidden="true"
                                    >&times;</span
                                >
                            </b-button>
                        </template>
                        <div class="confirm-delete-content">
                            <p>
                                Remove yourself and your panels from the group?
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

            <panel-listing-grid
                v-if="hasPanels"
                list_root="user"
                batchSelectDisabled
            ></panel-listing-grid>

            <b-alert
                v-if="!hasPanels && !isLoadingPanels"
                show
                variant="danger"
                class="no-panel-alert mt-3"
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
import Axios from "axios";
import ImageUploader from "vue-image-crop-upload/upload-2.vue";
import store from "@/stores/store";
import { mapGetters, mapActions } from "vuex";
import FilterBar from "../FilterBar";
import PanelListingGrid from "@/components/panel-grid/PanelListingGrid";
import InfoFooter from "@/components/InfoFooter";
import GroupTitleIcon from "../helpers/GroupTitleIcon";
import GroupMemberRequestIcon from "../helpers/GroupMemberRequestIcon";
import GroupUserIcon from "../helpers/GroupUserIcon";
import Lightbox from "vue-easy-lightbox";
import PanelAuthorsEditModal from "@/components/authors/PanelAuthorsEditModal";
import PanelDropZone from "@/components/helpers/PanelDropZone.vue";

export default {
    name: "GroupListing",
    components: {
        ImageUploader,
        PanelListingGrid,
        FilterBar,
        InfoFooter,
        GroupTitleIcon,
        GroupUserIcon,
        GroupMemberRequestIcon,
        Lightbox,
        PanelDropZone,
        PanelAuthorsEditModal
    },
    props: ["group_id"],

    data() {
        return {
            changingCoverPhoto: false,
            showCoverPhotoDeleteDialog: false,
            isSidebarExpanded: true,
            sendingMembershipRequest: false,
            showImageUploadDialog: false,
            requestMethod: "POST",
            requestHeaders: {
                "X-XSRF-TOKEN": this.$cookies.get("XSRF-TOKEN")
            },
            requestParams: {
                _method: "PATCH"
            }
        };
    } /* end of data */,
    computed: {
        ...mapGetters([
            "currentUser",
            "currentGroup",
            "isLoadingPanels",
            "hasPanels",
            "hasLoadedAllResults",
            "isGroupAdmin",
            "isGroupMember",
            "hasRequestedMembership",
            "isLightboxOpen",
            "expandedPanel"
        ]),
        shouldShowMembershipRequest() {
            return (
                this.currentGroup &&
                this.isGroupAdmin &&
                this.currentGroup.requested_users_count
            );
        },
        membershipRequestCaption() {
            if (!this.shouldShowMembershipRequest) return "";
            if (this.currentGroup.requested_users_count === 1)
                return "1 member request";
            return this.currentGroup.requested_users_count + " member requests";
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
            return [];
        },
        groupMembers() {
            if (!this.currentGroup.confirmed_users) {
                return [];
            }
            return this.currentGroup.confirmed_users.filter(
                user => user.pivot.role != "admin"
            );
        },
        coverPhotoUrl() {
            return this.currentGroup.cover_photo
                ? "/storage/cover_photos/" + this.currentGroup.cover_photo
                : "/images/group_cover.jpg";
        },
        coverPhotoUploadUrl() {
            return (
                process.env.MIX_API_URL +
                "/groups/" +
                this.currentGroup.id +
                "/cover"
            );
        }
    },
    methods: {
        ...mapActions([
            "removeUserFromGroup",
            "deleteUserGroup",
            "deleteCoverPhoto",
            "fetchCurrentGroup",
            "toggleLightbox",
            "modifyGroup"
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
            this.$store
                .dispatch("applyToJoin", { groupId: this.group_id })
                .then(response => {
                    this.sendingMembershipRequest = false;
                    this.$snotify.success("Application Sent!", "OK");
                })
                .catch(err => {
                    this.$snotify.error(
                        "Could not complete the action",
                        "Sorry!"
                    );
                });
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
        },
        toggleCoverPhotoUploadDialog() {
            this.showImageUploadDialog = !this.showImageUploadDialog;
        },
        coverPhotoUploadSuccess(response) {
            this.currentGroup.cover_photo = response.DATA.cover_photo;
            this.showImageUploadDialog = false;
            this.$refs.coverPhotoUploader.setStep(1);
        },
        toggleCoverPhotoDeleteDialog() {
            this.showCoverPhotoDeleteDialog = !this.showCoverPhotoDeleteDialog;
        },
        deleteGroupCoverPhoto() {
            this.changingCoverPhoto = true;
            this.toggleCoverPhotoDeleteDialog();
            this.deleteCoverPhoto(this.currentGroup).catch(err => {
                this.$snotify.error(err.data.MESSAGE, "Failed to change cover photo. Please try again later.")
            }).then(() => {
                this.changingCoverPhoto = false;
            })
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
        margin-bottom: 2rem;
        overflow: hidden;
        position: relative;
        width: 100%;

        img {
            width: 100%;
        }

        button.edit,
        button.delete-cover-photo {
            position: absolute;
            top: 0.25rem;
            color: white;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.25);
        }
        button.edit {
            right: 0.25rem;
        }
        button.delete-cover-photo {
            left: 0.25rem;
        }

        button,
        button * {
            border: none;
            outline: none;
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

    .group-members {
        display: flex;

        .group-members-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .group-members-list {
            display: flex;
            flex-wrap: wrap;
            margin: -0.25rem;

            .group-member {
                border-radius: 0.5rem;
                margin: 0.25rem;
                padding: 0.3rem 0.85rem;
            }
            .group-member-name {
                font-weight: bolder;
                text-decoration: underline;
            }
        }
    }
}
@media (min-width: 1200px) {
    header .group-header-image {
        width: 70%;
    }
}

.sd-filter-wrapper {
    flex: 0 0 300px;
    max-width: 300px;
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
