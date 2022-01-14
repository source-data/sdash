<template>
    <div id="sd-panel-filters">
        <div
            id="sd-panel-filters-toggle"
            class="bg-primary sd-panel-filters-toggle"
            :class="{expanded: isSidebarExpanded}"
            aria-controls="sd-panel-filters-sidebar"
            :aria-expanded="isSidebarExpanded"
            @click="toggleSidebar"
        >
            <div class="toggle-icon-background bg-primary"></div>

            <div class="toggle-icon text-dark">
                <font-awesome-icon icon="sliders-h" size="lg" />
            </div>

            <div class="sd-panel-filters-toggle-icon-wrapper">
                <b-badge v-if="filtersAreApplied" pill class="sd-panel-filter-toggle-icon sd-panel-filter-toggle-icon--filters" variant="success" v-b-tooltip.hover.right.v-light title="Filters applied"><font-awesome-icon icon="sliders-h" size="sm"/></b-badge>
                <b-badge v-if="hasPendingUserGroups" pill class="sd-panel-filter-toggle-icon sd-panel-filter-toggle-icon--group-invitations" variant="info" v-b-tooltip.hover.right.v-light title="Group invitations"><font-awesome-icon icon="users" size="sm"/></b-badge>
            </div>
        </div>

        <b-sidebar
            id="sd-panel-filters-sidebar"
            backdrop left no-header no-slide shadow
            bg-variant="light"
            text-variant="dark"
            v-model="isSidebarExpanded"
        >
            <section v-if="isLoggedIn" id="sd-panel-owner">
                <div class="filter-group">
                    <b-form-radio-group
                        stacked
                        name="toggle-panel-list-privacy"
                        v-model="privacyLevel"
                    >
                        <b-form-radio value="all">Show all panels</b-form-radio>
                        <b-form-radio value="private">Show my own panels</b-form-radio>
                    </b-form-radio-group>
                </div>
            </section>

            <section id="sd-panel-filters">
                <h4 class="text-sm mb-0">Search by</h4>

                <div class="filter-group pt-0">
                    <h5 hidden>
                        Authors
                    </h5>

                    <author-multiselect
                        class="filter-author-selector"
                        @select="addAuthor"
                        placeholder="Authors & Users"
                    ></author-multiselect>

                    <b-list-group class="filter-author-list" v-if="filterAuthorList.length > 0">
                        <b-list-group-item v-for="a in filterAuthorList" :key="a.id" class="filter-author-list-item">
                            {{a.firstname}} {{a.surname}}
                            <button type="button" class="close" aria-label="Remove" @click="removeAuthor(a.id)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </b-list-group-item>
                    </b-list-group>
                </div>

                <div class="filter-group">
                    <h5 hidden>
                        Keywords
                    </h5>

                    <keyword-multiselect :initital-keywords="filterKeywords" class="filter-keyword-selector" @select="addKeyword"></keyword-multiselect>

                    <b-list-group class="filter-keyword-list" v-if="filterKeywords.length > 0">
                        <b-list-group-item v-for="k in filterKeywords" :key="k.id" class="filter-keyword-list-item">
                            {{k.name}}
                            <button type="button" class="close" aria-label="Remove" @click="removeKeyword(k.id)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </b-list-group-item>
                    </b-list-group>
                </div>

                <b-button variant="primary" class='pull-right' @click="resetFilters()" v-if="hasActiveFilters">
                    Reset
                </b-button>
            </section>

            <section id="sd-panel-sorting">
                <h4 class="text-sm mb-0">Sort by</h4>

                <div class="filter-group">
                    <b-form-select
                        v-model="sortOrder"
                        :options="sortOrderOptions"
                        value-field="item"
                        text-field="name"
                        @change="changeSortOrder"
                    ></b-form-select>
                </div>
            </section>

            <section v-if="isLoggedIn" id="sd-my-groups">
                <h4 class="text-sm">My Groups</h4>

                <div role="tablist" class="sd-group-list-wrapper">
                    <b-card class="mb-1" no-body>
                        <b-card-header
                            header-tag="header"
                            class="p-1"
                            role="tab"
                            >
                            <router-link
                                :to="{ name: 'creategroup' }"
                                class="sd-filter-accordion-header create-group-link btn-block"
                                variant="light"
                                >
                                Create New Group
                                <font-awesome-icon icon="external-link-alt" />
                            </router-link>
                        </b-card-header>
                    </b-card>

                    <b-card
                        v-for="group in pendingUserGroups"
                        :key="group.id"
                        no-body
                        class="mb-1 user-group--pending"
                    >
                        <b-card-header header-tag="header" class="p-1" role="tab">
                            <b-button
                                block
                                v-b-toggle="'group-' + group.id"
                                class="sd-filter-accordion-header"
                                variant="light"
                                >
                                <b-badge pill variant="info"><font-awesome-icon icon="star" class="sd-group-new-icon"/>New group invitation!</b-badge>
                                <br />
                                {{ group.name }} <br />|
                                <font-awesome-icon icon="users" />
                                {{ group.confirmed_users_count }} |
                                <font-awesome-icon icon="layer-group" />
                                {{ group.panels_count }}
                            </b-button>
                        </b-card-header>
                        <b-collapse
                            :id="'group-' + group.id"
                            accordion="sd-filter-accordion"
                            role="tabpanel"
                        >
                            <b-card-body>
                                <b-card-text>
                                    {{ group.description }}
                                </b-card-text>
                                <b-card-text>
                                    <b-button size="sm" variant="success" @click="acceptGroupInvitation(group.id, group.pivot.token)">Accept</b-button>
                                    <b-button size="sm" variant="danger" @click="declineGroupInvitation(group.id, group.pivot.token)">Reject</b-button>
                                </b-card-text>

                            </b-card-body>
                        </b-collapse>
                    </b-card>

                    <b-card
                        v-for="group in confirmedUserGroups"
                        :key="group.id"
                        no-body
                        class="mb-1"
                    >
                        <b-card-header header-tag="header" class="p-1" role="tab">
                            <b-button
                                block
                                v-b-toggle="'group-' + group.id"
                                class="sd-filter-accordion-header"
                                variant="light"
                                >
                                <b-badge pill variant="info" v-if="group.requested_users_count > 0 && group.pivot.role==='admin' && group.pivot.status==='confirmed'"><font-awesome-icon icon="user-plus" class="sd-group-new-icon"/>New member request!</b-badge>
                                <br v-if="group.requested_users_count > 0 && group.pivot.role==='admin' && group.pivot.status==='confirmed'"/>
                                {{ group.name }} <br />
                                <font-awesome-icon icon="users" />
                                {{ group.confirmed_users_count }} |
                                <font-awesome-icon icon="layer-group" />
                                {{ group.panels_count }} |
                                <span v-if="group.is_public">
                                    <font-awesome-icon icon="lock-open" title="Public group" /> |
                                </span>
                                <router-link :to="{name: 'group', params: {group_id: group.id}}"
                                    ><font-awesome-icon icon="external-link-alt" />
                                    Go</router-link
                                ></b-button
                            >
                        </b-card-header>
                        <b-collapse
                            :id="'group-' + group.id"
                            accordion="sd-filter-accordion"
                            role="tabpanel"
                        >
                            <b-card-body>
                                <b-card-text>
                                    <router-link :to="{name: 'group', params: {group_id: group.id}}"
                                        >Go to group</router-link
                                    >
                                </b-card-text>
                                <b-card-text>
                                    {{ group.description }}
                                </b-card-text>
                            </b-card-body>
                        </b-collapse>
                    </b-card>
                </div>
            </section>

        </b-sidebar>
    </div>
