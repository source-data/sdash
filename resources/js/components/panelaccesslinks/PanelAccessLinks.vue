<template>
    <div>
        <section v-if="iCanEditThisPanel" class="container-fluid share-with-group">
            <b-row>
                <h3 class="col-7">
                    Share with user groups
                </h3>

                <div class="col-5">
                    <b-button v-b-modal.sd-share-modal variant="primary">
                        <font-awesome-icon icon="users" />
                        Share with Group
                    </b-button>

                    <b-modal
                        id="sd-share-modal"
                        ref="sd-share-modal"
                        title="Share with Group"
                        @show="resetSharingModal"
                        ok-only
                        ok-variant="secondary"
                        ok-title="Cancel"
                        button-size="sm"
                        header-border-variant="dark"
                        footer-border-variant="dark"
                    >
                        <b-form-group
                            id="sd-group-selector"
                            label="Share with an existing group"
                            label-for="sd-group-dropdown"
                        >
                            <b-form-select
                                size="sm"
                                id="sd-group-dropdown"
                                :options="myGroups"
                                v-model="selectedSharingGroupId"
                                trim
                            >
                                <template #first>
                                    <b-form-select-option :value="null">
                                        Select group
                                    </b-form-select-option>
                                </template>
                            </b-form-select>
                        </b-form-group>

                        <p>
                            <b-button
                                @click="addPanelToGroup"
                                size="sm"
                                variant="primary"
                                :disabled="!selectedSharingGroupId"
                            >
                                <font-awesome-icon icon="users" size="1x" />
                                Share
                            </b-button>
                        </p>
                        <p>
                            <label class="d-block">or create a new group</label>
                            <b-button @click="createGroup" size="sm" variant="success">
                                <font-awesome-icon icon="users" size="1x" />
                                Create New Group
                            </b-button>
                        </p>
                    </b-modal>
                </div>
            </b-row>

            <b-row v-if="expandedPanel.groups && expandedPanel.groups.length > 0">
                <b-col>
                    <p>
                        This SmartFigure is shared with:
                    </p>

                    <ul class="list-group groups-shared-with text-dark">
                        <li
                            v-for="group in expandedPanel.groups"
                            :key="group.id"
                            class="list-group-item bg-light-gray d-flex justify-content-between"
                        >
                            {{ group.name }}

                            <b-button
                                variant="link"
                                class="badge text-dark"
                                @click="removePanelFromGroup(group.id)"
                            >
                                &#10005;
                            </b-button>
                        </li>
                    </ul>
                </b-col>
            </b-row>
        </section>

        <section class="container-fluid get-link">
            <b-row v-if="iCanEditThisPanel">
                <h3 class="col-7">
                    Get a link to share this SmartFigure with anyone.
                </h3>

                <b-col cols="5">
                    <b-button v-if="!hasLinks" variant="primary" @click="generateLink">
                        <font-awesome-icon icon="link" /> Get Link + QR Code
                    </b-button>

                    <b-button v-else variant="danger" @click="revokeLink">
                        <font-awesome-icon icon="unlink" /> Revoke Link
                    </b-button>
                </b-col>
            </b-row>

            <b-row v-if="loading">
                <b-col class="text-center">
                    <b-spinner
                        variant="primary"
                        label="Spinning"
                        class="m-2"
                        style="width: 2rem; height: 2rem;"></b-spinner>
                </b-col>
            </b-row>

            <b-row v-if="!loading && !iCanEditThisPanel && !hasLinks">
                <b-col v-if="!loading && !hasLinks">
                    The panel owner has not created a public link.
                </b-col>
            </b-row>

            <b-row v-if="!loading && hasLinks" class="copy-link">
                <b-col cols="12">
                    <b-input-group>
                        <b-form-input
                            :value="tokenizedPanelUrl"
                            id="sd-public-link"
                            readonly></b-form-input>

                        <b-input-group-append>
                            <b-button variant="light" @click="copyLink">
                                <font-awesome-icon icon="copy" />
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>

                <b-col class="download-link">
                    <a
                        class="text-light"
                        :href="'/panels/' + expandedPanel.id + '/token/qr'"
                        download="qr_code.jpg"
                    >
                        <font-awesome-icon icon="qrcode" /> Download link as QR code
                    </a>
                </b-col>
            </b-row>
        </section>

        <section v-if="iCanEditThisPanel" class="container-fluid make-public">
            <b-row v-if="loadingStatus">
                <b-col class="text-center">
                    <b-spinner variant="light" label="Loading..."></b-spinner>
                </b-col>
            </b-row>

            <b-row v-if="!isPublic && !loadingStatus">
                <h3 class="col-7">
                    Make the SmartFigure publicly available on the web.
                </h3>

                <b-col cols="5">
                    <b-button
                        variant="dark"
                        class="text-primary"
                        id="sd-publish-button"
                        ref="sd-publish-button"
                    >
                        <font-awesome-icon icon="globe" /> Make Public
                    </b-button>

                    <b-popover
                        ref="sd-publish-popover"
                        target="sd-publish-button"
                        triggers="click blur"
                        placement="top"
                        selector="sd-publish-button"
                        custom-class="sd-custom-popover"
                    >
                        <template v-slot:title>
                            Make Public

                            <b-button @click="closePublishPopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>

                        <template>
                            A public SmartFigure will be accessible to everyone from the SDash
                            <a :href="dashboardUrl" class="">public page</a> under a CC BY 4.0 license
                            (use, distribution, and reproduction in any medium allowed,
                            provided the original work is properly cited).
                        </template>

                        <div class="sd-popover-content">
                            <p>I confirm that all co-authors agreed to make this figure public.</p>
                            <div>
                                <b-button variant="primary" small @click="updatePublicStatus(1)">Yes</b-button>
                                <b-button variant="outline-dark" small @click="closePublishPopover">No</b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-col>
            </b-row>

            <b-row v-if="isPublic && !loadingStatus">
                <b-col>
                    <p>
                        This panel is publicly visible to all on SDash at<br />
                        <a :href="panelUrl">{{panelUrl}}</a>
                    </p>

                    <b-button variant="danger" class="py-2" id="sd-unpublish-button" ref="sd-unpublish-button">
                        Make Private
                    </b-button>

                    <b-popover
                        ref="sd-unpublish-popover"
                        target="sd-unpublish-button"
                        triggers="click blur"
                        placement="top"
                        selector="sd-unpublish-button"
                        custom-class="sd-custom-popover"
                    >
                        <template v-slot:title>
                            Make Private

                            <b-button @click="closeUnpublishPopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>

                        <div class="sd-popover-content">
                            <p>Are you sure?</p>

                            <div>
                                <b-button variant="success" small @click="updatePublicStatus(0)">Yes</b-button>
                                <b-button variant="outline-dark" small @click="closeUnpublishPopover">No</b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-col>
            </b-row>
        </section>
    </div>
