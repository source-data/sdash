<template>
    <div>
        <header class="sd-view-title">
            <h2 class="text-primary">Groups</h2>

            <div class="details-bar">
                <div class="selection-criteria">
                    <div class="search" v-if="searchQuery">
                        <font-awesome-icon icon="search" />
                        <div class="tag">
                            {{ searchQuery }}
                            <button
                                type="button"
                                class="close"
                                @click="clearSearch"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="result-count">{{ publicGroups.length }} Groups</div>
            </div>
        </header>

        <b-container class="sd-view-content" ref="mainContent" fluid>
            <b-row
                class="sd-group-grid"
                cols="1"
                cols-md="2"
                cols-lg="3"
                cols-xl="4"
            >
                <b-col
                    class="sd-group-grid-item"
                    v-for="group in publicGroups"
                    :key="group.id"
                >
                    <div class="group-img-container">
                        <router-link
                            :to="{
                                name: 'group',
                                params: { group_id: group.id }
                            }"
                        >
                            <img
                                :src="coverPhotoUrl(group.cover_photo)"
                                :alt="group.name"
                            />
                        </router-link>
                    </div>

                    <div>
                        <h6 class="group-title text-md">
                            <router-link
                                class="text-light"
                                :to="{
                                    name: 'group',
                                    params: { group_id: group.id }
                                }"
                            >
                                {{ group.name }}
                            </router-link>
                        </h6>

                        <div class="group-description text-sm">
                            {{ group.description | truncate(100, "...") }}
                        </div>

                        <ul class="group-details list-unstyled list-inline">
                            <li>
                                <font-awesome-icon icon="users" fixed-width />
                                {{ group.confirmed_users_count }}
                            </li>
                            <li>
                                <font-awesome-icon
                                    icon="layer-group"
                                    fixed-width
                                />
                                {{ group.panels_count }}
                            </li>
                            <li>
                                <font-awesome-icon
                                    icon="envelope"
                                    fixed-width
                                />

                                <ul
                                    class="group-admins list-unstyled list-inline"
                                >
                                    <li
                                        v-for="user in group.administrators"
                                        :key="user.id"
                                    >
                                        <b-link
                                            :id="
                                                'popover-' +
                                                    group.id +
                                                    '-' +
                                                    user.id
                                            "
                                            class="text-light"
                                            href="#"
                                        >
                                            {{ user.firstname }}
                                            {{ user.surname }}
                                        </b-link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <b-popover
                        v-for="user in group.administrators"
                        :key="'user-' + group.id + '-' + user.id"
                        :target="'popover-' + group.id + '-' + user.id"
                        triggers="click blur"
                        placement="bottom"
                        custom-class="sd-custom-popover"
                    >
                        <ul class="list-unstyled mt-1 mb-1">
                            <li v-if="user.email">
                                <font-awesome-icon
                                    icon="envelope"
                                    fixed-width
                                />
                                <a :href="'mailto:' + user.email">{{
                                    user.email
                                }}</a>
                            </li>
                            <li v-if="user.orcid">
                                <font-awesome-icon
                                    :icon="['fab', 'orcid']"
                                    fixed-width
                                />
                                <a :href="'https://orcid.org/' + user.orcid">{{
                                    "orcid.org/" + user.orcid
                                }}</a>
                            </li>
                            <li v-if="user.institution_name">
                                <font-awesome-icon
                                    icon="building"
                                    fixed-width
                                />
                                {{ user.institution_name }}
                            </li>
                            <li v-if="user.department_name">
                                <font-awesome-icon icon="sitemap" fixed-width />
                                {{ user.department_name }}
                            </li>
                        </ul>
                    </b-popover>
                </b-col>
            </b-row>
        </b-container>

        <info-footer></info-footer>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import InfoFooter from "@/components/InfoFooter";

export default {
    name: "GroupGrid",
    components: {
        InfoFooter
    },

    props: {
        query: String
    },

    data() {
        return {
            searchQuery: ""
        };
    },

    computed: {
        ...mapGetters(["apiUrls", "publicGroups"])
    },

    methods: {
        ...mapActions(["fetchPublicGroups"]),
        coverPhotoUrl(filename) {
            return filename
                ? "/storage/cover_photos/" + filename
                : "/images/group_cover_thumbnail.jpg";
        },
        reloadGroups() {
            const params = {};
            if (this.searchQuery) {
                params.search = this.searchQuery;
            }
            this.fetchPublicGroups(params);

            // Click on main content block to hide open dropdowns
            this.$refs.mainContent.click();
        },
        clearSearch() {
            this.$router
                .push({
                    name: "groups"
                })
                .catch(err => {});
        }
    },

    mounted() {
        this.searchQuery = this.query;
        this.reloadGroups();
    },

    watch: {
        $route(to) {
            this.searchQuery = to.query.q || "";
            this.reloadGroups();
        }
    }
};
</script>

<style lang="scss" scoped>
header {
    padding-top: 6rem;
}

.sd-group-grid {
    width: 100%;

    .group-title {
        margin-bottom: 0.25rem;
        margin-top: 1rem;
    }

    .group-title,
    .group-details {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .group-description {
        margin-bottom: 0.5rem;
    }

    .group-description,
    .group-details {
        font-weight: lighter;
    }

    .group-details {
        margin-bottom: 2.5rem;
        width: 100%;

        > li {
            display: inline;
            font-size: 0.875em;
        }

        > li + li::before {
            content: "|";
            padding: 0 0.35em;
        }

        .group-admins,
        .group-admins li {
            display: inline;
        }

        .group-admins li + li::before {
            content: ", ";
        }
    }

    .group-img-container img {
        border: none;
        height: 20rem;
        object-fit: cover;
        width: 100%;
    }

    .group-img-container img.transparent {
        opacity: 0.8;
    }
}
</style>
