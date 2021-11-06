<template>
    <li
        :id="itemId"
        class="sd-grid-item"
        :class="{ 'sd-grid-item__expanded': isExpanded }"
        :style="sdGridItemStyle"
    >
        <div class="sd-grid-image-container">
            <header class="sd-grid-item--image-header">
                <button
                    class="panel-select-button"
                    @click="toggleSelected"
                    v-if="IOwnThisPanel"
                >
                    <transition name="fade">
                        <font-awesome-layers
                            class="fa-2x panel-select-button--icon text-success"
                            v-if="panelSelected"
                        >
                            <font-awesome-icon icon="circle" />
                            <font-awesome-icon
                                icon="check"
                                transform="shrink-6"
                                :style="{ color: 'white' }"
                            />
                        </font-awesome-layers>
                    </transition>
                </button>
            </header>
            <div
                class="sd-grid-image-container-inner"
                @click="toggleExpanded"
                tabindex="0"
            >
                <img class="sd-grid-image" v-lazy="thumbnailUrl" draggable="false"/>

                <footer
                    class="sd-grid-item--image-footer"
                    :id="'scroll-anchor-' + panelId"
                >
                    <font-awesome-icon
                        class="sd-grid-item--author-icon"
                        icon="book"
                        v-if="IAmAnAuthor"
                        title="Author"
                    />
                    <font-awesome-icon
                        :class="panelAccessReason"
                        icon="lock"
                        v-if="panelAccessReason == 'private'"
                        title="Private panel"
                    />
                    <font-awesome-icon
                        :class="panelAccessReason"
                        icon="lock-open"
                        v-if="panelAccessReason == 'public'"
                        title="Public panel"
                    />
                    <font-awesome-icon
                        :class="panelAccessReason"
                        icon="exchange-alt"
                        v-if="panelAccessReason == 'group'"
                        title="Shared with group"
                    />
                </footer>
            </div>

            <div class="sd-grid-item-text">
                <h6 class="panel-title">
                    {{ thisPanel.title }}
                </h6>

                <address class="panel-authors">
                    {{ panelAuthorsAbbreviated }}
                </address>
            </div>

            <div class="css_arrow" v-if="isExpanded"></div>
        </div>

        <div
            class="sd-grid-extra"
            id="panel-detail"
            v-if="isExpanded"
            :style="sdGridExtraStyle"
        >
            <button
                type="button"
                aria-label="Close"
                class="close sd-grid-extra--close text-light"
                @click.prevent="toggleExpanded"
            >
                <span aria-hidden="true">&#10005;</span>
            </button>

            <b-row v-if="!hasPanelDetail">
                <b-col class="text-center">
                    <b-spinner
                        variant="primary"
                        label="Spinning"
                        class="m-5"
                        style="width: 4rem; height: 4rem;"
                    ></b-spinner>
                </b-col>
            </b-row>

            <panel-detail v-if="hasPanelDetail" @resized="updatePanelDetailHeight"></panel-detail>
        </div>
    </li>
</template>

<script>
import { mapGetters } from "vuex";
import store from "@/stores/store";
import PanelDetail from "./PanelDetail";
import AuthorTypes from "@/definitions/AuthorTypes";

