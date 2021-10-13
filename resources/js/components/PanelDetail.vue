<template>
    <article class="container-fluid text-light text-md panel-detail">
        <header class="row">
            <!-- panel title -->
            <h1 class="col col-12 text-xl">
                <b-input-group
                    v-if="iCanEditThisPanel && isEditingTitle"
                    class="sd-panel-detail-title--editor"
                >
                    <b-form-input v-model="titleText"></b-form-input>

                    <b-input-group-append>
                        <b-button variant="danger" @click="stopEditingPanelTitle">
                            Cancel
                        </b-button>

                        <b-button variant="primary" @click="saveEditedTitle">
                            Save
                        </b-button>
                    </b-input-group-append>
                </b-input-group>

                <span v-if="!isEditingTitle">
                    <a :href="panelUrl" target="_blank" title="Open panel in a new tab">
                        {{ expandedPanel.title }}
                        <font-awesome-icon icon="external-link-alt" size="sm" />
                    </a>
                </span>

                <button
                    class='edit text-xs'
                    v-if="iCanEditThisPanel && !isEditingTitle"
                    @click="editPanelTitle"
                    title="Edit panel title"
                >
                    <font-awesome-icon icon="pen" />
                </button>
            </h1>

            <!-- panel authors -->
            <address class="col col-12" v-if="expandedPanelAuthors.length > 0">
                <author-list></author-list>

                <button
                    class='edit text-xs'
                    v-if="iCanEditThisPanel"
                    @click="editAuthorList"
                    title="Edit panel authors"
                >
                    <font-awesome-icon icon="pen" />
                </button>

                <div>
                    <span>
                        <font-awesome-icon icon="envelope" />
                        indicates corresponding author
                    </span>
                </div>
            </address>
        </header>

        <!-- further details about the panel -->
        <b-row>
            <b-col lg="7">
                <section class="image">
                    <img
                        class="content"
                        :src="fullImageUrl"
                        :alt="'image for' + expandedPanel.title"
                        tabindex="0"
                        @click="openLightBox"
                        style="cursor:pointer"
                        draggable="false" />

                    <font-awesome-icon
                        @click="openLightBox"
                        class="sd-panel-zoom-icon"
                        icon="search"
                        size="2x"
                        title="View image" />

                    <button
                        class='edit text-xs'
                        v-if="iCanEditThisPanel"
                        @click="displayImageUploader"
                        title="Change image"
                    >
                        <font-awesome-icon icon="pen" />
                    </button>

                    <b-form-file
                        ref="imageUploader"
                        class="d-none"
                        accept="image/jpeg, image/png, image/gif, image/tiff, application/pdf"
                        v-model="imageFile"
                        @input="changeImage"></b-form-file>
                </section>

                <section class="description">
                    <h2 class="text-xs">
                        Description
                    </h2>

                    <div v-if="editingCaption" class="content panel-detail-caption-edit">
                        <caption-editor></caption-editor>
                    </div>

                    <div v-else class="content panel-detail-caption">
                        {{ expandedPanel.caption }}
                    </div>

                    <button
                        class='edit text-xs'
                        v-if="!editingCaption && iCanEditThisPanel"
                        @click="editPanelCaption"
                        title="Edit panel description"
                    >
                        <font-awesome-icon icon="pen" />
                    </button>

                    <span class="sd-license-notice" v-if="isPublic">
                        <font-awesome-icon :icon="['fab', 'creative-commons']" />
                        2021 The Authors. Published under the terms of the
                        <a href="https://creativecommons.org/licenses/by/4.0/"
                            target="_blank">CC BY 4.0</a> license.
                    </span>
                </section>

                <section class="comments">
                    <h2 class="text-xs">
                        Comments
                    </h2>

                    <div class="content">
                        <comment-list></comment-list>
                    </div>
                </section>
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
                </b-tabs>

                <div>
                    <b-tab title="Share">
                        <b-card-text>
                            <panel-access-links></panel-access-links>
                        </b-card-text>
                    </b-tab>
                </div>

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
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CommentList from "@/components/comments/CommentList";
import FileUploads from "@/components/files/FileUploads";
import CaptionEditor from "@/components/caption/CaptionEditor";
import SmartTagsPanel from "@/components/tags/SmartTagsPanel";
import PanelAccessLinks from "@/components/panelaccesslinks/PanelAccessLinks";
import DownloadBar from "@/components/DownloadBar";
import AuthorList from "@/components/panel-detail/AuthorList";

export default {
    name: "PanelDetail",
    components: {
        CommentList,
        FileUploads,
        CaptionEditor,
        SmartTagsPanel,
        DownloadBar,
        PanelAccessLinks,
        AuthorList,
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
            "commentCount",
            "fileCount",
            "editingCaption",
            "expandedPanelAuthors",
        ]),
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
    }
};
</script>

<style lang="scss" scoped>
@use "sass:math";
@import 'resources/sass/_colors.scss';

/*********************
 * General settings
 *********************/
.panel-detail {
    background-color: $very-dark-desaturated-blue;

    padding-left: 3vw;
    padding-right: 2vw;
    padding-top: 3rem;
    padding-bottom: 2rem;
}
.panel-detail a {
    color: inherit;
}

.row {
    margin-bottom: 1rem;
}

button.edit {
    background-color: inherit;
    border: none;
    color: inherit;
    cursor: pointer;
}

/*********************
 * Content containers
 *********************/
section {
    border: 1px solid $mostly-white-gray;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    padding-bottom: 0.5rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    padding-top: 1rem;
    position: relative;
}
section h2 {
    background-color: $very-dark-desaturated-blue;
    color: inherit;
    padding: 0 0.4rem;

    position: absolute;
    top: -0.65rem;
    left: 1rem;
}
section .content {
    min-height: 5rem;
    max-height: 20rem;
    overflow-y: auto;
}

/*********************
 * Title row
 *********************/
h1 {
    /* Leave enough space on the right for the edit icon */
    max-width: calc(100% - 5rem);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/*********************
 * Image
 *********************/
section.image {
    border: none;
    padding: 0;
    /* Makes the absolutely positioned icons (see below) relative to this container. */
    position: relative;
}

section.image img {
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

section.image button.edit {
    position: absolute;
    top: 1rem;
    right: 1rem;
}
section.image button.edit,
section.image button.edit * {
    background-color: transparent;
}

/*********************
 * Description
 *********************/
section.description button.edit {
    background-color: $very-dark-desaturated-blue;
    padding: 0 0.4rem;
    position: absolute;
    bottom: -0.65rem;
    right: 1rem;
}
</style>
