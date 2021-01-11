<template>
    <aside class="sd-filter-bar">
        <section class="pb-2">
            <h4 class="pb-1">
                Filters
                <b-button size="sm" variant="outline-secondary" class='pull-right' @click="resetFilters()" v-if="hasActiveFilters">Reset</b-button>
            </h4>

            <div class="sd-filters-wrapper">
                <h5 class="pb-0">
                    Authors
                    <span v-b-tooltip.hover.top title="Lists only registered users">
                        <font-awesome-icon icon="info-circle" size="sm" />
                    </span>
                </h5>
                <author-multiselect class="filter-author-selector" @select="addAuthor"></author-multiselect>
                <b-list-group class="filter-author-list" v-if="filterAuthorList.length > 0">
                    <b-list-group-item v-for="a in filterAuthorList" :key="a.id" class="filter-author-list-item">
                        {{a.firstname}} {{a.surname}}
                        <button type="button" class="close" aria-label="Remove" @click="removeAuthor(a.id)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </b-list-group-item>
                </b-list-group>

                <h5 class="pt-2 pb-0">Keywords</h5>
                <keyword-multiselect class="filter-keyword-selector" @select="addKeyword"></keyword-multiselect>
                <b-list-group class="filter-keyword-list" v-if="filterKeywordList.length > 0">
                    <b-list-group-item v-for="k in filterKeywordList" :key="k.id" class="filter-keyword-list-item">
                        {{k.name}}
                        <button type="button" class="close" aria-label="Remove" @click="removeKeyword(k.id)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </b-list-group-item>
                </b-list-group>

                <h5 class="pt-2 pb-0">Sort by</h5>
                <b-form-select
                    v-model="sortOrder"
                    :options="sortOrderOptions"
                    value-field="item"
                    text-field="name"
                    @change="changeSortOrder"
                ></b-form-select>
            </div>
        </section>
    </aside>
</template>

<script>
import store from "@/stores/store"
import AuthorMultiselect from '@/components/helpers/AuthorMultiselect'
import KeywordMultiselect from '@/components/helpers/KeywordMultiselect'

export default {
    components: {
        AuthorMultiselect,
        KeywordMultiselect
    },
    data() {
      return {
        filterAuthorList:[],
        filterKeywordList:[],
        sortOrder: 'creation-date-desc',
        sortOrderOptions: [
            { item: 'title-asc', name: 'Title' },
            { item: 'creation-date-desc', name: 'Newest' },
            { item: 'creation-date-asc', name: 'Oldest' },
            { item: 'modification-date-desc', name: 'Recently updated' },
            { item: 'modification-date-asc', name: 'Least recently updated' },
        ]
      }
    },
    computed: {
        hasActiveFilters() {
            return (this.filterAuthorList.length > 0) || (this.filterKeywordList.length > 0) || (this.sortOrder !== 'creation-date-desc')
        }
    },
    methods: {
        addAuthor(authorData) {
            const newAuthor = {
                id: authorData.id,
                firstname: authorData.firstname,
                surname: authorData.surname,
            };
            let index = this.filterAuthorList.findIndex(author => author.id === authorData.id)
            if (index === -1) {
                this.filterAuthorList.push(newAuthor);
            }
            this.$store.commit("setAuthorFilter", this.filterAuthorList)
            this.applyFilters()
        },
        removeAuthor(authorId) {
            let index = this.filterAuthorList.findIndex(author => author.id === authorId)
            if (index > -1) this.filterAuthorList.splice(index, 1)
            this.$store.commit("setAuthorFilter", this.filterAuthorList)
            this.applyFilters()
        },
        addKeyword(keywordData) {
            const newKeyword = {
                id: keywordData.id,
                name: keywordData.content
            };
            let index = this.filterKeywordList.findIndex(keyword => keyword.id === keywordData.id)
            if (index === -1) {
                this.filterKeywordList.push(newKeyword)
            }
            this.$store.commit("setKeywordFilter", this.filterKeywordList)
            this.applyFilters()
        },
        removeKeyword(keywordId) {
            let index = this.filterKeywordList.findIndex(keyword => keyword.id === keywordId)
            if (index > -1) this.filterKeywordList.splice(index, 1)
            this.$store.commit("setKeywordFilter", this.filterKeywordList)
            this.applyFilters()
        },
        changeSortOrder() {
            this.$store.commit("setSortOrder", this.sortOrder)
            this.applyFilters()
        },
        resetFilters() {
            this.filterAuthorList = []
            this.filterKeywordList = []
            this.sortOrder = 'creation-date-desc'
            this.$store.commit("resetAuthorFilter")
            this.$store.commit("resetKeywordFilter")
            this.$store.commit("resetSortOrder")
            this.applyFilters()
        },
        applyFilters() {
            this.$store.commit("setPanelLoadingState", true)
            this.$store.commit("clearLoadedPanels")
            this.$store.dispatch("fetchPanelList")
        }
    }
};
</script>

<style>
.sd-filter-accordion-header {
    text-align: left;
    background-color: none;
}

.sd-filters-wrapper,
.sd-group-list-wrapper {
    border: solid 1px #e0e0e0;
    padding: 0.75rem;
}

.sd-group-new-icon {
    color: gold;
    margin-right: 6px;
}
.user-group--pending .sd-filter-accordion-header {
    background-color: #d4ead4;
}

.filter-author-selector,
.filter-keyword-selector {
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
}

.filter-author-list,
.filter-keyword-list {
    margin-bottom: 0.25rem;
}

.filter-author-list-item,
.filter-keyword-list-item {
    padding: 0.5rem 0.75rem;
}

.filter-author-list-item .close,
.filter-keyword-list-item .close {
    font-size: 1.38rem;
}
</style>
