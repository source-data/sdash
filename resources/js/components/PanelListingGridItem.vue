<template>
    <li :id="itemId" class="sd-grid-item">
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
                v-b-modal="modalId"
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
        </div>

        <b-modal
            :id="modalId"
            hide-header hide-footer
            static lazy
            @show="showPanel" @hidden="hidePanel"
        >
            <div class="sd-grid-extra">
                <button
                    type="button"
                    aria-label="Close"
                    class="close sd-grid-extra--close text-light"
                    @click="$bvModal.hide(modalId)"
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
        </b-modal>
    </li>
</template>

<script>
import { mapGetters } from "vuex";
import PanelDetail from "./PanelDetail";

export default {
    name: "PanelListingGridItem",
    components: { PanelDetail },
    props: {
        panelId: Number
    },

    data() {
        return {};
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
        modalId() {
            return "panel-detail-modal-" + this.panelId;
        }
    },

    methods: {
        //run as event handlers, for example
        showPanel() {
            this.$store.dispatch("expandPanel", this.panelId);
            this.$store.dispatch("loadPanelDetail", this.panelId);
        },
        hidePanel() {
            this.$store.dispatch("closeExpandedPanels");
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

.sd-grid-item {
    box-sizing: border-box;
    transition: all 0.3s ease-in;
    outline: 1px red;
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-item {
        width: unset;
    }
}

.sd-grid-image-container {
    // Having the container as display:table and the text child as display:table-caption lets us limit the width of the
    // text to the width of the image above it. Adapted from https://stackoverflow.com/a/25386583/3385618
    display: table;
    // needed to position the <header> with the panel select button
    position: relative;
    width: 100%;
}

.sd-grid-image-container-inner {
    // indicate that the image can be interacted with
    cursor: pointer;
    // needed to position the panel access reasons in the <footer> at the bottom-right of the image
    position: relative;
}

.sd-grid-image {
    display: block;
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-image {
        display: block;
        height: 250px;
        max-width: 100%;
        width: auto;
    }
}

.sd-grid-item-text {
    // see comment at .sd-grid-image-container
    display: table-caption;
    caption-side: bottom;
    max-height: 100px;
    overflow: hidden;

    padding-top: 0.25rem;

    .panel-title {
        font-size: $font-size-md;
        line-height: 1.6rem;
        max-height: 3.2rem;
        word-break: break-all;
        margin-bottom: 0.25rem;
        overflow: hidden;
    }
    .panel-authors {
        font-size: $font-size-sm;
        font-weight: lighter;
        line-height: 1.3rem;
        max-height: 2.6rem;
        margin: 0;
        overflow: hidden;
    }
}

.sd-grid-extra {
    width: 100%;
    background-color: $very-dark-desaturated-blue;
}
.sd-grid-extra--close {
    position: absolute;
    font-size: 1.5em;
    top: 1rem;
    right: 2vw;
    opacity: 1;
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
    right: 6px;
    padding: 0;
    margin: 0;
    background: none;
    border: solid 2px $mostly-white-gray;
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.sd-grid-item::v-deep .modal-dialog {
    max-width: initial;
    margin: 5rem 0;
}
@media (min-width: 768px) {
    .sd-grid-item::v-deep .modal-dialog {
        margin: 5rem 1rem;
    }
}
.sd-grid-item::v-deep .modal-body {
    padding: 0;
}
</style>