</template>

<script>
import { mapGetters } from "vuex"
import AuthorMultiselect from '@/components/helpers/AuthorMultiselect';
import KeywordMultiselect from '@/components/helpers/KeywordMultiselect';

export default {
    components: {
        AuthorMultiselect,
        KeywordMultiselect
    },
    data() {
      return {
        filterAuthorList:[],
        isSidebarExpanded: false,
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
        ...mapGetters([
            "isLoggedIn",
            "userGroups",
            "confirmedUserGroups",
            "pendingUserGroups",
            "privatePanels",
            "filtersAreApplied",
            "filterKeywords",
        ]),
        hasActiveFilters() {
            return (this.filterAuthorList.length > 0) || (this.filterKeywords.length > 0) || (this.sortOrder !== 'creation-date-desc')
        },
        hasPendingUserGroups() {
            return this.pendingUserGroups.length > 0;
        },
        privacyLevel: {
            get() {
                return this.privatePanels === true ? "private" : "all";
            },
            set(value) {
                let privacy = value === "private" ? true : false;
                this.toggleAccess(privacy);
            }
        },
        sidebarToggleText: function() {
            return this.isSidebarExpanded ? "Hide sidebar" : "Show sidebar";
        },
    },
    methods: {
        toggleAccess(value) {
            this.$store.dispatch("setLoadingState", true);
            this.$store.dispatch("clearLoadedPanels");
            this.$store.dispatch("setSearchString", "");
            this.$store.dispatch("setPrivate", value);
            this.$store.dispatch("fetchPanelList");
        },
        toggleSidebar() {
            this.isSidebarExpanded = !this.isSidebarExpanded;
        },
        acceptGroupInvitation(groupId, token) {
            this.$store.dispatch("acceptGroupMembership", {groupId, token})
            .then(response => {
                this.$store.dispatch("setLoadingState", true);
                this.$store.dispatch("clearLoadedPanels");
                this.$store.dispatch("fetchPanelList");
                this.$snotify.success("Invitation accepted!", "OK")

            }).catch(err => {
                this.$snotify.error("Could not complete the action", "Sorry!")
            })
        },
        declineGroupInvitation(groupId, token) {
            this.$store.dispatch("declineGroupMembership", {groupId, token})
            .then(response => {
                this.$store.commit("removeGroupFromUserGroups", groupId)
                this.$snotify.info("Group membership declined", "OK!")
            }).catch(err => {
                this.$snotify.error("Could not complete the action", "Sorry!")
            })
        },
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
            const localKeywordList = [...this.filterKeywords]
            let index = localKeywordList.findIndex(keyword => keyword.id === keywordData.id)
            if (index === -1) {
                localKeywordList.push(newKeyword)
            }
            this.$store.commit("setKeywordFilter", localKeywordList)
            this.applyFilters()
        },
        removeKeyword(keywordId) {
            const localKeywordList = [...this.filterKeywords]
            let index = localKeywordList.findIndex(keyword => keyword.id === keywordId)
            if (index > -1) localKeywordList.splice(index, 1)
            this.$store.commit("setKeywordFilter", localKeywordList)
            this.applyFilters()
        },
        changeSortOrder() {
            this.$store.commit("setSortOrder", this.sortOrder)
            this.applyFilters()
        },
        clearFilters() {
            this.filterAuthorList = []
            this.sortOrder = 'creation-date-desc'
            this.$store.commit("resetAuthorFilter")
            this.$store.commit("resetKeywordFilter")
            this.$store.commit("resetSortOrder")
        },
        resetFilters() {
            this.clearFilters()
            this.applyFilters()
        },
        applyFilters() {
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("fetchPanelList")
        },
    },
    mounted() {
        if (localStorage.getItem("isSidebarExpanded") !== null) {
            this.isSidebarExpanded =
                localStorage.getItem("isSidebarExpanded") === "true";
        }
    },
    watch: {
        isSidebarExpanded(newStatus) {
            localStorage.setItem("isSidebarExpanded", newStatus);
        }
    },
    destroyed() {
        this.clearFilters();
    }
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_layout.scss';

$panel-filters-sidebar-width: 80vw;
$panel-filters-sidebar-width-sm: 576px * 0.8;
$sidebar-z-index: $navbar-z-index - 2;

#sd-panel-filters::v-deep .b-sidebar-outer,
#sd-panel-filters::v-deep .b-sidebar {
    // Add the top padding to make sure any sidebar content is not hidden by the navbar.
    padding-top: $navbar-height;
    // Position the sidebar above all content except for the navbar.
    z-index: $sidebar-z-index;
}
@media (min-width: 768px) {
    #sd-panel-filters::v-deep .b-sidebar-outer,
    #sd-panel-filters::v-deep .b-sidebar {
        padding-top: $navbar-height-md;
    }
}

