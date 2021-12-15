<template>
    <div class="sd-view-content">
        <div v-if="notAllowed">
            You are not allowed to view this panel.
        </div>

        <div v-if="otherError">
            An error occurred.
        </div>

        <article v-if="expandedPanel.id">
            <header>
                <h1 class="panel-title">{{ expandedPanel.title }}</h1>

                <address class="panel-authors">
                    <author-list></author-list>
                </address>
            </header>

            <section class="panel-image">
                <h2 hidden>Panel Image</h2>

                <img :src="panelImageUrl" :alt="'image for ' + expandedPanel.title" />
            </section>

            <section class="panel-description">
                <h2>
                    Description
                </h2>

                <div class="content">
                    {{ expandedPanel.caption }}
                </div>
            </section>

            <section class="panel-sources">
                <h2>
                    Sources
                </h2>

                <div class="content">
                    <table class="table table-sm table-borderless text-light">
                        <tr v-for="source in getFiles" :key="source.id">
                            <td>
                                <span>
                                    {{ getFileCategoryName(source) }}
                                </span>
                            </td>

                            <td>
                                <span v-if="source.description">
                                    {{ source.description }}
                                </span>
                            </td>

                            <td>
                                <a v-if="source.url" class="text-info" :href="source.url">
                                    {{ source.url }}
                                </a>

                                <a v-else class="text-info" :href="'/files/' + source.id">
                                    {{source.original_filename}}
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>

            <section class="panel-keywords-experiment">
                <h2>
                    Experimental Design Keywords
                </h2>

                <div class="content">
                    <div>
                        Measured Variables
                    </div>

                    <div class="panel-keywords">
                        <span class="panel-keyword" v-for="tag in assayTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>
            
                    <div>
                        Controlled Variables
                    </div>

                    <div class="panel-keywords">
                        <span class="panel-keyword" v-for="tag in interventionTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>
            
                    <div>
                        Instruments / Methods
                    </div>

                    <div class="panel-keywords">
                        <span class="panel-keyword" v-for="tag in methodTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="panel-keywords-general">
                <h2>
                    General Keywords
                </h2>

                <div class="content panel-keywords">
                    <span class="panel-keyword" v-for="tag in methodTags" :key="tag.id">
                        {{ tag.content }}
                    </span>
                </div>
            </section>

            <section class="panel-download-links">
                <h2>
                    Download SmartFigure
                </h2>

                <div class="content">
                    <div class="panel-download-link">
                        <a class="text-info" :href="'/panels/' + expandedPanel.id + '/zip'">
                            Archive containing all files (.zip)
                        </a>
                    </div>

                    <div class="panel-download-link">
                        <a class="text-info" :href="'/panels/' + expandedPanel.id + '/dar'">
                            SmartFigure Editor document (.smartfigure)
                        </a>
                    </div>

                    <div class="panel-download-link">
                        <a class="text-info" :href="'/panels/' + expandedPanel.id + '/pdf'">
                            Adobe Acrobat Reader file (.pdf)
                        </a>
                    </div>

                    <div class="panel-download-link">
                        <a class="text-info" :href="'/panels/' + expandedPanel.id + '/powerpoint'">
                            Microsoft PowerPoint slide (.pptx)
                        </a>
                    </div>

                    <div class="panel-download-link">
                        <a class="text-info" :href="'/panels/' + expandedPanel.id + '/original'">
                            Original image file
                        </a>
                    </div>
                </div>
            </section>

            <footer>
                <p v-if="expandedPanel.is_public">
                    &copy; 2021 The Authors. This figure is licensed under the terms of the
                    <a class="text-info" href="https://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution License</a>,
                    which permits use, distribution and reproduction in any medium, provided the original work is
                    properly cited.
                </p>

                <p>
                    <Strong>Created:</Strong> {{ formatDate(expandedPanel.created_at) }} | <strong>Last updated:</strong> {{ formatDate(expandedPanel.updated_at) }}
                </p>
            </footer>
        </article>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex"
import AuthorList from "@/components/panel-detail/AuthorList";

export default {
    name: "SinglePanel",
    components: {
        AuthorList,
    },
    data() {
        return {
            notAllowed: false,
            otherError: false,
        }
    },
    computed: {
        ...mapGetters([
            "apiUrls",
            "expandedPanel",
            "getFiles",
            "getFileCategoryById",
            // tags
            "methodTags",
            "interventionTags",
            "assayTags",
            "otherTags",
        ]),
        panelImageUrl() {
            return this.apiUrls.panelImage(this.expandedPanel);
        },
    },
    methods: {
        ...mapActions([
            "loadPanelDetail",
            "closeExpandedPanels",
            "fetchFileCategories",
        ]),
        getFileCategoryName: function(source) {
            if (source.file_category_id !== null) {
                let category = this.getFileCategoryById(source.file_category_id)
                if (category && category.name) {
                    return category.name
                }
            }
            return ""
        },
        fetchPanel: function() {
            this.loadPanelDetail(this.$route.params.panel_id).catch(error => {
                if (error.status == 401) {
                    this.notAllowed = true;
                } else {
                    this.otherError = true;
                }
            });
        },
        formatDate(dateString) {
            let date = new Date(dateString),
                locale = 'en-US',
                formatOptions = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: false,
                    timeZoneName: 'short'
                }
            return date.toLocaleString(locale, formatOptions)
        }
    },
    watch: {
        "$route.params.panel_id": panel_id => this.fetchPanel(),
    },
    created() {
        this.fetchFileCategories()
        this.fetchPanel()
    },
    destroyed() {
        this.closeExpandedPanels();
    },
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';

.sd-view-content {
    padding: 2rem;
}
@media (min-width: 1200px) {
    .sd-view-content {
        margin: 4rem auto;
        padding: 0;
        max-width: 1200px;
    }
}

article {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    justify-content: space-between;
}

/* Ensure that panel titles that don't fit on a single line AND can't be line-wrapped (like filenames without spaces or
 * dashes) don't inadvertently increase the width of the page.
 */
header {
    width: 100%;

    h1 {
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

section {
    border: 1px solid $mostly-white-gray;
    border-radius: 0.5rem;
    padding-bottom: 1.25rem;
    padding-left: 1rem;
    padding-right: 1rem;
    padding-top: 1.5rem;
    position: relative;
    width: 100%;

    > h2 {
        background-color: $mostly-black-blue;
        font-size: $font-size-xxs;
        padding: 0 0.4rem;

        position: absolute;
        top: -0.65rem;
        left: 1.5rem;
    }
    > .content {
        min-height: 5rem;
        overflow: auto;
    }
}
@media (min-width: 992px) {
    .panel-keywords-experiment,
    .panel-keywords-general,
    .panel-download-links {
        width: 47.5%;
    }
}

.panel-image {
    border: none;
    padding: 0;

    img {
        width: 100%;
    }
}

.panel-keywords {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;

    .panel-keyword {
        background-color: $mostly-white-gray;
        border-radius: 0.5rem;
        color: $mostly-black-blue;
        height: 2rem;
        padding: 0.2rem 1rem;
    }
}
@media (min-width: 568px) {
    .panel-keywords-experiment .content {
        display: grid;
        gap: 1rem;
        grid-template-columns: max-content auto;
    }

    .panel-keywords {
        margin-bottom: 0;
    }
}

.panel-download-link:not(:last-child) {
    margin-bottom: 1rem;
}
</style>