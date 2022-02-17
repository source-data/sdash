<template>
    <div class="sd-view-content">
        <div v-if="notAllowed">
            You are not allowed to view this SmartFigure.
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
                <h2 hidden>SmartFigure Image</h2>

                <img :src="panelImageUrl" :alt="'image for ' + expandedPanel.title" />
            </section>

            <section class="panel-description" v-if="showDescription">
                <h2>
                    Description
                </h2>

                <div class="content">
                    {{ expandedPanel.caption }}
                </div>
            </section>

            <section class="panel-sources" v-if="showSources">
                <h2>
                    Sources
                </h2>

                <div class="content">
                    <table class="table table-sm table-borderless text-light">
                        <thead>
                            <tr>
                                <td>Filename / URL</td>
                                <td>Category</td>
                                <td>Description</td>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="source in getFiles" :key="source.id">
                                <td>
                                    <a v-if="source.url" class="text-info" :href="source.url">
                                        {{ source.url }}
                                    </a>

                                    <a v-else class="text-info" :href="'/files/' + source.id">
                                        {{source.original_filename}}
                                    </a>
                                </td>

                                <td>
                                    <span v-if="source.file_category_id">
                                        {{ getFileCategoryName(source) }}
                                    </span>

                                    <span v-else>&mdash;</span>
                                </td>

                                <td>
                                    <span v-if="source.description">
                                        {{ source.description }}
                                    </span>

                                    <span v-else>&mdash;</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel-keywords-experiment" v-if="showExperimentalKeywords">
                <h2>
                    Experimental Design Keywords
                </h2>

                <div class="content">
                    <div class="panel-keyword-category">
                        Measured Variables
                    </div>

                    <div class="panel-keywords sourcedata-tags-assay">
                        <span class="panel-keyword" v-for="tag in assayTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>

                    <div class="panel-keyword-category">
                        Controlled Variables
                    </div>

                    <div class="panel-keywords sourcedata-tags-intervention">
                        <span class="panel-keyword" v-for="tag in interventionTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>

                    <div class="panel-keyword-category">
                        Instruments / Methods
                    </div>

                    <div class="panel-keywords sourcedata-tags-method">
                        <span class="panel-keyword" v-for="tag in methodTags" :key="tag.id">
                            {{ tag.content }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="panel-keywords-general" v-if="showGeneralKeywords">
                <h2>
                    General Keywords
                </h2>

                <div class="content panel-keywords general-tags">
                    <span class="panel-keyword" v-for="tag in otherTags" :key="tag.id">
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
                    <font-awesome-icon :icon="['fab', 'creative-commons']" /> 2022 The Authors. This figure is licensed
                    under the terms of the <a class="text-info" href="https://creativecommons.org/licenses/by/4.0/">
                    Creative Commons Attribution License</a>, which permits use, distribution and reproduction in any
                    medium, provided the original work is properly cited.
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
        showDescription() {
            return this.notEmpty(this.expandedPanel.caption);
        },
        showSources() {
            return this.notEmpty(this.getFiles);
        },
        showExperimentalKeywords() {
            return this.notEmpty(this.assayTags.length)
                || this.notEmpty(this.interventionTags)
                || this.notEmpty(this.methodTags);
        },
        showGeneralKeywords() {
            return this.notEmpty(this.otherTags);
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
        },
        notEmpty(strOrList) {
            return strOrList && strOrList.length > 0;
        },
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
        margin: 4rem auto 0;
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
        border-radius: 0.5rem;
    }
}
.panel-keyword-category,
.panel-keyword {
    height: 2rem;
    padding: 0.2rem 1rem;
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

.sourcedata-tags-assay .panel-keyword {
    background-color: $sourcedata-color-assay;
}
.sourcedata-tags-intervention .panel-keyword {
    background-color: $sourcedata-color-intervention;
}
.sourcedata-tags-method .panel-keyword {
    background-color: $sourcedata-color-method;
    color: $very-dark-blue;
}
.general-tags .panel-keyword {
    background-color: $sourcedata-color-other;
    color: $mostly-white-gray;
}

.panel-download-link:not(:last-child) {
    margin-bottom: 1rem;
}
</style>