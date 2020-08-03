<template>
    <article>
        <b-row class="m-3 my-4 sd-panel-detail-title-wrapper">
            <div class="sd-panel-detail-title">
                <b-input-group
                    v-if="iOwnThisPanel && isEditingTitle"
                    class="sd-panel-detail-title--editor"
                >
                    <b-form-input v-model="titleText"></b-form-input>
                    <b-input-group-append>
                        <b-button
                            variant="danger"
                            @click="stopEditingPanelTitle"
                            >Cancel</b-button
                        >
                        <b-button variant="success" @click="saveEditedTitle"
                            >Save</b-button
                        >
                    </b-input-group-append>
                </b-input-group>

                <h3 v-if="!isEditingTitle">{{ expandedPanel.title }}</h3>
                <span
                    class="sd-panel-detail-title-edit-icon sd-edit-icon"
                    v-if="iOwnThisPanel && !isEditingTitle"
                    tabindex="0"
                    @click="editPanelTitle"
                >
                    <font-awesome-icon icon="edit" title="Edit panel title" />
                    Edit title
                </span>
            </div>
            <ul class="sd-panel-author-list list-unstyled list-inline">
                <li
                    class="sd-panel-author-list--item list-inline-item"
                    v-for="author in authors"
                    :key="'author-' + author.order + '-' + author.id"
                    :class="'sd-panel-author-list--item_' + author.author_role"
                >
                    <router-link :to="{ path: '/user/' + author.id }">
                        {{ author.firstname }} {{ author.surname }}
                        <sup
                            class="sd-panel-author-list--asterisk"
                            v-if="author.corresponding"
                            >*</sup
                        ></router-link
                    >
                </li>
            </ul>
        </b-row>
        <b-row class="m-3 sd-panel-detail-content-wrapper">
            <b-col class="sd-panel-detail-col sd-panel-detail-col--left">
                <div class="panel-detail-image-outer-container">
                    <img
                        class="panel-detail-image"
                        :src="fullImageUrl"
                        :alt="'image for' + expandedPanel.title"
                        tabindex="0"
                        @click="openLightBox"
                        style="cursor:pointer"
                    />
                    <font-awesome-icon
                        @click="openLightBox"
                        class="sd-panel-zoom-icon"
                        icon="search-plus"
                        size="2x"
                        title="View image"
                    />
                </div>
                <span
                    class="sd-panel-change-image-link sd-edit-icon"
                    v-if="iOwnThisPanel"
                    tabindex="0"
                    @click="displayImageUploader"
                >
                    <font-awesome-icon icon="edit" title="Change image" />
                    Change image
                </span>
                <b-form-file
                    ref="uploader"
                    class="d-none"
                    accept="image/jpeg, image/png, image/gif, image/tiff, application/pdf"
                    v-model="imageFile"
                    @input="changeImage"
                >
                </b-form-file>
                <div class="panel-detail-caption-wrapper">
                    <div
                        class="panel-detail-caption-container"
                        v-if="!editingCaption"
                    >
                        {{ expandedPanel.caption }}
                    </div>
                    <div
                        class="panel-detail-caption-edit-container"
                        v-if="editingCaption"
                    >
                        <caption-editor></caption-editor>
                    </div>
                </div>
                <div class="panel-detail-caption-edit-container">
                    <span
                        class="sd-panel-detail-caption-edit-icon sd-edit-icon"
                        v-if="iOwnThisPanel"
                        tabindex="0"
                        @click="editPanelCaption"
                    >
                        <font-awesome-icon
                            icon="edit"
                            title="Edit panel title"
                        />
                        Edit description
                    </span>
                </div>
            </b-col>
            <b-col class="sd-panel-detail-col sd-panel-detail-col--right">
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
                        v-if="iOwnThisPanel"
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
import store from "@/stores/store";
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
        PanelAccessLinks
    },
    data() {
        return {
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
            "comments",
            "commentCount",
            "fileCount",
            "editingCaption",
            "expandedPanelAuthors"
        ]),
        authors() {
            // don't display the curator in the author list
            return this.expandedPanelAuthors.filter(
                author => author.role !== AuthorTypes.CURATOR
            );
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
        }
    },
    methods: {
        //run as event handlers, for example

        openLightBox() {
            console.log(this.expandedPanelAuthors);
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
            this.$refs.uploader.$el.childNodes[0].click();
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
        }
    }
};
</script>

