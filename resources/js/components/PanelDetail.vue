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

                <a
                    v-if="!isEditingTitle"
                    :href="panelUrl"
                    target="_blank"
                    title="Open SmartFigure in a new tab"
                >{{ expandedPanel.title }}</a>

                <button
                    class='edit text-xxs'
                    v-if="iCanEditThisPanel && !isEditingTitle"
                    @click="editPanelTitle"
                    title="Edit SmartFigure title"
                >
                    <font-awesome-icon icon="pen" />
                </button>
            </h1>

            <!-- panel authors -->
            <address class="col col-12" v-if="expandedPanelAuthors.length > 0">
                <author-list></author-list>

                <button
                    class='edit text-xxs'
                    v-if="iCanEditThisPanel"
                    @click="editAuthorList"
                    title="Edit SmartFigure authors"
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
            <b-col xl="7">
                <section class="image">
                    <div class="content bg-dark">
                        <img
                            :src="fullImageUrl"
                            :alt="'image for' + expandedPanel.title"
                            tabindex="0"
                            @click="openLightBox"
                            style="cursor:pointer"
                            draggable="false"
                            class="sd-panel-main-image"
                            />

                        <font-awesome-icon
                            @click="openLightBox"
                            class="sd-panel-zoom-icon"
                            icon="search"
                            size="2x"
                            title="View image" />

                        <button
                            class='edit text-xxs'
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
                    </div>
                </section>

                <section class="description">
                    <h2 class="text-xxs">
                        Description
                    </h2>

                    <div v-if="editingCaption" class="content panel-detail-caption-edit">
                        <caption-editor></caption-editor>
                    </div>

                    <div v-else class="content panel-detail-caption">
                        {{ expandedPanel.caption }}
                    </div>

                    <button
                        class='edit text-xxs'
                        v-if="!editingCaption && iCanEditThisPanel"
                        @click="editPanelCaption"
                        title="Edit SmartFigure description"
                    >
                        <font-awesome-icon icon="pen" />
                    </button>

                    <span class="sd-license-notice text-xxs" v-if="isPublic">
                        <font-awesome-icon :icon="['fab', 'creative-commons']" /> 2022 The Authors. Published under the
                        terms of the <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank">CC BY 4.0</a>
                        license.
                    </span>
                </section>

                <section class="comments">
                    <h2 class="text-xxs">
                        Comments
                    </h2>

                    <div class="content">
                        <comment-list></comment-list>
                    </div>
                </section>
            </b-col>

            <b-col xl="5">
                <div class="sources-and-keywords">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a
                                class="nav-link active"
                                id="panel-sources-tab"
                                data-toggle="tab"
                                href="#panel-sources"
                                role="tab"
                                aria-controls="panel-sources"
                                aria-selected="true"
                            >
                                <h2 class="section-heading text-xxs">
                                    Sources ({{ fileCount }})
                                </h2>
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a
                                class="nav-link"
                                id="panel-keywords-tab"
                                data-toggle="tab"
                                href="#panel-keywords"
                                role="tab"
                                aria-controls="panel-keywords"
                            >
                                <h2 class="section-heading text-xxs">
                                    Keywords
                                </h2>
                            </a>
                        </li>
                    </ul>

                    <div class="content tab-content text-xxs">
                        <section
                            class="tab-pane fade show active"
                            id="panel-sources"
                            role="tabpanel"
                            aria-labelledby="panel-sources-tab"
                        >
                            <file-uploads></file-uploads>
                        </section>

                        <section
                            class="tab-pane fade"
                            id="panel-keywords"
                            role="tabpanel"
                            aria-labelledby="panel-keywords-tab"
                        >
                            <div>
                                <label>
                                    Measured Variables

                                    <a
                                        href="#"
                                        v-b-tooltip.hover.click.blur.top
                                        title="Variables that were observed, measured or  described in this experiment"
                                    >
                                        <font-awesome-icon icon="info-circle" />
                                    </a>
                                </label>

                                <smart-tags-category type="assay"></smart-tags-category>
                            </div>

                            <div>
                                <label>
                                    Controlled Variables

                                    <a
                                        href="#"
                                        v-b-tooltip.hover.click.blur.top
                                        title="Variables tested by controlled experimentation for their potential causal effect on (some of) the measured variables (controlled pharmacological treatment, genetic perturbation, surgical intervention, ...)"
                                    >
                                        <font-awesome-icon icon="info-circle" />
                                    </a>
                                </label>

                                <smart-tags-category type="intervention"></smart-tags-category>
                            </div>

                            <div>
                                <label>
                                    Instruments / Methods

                                    <a
                                        href="#"
                                        v-b-tooltip.hover.click.blur.top
                                        title="Methods, measurement platforms, experimental assays"
                                    >
                                        <font-awesome-icon icon="info-circle" />
                                    </a>
                                </label>

                                <smart-tags-category type="method"></smart-tags-category>
                            </div>

                            <div>
                                <label>
                                    General Keywords

                                    <a
                                        href="#"
                                        v-b-tooltip.hover.click.blur.top
                                        title="Free text keywords"
                                    >
                                        <font-awesome-icon icon="info-circle" />
                                    </a>
                                </label>

                                <smart-tags-category type="other"></smart-tags-category>
                            </div>
                            <copy-tags></copy-tags>
                        </section>
                    </div>
                </div>

                <section class="sharing">
                    <h2 class="text-xxs">
                        Sharing options
                    </h2>

                    <div class="content text-xxs">
                        <panel-access-links></panel-access-links>
                    </div>
                </section>

                <section class="panel-actions container-fluid">
                    <h2 class="text-xxs" hidden>
                        Actions
                    </h2>

                    <div class="content row">
                        <b-col cols="8">
                            <b-button
                                v-if="iCanEditThisPanel"
                                id="sd-delete-panel"
                                variant="danger"
                                class="float-left mr-2 mb-2"
                            >
                                <font-awesome-icon
                                    class="sd-delete-panel-icon"
                                    icon="trash-alt"
                                    title="Delete panel" />
                                Delete SmartFigure
                            </b-button>

                            <b-popover
                                v-if="iCanEditThisPanel"
                                ref="delete-panel-popover"
                                target="sd-delete-panel"
                                triggers="click"
                                placement="top"
                                custom-class="sd-custom-popover"
                            >
                                <template v-slot:title>
                                    Are you sure?
                                    <b-button
                                        @click="closeDeletePanelPopover"
                                        class="close"
                                        aria-label="Close"
                                    >
                                        <span class="d-inline-block" aria-hidden="true">&times;</span>
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

                            <!-- report post -->
                            <b-button
                            v-b-tooltip.hover.click.blur.top
                            title="Report inappropriate content"
                            variant="secondary"
                            :href="reportLinkContent"
                            class="float-left sd-report-content-button mb-2"
                            >
                                <font-awesome-icon
                                class="sd-report-content-icon"
                                icon="exclamation-triangle"
                                title="Report content" />
                                Report
                            </b-button>

                            <!-- duplicate this panel -->
                            <b-button
                            v-if="iCanEditThisPanel"
                            v-b-tooltip.hover.click.blur.top
                            title="Make a copy of this panel along with groups, tags and authors"
                            variant="primary"
                            class="float-left sd-duplicate-panel-button mb-2"
                            @click.prevent="duplicateThisPanel"
                            >
                                <font-awesome-icon
                                class="sd-report-content-icon"
                                icon="plus-circle"
                                title="Duplicate panel" />
                                Duplicate Panel
                            </b-button>
                        </b-col>

                        <b-col>
                            <download-bar class="float-right"></download-bar>
                        </b-col>
                    </div>
                </section>
            </b-col>
        </b-row>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CommentList from "@/components/comments/CommentList";
