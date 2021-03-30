<template>
    <b-navbar sticky type="light" class="sd-action-bar-top shadow-sm">
        <b-container>
            <b-nav-form>
                <b-input-group class="mr-sm-2 my-2">
                    <b-form-input class="border border-dark" placeholder="Search" :value="searchString" @input="updateLocalSearchString"></b-form-input>
                    <b-input-group-append>
                        <b-button variant="outline-dark" type="submit" @click.prevent.stop="performTextSearch" >Search</b-button>
                    </b-input-group-append>
                </b-input-group>
                        <b-button variant="outline-dark" class="my-2"  type="submit" @click.prevent.stop="clearTextSearch" >Clear</b-button>
            </b-nav-form>
            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">
                <b-nav-form v-if="searchMode=='group' && isGroupAdmin">
                    <b-button variant="outline-danger" class="my-2" @click.prevent id="sd-delete-group" type="submit" v-b-tooltip.hover.top title="Delete the group">
                        <font-awesome-icon icon="trash-alt" />
                        Delete Group
                    </b-button>
                    <b-popover
                        ref="delete-group-popover"
                        target="sd-delete-group"
                        triggers="click"
                        placement="bottom"
                        selector="sd-delete-group"
                    >
                        <template v-slot:title>
                                Are you sure?
                            <b-button @click="closeDeleteGroupPopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>
                        <div class="confirm-delete-content">
                            <p>
                                Delete the user group? Panels will not be deleted from the system but will no longer be shared with the group.
                            </p>
                            <div class="delete-buttons">
                                <b-button variant="danger" small @click="deleteGroup">Delete Group</b-button>
                                <b-button variant="outline-dark" small @click="closeDeleteGroupPopover">Cancel</b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-nav-form>
                <b-nav-form v-if="searchMode=='group' && !isGroupOwner">
                    <b-button variant="outline-danger" class="my-2" @click.prevent id="sd-quit-group" type="submit" v-b-tooltip.hover.top title="Remove yourself from the group">
                        <font-awesome-icon icon="sign-out-alt" />
                        Leave Group
                    </b-button>
                    <b-popover
                        ref="quit-group-popover"
                        target="sd-quit-group"
                        triggers="click"
                        placement="bottom"
                        selector="sd-quit-group"
                    >
                    <template v-slot:title>
                            Are you sure?
                        <b-button @click="closeQuitGroupPopover" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">&times;</span>
                        </b-button>
                    </template>
                        <div class="confirm-delete-content">
                            <p>
                                Remove yourself and your panels from the group?
                            </p>
                            <div class="delete-buttons">
                                <b-button variant="danger" small @click="quitGroup">Remove Me</b-button>
                                <b-button variant="outline-dark" small @click="closeQuitGroupPopover">Cancel</b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-nav-form>
                <b-nav-form v-if="searchMode=='user' && countSelectedPanels > 0">
                    <span>
                        {{ countSelectedPanels }} panel(s) selected (</span><b-button @click="clearSelectedPanels" class="action-bar-top--clear-button" variant="link">clear selection</b-button><span>)</span>

                    <b-button class="sd-action-bar-top--delete-button" id="sd-mass-delete-panels" v-b-tooltip.hover.top title="Delete selected panels">
                        <font-awesome-icon icon="trash-alt" size="2x" />
                    </b-button>
                    <b-button class="sd-action-bar-top--group-button" id="sd-add-panels-to-sharing-group" v-b-tooltip.hover.top title="Add panels to sharing group">
                        <font-awesome-icon icon="users" size="2x" />
                    </b-button>
                    <!--
                    <b-button class="sd-action-bar-top--figure-button" v-b-tooltip.hover.top title="Combine panels into figure">
                        <font-awesome-icon icon="layer-group" size="2x" />
                    </b-button>
                    -->
                    <!-- panel mass-deleting popover -->
                    <b-popover
                        ref="delete-popover"
                        target="sd-mass-delete-panels"
                        triggers="click"
                        placement="bottom"
                    >
                        <template v-slot:title>
                                Are you sure?
                            <b-button @click="closeDeletePopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>
                        <p>
                            <b-button @click="closeDeletePopover" size="sm" >Cancel</b-button>
                            <b-button @click="deleteMultiplePanels" size="sm" variant="danger">
                                <font-awesome-icon icon="trash-alt" size="1x" />
                                Delete {{ countSelectedPanels }} panel(s)
                            </b-button>
                        </p>
                    </b-popover>

                    <!-- panel sharing group popover -->
                    <b-popover
                        ref="sharing-group-popover"
                        target="sd-add-panels-to-sharing-group"
                        triggers="click"
                        placement="bottom"
                        @hidden="onGroupsPopoverHide"
                    >
                        <template v-slot:title>
                                Add to group
                            <b-button @click="closeGroupsPopover" class="close" aria-label="Close">
                                <span class="d-inline-block" aria-hidden="true">&times;</span>
                            </b-button>
                        </template>

                        <b-form-group
                        id="sd-group-fieldset-1"
                        label="Add to existing group"
                        label-for="sd-group-select"
                        >
                        <b-form-select size="sm" id="sd-group-select" :options="myGroups" v-model="selectedSharingGroupId" trim>
                            <template #first>
                                <b-form-select-option :value="null">Select group</b-form-select-option>
                            </template>
                        </b-form-select>
                        </b-form-group>
                        <p>
                            <b-button @click="addPanelsToGroup" size="sm" variant="info" :disabled="!selectedSharingGroupId">
                                <font-awesome-icon icon="users" size="1x" />
                                Add to Group
                            </b-button>
                        </p>
                        <p>
                            <label class="d-block">or create new sharing group</label>
                            <b-button @click="closeGroupsPopover" size="sm">Cancel</b-button>
                            <b-button @click="createGroup" size="sm" variant="success">
                                <font-awesome-icon icon="users" size="1x" />
                                Create New Group
                            </b-button>
                        </p>
                    </b-popover>
                </b-nav-form>

                <!-- create a new SmartFigure and upload image file -->
                <b-navbar-nav v-if="searchMode=='user' && countSelectedPanels < 1">
                    <b-nav-item >
                        <b-button
                            class="rounded-circle"
                            variant="light"
                            v-b-modal.modal-uploader
                            v-b-tooltip.hover title="Create new SmartFigures"
                        >
                            <font-awesome-icon icon="plus" size="2x" />
                        </b-button>
                    </b-nav-item>
                </b-navbar-nav>
                <b-modal
                    id="modal-uploader"
                    @ok="handleUploaderOK"
                    @cancel="handleUploaderCancel"
                    @close="handleUploaderClose"
                >
                    <template v-slot:modal-title>Create new SmartFigures</template>
                    <vue-dropzone
                        :options="uploadZoneOptions"
                        :useCustomSlot=true
                        ref="uploading-zone"
                        id="uploading-zome"
                    >
                        <div class="dropzone-custom-content">
                            <h3 class="dropzone-custom-title">
                                <!-- <font-awesome-icon :icon="['fas', 'cloud-upload']" size="lg" /> -->
                                <i class='fa fa-cloud-upload'></i>
                                Drag & drop image files!
                            </h3>
                            <div class="subtitle">...or select files from computer</div>
                        </div>
                    </vue-dropzone>
                </b-modal>
            </b-navbar-nav>
        </b-container>
    </b-navbar>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import Lightbox from 'vue-easy-lightbox'