</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'PanelAccessLinks',

    data(){

        return {
            loading: false,
            loadingStatus: false,
            link_base: process.env.MIX_API_PANEL_URL,
            selectedSharingGroupId: null,
            fields:[
                {key:'action', label:'', sortable: false},
                {key:'group_name', label:'Group Name', sortable: false}
            ]
        }

    }, /* end of data */

    computed: {
        ...mapGetters(['expandedPanel', 'iOwnThisPanel', 'iHaveAuthorPrivileges', 'userGroups']),
        iCanEditThisPanel(){
            return (this.iOwnThisPanel || this.iHaveAuthorPrivileges)
        },
        isPublic(){
            return !!(this.expandedPanel.is_public);
        },
        hasLinks(){
            return this.expandedPanel.access_token.hasOwnProperty('token')
        },
        dashboardUrl(){
            return window.location.origin + '/dashboard'
        },
        panelUrl(){
            return this.link_base + '/' + this.expandedPanel.id
        },
        tokenizedPanelUrl(){
            return this.panelUrl + '?token=' + this.expandedPanel.access_token.token
        },
        myGroups(){
            let groups = this.userGroups.reduce((myGroups, group) => {
                myGroups.push({text: group.name, value: group.id})
                return myGroups
            },[])
            return groups
        }
    },

    methods:{ //run as event handlers, for example
        ...mapActions(['selectPanel']),
        generateLink () {
            this.loading = true
            this.$store.dispatch("generatePublicLink")
            .then(response => {
                this.loading = false
                this.$snotify.success("New public link generated.", "Success!")
            }).catch(error => {
                this.loading = false
                this.$snotify.error(error.data.MESSAGE, "Action Failed!")
            })
        },
        revokeLink () {
            this.loading = true
            this.$store.dispatch("revokePublicLink")
            .then(response => {
                this.loading = false
                this.$snotify.success("Public link removed.", "Success!")
            }).catch(error => {
                this.loading = false
                this.$snotify.error(error.data.MESSAGE, "Action Failed!")
            })
        },
        copyLink () {
            let target = document.getElementById("sd-public-link")
            target.select()
            document.execCommand("copy")
            this.$snotify.info("The link is stored in your clipboard", "Copied Link")
        },
        addPanelToGroup(groupId){
            this.$store.dispatch("manageGroupPanels", {
                action: 'add',
                target: 'expanded',
                groupId: this.selectedSharingGroupId
            })
            .then((response)=>{
                this.$refs['sd-share-modal'].hide()
            })
            .catch(error => {
                this.$snotify.error("The panel could not be added to group", "Failure")
            })
        },
        removePanelFromGroup(groupId){
            this.$store.dispatch("manageGroupPanels", {
                action: 'remove',
                target: 'expanded',
                groupId
            })
            .catch(error => {
                this.$snotify.error("The panel could not be removed from group", "Failure")
            })
        },
        createGroup(){
            this.$refs['sd-share-modal'].hide()
            this.selectPanel(this.expandedPanel.id)
            this.$router.push({name: "creategroup"})
        },
        updatePublicStatus(status){
            this.loadingStatus = true
            this.closePublishPopover()
            this.closeUnpublishPopover()
            this.$store.dispatch("updatePanelStatus", {
                id: this.expandedPanel.id,
                is_public: Boolean(status)
            })
            .then(response => {
                this.loadingStatus = false
                const status = response.data.DATA ? "public" : "private"
                this.$snotify.success("The panel is now " + status, "Success!")
            })
            .catch(error => {
                this.loadingStatus = false
                this.$snotify.error(error.data.MESSAGE, "Action Failed!")
            });
        },
        resetSharingModal(){
            this.selectedSharingGroupId = null
        },
        closePublishPopover() {
            if(this.$refs["sd-publish-popover"]) {
                this.$refs["sd-publish-popover"].$emit("close")
            }
        },
        closeUnpublishPopover() {
            if(this.$refs["sd-unpublish-popover"]) {
                this.$refs["sd-unpublish-popover"].$emit("close")
            }
        },
    }

}
</script>