#sd-panel-filters::v-deep .b-sidebar {
    width: $panel-filters-sidebar-width;
}
@media (min-width: 576px) {
    #sd-panel-filters::v-deep .b-sidebar {
        width: $panel-filters-sidebar-width-sm;
    }
}

$filter-bar-box-shadow: 1px 0 1px #999;
#sd-panel-filters-toggle {
    box-shadow: $filter-bar-box-shadow;
    cursor: pointer;
    height: 100vh;
    position: fixed;
    top: 0;
    width: $left-sidebar-toggle-width;
    z-index: $sidebar-z-index + 1;

    .toggle-icon-background {
        filter: drop-shadow($filter-bar-box-shadow);
        // Position the icon 1/3rd of the way down
        position: absolute;
        right: -23px;
        top: 35vh;

        // The curved border comes from an svg
        background-color: transparent !important;
        background-image: url(/images/filter-sidebar-toggle.svg);
        height: 100px;
        width: 23px;
    }

    .toggle-icon {
        // Position the icon 1/3rd of the way down
        position: absolute;
        right: -15px;
        top: 35vh;
        height: 100px;

        background-color: transparent !important;
        // Vertically align the icon in the middle
        display: flex;
        align-items: center;
    }
}
@media (min-width: 768px) {
    #sd-panel-filters-toggle {
        width: $left-sidebar-toggle-width-md;

        .toggle-icon {
            right: -12px;
        }
    }
}

#sd-panel-filters-toggle.expanded {
    left: $panel-filters-sidebar-width;
}
@media (min-width: 576px) {
    #sd-panel-filters-toggle.expanded {
        left: $panel-filters-sidebar-width-sm;
    }
}

#sd-panel-filters::v-deep {
    select,
    .multiselect__tags {
        border: solid 1.5px $mostly-black-blue;
        border-radius: 1.5rem !important;
    }
    .multiselect__content-wrapper {
        border: solid 1px $mostly-black-blue;
        border-radius: 1.5rem !important;
    }
}

section {
    padding: 1rem 1.5rem;

    .filter-group {
        padding: 0.5rem 0;
    }
}
@media (min-width: 576px) {
    section {
        padding: 1rem 6rem;
    }
}

.sd-filter-accordion-header {
    text-align: left;
    background-color: none;
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
    background: inherit;
    border: none;
    padding: 0.5rem 0.75rem;
}

.filter-author-list-item .close,
.filter-keyword-list-item .close {
    font-size: 1.38rem;
}

.create-group-link {
    padding: 0.75rem;
}

.sd-panel-filters-toggle-icon-wrapper {
    position: absolute;
    top: 7rem;
}

.badge.sd-panel-filter-toggle-icon {
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 3px;
    margin-left: 2px;
}



</style>
