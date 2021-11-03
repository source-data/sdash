<template>
    <div>
        <b-container class="sd-group-grid-container" fluid>
            <b-row class="sd-group-grid" cols="1" cols-sm="2" cols-md="3" cols-lg="4">
                <b-col class="sd-group-grid-item" v-for="group in publicGroups" :key="group.id">
                    <b-card no-body>
                        <div class="card-img-container" :style="group.public_panels.length == 0 ? imageContainerColor(group.id) : ''">
                            <b-card-img
                                :class="{'transparent' : group.public_panels.length == 0}"
                                :src="thumbnailUrl(group.public_panels)"
                                :alt="group.name"
                                @error="setDefaultThumbnail"
                                top
                            ></b-card-img>
                        </div>
                        <b-card-body>
                            <b-card-title>
                                <router-link :to="{ path: '/group/' + group.id }">
                                    {{ group.name }}
                                </router-link>
                            </b-card-title>
                            <b-card-text>
                                {{ group.description | truncate(100, "...") }}
                            </b-card-text>
                            <ul class="card-details list-unstyled list-inline">
                                <li>
                                    <font-awesome-icon icon="users" fixed-width />
                                    {{ group.confirmed_users_count }}
                                </li>
                                <li>
                                    <font-awesome-icon icon="layer-group" fixed-width />
                                    {{ group.public_panels_count }}
                                </li>
                                <li>
                                    <font-awesome-icon icon="envelope" fixed-width />
                                    <ul class="group-admins list-unstyled list-inline">
                                        <li v-for="user in group.administrators" :key="user.id">
                                            <b-link :id="'popover-' + group.id + '-' + user.id" href="#">{{ user.firstname }} {{ user.surname }}</b-link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </b-card-body>
                        <b-popover v-for="user in group.administrators" :key="'user-' + group.id + '-' + user.id"
                            :target="'popover-' + group.id + '-' + user.id" triggers="click blur" placement="bottom">
                            <ul class="list-unstyled mt-1 mb-1">
                                <li v-if="user.email">
                                    <font-awesome-icon icon="envelope" fixed-width />
                                    <a :href="'mailto:' + user.email">{{ user.email }}</a>
                                </li>
                                <li v-if="user.orcid">
                                    <font-awesome-icon :icon="['fab', 'orcid']" fixed-width />
                                    <a :href="'https://orcid.org/' + user.orcid">{{ 'orcid.org/' + user.orcid }}</a>
                                </li>
                                <li v-if="user.institution_name">
                                    <font-awesome-icon icon="building" fixed-width />
                                    {{ user.institution_name }}
                                </li>
                                <li v-if="user.department_name">
                                    <font-awesome-icon icon="sitemap" fixed-width />
                                    {{ user.department_name }}
                                </li>
                            </ul>
                        </b-popover>
                        <template #footer>
                            <router-link :to="{ path: '/group/' + group.id }">
                                <b-button size="sm" variant="outline-secondary">View SmartFigures</b-button>
                            </router-link>
                        </template>
                    </b-card>
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
        InfoFooter,
    },
    props: [],

    data() {
        return {
            defaultThumbnailUrl: '/images/group_cover_thumbnail.jpg',
            backgroundColors: [
                '#f06292', // pink
                '#dce775', // lime
                '#a1887f', // brown
                '#4dd0e1', // cyan
                '#81c784', // green
                '#ba68c8', // purple
                '#4db6ac', // teal
                '#9575cd', // deep-purple
                '#fff176', // yellow
                '#7986cb', // indigo
                '#ffb74d', // orange
                '#e57373', // red
                '#ffd54f', // amber
                '#64b5f6', // blue
                '#ff8a65', // deep-orange
                '#4fc3f7', // light-blue
            ]
        }
    },

    computed: {
        ...mapGetters(["apiUrls", "publicGroups"])
    },

    methods: {
        ...mapActions(["fetchPublicGroups"]),
        thumbnailUrl(panels) {
            if (panels.length) {
                const panel = panels[0];
                return this.apiUrls.panelThumbnail(panel);
            } else {
                return this.defaultThumbnailUrl;
            }
        },
        imageContainerColor(panelId) {
            const color = this.backgroundColors[panelId % this.backgroundColors.length];
            return {
                backgroundColor: color
            };
        },
        setDefaultThumbnail(event) {
            event.target.src = this.defaultThumbnailUrl;
        }
    },

    mounted() {
        this.fetchPublicGroups();
    }
};
</script>

<style lang="scss">

.sd-group-grid-container {
    padding: 0;
    overflow: hidden;
}

.sd-group-grid {
    width: 100%;
    margin: 0;

    .card-title,
    .card-details {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-title {
        margin-bottom: 0.25em;
    }

    .card-title a {
        color: #114685;
    }

    .card-body,
    .card-footer {
        padding: 0.75em;
    }

    .card-text {
        min-height: 3rem;
        margin-bottom: 0.5em;
    }

    .card-details {
        width: 100%;
        margin: 0;
        
        > li {
            display: inline;
            font-size: 0.875em;
            color: #6c757d;
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

    .card-img-container {
        border-top-left-radius: calc(0.25rem - 1px);
        border-top-right-radius: calc(0.25rem - 1px);
        box-shadow: 0 3px 4px -2px rgba(0, 0, 0, 0.1);
    }

    .card-img-top {
        width: 100%;
        height: 15vh;
        object-fit: cover;
    }

    .card-img-top.transparent {
        opacity: 0.8;
    }
}

.sd-group-grid,
.sd-group-grid-item {
    padding: 10px;
}

</style>