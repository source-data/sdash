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
                    <template v-if="iCanEditThisPanel" v-slot:cell(action)="data">
                        <b-button variant="link" class="text-light" @click="removePanelFromGroup(data.item.id)">
                            <font-awesome-icon icon="times" size="lg"/>
                        </b-button>
                    </template>
                    <template v-slot:cell(group_name)="data">
                        {{ data.item.name }}
                    </template>
                </b-table>
            </template>
            <template v-if="!hasLinks">
                <div class="sd-generate-panel-access-links">
                    <b-button variant="success" class="py-2" @click="generateLink"><font-awesome-icon icon="link" /> Generate Public Link</b-button>
                </div>
                <p>This link will allow public access to this SmartFigure and create a QR code for this link.</p>
            </template>
        </template>
        <b-alert show variant="primary" v-if="!hasLinks && !iCanEditThisPanel">
            The panel owner has not created a public link.
        </b-alert>
        <b-row v-if="loading">
            <b-col class="text-center">
                <b-spinner variant="primary" label="Spinning" class="m-2" style="width: 2rem; height: 2rem;"></b-spinner>
            </b-col>
        </b-row>
        <b-row v-if="hasLinks">
            <b-col>
                <b-input-group class="my-3">
                    <b-form-input :value="linkUrl" id="sd-public-link" disabled></b-form-input>
                    <b-input-group-append>
                        <b-button variant="light" @click.prevent="copyLink"><font-awesome-icon icon="copy" /> Copy</b-button>
                    </b-input-group-append>
                </b-input-group>
                <div class="sd-qr-code-container">
                    <a v-if="!loading" :href="'/panels/' + expandedPanel.id + '/token/qr'" download="qr_code.jpg">
                        <img class="sd-qr-code" :src="'/panels/' + expandedPanel.id + '/token/qr'" alt="QR code leading to the public panel link">
                    </a>
                </div>
            </b-col>
        </b-row>
        <div class="sd-modify-panel-access-links text-right" v-if="hasLinks && iCanEditThisPanel">
            <b-button variant="success" class="py-2" size="sm" id="sd-refresh-link" ref="sd-refresh-link"><font-awesome-icon icon="link" /> Refresh Link</b-button>
            <b-button variant="danger" class="py-2" size="sm" @click="revokeLink"><font-awesome-icon icon="link" /> Revoke Link</b-button>
                <b-popover
                    ref="refresh-link-popover"
                    target="sd-refresh-link"
                    triggers="click"
                    placement="top"
                    selector="sd-refresh-link"
                >
                <template v-slot:title>
                        Refresh the public link?
                    <b-button @click="closeRefreshLinkPopover" class="close" aria-label="Close">
                        <span class="d-inline-block" aria-hidden="true">&times;</span>
                    </b-button>
                </template>
                    <div class="confirm-refresh-link">
                        <p>
                            Refreshing the external link will revoke the existing link and
                            generate a new one. Do you wish to continue?
                        </p>
                        <div class="refresh-buttons">
                            <b-button variant="success" small @click="generateLink">Yes</b-button>
                            <b-button variant="outline-dark" small @click="closeRefreshLinkPopover">No</b-button>
                        </div>
                    </div>
                </b-popover>
        </div>
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
        hasLinks(){
            return this.expandedPanel.access_token.hasOwnProperty('token')
        },
        linkUrl(){
            return this.hasLinks ? this.link_base + "/" + this.expandedPanel.id + "?token=" + this.expandedPanel.access_token.token : false
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
            this.closeRefreshLinkPopover()
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
            target.disabled = false
            target.select()
            document.execCommand("copy")
            target.disabled="disabled"
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
        resetSharingModal(){
            this.selectedSharingGroupId = null
        },
        closeRefreshLinkPopover() {
            if(this.$refs["refresh-link-popover"]) {
                this.$refs["refresh-link-popover"].$emit("close")
            }
        },
    }

}
</script>

<style lang="scss">
.sd-panel-access-links .table td {
    vertical-align: middle;
}

.sd-panel-access-links .table td:first-child {
    width: 1%;
}

.sd-share-button,
.sd-generate-panel-access-links {
    margin-bottom: 0.5rem;
}

.sd-qr-code {
    height: 150px;
    width: auto;
}

.sd-qr-code-container {
    text-align: center;
    padding: 0.5rem 0 1.5rem;
}

.refresh-buttons {
  text-align: right;
}
</style>