export default {
    name: "PanelListingGridItem",
    components: { PanelDetail },
    props: {
        panelId: Number
    },

    data() {
        return {
            panelDetailHeight: false,
        };
    } /* end of data */,

    computed: {
        ...mapGetters([
            "apiUrls",
            "currentUser",
            "expandedPanelId",
            "hasPanelDetail",
            "loadedPanels",
            "panelAuthors",
            "selectedPanels",
        ]),
        panelAuthorsAbbreviated() {
            let authors = this.panelAuthors(this.thisPanel);
            if (authors.length <= 0) {
                return '';
            }

            let getAuthorDisplayName = author => `${author.firstname} ${author.surname}`,
                nameFirstAuthor = getAuthorDisplayName(authors[0]);

            if (authors.length == 1) {
                return nameFirstAuthor;
            }
            let lastAuthor = authors[authors.length - 1],
                nameLastAuthor = getAuthorDisplayName(lastAuthor);
            return `${nameFirstAuthor} [...] ${nameLastAuthor}`;
        },
        thumbnailUrl() {
            return this.apiUrls.panelThumbnail(this.thisPanel);
        },
        thisPanel() {
            let thisPanel = this.loadedPanels.filter(
                panel => panel.id === this.panelId
            );
            if (thisPanel) {
                return thisPanel[0];
            }
            return false;
        },
        isExpanded() {
            return this.panelId === this.expandedPanelId;
        },
        itemId() {
            return "grid-item-" + this.panelId;
        },
        panelAccessReason() {
            let thisPanel = this.thisPanel;
            if (
                !thisPanel.is_public &&
                thisPanel.groups.length === 0 &&
                thisPanel.user_id === this.currentUser.id
            )
                return "private";
            if (thisPanel.is_public) return "public";
            if (thisPanel.groups.length > 0) return "group";
        },
        IOwnThisPanel() {
            return this.thisPanel.user_id === this.currentUser.id;
        },
        IAmAnAuthor() {
            let thisPanel = this.thisPanel
            if (thisPanel.authors && thisPanel.authors.length > 0) {
                return (thisPanel.authors.filter(({id}) => id === this.currentUser.id).length > 0)
            }

            return false
        },
        panelSelected() {
            if (this.selectedPanels.length === 0) return false;
            return _.includes(this.selectedPanels, this.panelId);
        },
        sdGridItemStyle() {
            return this.panelDetailHeight ? {
                'margin-bottom': (this.panelDetailHeight + 32) + 'px',
            } : {};
        },
        sdGridExtraStyle() {
            return this.panelDetailHeight ? {
                'max-height': this.panelDetailHeight + 'px',
                'height': this.panelDetailHeight + 'px',
            } : {};
        },
    },

    methods: {
        //run as event handlers, for example

        toggleExpanded() {
            if (this.isExpanded) {
                this.$store.dispatch("closeExpandedPanels");
            } else {
                this.$store.dispatch("expandPanel", this.panelId);
                this.$store.dispatch("loadPanelDetail", this.panelId);
                this.$scrollTo("#scroll-anchor-" + this.panelId, 500, {
                    offset: -60
                });
            }
        },
        toggleSelected() {
            if (this.panelSelected) {
                this.$store.dispatch("deselectPanel", this.panelId);
            } else {
                this.$store.dispatch("selectPanel", this.panelId);
            }
        },
        updatePanelDetailHeight(newHeight) {
            this.panelDetailHeight = newHeight ? newHeight : false;
        },
    },
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';

.sd-grid-item__expanded .sd-grid-extra {
    min-height: 25rem;
    margin-left: 30px;
    margin-right: 30px;
}
.sd-grid-item.sd-grid-item__expanded {
    margin-bottom: 27rem;
}

$panel-thumbnail-height: 20rem;
$panel-title-font-size: $font-size-md;
$panel-authors-font-size: $font-size-sm;
$panel-title-max-height: 2.5 * $panel-title-font-size;
$panel-authors-max-height: 2.5 * $panel-authors-font-size;
$panel-text-margins: 1.5rem;

.sd-grid-item {
    box-sizing: border-box;
    height: $panel-thumbnail-height + $panel-title-max-height + $panel-authors-max-height + $panel-text-margins;
    margin: 10px 30px;
    transition: all 0.3s ease-in;
    outline: 1px red;
}

.sd-grid-image-container {
    height: 100%;
    position: relative;
}


.sd-grid-image-container-inner {
    cursor: pointer;
    height: $panel-thumbnail-height;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.sd-grid-image {
    display: block;
    height: 100%;
    max-width: 100%;
    width: auto;
}

.sd-grid-item-text {
    height: $panel-title-max-height + $panel-authors-max-height;
    padding-top: 0.5rem;
    /* Take the element out of the flow to limit its width. */
    position: absolute;
    width: 100%;
}
.sd-grid-item-text * {
    overflow: hidden;
    text-overflow: ellipsis;
}
.sd-grid-item-text .panel-title {
    font-size: $panel-title-font-size;
    max-height: $panel-title-max-height;
}
.sd-grid-item-text .panel-authors {
    font-size: $panel-authors-font-size;
    font-weight: lighter;
    line-height: 1.2;
    max-height: $panel-authors-max-height;
}

.sd-grid-extra {
    border-radius: 0.75rem;
    cursor: default;
    position: absolute;
    left: 0;
    width: 100%;
    background-color: $very-dark-desaturated-blue;
    color: #ddd;
    font-size: 16px;
    margin-top: 6px;
    max-height: 0;
    height: 0;
    overflow: hidden;
    transition: all 0.3s ease-in;
    z-index: 5;
}
.sd-grid-extra--close {
    position: absolute;
    font-size: 1.5em;
    top: 1rem;
    right: 2vw;
    opacity: 1;
}
.sd-grid-extra--wrapper {
    height: 100%;
    display: flex;
    flex-wrap: nowrap;
}
.sd-grid-extra--wrapper > div {
    padding: 48px;
}
.sd-grid-extra--image-container {
    flex: 0 0 50%;
    display: flex;
}
.sd-grid-extra--info-container {
    flex: 0 0 50%;
}
.sd-grid-extra--image-wrapper {
    height: 100%;
    width: 100%;
    text-align: center;
}
.sd-grid-extra--image {
    max-width: 100%;
    max-height: 100%;
    display: none;
}
.css_arrow {
    display: none;
}
.sd-grid-item__expanded .css_arrow {
    display: block;
    width: 0px;
    height: 0px;
    border: solid 15px transparent;
    border-bottom: solid 20px $very-dark-desaturated-blue;
    border-top: none;
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-10px);
}
.sd-grid-actions {
    margin-top: 1em;
}
.sd-grid-item--image-header {
    cursor: auto;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    width: 100%;
    padding: 6px;
}

.sd-grid-item--image-footer {
    position: absolute;
    text-align: right;
    bottom: 0;
    left: 0;
    z-index: 2;
    width: 100%;
    padding: 3px 6px;

    .private {
        color: #882323;
    }

    .public {
        color: #237788;
    }

    .group {
        color: #235588;
    }
}

.sd-grid-item--author-icon {
    color:#634782;
}

.panel-select-button {
    position: absolute;
    top: 6px;
}

.panel-select-button {
    right: 6px;
    padding: 0;
    margin: 0;
    background: none;
    border: solid 2px $mostly-white-gray;
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
