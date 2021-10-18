<template>
    <ul class="d-inline list-unstyled list-inline">
        <li
            class="sd-panel-author-list--item list-inline-item"
            v-for="author in authors"
            :key="'author-' + author.order + '-' + author.id"
            :class="author.author_role"
        >
            <b-link
                v-if="author.corresponding"
                :id="'popover-' + author.origin + '-' + author.id"
                href="#"
            >
                {{ author.firstname }} {{ author.surname }}

                <font-awesome-icon icon="envelope" />

                <b-popover
                    :target="'popover-' + author.origin + '-' + author.id"
                    triggers="click blur"
                    placement="bottom"
                    custom-class="bg-light"
                >
                    <ul class="list-unstyled mt-1 mb-1">
                        <li v-if="author.email">
                            <font-awesome-icon icon="envelope" fixed-width />
                            <a :href="'mailto:' + author.email">{{ author.email }}</a>
                        </li>

                        <li v-if="author.orcid">
                            <font-awesome-icon :icon="['fab', 'orcid']" fixed-width />
                            <a :href="'https://orcid.org/' + author.orcid">{{ 'orcid.org/' + author.orcid }}</a>
                        </li>

                        <li v-if="author.institution_name">
                            <font-awesome-icon icon="building" fixed-width />
                            {{ author.institution_name }}
                        </li>

                        <li v-if="author.department_name">
                            <font-awesome-icon icon="sitemap" fixed-width />
                            {{ author.department_name }}
                        </li>
                    </ul>

                    <p v-if="author.origin==='users'" class="mt-2 mb-1">
                        <router-link :to="{ path: '/user/' + author.id }" target="_blank">
                            View Full Profile

                            <font-awesome-icon icon="external-link-alt" size="sm" />
                        </router-link>
                    </p>
                </b-popover>
            </b-link>

            <span v-else>{{ author.firstname }} {{ author.surname }}</span>

            <!-- remove me as author popover-->
            <span v-if="author.origin==='users' && author.id===currentUser.id && !iOwnThisPanel">
                <button
                    v-b-tooltip.hover.left="{ customClass: 'sd-remove-author-tooltip' }" title="Remove me"
                    id="sd-panel-author-list--remove-me"
                    ref="sd-panel-author-list--remove-me"
                >
                    &times;
                </button>

                <b-popover
                    :ref="'remove-me-popover-' + currentUser.id"
                    target="sd-panel-author-list--remove-me"
                    triggers="click"
                    placement="bottom"
                >
                    <template v-slot:title>
                        Remove yourself as author?

                        <b-button @click="closeRemoveMePopover" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">
                                &times;
                            </span>
                        </b-button>
                    </template>

                    <div class="confirm-refresh-link">
                        <p>
                            Delete yourself from the author list of this panel?
                        </p>

                        <div class="refresh-buttons">
                            <b-button variant="danger" small @click="removeMeAsAuthor">Remove Me</b-button>
                            <b-button variant="outline-dark" small @click="closeRemoveMePopover">Cancel</b-button>
                        </div>
                    </div>
                </b-popover>
            </span>
        </li>
    </ul>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import AuthorTypes from "@/definitions/AuthorTypes";

export default {
    name: "AuthorList",
    computed: {
        ...mapGetters([
            "expandedPanel",
            "iOwnThisPanel",
            "iCanSeeThisPanelViaAGroup",
            "expandedPanelAuthors",
            "currentUser",
        ]),
        authors() {
            // don't display the curator in the author list
            return this.expandedPanelAuthors.filter(
                author => author.author_role !== AuthorTypes.CURATOR
            );
        },
    },
    methods: {
        removeMeAsAuthor() {
            let panelId = this.expandedPanel.id;
            let authorId = this.currentUser.id;

            this.closeRemoveMePopover();
            this.$store.dispatch("removeUserFromExpandedPanelAuthors")
            .then(response => {
                this.$snotify.success(response.data.MESSAGE, "Author removed");
                // remove author from expanded panel detail and loaded panel detail
                this.$store.commit("removeAuthorFromPanel",{
                    author_id: authorId,
                    panel_id: panelId
                });

                /*
                    How can an person have access to a panel?
                    It's public
                    They're an owner
                    They're an author
                    They're a group member
                */

                if (!this.expandedPanel.is_public
                && !this.iCanSeeThisPanelViaAGroup
                && !this.iOwnThisPanel) {
                    // remove panel from loaded/expanded panels
                    this.$store.commit("clearExpandedPanelDetail");
                    this.$store.commit("removePanelFromStore", panelId);
                }

            }).catch(error => {
                this.$snotify.error(error.data.MESSAGE, "Error");
            });
        },
        closeRemoveMePopover() {
            const popupRef = 'remove-me-popover-' + this.currentUser.id;
            if (this.$refs[popupRef][0]) {
                this.$refs[popupRef][0].$emit("close");
            }
        },
    }
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.corresponding a {
    color: $mostly-white-gray;
}

.sd-panel-author-list--item:not(:last-child):after {
    content: ", ";
}

#sd-panel-author-list--remove-me {
    border: none;
    padding: 0;
    line-height: 1px;
    border-radius: 50%;
    font-size: 16px;
    width: 16px;
    height: 16px;
    color: #fff;
    background-color: red;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}
</style>
