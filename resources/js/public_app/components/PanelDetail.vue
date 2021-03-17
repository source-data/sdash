<template>
    <article>
        <b-row class="m-3 my-4 sd-panel-detail-title-wrapper">
            <div class="sd-panel-detail-title">
                <h3>
                    <a :href="panelUrl" target="_blank" title="Open panel in a new tab">
                    {{ expandedPanel.title }}
                    <font-awesome-icon icon="external-link-alt" size="sm" />
                    </a>
                </h3>
            </div>
            <div v-if="expandedPanelAuthors.length > 0">
                <ul class="sd-panel-author-list list-unstyled list-inline">
                    <li
                        class="sd-panel-author-list--item list-inline-item"
                        v-for="author in authors"
                        :key="'author-' + author.order + '-' + author.id"
                        :class="
                            'sd-panel-author-list--item_' + author.author_role
                        "
                    >
                        <b-link v-if="author.corresponding" :id="'popover-' + author.origin + '-' + author.id" href="#">
                            {{ author.firstname }} {{ author.surname }}
                            <font-awesome-icon icon="envelope" />
                        </b-link>
                        <span v-else>{{ author.firstname }} {{ author.surname }}</span>
                    </li>
                </ul>
                <div class="sd-panel-author-list--note">
                    <span class="sd-panel-author-list--corresponding-author-note">
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
                </b-popover>
            </div>

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
                <div class="panel-detail-caption-wrapper">
                    <div
                        class="panel-detail-caption-container"
                    >
                        {{ expandedPanel.caption }}
                    </div>
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
                            <file-uploads v-if="fileCount"></file-uploads>
                            <p v-if="!fileCount">No data sources attached</p>
                        </b-card-text>
                    </b-tab>
                    <b-tab title="Keywords">
                        <b-card-text>
                            <smart-tags-panel v-if="expandedPanel.tags.length > 0"></smart-tags-panel>
                            <p v-if="expandedPanel.tags.length === 0">No tags assigned</p>
                        </b-card-text>
                    </b-tab>
                </b-tabs>

                <div class="sd-panel-detail--panel-actions">
                    <download-bar></download-bar>
                </div>
            </b-col>
        </b-row>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CommentList from "@/components/comments/CommentList";
import FileUploads from "@/public_app/components/FileUploads";
import CaptionEditor from "@/components/caption/CaptionEditor";
import SmartTagsPanel from "@/public_app/components/SmartTagsPanel";
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
        };
    },
    computed: {
        ...mapGetters([
            "expandedPanel",
            "fileCount",
            "expandedPanelAuthors",
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
        panelUrl() {
            return this.link_base + '/' + this.expandedPanel.id
        },
        fullImageUrl() {
            return (
                "/public-api/panels/" +
                this.expandedPanel.id +
                "/image?v=" +
                this.expandedPanel.version
            );
        }
    },
    methods: {
        //run as event handlers, for example

        openLightBox() {
            this.$store.commit("toggleLightbox");
        },

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

.sd-panel-detail-title a {
    color: #6e89aa;
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

.sd-panel-author-list--edit-row {
    padding-left:15px;
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
    height: 265px;
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

.sd-panel-author-list--corresponding-author-note {
    color: orange;
    font-size: 0.85em;
}

.sd-panel-author-list--remove-me {
    border: none;
    padding: 0;
    line-height: 1px;
    border-radius: 50%;
    font-size: 16px;
    width: 16px;
    height: 16px;
    color: #fff;
    background-color: red;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.sd-remove-author-tooltip {
    .tooltip-inner {
        background-color:#eee;
        color: #333;
    }
    .arrow:before {
        border-left-color:#eee;
    }

}

</style>