<style lang="scss">
.panel-detail-image-outer-container {
    width: 100%;
    height: 380px;
    display: flex;
    text-align: center;
    align-items: center;
    justify-content: center;
    background-color: #2f2f2f;
    position: relative;
    margin: 0;
}

.sd-panel-detail-title-wrapper {
    padding-left: 15px;
    margin-bottom: 0 !important;
}

.sd-panel-detail-title-wrapper a {
    color: white;
}

.sd-panel-detail-content-wrapper {
    margin-top: 0.5rem !important;
    margin-bottom: 0 !important;
}

.sd-panel-detail-col {
    min-width: 0;
}

.panel-detail-image {
    max-width: 100%;
    max-height: 100%;
}

.sd-panel-detail-title,
.sd-panel-author-name {
    width: 100%;
}

.sd-panel-detail-title {
    height: 36px;
}

.sd-panel-author-name {
    margin: 0;
}

.sd-panel-detail-title h3 {
    display: inline-block;
    margin-right: 6px;
    margin-bottom: 4px;
    max-width: calc(100% - 5em);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.sd-edit-icon {
    cursor: pointer;
    position: relative;
    color: #65dd65;
    font-size: 0.85em;
}

.sd-panel-detail-title-edit-icon {
    top: -1em;
}
.sd-edit-icon:hover,
.sd-edit-icon:focus,
.sd-edit-icon:active {
    text-decoration: underline;
}
.panel-detail-caption-edit-container {
    height: 32px;
    padding: 6px 0;
}

.sd-panel-change-image-link {
    display: inline-block;
    margin-top: 8px;
    margin-bottom: 6px;
}

.panel-detail-caption-wrapper {
    // height:130px;
    height: 200px;
    max-width: 100%;
    border: solid 1px #eee;
    padding: 6px;
    margin-top: 0.5rem;
}

.panel-detail-caption-container {
    height: 100%;
    padding-right: 6px;
    overflow-y: auto;
}

.sd-panel-detail-tab-card {
    border: solid 1px #fff;
    border-radius: 6px 6px 0 0;
    max-height: 540px;
    margin-top: -1px;
}
.sd-panel-detail-tab-card .tab-panel.card-body {
    max-height: 540px;
}
.sd-panel-detail-tab-card .card-text {
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 500px;
}

.sd-panel-details-tabs .card-header {
    padding-top: 0;
    color: #333;
}

.sd-panel-details-tabs .nav-tabs a.nav-link {
    color: #ddd;
    border: solid 1px #fff;
}
.sd-panel-details-tabs .nav-tabs a.nav-link.active {
    color: #333;
}

.sd-panel-detail-title--editor {
    max-width: 800px;
}

.sd-panel-detail--panel-actions {
    text-align: right;
    padding: 0.5em 0;
}

.sd-delete-panel-icon {
    margin-right: 3px;
}

.sd-panel-zoom-icon {
    color: #2a2a2a;
    position: absolute;
    bottom: 12px;
    right: 12px;
    opacity: 0.7;
    background: #fff;
    padding: 2px;
    border-radius: 3px;
    cursor: pointer;
}

.sd-sharing-toggle {
    margin-right: 1em !important;
}

.sd-panel-author-list--item:not(:last-child):after {
    content: ", ";
}

.sd-panel-author-list--item_corresponding a {
    color: orange;
}
</style>
