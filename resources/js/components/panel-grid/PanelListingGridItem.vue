<template>
    <li :id="itemId" class="sd-grid-item">
        <div class="sd-grid-image-container">
            <div
                class="sd-grid-image-container-inner"
                tabindex="0"
                @click="showPanelDetail"
            >
                <header class="sd-grid-item--image-header">
                    <label
                        type="button"
                        class="panel-select-button"
                        :class="{'bg-success': panelSelected}"
                        v-on:click.stop="toggleSelected"
                        v-if="!batchSelectDisabled && IOwnThisPanel"
                    >

                        <font-awesome-icon icon="check" size="xs" />
                    </label>

                    <label
                        v-else
                        class="panel-select-button panel-select-explanation"
                        v-on:click.stop.prevent
                        v-b-popover.hover.top="{
                            customClass: 'sd-custom-popover',
                            content: 'You cannot select this SmartFigure because you are not its owner.'
                        }"
                    >
                        <font-awesome-icon icon="question" size="xs" />
                    </label>
                </header>

                <img class="sd-grid-image" v-lazy="thumbnailUrl" draggable="false"/>

                <footer
                    class="sd-grid-item--image-footer"
                    :id="'scroll-anchor-' + panel.id"
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
                        title="Private SmartFigure"
                    />
                    <font-awesome-icon
                        :class="panelAccessReason"
                        icon="lock-open"
                        v-if="panelAccessReason == 'public'"
                        title="Public SmartFigure"
                    />
                    <font-awesome-icon
                        :class="panelAccessReason"
                        icon="exchange-alt"
                        v-if="panelAccessReason == 'group'"
                        title="Shared with group"
                    />
                </footer>
            </div>
        </div>

        <div class="sd-grid-item-text">
            <h6 class="panel-title" :title="panel.title">
                {{ panel.title }}
            </h6>

            <address class="panel-authors" :title="panelAuthorsAbbreviated">
                {{ panelAuthorsAbbreviated }}
            </address>
        </div>
    </li>
</template>

<script>
import { mapGetters } from "vuex";
import AuthorTypes from "@/definitions/AuthorTypes";