import FileUploads from "@/components/files/FileUploads";
import CaptionEditor from "@/components/caption/CaptionEditor";
import SmartTagsCategory from '@/components/tags/SmartTagsCategory'
import CopyTags from '@/components/tags/CopyTags'
import PanelAccessLinks from "@/components/panelaccesslinks/PanelAccessLinks";
import DownloadBar from "@/components/DownloadBar";
import AuthorList from "@/components/panel-detail/AuthorList";

export default {
    name: "PanelDetail",
    components: {
        CommentList,
        FileUploads,
        CaptionEditor,
        SmartTagsCategory,
        CopyTags,
        DownloadBar,
        PanelAccessLinks,
        AuthorList,
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
            "apiUrls",
            "expandedPanel",
            "iOwnThisPanel",
            "iHaveAuthorPrivileges",
            "commentCount",
            "fileCount",
            "editingCaption",
            "expandedPanelAuthors",
            "viewUrls",
        ]),
        panelUrl(){
            return this.viewUrls.panel(this.expandedPanel);
        },
        fullImageUrl() {
            return this.apiUrls.panelImage(this.expandedPanel);
        },
        commentCountTitle() {
            return "Discuss (" + this.commentCount + ")";
        },
        iCanEditThisPanel() {
            return (this.iOwnThisPanel || this.iHaveAuthorPrivileges)
        },
        isPublic(){
            return !!(this.expandedPanel.is_public);
        },
        reportLinkContent() {
            let link = 'mailto:' + process.env.MIX_FEEDBACK_RECIPIENT + '?subject=';
            link += encodeURI('[Panel ID ' + this.expandedPanel.id + '] ') + 'Report%20of%20Inappropriate%20SDash%20Content&body=';
            link += encodeURI('The panel: "' + this.expandedPanel.title + '" contains innapropriate content.\r\n\r\n Please review.\r\n\r\n' + this.viewUrls.panel(this.expandedPanel));
            return link;
        },
    },
    methods: {
        //run as event handlers, for example
        ...mapActions(['duplicatePanel', 'closeExpandedPanels']),
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
                        this.$store.commit("updateLoadedPanel", updatedPanel);
                    })
                    .catch(error => {
                        this.$snotify.error(
                            error.message,
                            "SmartFigure Update Failed"
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
                    this.$emit("sd-request-close-expanded-panel");
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
                        "SmartFigure Updated"
                    );
                })
                .catch(error => {
                    this.$snotify.error("Could not update SmartFigure", "Failed");
                });
        },
        editAuthorList(){
            this.$store.commit("setAuthorSidebar", true);
        },
        emitResizeEvent() {
            this.$emit('resized', this.$el.clientHeight);
        },
        duplicateThisPanel() {
            this.duplicatePanel().then(response => {
                this.closeExpandedPanels();
                this.$emit("sd-request-close-expanded-panel");
                this.$snotify.success(response.data.MESSAGE, 'Panel Duplicated')
             }).catch( error => {
                 this.$snotify.error('Could not copy the panel', 'Failed');
             });
        },
    },
    mounted: function() {
        new ResizeObserver(_.debounce(this.emitResizeEvent, 150)).observe(this.$el);
    },
    destroyed: function() {
        this.$emit('resized', undefined);
    },
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
a, h1, h2, h3, h4, h5, h6 {
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
    font-style: normal;
}