import vueDropzone from "vue2-dropzone"
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {

    name: 'ActionBarTop',
    components: { Lightbox, vueDropzone },

    data(){
        return {
            localSearchString: "",
            file: null,
            selectedSharingGroupId: null,
            uploadZoneOptions: {
                url: "https://httpbin.org/post",
                addRemoveLinks: true,
                maxFiles: 1,
            },
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['currentUser', 'selectedPanels', 'countSelectedPanels', 'userGroups', 'searchString', 'hasLoadedAllResults', 'userAdminGroups', 'searchMode', 'isGroupOwner', 'isGroupAdmin']),
        myAdminGroups(){
            let groups = this.userAdminGroups.reduce((myGroups, group) => {
                myGroups.push({text: group.name, value: group.id})
                return myGroups
            },[])
            return groups
        },
        myGroups(){
            let groups = this.userGroups.reduce((myGroups, group) => {
                myGroups.push({text: group.name, value: group.id})
                return myGroups
            },[])
            return groups
        },
    },

    methods: { //run as event handlers, for example

        ...mapActions(['clearSelectedPanels', 'removeUserFromGroup', 'deleteSelectedPanels', 'deleteUserGroup']),
        updateLocalSearchString(value){
            this.localSearchString = value
        },
        performTextSearch(){
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("setSearchString", this.localSearchString)
            this.$store.dispatch("fetchPanelList")
        },
        clearTextSearch(){
            this.localSearchString = ""
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("setSearchString", this.localSearchString)
            this.$store.dispatch("fetchPanelList")
        },
        handleUploaderOK(bvModalEvt) {
            // https://bootstrap-vue.org/docs/components/modal#prevent-closing
            bvModalEvt.preventDefault()
            const new_files = this.$refs["uploading-zone"].getAcceptedFiles()
            console.debug("new_files", new_files)
            this.file = new_files[0]
            this.attemptPanelUpload()
            this.hideUploader()
        },
        handleUploaderClose(bvModalEvt) {
            this.file = null
        },
        handleUploaderCancel() {
            this.handleUploaderClose()
        },
        hideUploader() {
            this.$nextTick(() => {
                this.$bvModal.hide('modal-uploader')
            })
        },
        attemptPanelUpload(){
            if(this.file == null) return

            let submission = new FormData()
            submission.append("file", this.file)
            this.$store.dispatch("uploadNewPanel", submission)
            .then(response => {
                    this.$snotify.success("New panel created", "Uploaded")
                    this.file = null
                    this.hideUploader()
                })
                .catch(error => {
                    this.$snotify.error(error.data.errors.file[0], "Upload failed")
                    this.file = null
                    this.hideUploader()
                })
        },
        addPanelsToGroup(){
            this.$store.dispatch("manageGroupPanels", {
                action: 'add',
                target: 'selected',
                groupId: this.selectedSharingGroupId
            })
            .then((response)=>{
                this.$snotify.success(response.data.MESSAGE, "Success")
                this.closeGroupsPopover()
            })
            .catch(error => {
                this.$snotify.error("Panels could not be added to group", "Failure")
            })
        },
        closeGroupsPopover(){
            if(this.$refs["sharing-group-popover"]) {
                this.$refs["sharing-group-popover"].$emit("close")
            }
        },
        onGroupsPopoverHide(){
            this.selectedSharingGroupId = null
        },
        createGroup(){
            this.closeGroupsPopover()
            this.$router.push({name: "creategroup"})
        },
        closeQuitGroupPopover(){
            if(this.$refs["quit-group-popover"]) {
                this.$refs["quit-group-popover"].$emit("close")
            }
        },
        closeDeleteGroupPopover(){
            if(this.$refs["delete-group-popover"]) {
                this.$refs["delete-group-popover"].$emit("close")
            }
        },
        closeDeletePopover(){
            if(this.$refs["delete-popover"]) {
                this.$refs["delete-popover"].$emit("close")
            }
        },
        deleteMultiplePanels(){
            this.deleteSelectedPanels().then((response)=>{
                    this.$snotify.success(response.data.MESSAGE, "Success")
                }).catch(error => {
                    this.$snotify.error(error.data.MESSAGE, "Deletion Failed!")
            })
        },
        quitGroup(){
            this.removeUserFromGroup(this.currentUser.id).then(response => {
                this.closeQuitGroupPopover()
                this.$router.push({name: 'dashboard'})
                this.$snotify.success(response.data.MESSAGE, "Success")
            }).catch(error => {
                this.$snotify.error(error.data.MESSAGE, "Update Failed!")
                this.closeQuitGroupPopover()

            })
        },
        deleteGroup(){
            this.deleteUserGroup().then(response => {
                this.closeDeleteGroupPopover()
                this.$router.push({name: 'dashboard'})
                this.$snotify.success(response.data.MESSAGE, "Success")
            }).catch(error => {
                this.$snotify.error(error.data.MESSAGE, "Delete Failed!")
                this.closeDeleteGroupPopover()

            })
        }


    }

}
</script>

<style lang="scss">
.sd-action-bar-top {
    background-color: #b4c9ea;
}

.sd-action-bar-top--delete-button,
.sd-action-bar-top--group-button,
.sd-action-bar-top--figure-button,
{
    color: #383838;
    background: none;
    border:none;
}

.sd-action-bar-top--delete-button:hover,
.sd-action-bar-top--delete-button:focus,
.sd-action-bar-top--delete-button:active{
    background:none;
    color: red;

}

.sd-action-bar-top--group-button:hover,
.sd-action-bar-top--group-button:focus,
.sd-action-bar-top--group-button:active,
.sd-action-bar-top--figure-button:hover,
.sd-action-bar-top--figure-button:focus,
.sd-action-bar-top--figure-button:active,
{
    background:none;
    color: green;

}

.fade-enter-active, .fade-leave-active {
    transition: opacity .25s;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}

.sd-action-bar--file-upload .custom-file-label {
    border: solid 1px #343a40;
    color: #6d767e;
}

.sd-action-bar--file-upload .custom-file-label:after {
    background-color:#b4c9ea;
}

#sd-quit-group {
    margin-left:0.5rem;
}
</style>