<style lang="scss" scoped>
@import "~bootstrap/scss/functions";
@import 'resources/sass/_colors.scss';

$section-padding-top: 0.75rem;
$section-padding-bottom: $section-padding-top;
$section-padding-left: 0.75rem;
$section-padding-right: $section-padding-left;

section {
    padding-top: $section-padding-top;
    padding-bottom: $section-padding-bottom;

    padding-left: $section-padding-left;
    padding-right: $section-padding-right;

    overflow-x: hidden;
}
section:not(:last-child) {
    border-bottom: 1px solid;
}
h3 {
    color: inherit;
    font-size: inherit;
}

.share-with-group p {
    margin-top: 0.1rem;
    margin-bottom: 0.3rem;
}
.groups-shared-with {
    max-height: 10rem;
    overflow-y: auto;
}

.groups-shared-with .list-group-item {
    border-radius: 0.25rem;
    margin-bottom: 0.1rem;
    padding: 0.2rem 0.75rem;
}
.groups-shared-with .list-group-item button:active,
.groups-shared-with .list-group-item button:focus,
.groups-shared-with .list-group-item button:hover {
    text-decoration: none;
    color: $mostly-black-blue-hover !important;
}

.col-5 > button {
    width: 100%;
}

button.btn-dark.text-primary:active,
button.btn-dark.text-primary:focus,
button.btn-dark.text-primary:hover {
    color: theme-color("light") !important;
}

.get-link .copy-link {
    margin-top: 0.5rem;
}
.download-link {
    margin-top: 0.5rem;
    text-align: right;
}
.download-link a {
    text-decoration: underline;
}

</style>

<style lang="scss">
@import 'resources/sass/_colors.scss';

#sd-share-modal {
    .modal-header {
        background-color:$very-dark-desaturated-blue;
    }

    .modal-title {
        color: $mostly-white-gray !important;
    }

    .modal-header {
        margin-bottom: 1rem;
    }

    .modal-footer {
        background-color:$very-dark-desaturated-blue;
        margin-top: 1rem;
    }
}
</style>