/*********************
 * Content containers
 *********************/
$content-padding-left: 0.75rem;
$content-padding-right: $content-padding-left;

$content-padding-top: 1rem;
$content-padding-bottom: 0.75rem;

section {
    border: 1px solid $mostly-white-gray;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    padding-bottom: $content-padding-bottom;
    padding-left: $content-padding-left;
    padding-right: $content-padding-right;
    padding-top: $content-padding-top;
    position: relative;
}
section > h2,
.section-heading {
    background-color: $very-dark-desaturated-blue;
    color: inherit;
    padding: 0 0.4rem;

    position: absolute;
    top: -0.65rem;
    left: 1rem;
}
section > .content {
    min-height: 5rem;
    max-height: 20rem;
    overflow-y: auto;
}

/*********************
 * Title row
 *********************/
h1 a {
    /* Leave enough space on the right for the edit icon */
    display: inline-block;
    max-width: calc(100% - 5rem);
    overflow: hidden;
    white-space: nowrap;
    text-decoration: underline;
    text-overflow: ellipsis;
}
h1 .edit {
    position: relative;
    top: -0.75rem;
}

address .edit {
    position: relative;
    top: -0.25rem;
}

/*********************
 * Image
 *********************/
section.image {
    border: none;
    padding: 0;
    /* Makes the absolutely positioned icons (see below) relative to this container. */
    position: relative;

    .content {
        max-height: 40rem;
    }

    img {
        // transparent images get a white background
        background-color: white;
        display: block;
        margin: auto;
        max-width: 100%;
        max-height: 40rem;
    }

    button.edit {
        background-color: $mostly-black-blue;
        border-radius: 50%;
        height: 2rem;
        width: 2rem;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
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

/*********************
 * Sources & Keywords
 *********************/
.sources-and-keywords section {
    border-top-left-radius: 0;
}

.sources-and-keywords .nav-tabs {
    border-bottom: none;
}

.sources-and-keywords .nav-item {
    margin-right: 0.5rem;
    width: 40%;
}

.sources-and-keywords .nav-link {
    background-color: $very-dark-desaturated-blue;
    border: 1px solid $mostly-white-gray;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    color: inherit;
    height: 1.5rem;

    /* Make the h2 positioning work by setting position to relative */
    position: relative;
}

.sources-and-keywords .nav-link.active {
    border-bottom-color: $very-dark-desaturated-blue;
    z-index: 10;
}

/*********************
 * Sharing options
 *********************/
section.sharing {
    padding: 0;
}

section.sharing .content {
    max-height: 60rem;
}

/*********************
 * Panel actions
 *********************/
section.panel-actions {
    border: none;
    padding-top: 0;
}
section.panel-actions .content {
    min-height: 0;
    overflow-y: initial;
}
</style>