export default {
    name: "PanelListingGridItem",
    components: {},
    props: {
        panel: Object,
        batchSelectDisabled: Boolean,
    },

    data() {
        return {};
    } /* end of data */,

    computed: {
        ...mapGetters([
            "apiUrls",
            "currentUser",
            "loadedPanels",
            "panelAuthors",
            "selectedPanels",
        ]),
        panelAuthorsAbbreviated() {
            let authors = this.panelAuthors(this.panel).filter(
                author => author.author_role !== AuthorTypes.CURATOR
            );
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

            if (authors.length == 2) {
                return `${nameFirstAuthor}, ${nameLastAuthor}`;
            }
            return `${nameFirstAuthor} [...] ${nameLastAuthor}`;
        },
        thumbnailUrl() {
            return this.apiUrls.panelThumbnail(this.panel);
        },
        itemId() {
            return "grid-item-" + this.panel.id;
        },
        panelAccessReason() {
            let thisPanel = this.panel;
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
            return this.panel.user_id === this.currentUser.id;
        },
        IAmAnAuthor() {
            let thisPanel = this.panel
            if (thisPanel.authors && thisPanel.authors.length > 0) {
                return (thisPanel.authors.filter(({id}) => id === this.currentUser.id).length > 0)
            }

            return false
        },
        panelSelected() {
            if (this.selectedPanels.length === 0) return false;
            return _.includes(this.selectedPanels, this.panel.id);
        },
    },

    methods: {
        //run as event handlers, for example
        toggleSelected() {
            if (this.panelSelected) {
                this.$store.dispatch("deselectPanel", this.panel.id);
            } else {
                this.$store.dispatch("selectPanel", this.panel.id);
            }
        },
        showPanelDetail() {
            this.$emit('show-panel-detail', this.panel.id);
        },
    },
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';

$image-height: 233px;

.sd-grid-item {
    // Having the container as display:table and the text child as display:table-caption lets us limit the width of the
    // text to the width of the image above it. Adapted from https://stackoverflow.com/a/25386583/3385618
    display: table;
    box-sizing: border-box;
    transition: all 0.3s ease-in;
    outline: 1px red;
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-item {
        max-width: 48%;
        min-width: 31%;
        width: unset;
    }
}
@media (min-width: 768px) {
    .sd-grid-item {
        max-width: 31%;
        min-width: 16%;
    }
}

.sd-grid-image-container {
    // flex display to center the image inside this container
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-image-container {
        height: $image-height;
    }
}

.sd-grid-image-container-inner {
    // transparent images get a white background
    background-color: white;
    // indicate that the image can be interacted with
    cursor: pointer;
    // needed to position <header> with the panel select button and the panel access reasons in the <footer> at the
    // bottom-right of the image
    position: relative;
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-image-container-inner {
        width: auto; // override the width set above
    }
}

.sd-grid-image {
    width: 100%;
}
@media (min-width: 576px) {
    .sd-grid-image {
        max-height: $image-height;
        max-width: 100%;
        width: auto; // override the width set above
    }
}

$title-font-size: $font-size-sm;
$title-height: $font-size-md;
$authors-font-size: $font-size-xs;
$authors-height: $font-size-sm;
.sd-grid-item-text {
    // see comment at .sd-grid-item
    display: table-caption;
    caption-side: bottom;
    padding-top: 0.25rem;

    .panel-title {
        font-size: $title-font-size;
        line-height: $title-height;
        max-height: $title-height;
        margin-bottom: 0.25rem;
    }
    .panel-authors {
        font-size: $authors-font-size;
        font-weight: lighter;
        line-height: $authors-height;
        max-height: $authors-height;
        margin: 0;
    }

    /* We have some requirements for the title and authors:
     * 1) They must each take up only a single line. 
     * 2) They must be only as wide as the panel image they belong to (or slightly wider if the image is very narrow:
     *    their target width then comes from the min-width set on their surrounding sd-grid-item).
     * 3) If their text overflows they must indicate to users that there is more text.
     *
     * The only way to accomplish 2) in CSS seems to be using `display: table-caption`. Since we then can't set an
     * explicit width, and that is a requirement for text-overflow: ellipsis, 3) can't be accomplished that way. This
     * solution here uses a fade-out instead to show that there is more text than can be shown.
     */
    .panel-title,
    .panel-authors {
        // relative positioning is needed here to absolutely position the fade-out below
        position: relative;
        // hide any text overflow beyond the first line
        overflow: hidden;
        // break words as close to the edge of the first line as possible
        word-break: break-all;
    }
    // These pseudo-elements generate the fade-out effect through a linear gradient from transparent to the background
    // color.
    .panel-title::after,
    .panel-authors::after {
        content: "";
        text-align: right;
        position: absolute;
        top: 0;
        right: 0;
        width: 15%;
        background: linear-gradient(to right, rgba(02, 01, 38, 0), rgba(02, 01, 38, 1) 100%);
    }
    .panel-authors::after {
        height: $authors-height;
    }
    .panel-title::after {
        height: 1.3rem;
    }
}

$panel-select-button-diameter: $image-height * 0.1;
.sd-grid-item--image-header {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    height: $panel-select-button-diameter * 2;
    width: 100%;
    padding: 6px;

    .panel-select-explanation {
        background-color: transparent;

        svg {
            color: transparent;
        }
    }
}
.sd-grid-image-container-inner:hover .sd-grid-item--image-header {
    background-image: linear-gradient(
        to bottom,
        rgba(0, 0, 0, .50),
        rgba(0, 0, 0, .30),
        transparent
    );

    .panel-select-explanation {
        background-color: $light-gray;

        svg {
            color: gray;
        }
    }
}

.sd-grid-item--image-footer {
    position: absolute;
    text-align: right;
    bottom: 0;
    left: 0;
    z-index: 2;
    width: 100%;
    padding: 14px 6px 3px;

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
.sd-grid-image-container-inner:hover .sd-grid-item--image-footer {
    background-image: linear-gradient(
        to top,
        rgba(0, 0, 0, .50),
        rgba(0, 0, 0, .30),
        transparent
    );
}

.sd-grid-item--author-icon {
    color:#634782;
}

.panel-select-button {
    // vertically align the green checkmark inside the button
    display: flex;
    justify-content: center;
    align-items: center;

    // position the button within the header
    position: absolute;
    top: 6px;
    right: 6px;
    padding: 0;
    margin: 0;

    width: $panel-select-button-diameter;
    height: $panel-select-button-diameter;

    background-color: $mostly-white-gray;
    border-radius: 50%;

    svg {
        color: $light-gray;
    }
}
.panel-select-button:hover,
.panel-select-button:focus {
    background-color: $very-light-gray;

    svg {
        color: gray;
    }
}
.panel-select-button.bg-success svg {
    color: $mostly-white-gray;
}
.panel-select-button.bg-success:hover svg {
    color: $very-light-gray;
}
</style>
