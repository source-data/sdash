<template>
    <b-container fluid class="panel-detail-container">
        <b-row>
            <b-col>
                <div class="sd-panel-detail-title">
                    <b-input-group
                        v-if="iCanEditThisPanel && isEditingTitle"
                        class="sd-panel-detail-title--editor"
                    >
                        <b-form-input v-model="titleText"></b-form-input>

                        <b-input-group-append>
                            <b-button variant="danger" @click="stopEditingPanelTitle">
                                Cancel
                            </b-button>

                            <b-button variant="success" @click="saveEditedTitle">
                                Save
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>

                    <h3 v-if="!isEditingTitle">
                        <a :href="panelUrl" target="_blank" title="Open panel in a new tab">
                            {{ expandedPanel.title }}
                            <font-awesome-icon icon="external-link-alt" size="sm" />
                        </a>
                    </h3>

                    <font-awesome-icon
                        v-if="iCanEditThisPanel && !isEditingTitle"
                        @click="editPanelTitle"
                        class="sd-edit-icon"
                        icon="pen"
                        title="Edit panel title" />
                </div>
            </b-col>
        </b-row>

        <b-row v-if="expandedPanelAuthors.length > 0">
            <b-col>
                <ul class="d-inline list-unstyled list-inline">
                    <li
                        class="sd-panel-author-list--item list-inline-item"
                        v-for="author in authors"
                        :key="'author-' + author.order + '-' + author.id"
                        :class="
                            'sd-panel-author-list--item_' + author.author_role
                        "
                    >
                        <button class="sd-panel-author-list--remove-me"
                            v-if="author.origin==='users' && author.id===currentUser.id && !iOwnThisPanel"
                            v-b-tooltip.hover.left="{ customClass: 'sd-remove-author-tooltip' }" title="Remove me"
                            id="sd-panel-author-list--remove-me"
                            ref="sd-panel-author-list--remove-me"
                        >&times;</button>

                        <!-- remove me popover-->
                        <b-popover
                            v-if="author.origin==='users' && author.id===currentUser.id"
                            :ref="'remove-me-popover-' + currentUser.id"
                            target="sd-panel-author-list--remove-me"
                            triggers="click"
                            placement="bottom"
                        >
                        <template v-slot:title>
                                Remove yourself as author?
                            <b-button @click="closeRemoveMePopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>
                            <div class="confirm-refresh-link">
                                <p>
                                    Delete yourself from the author list of this panel?
                                </p>
                                <div class="refresh-buttons">
                                    <b-button variant="danger" small @click="removeMeAsAuthor">Remove Me</b-button>
                                    <b-button variant="outline-dark" small @click="closeRemoveMePopover">Cancel</b-button>
                                </div>
                            </div>
                        </b-popover>
                        <!-- end of remove me popover -->

                        <b-link v-if="author.corresponding" :id="'popover-' + author.origin + '-' + author.id" href="#">
                            {{ author.firstname }} {{ author.surname }}
                            <font-awesome-icon icon="envelope" />
                        </b-link>
                        <span v-else>{{ author.firstname }} {{ author.surname }}</span>
                    </li>
                </ul>

                <span
                    v-if="iCanEditThisPanel"
                    class="ml-2 sd-edit-icon"
                    tabindex="0"
                    @click="editAuthorList">
                    <font-awesome-icon icon="pen" title="Edit panel authors" />
                </span>

                <div>
                    <span>
                        <font-awesome-icon icon="envelope" />
                        indicates corresponding author
                    </span>
                </div>

                <b-popover v-for="author in correspondingAuthors" :key="'author-' + author.order + '-' + author.id"
                    :target="'popover-' + author.origin + '-' + author.id" triggers="click blur" placement="bottom">
                    <ul class="list-unstyled mt-1 mb-1">
                        <li v-if="author.email">
                            <font-awesome-icon icon="envelope" fixed-width />
                            <a :href="'mailto:' + author.email">{{ author.email }}</a>
                        </li>
                        <li v-if="author.orcid">
                            <font-awesome-icon :icon="['fab', 'orcid']" fixed-width />
                            <a :href="'https://orcid.org/' + author.orcid">{{ 'orcid.org/' + author.orcid }}</a>
                        </li>
                        <li v-if="author.institution_name">
                            <font-awesome-icon icon="building" fixed-width />
                            {{ author.institution_name }}
                        </li>
                        <li v-if="author.department_name">
                            <font-awesome-icon icon="sitemap" fixed-width />
                            {{ author.department_name }}
                        </li>
                    </ul>
                    <p v-if="author.origin==='users'" class="mt-2 mb-1">
                        <router-link :to="{ path: '/user/' + author.id }" target="_blank">
                            View Full Profile
                            <font-awesome-icon icon="external-link-alt" size="sm" />
                        </router-link>
                    </p>
                </b-popover>
            </b-col>

            <b-col>
            </b-col>
        </b-row>

        <b-row>
            <b-col lg="7">
                <div class="panel-detail-content-container panel-detail-image-container">
                    <img
                        class="panel-detail-image"
                        :src="fullImageUrl"
                        :alt="'image for' + expandedPanel.title"
                        tabindex="0"
                        @click="openLightBox"
                        style="cursor:pointer"
                        draggable="false"
                    />

                    <font-awesome-icon
                        @click="openLightBox"
                        class="sd-panel-zoom-icon"
                        icon="search"
                        size="2x"
                        title="View image"
                    />

                    <font-awesome-icon
                        v-if="iCanEditThisPanel"
                        @click="displayImageUploader"
                        class="sd-edit-icon"
                        icon="pen"
                        title="Change image" />

                    <b-form-file
                        ref="imageUploader"
                        class="d-none"
                        accept="image/jpeg, image/png, image/gif, image/tiff, application/pdf"
                        v-model="imageFile"
                        @input="changeImage"
                    ></b-form-file>
                </div>

                <div class="panel-detail-content-container panel-detail-caption-container">
                    <div class="content-name">
                        Description
                    </div>

                    <div v-if="editingCaption" class="panel-detail-content panel-detail-caption-edit">
                        <caption-editor></caption-editor>
                    </div>

                    <div v-else class="panel-detail-content panel-detail-caption">
                        {{ expandedPanel.caption }}
                    </div>

                    <div v-if="!editingCaption" class="sd-edit-icon">
                        <font-awesome-icon
                            v-if="iCanEditThisPanel"
                            @click="editPanelCaption"
                            icon="pen"
                            title="Edit panel description"
                        />
                    </div>

                    <span class="sd-license-notice" v-if="isPublic">
                        <font-awesome-icon :icon="['fab', 'creative-commons']" />
                        2021 The Authors. Published under the terms of the
                        <a href="https://creativecommons.org/licenses/by/4.0/"
                            target="_blank">CC BY 4.0</a> license.
                    </span>
                </div>


                <div class="panel-detail-content-container panel-detail-comment-container">
                    <div class="content-name">
                        Comments
                    </div>

                    <comment-list class="panel-detail-content panel-detail-comment"></comment-list>
                </div>
            </b-col>

            <b-col lg="5">
                <b-tabs
                    card
                    content-class="sd-panel-detail-tab-card"
                    class="sd-panel-details-tabs"
                >
                    <b-tab :title="'Sources (' + fileCount + ')'" active>
                        <b-card-text>
                            <file-uploads></file-uploads>
                        </b-card-text>
                    </b-tab>
                    <b-tab title="Keywords">
                        <b-card-text>
                            <smart-tags-panel></smart-tags-panel>
                        </b-card-text>
                    </b-tab>
                    <b-tab title="Share">
                        <b-card-text>
                            <panel-access-links></panel-access-links>
                        </b-card-text>
                    </b-tab>
                    <b-tab :title="commentCountTitle">
                        <b-card-text>
                            <comment-list></comment-list>
                        </b-card-text>
                    </b-tab>
                </b-tabs>

                <div class="sd-panel-detail--panel-actions">
                    <b-button
                        id="sd-delete-panel"
                        variant="danger"
                        v-if="iCanEditThisPanel"
                        class="sd-delete-panel-button"
                        ><font-awesome-icon
                            class="sd-delete-panel-icon"
                            icon="trash-alt"
                            title="Delete panel"
                        />
                        Delete Panel</b-button
                    >
                    <b-popover
                        ref="delete-panel-popover"
                        target="sd-delete-panel"
                        triggers="click"
                        placement="top"
                    >
                        <template v-slot:title>
                            Are you sure?
                            <b-button
                                @click="closeDeletePanelPopover"
                                class="close"
                                aria-label="Close"
                            >
                                <span class="d-inline-block" aria-hidden="true"
                                    >&times;</span
                                >
                            </b-button>
                        </template>
                        <div class="confirm-delete-content">
                            <div class="delete-buttons">
                                <b-button
                                    variant="danger"
                                    small
                                    @click="deletePanel"
                                    >Delete it!</b-button
                                >
                                <b-button
                                    variant="outline-dark"
                                    small
                                    @click="closeDeletePanelPopover"
                                    >Cancel</b-button
                                >
                            </div>
                        </div>
                    </b-popover>

                    <download-bar></download-bar>
                </div>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CommentList from "@/components/comments/CommentList";
