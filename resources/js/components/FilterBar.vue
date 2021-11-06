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
            <div class="toggle-icon bg-primary text-dark">
                <font-awesome-icon icon="sliders-h" />
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
                <h4>Search by</h4>


                <div class="filter-group">
                    <h5>
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
                </div>

                <div class="filter-group">
                    <h5>
                        Keywords
                    </h5>

                    <keyword-multiselect class="filter-keyword-selector" @select="addKeyword"></keyword-multiselect>

                    <b-list-group class="filter-keyword-list" v-if="filterKeywordList.length > 0">
                        <b-list-group-item v-for="k in filterKeywordList" :key="k.id" class="filter-keyword-list-item">
                            {{k.name}}
                            <button type="button" class="close" aria-label="Remove" @click="removeKeyword(k.id)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </b-list-group-item>
                    </b-list-group>
                </div>

                <b-button variant="outline-secondary" class='pull-right' @click="resetFilters()" v-if="hasActiveFilters">
                    Reset
                </b-button>
            </section>

            <section id="sd-panel-sorting">
                <h4>Sort by</h4>

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
                <h4>My Groups</h4>

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
                                <router-link :to="{ path: '/group/' + group.id }"
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
                                    <router-link :to="{ path: '/group/' + group.id }"
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
        filterKeywordList:[],
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
            "privatePanels"
        ]),
        hasActiveFilters() {
            return (this.filterAuthorList.length > 0) || (this.filterKeywordList.length > 0) || (this.sortOrder !== 'creation-date-desc')
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
    }
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_layout.scss';

$panel-filters-sidebar-width: 420px;
$sidebar-z-index: $navbar-z-index - 2;

#sd-panel-filters::v-deep .b-sidebar-outer,
#sd-panel-filters::v-deep .b-sidebar {
    // Add the top padding to make sure any sidebar content is not hidden by the navbar.
    padding-top: $navbar-height;
    // Position the sidebar above all content except for the navbar.
    z-index: $sidebar-z-index;
}
#sd-panel-filters::v-deep .b-sidebar {
    width: $panel-filters-sidebar-width;
}

#sd-panel-filters-toggle {
    cursor: pointer;
    height: 100vh;
    position: fixed;
    width: $left-sidebar-toggle-width;
    z-index: $sidebar-z-index + 1;

    .toggle-icon {
        border-radius: 50%;
        padding: 1rem;
        position: absolute;
        top: 35vh;
    }
}
#sd-panel-filters-toggle.expanded {
    left: $panel-filters-sidebar-width;
}

section {
    padding: 1rem 1.5rem;

    .filter-group {
        padding: 0.5rem 0;
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
    padding: 0.5rem 0.75rem;
}

.filter-author-list-item .close,
.filter-keyword-list-item .close {
    font-size: 1.38rem;
}

.create-group-link {
    padding: 0.75rem;
}
</style>
