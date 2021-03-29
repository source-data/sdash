<template>
    <section class="sd-panel-access-links">
        <template v-if="iCanEditThisPanel">
            <div>
                <b-button v-b-modal.sd-share-modal variant="success" class="py-2 sd-share-button">
                    <font-awesome-icon icon="users" /> Share with Group
                </b-button>
                <b-modal id="sd-share-modal" ref="sd-share-modal" title="Share with Group" @show="resetSharingModal"
                        ok-only ok-variant="secondary" ok-title="Cancel" button-size="sm">
                    <b-form-group
                    id="sd-group-selector"
                    label="Share with an existing group"
                    label-for="sd-group-dropdown"
                    >
                        <b-form-select size="sm" id="sd-group-dropdown" :options="myGroups" v-model="selectedSharingGroupId" trim>
                            <template #first>
                                <b-form-select-option :value="null">Select group</b-form-select-option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <p>
                        <b-button @click="addPanelToGroup" size="sm" variant="info" :disabled="!selectedSharingGroupId">
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
            <template v-if="expandedPanel.groups && expandedPanel.groups.length > 0">
                <p>This figure is shared with:</p>
                <b-table
                    small
                    dark
                    thead-class="d-none"
                    :items="expandedPanel.groups"
                    :fields="fields"
                >
                    <template v-slot:cell(action)="data">
                        <b-button variant="link" class="text-light" @click="removePanelFromGroup(data.item.id)">
                            <font-awesome-icon icon="times" size="lg"/>
                        </b-button>
                    </template>
                    <template v-slot:cell(group_name)="data">
                        {{ data.item.name }}
                    </template>
                </b-table>
            </template>
            <b-row>
                <b-col class="sd-button-container">
                    <b-button variant="success" class="py-2" @click="generateLink" v-if="!hasLinks">
                        <font-awesome-icon icon="link" /> Get Link + QR Code
                    </b-button>
                    <b-button variant="danger" class="py-2" @click="revokeLink" v-if="hasLinks">
                        <font-awesome-icon icon="unlink" /> Revoke Link
                    </b-button>
                </b-col>
                <b-col>
                    <p>Anyone with this link can view this SmartFigure and add comments.</p>
                </b-col>
            </b-row>
        </template>
        <b-alert show variant="primary" v-if="!hasLinks && !iCanEditThisPanel">
            The panel owner has not created a public link.
        </b-alert>
        <b-row v-if="loading">
            <b-col class="text-center">
                <b-spinner variant="primary" label="Spinning" class="m-2" style="width: 2rem; height: 2rem;"></b-spinner>
            </b-col>
        </b-row>
        <b-row v-if="!loading && hasLinks">
            <b-col class="sd-button-container">
                <b-button variant="light" class="py-2" @click="copyLink">
                    <font-awesome-icon icon="copy" /> Copy Link
                </b-button>
                <b-form-input :value="tokenizedPanelUrl" id="sd-public-link" size="sm" readonly></b-form-input>
            </b-col>
            <b-col>
                <a :href="'/panels/' + expandedPanel.id + '/token/qr'" download="qr_code.jpg">
                    <img class="sd-qr-code" :src="'/panels/' + expandedPanel.id + '/token/qr'" alt="QR code leading to the public panel link">
                </a>
            </b-col>
        </b-row>
        <template v-if="iCanEditThisPanel">
            <hr />
            <b-row v-if="loadingStatus">
                <b-col class="text-center">
                    <b-spinner variant="light" label="Loading..."></b-spinner>
                </b-col>
            </b-row>
            <b-row v-if="!isPublic && !loadingStatus">
                <b-col class="sd-button-container">
                    <b-button variant="success" class="py-2" id="sd-publish-button" ref="sd-publish-button">
                        <font-awesome-icon :icon="['fab', 'creative-commons']" /> Make Public
                    </b-button>
                    <b-popover
                        ref="sd-publish-popover"
                        target="sd-publish-button"
                        triggers="click"
                        placement="top"
                        selector="sd-publish-button"
                    >
                        <template v-slot:title>
                            Make Public 
                            <b-button @click="closePublishPopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>
                        <div class="sd-popover-content">
                            <p>I confirm that all co-authors agreed to make this figure public.</p>
                            <div>
                                <b-button variant="success" small @click="updatePublicStatus(1)">Yes</b-button>
                                <b-button variant="outline-dark" small @click="closePublishPopover">No</b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-col>
                <b-col>
                    <p>
                        A public SmartFigure will be accessible to everyone from the SDash 
                        <a :href="dashboardUrl">public page</a> under a CC BY 4.0 license
                        (use, distribution, and reproduction in any medium allowed,
                        provided the original work is properly cited).
                    </p>
                </b-col>
            </b-row>
            <b-row v-if="isPublic && !loadingStatus">
                <b-col class="text-center">
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
                        triggers="click"
                        placement="top"
                        selector="sd-unpublish-button"
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
        </template>
    </section>
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

<style lang="scss">

.sd-panel-access-links {
    overflow: hidden;
}

.sd-panel-access-links .table td {
    vertical-align: middle;
}

.sd-panel-access-links .table td:first-child {
    width: 1%;
}

.sd-panel-access-links hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid white;
}

.sd-panel-access-links a {
    color: #b0cddb;
    text-decoration: underline;
}

.sd-panel-access-links a {
    color: #b0cddb;
}

.sd-panel-access-links a:hover {
    color: darken(#b0cddb, 15%);
}

.sd-button-container {
    flex: 0 0 250px;
}

.sd-popover-content {
    width: 200px;
}

.sd-share-button {
    margin-bottom: 0.5rem;
}

.sd-qr-code {
    height: 150px;
    width: auto;
}

#sd-public-link {
    display: block;
    margin-top: 1rem;
}

</style>