import FileUploads from "@/components/files/FileUploads";
import CaptionEditor from "@/components/caption/CaptionEditor";
import SmartTagsPanel from "@/components/tags/SmartTagsPanel";
import PanelAccessLinks from "@/components/panelaccesslinks/PanelAccessLinks";
import DownloadBar from "@/components/DownloadBar";
import AuthorTypes from "@/definitions/AuthorTypes";

export default {
    name: "PanelDetail",
    components: {
        CommentList,
        FileUploads,
        CaptionEditor,
        SmartTagsPanel,
        DownloadBar,
        PanelAccessLinks,
    },
    data() {
        return {
            link_base: process.env.MIX_API_PANEL_URL,
            isEditingTitle: false,
            titleText: "",
            showDeleteConfirmation: false,
            imageFile: null
        };
    },
    computed: {
        ...mapGetters([
            "expandedPanel",
            "iOwnThisPanel",
            "iHaveAuthorPrivileges",
            "iCanSeeThisPanelViaAGroup",
            "comments",
            "commentCount",
            "fileCount",
            "editingCaption",
            "expandedPanelAuthors",
            "showAuthorSidebar",
            "currentUser",
        ]),
        authors() {
            // don't display the curator in the author list
            return this.expandedPanelAuthors.filter(
                author => author.author_role !== AuthorTypes.CURATOR
            );
        },
        correspondingAuthors() {
            return this.expandedPanelAuthors.filter(
                author => author.author_role === AuthorTypes.CORRESPONDING
            );
        },
        panelUrl(){
            return this.link_base + '/' + this.expandedPanel.id
        },
        fullImageUrl() {
            return (
                "/panels/" +
                this.expandedPanel.id +
                "/image?v=" +
                this.expandedPanel.version
            );
        },
        commentCountTitle() {
            return "Discuss (" + this.commentCount + ")";
        },
        iCanEditThisPanel() {
            return (this.iOwnThisPanel || this.iHaveAuthorPrivileges)
        },
        isPublic(){
            return !!(this.expandedPanel.is_public);
        }
    },
    methods: {
        //run as event handlers, for example

        openLightBox() {
            this.$store.commit("toggleLightbox");
        },
        editPanelTitle() {
            this.isEditingTitle = true;
            this.titleText = this.expandedPanel.title;
        },
        stopEditingPanelTitle() {
            this.isEditingTitle = false;
            this.titleText = "";
        },
        saveEditedTitle() {
            if (this.titleText === "") {
                this.$snotify.error(
                    "You cannot remove the title.",
                    "Not Permitted."
                );
            } else {
                let updatedPanel = Object.assign({}, this.expandedPanel);
                updatedPanel.title = this.titleText;
                this.$store
                    .dispatch("updatePanel", updatedPanel)
                    .then(response => {
                        this.$snotify.success(
                            response.data.MESSAGE,
                            "Update succeeded"
                        );
                        this.stopEditingPanelTitle();
                    })
                    .catch(error => {
                        this.$snotify.error(
                            error.message,
                            "Panel Update Failed"
                        );
                    });
            }
        },
        editPanelCaption() {
            this.$store.commit("toggleEditingCaption");
        },
        displayImageUploader() {
            this.$refs.imageUploader.$el.childNodes[0].click();
        },
        changeImage() {
            if (this.imageFile === null) {
                return;
            }
            this.$store
                .dispatch("changeImage", this.imageFile)
                .then(response => {
                    this.imageFile = null;
                })
                .catch(error => {
                    this.$snotify.error(
                        error.data.errors.file[0],
                        "Upload failed"
                    );
                    this.imageFile = null;
                });
        },
        closeDeletePanelPopover() {
            if (this.$refs["delete-panel-popover"]) {
                this.$refs["delete-panel-popover"].$emit("close");
            }
        },
        deletePanel() {
            this.$store.commit("setPanelLoadingState", true);

            this.$store
                .dispatch("deleteExpandedPanel")
                .then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Deleted");
                })
                .catch(error => {
                    this.$snotify.error("Could not delete panel", "Failed");
                });
        },
        toggleShareStatus() {
            this.$store
                .dispatch("toggleShareStatus")
                .then(response => {
                    this.$snotify.success(
                        response.data.MESSAGE,
                        "Panel Updated"
                    );
                })
                .catch(error => {
                    this.$snotify.error("Could not update panel", "Failed");
                });
        },
        editAuthorList(){
            this.$store.commit("setAuthorSidebar", true);
        },
        removeMeAsAuthor() {
            let panelId = this.expandedPanel.id;
            let authorId = this.currentUser.id;

            this.closeRemoveMePopover();
            this.$store.dispatch("removeUserFromExpandedPanelAuthors")
            .then(response => {
                this.$snotify.success(response.data.MESSAGE, "Author removed");
                // remove author from expanded panel detail and loaded panel detail
                this.$store.commit("removeAuthorFromPanel",{
                    author_id: authorId,
                    panel_id: panelId
                });


                /*
                    How can an person have access to a panel?
                    It's public
                    They're an owner
                    They're an author
                    They're a group member
                */

                if (!this.expandedPanel.is_public
                && !this.iCanSeeThisPanelViaAGroup
                && !this.iOwnThisPanel) {
                    // remove panel from loaded/expanded panels
                    this.$store.commit("clearExpandedPanelDetail");
                    this.$store.commit("removePanelFromStore", panelId);
                }

            }).catch(error => {
                this.$snotify.error(error.data.MESSAGE, "Error");
            });
        },
        closeRemoveMePopover() {
            const popupRef = 'remove-me-popover-' + this.currentUser.id;
            if (this.$refs[popupRef][0]) {
                this.$refs[popupRef][0].$emit("close");
            }
        },
    }
};
</script>

