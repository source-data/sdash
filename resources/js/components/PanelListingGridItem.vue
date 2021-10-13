<template>
    <li
        :id="itemId"
        class="sd-grid-item"
        :class="{ 'sd-grid-item__expanded': isExpanded }"
    >
        <div class="sd-grid-image-container">
            <header class="sd-grid-item--image-header">
                <span class="sd-grid-item--image-label">{{
                    panelOwnerName
                }}</span>
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
            </div>
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
            <div class="css_arrow" v-if="isExpanded"></div>
        </div>

        <div
            class="sd-grid-extra"
            id="panel-detail"
            ref="listGridExtra"
            v-if="isExpanded"
        >
            <button
                type="button"
                aria-label="Close"
                class="close sd-grid-extra--close"
                @click.prevent="toggleExpanded"
            >
                <span aria-hidden="true">&times;</span>
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
            <panel-detail v-if="hasPanelDetail"></panel-detail>
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
        return {};
    } /* end of data */,

    computed: {
        ...mapGetters([
            "expandedPanelId",
            "hasPanelDetail",
            "loadedPanels",
            "currentUser",
            "selectedPanels"
        ]),
        thumbnailUrl() {
            return (
                "/panels/" +
                this.thisPanel.id +
                "/image/thumbnail?v=" +
                this.thisPanel.version
            );
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
        panelOwnerName() {
            const authorList = [...this.thisPanel.authors, ...this.thisPanel.external_authors].sort((a,b) => a.author_role.order - b.author_role.order);
            const firstAuthor = authorList.find( author => author.author_role.role !== AuthorTypes.CURATOR);
            if (firstAuthor === undefined) {
                return this.thisPanel.user.firstname + ' ' + this.thisPanel.user.surname
            }
            return firstAuthor.firstname + ' ' + firstAuthor.surname;
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
        }
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
        }
    }
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

$sd-extra-height: 800; // info box height in pixels

.sd-grid-item {
    cursor: pointer;
    flex-grow: 1;
    box-sizing: border-box;
    height: 280px;
    min-width: 240px;
    max-width: 50%;
    margin: 8px;
    transition: all 0.3s ease-in;
    outline: 1px red;
}

.sd-grid-item.sd-grid-item__expanded {
    margin-bottom: $sd-extra-height + 32 + px;
}

.sd-grid-image-container {
    height: 100%;
    position: relative;
    background: rgb(166, 178, 198);
    background: linear-gradient(145deg, #a6b2c6 31%, #8caeb5 98%);
    padding: 12px;
}

.sd-grid-image-container-inner {
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.sd-grid-image {
    display: block;
    max-height: 100%;
    max-width: 100%;
    width: auto;
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
.sd-grid-item__expanded .sd-grid-extra {
    max-height: $sd-extra-height + px;
    height: $sd-extra-height + px;
}
.sd-grid-extra--close {
    position: absolute;
    font-size: 2em;
    top: 4px;
    right: 8px;
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

.sd-grid-item--image-label,
.panel-select-button {
    position: absolute;
    top: 6px;
}

.sd-grid-item--image-label {
    left: 6px;
    padding: 5px 10px;
    font: 16px Helvetica, Arial, sans-serif;
    line-height: 1;
    color: rgb(176, 205, 219);
    background-color: rgb(96, 125, 139);
}

.panel-select-button {
    right: 6px;
    padding: 0;
    margin: 0;
    background: none;
    border: solid 1px grey;
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