<style lang="scss" scoped>
@use "sass:math";
@import 'resources/sass/_variables.scss';

/*********************
 * General settings
 *********************/
.panel-detail-container {
    padding-left: 3vw;
    padding-right: 2vw;

    padding-top: 3rem;
    padding-bottom: 2rem;
}
.panel-detail-container,
.panel-detail-container a {
    color: $mostly-white-gray;
}

.panel-detail-container > .row {
    margin-bottom: 1rem;
}

.sd-edit-icon {
    cursor: pointer;
    position: relative;
    font-size: 0.85rem;
}
/*********************
 * Content containers
 *********************/
.panel-detail-content-container {
    border: 1px solid $mostly-white-gray;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    padding-bottom: 0.5rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    padding-top: 1rem;
    position: relative;
}
.panel-detail-content-container .content-name {
    background-color: $very-dark-desaturated-blue;
    font-size: 0.75rem;
    padding: 0 0.4rem;

    position: absolute;
    top: -0.65rem;
    left: 1rem;
}
.panel-detail-content-container .panel-detail-content {
    min-height: 5rem;
    max-height: 20rem;
    overflow-y: auto;
}

/*********************
 * Title row
 *********************/
.sd-panel-detail-title h3 {
    display: inline-block;
    margin: 0;
    /* Leave enough space on the right for the edit icon */
    max-width: calc(100% - 5rem);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.sd-panel-detail-title .sd-edit-icon {
    top: -0.5rem;
}

/*********************
 * Image
 *********************/
.panel-detail-image-container {
    border: none;
    padding: 0;
    /* Makes the absolutely positioned icons (see below) relative to this container. */
    position: relative;
}

.panel-detail-image {
    max-width: 100%;
    max-height: 100%;
}

$zoom-icon-size: 1rem;
/* The icon itself should take up about a third of the background */
$zoom-icon-background-size: 3 * $zoom-icon-size;
$zoom-icon-background-padding: $zoom-icon-size;
/* With bottom: 0, the bottom of the icon's background circle is exactly aligned with the bottom of the image. To make
 * the icon appear half on & half off the image, the former needs to be moved down by its background circle's padding
 * plus half the icon's size. */
$zoom-icon-bottom: -1 * (
    $zoom-icon-background-padding + math.div($zoom-icon-size, 2)
);
.sd-panel-zoom-icon {
    /* Make the background a dark circle */
    background-color: $mostly-black-blue;
    border-radius: 50%;
    height: $zoom-icon-background-size;
    width: $zoom-icon-background-size;

    padding: $zoom-icon-background-padding;

    position: absolute;
    bottom: $zoom-icon-bottom;
    right: $zoom-icon-size;

    cursor: pointer;
}

.panel-detail-image-container .sd-edit-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

/*********************
 * Description
 *********************/
.panel-detail-caption-container .sd-edit-icon {
    background-color: $very-dark-desaturated-blue;
    padding: 0 0.4rem;
    position: absolute;
    bottom: -0.65rem;
    right: 1rem;
}
</style>
