<template>
<article>

    <b-row class="m-3 my-4 sd-panel-detail-title-wrapper">
            <div class="sd-panel-detail-title">
                <b-input-group v-if="iOwnThisPanel && isEditingTitle" class="sd-panel-detail-title--editor">
                    <b-form-input v-model="titleText"></b-form-input>
                    <b-input-group-append>
                        <b-button variant="danger" @click="stopEditingPanelTitle">Cancel</b-button>
                        <b-button variant="success" @click="saveEditedTitle">Save</b-button>
                    </b-input-group-append>
                </b-input-group>

                <h3 v-if="!isEditingTitle">{{expandedPanel.title}}</h3>
                <span class="sd-panel-detail-title-edit-icon sd-edit-icon" v-if="iOwnThisPanel && !isEditingTitle" tabindex="0" @click="editPanelTitle">
                    <font-awesome-icon icon="edit" title="Edit panel title" />
                    Edit title
                </span>
            </div>
            <!--<div class="sd-panel-figure-name">
                Panel Figure Name
            </div>-->
    </b-row>
    <b-row class="m-3">
        <b-col class="sd-panel-detail-col sd-panel-detail-col--left">
            <div class="panel-detail-image-outer-container">
                <img class="panel-detail-image"
                    :src="fullImageUrl"
                    :alt="'image for' + expandedPanel.title"
                    tabindex="0"
                    @click="openLightBox"
                    style="cursor:pointer"
                ></img>
                <font-awesome-icon @click="openLightBox" class="sd-panel-zoom-icon" icon="search-plus" size="2x" title="View image" />
            </div>
            <div class="panel-detail-caption-edit-container">
                <span class="sd-panel-detail-caption-edit-icon sd-edit-icon" v-if="iOwnThisPanel" tabindex="0" @click="editPanelCaption">
                    <font-awesome-icon  icon="edit" title="Edit panel title" />
                    Edit caption
                </span>
            </div>
            <div class="panel-detail-caption-wrapper">
                <div class="panel-detail-caption-container" v-if="!editingCaption">
                    {{ expandedPanel.caption }}
                </div>
                <div class="panel-detail-caption-edit-container" v-if="editingCaption">
                    <caption-editor></caption-editor>
                </div>
            </div>
        </b-col>
        <b-col class="sd-panel-detail-col sd-panel-detail-col--right">
                <b-tabs card content-class="sd-panel-detail-tab-card" class="sd-panel-details-tabs">
                <b-tab :title="commentCountTitle" active>
                    <b-card-text>
                        <comment-list></comment-list>
                    </b-card-text>
                </b-tab>
                <b-tab :title="'Files (' + fileCount + ')'">
                    <b-card-text>
                        <file-uploads></file-uploads>
                    </b-card-text>
                </b-tab>
                <b-tab title="SmartTags">
                    <b-card-text>
                        <smart-tags-panel></smart-tags-panel>
                    </b-card-text>
                </b-tab>
                </b-tabs>

            <div class="sd-panel-detail--panel-actions">
                <span v-if="iOwnThisPanel">Sharing status: </span>
                <toggle-button v-if="iOwnThisPanel" :value="isShared" @change="toggleShareStatus" :color="{checked: '#65dd65', unchecked: '#666'}" :labels="{checked:'Shared', unchecked:'Private'}" :width="90" :height="30" :font-size="14" class="sd-sharing-toggle"/>

                <b-button id="sd-delete-panel" variant="danger" v-if="iOwnThisPanel" class="sd-delete-panel-button"><font-awesome-icon class="sd-delete-panel-icon" icon="trash-alt" title="Delete panel"/> Delete Panel</b-button>
                <b-popover
                    ref="delete-panel-popover"
                    target="sd-delete-panel"
                    triggers="click"
                    placement="top"
                >
                <template v-slot:title>
                        Are you sure?
                    <b-button @click="closeDeletePanelPopover" class="close" aria-label="Close">
                        <span class="d-inline-block" aria-hidden="true">&times;</span>
                    </b-button>
                </template>
                    <div class="confirm-delete-content">
                        <div class="delete-buttons">
                            <b-button variant="danger" small @click="deletePanel">Delete it!</b-button>
                            <b-button variant="outline-dark" small @click="closeDeletePanelPopover">Cancel</b-button>
                        </div>
                    </div>
                </b-popover>




                <download-bar></download-bar>

            </div>

        </b-col>
    </b-row>
</article>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'
import CommentList from '@/components/comments/CommentList'
import FileUploads from '@/components/files/FileUploads'
import CaptionEditor from '@/components/caption/CaptionEditor'
import SmartTagsPanel from '@/components/tags/SmartTagsPanel'
import DownloadBar from '@/components/DownloadBar'

export default {

    name: 'PanelDetail',
    components: {
        CommentList,
        FileUploads,
        CaptionEditor,
        SmartTagsPanel,
        DownloadBar,
    },
    data(){
        return {
            isEditingTitle:false,
            titleText:"",
            showDeleteConfirmation: false,
        }
    },
    computed:{
        ...mapGetters(['expandedPanel', 'iOwnThisPanel', 'comments', 'commentCount', 'fileCount', 'editingCaption']),
        fullImageUrl(){
            return "/panels/" + this.expandedPanel.id + "/image"
        },
        commentCountTitle(){
            return "Comments (" + this.commentCount + ")"
        },
        isShared(){
            //temporary sharing hack - the panel is shared if it's part of group 5
            console.log("_-_-_-_-_-_-_-_")
            console.log(this.expandedPanel.groups)
            console.log(this.expandedPanel.groups.filter(group => group.id === 5))
            return this.expandedPanel.groups.filter(group => group.id === 5).length > 0
        },

    },
    methods:{ //run as event handlers, for example

        openLightBox(){
            this.$store.commit("toggleLightbox")
        },
        editPanelTitle(){
            this.isEditingTitle = true
            this.titleText=this.expandedPanel.title
        },
        stopEditingPanelTitle(){
            this.isEditingTitle = false
            this.titleText=""
        },
        saveEditedTitle(){
            if(this.titleText === ""){
                this.$snotify.error("You cannot remove the title.", "Not Permitted.")
            } else {
                let updatedPanel = Object.assign({}, this.expandedPanel)
                updatedPanel.title = this.titleText
                this.$store.dispatch("updatePanel", updatedPanel).then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Update succeeded")
                    this.stopEditingPanelTitle()
                }).catch(error => {
                    console.log(error)
                    this.$snotify.error(error.message, "Panel Update Failed")
                })
            }


        },
        editPanelCaption(){
            this.$store.commit("toggleEditingCaption")
        },
        closeDeletePanelPopover(){
            if(this.$refs["delete-panel-popover"]) {
                this.$refs["delete-panel-popover"].$emit("close")
            }
        },
        deletePanel(){

            this.$store.commit("setPanelLoadingState", true)

            this.$store.dispatch("deleteExpandedPanel").then(response => {
                this.$snotify.success(response.data.MESSAGE, "Deleted")
            }).catch(error => {
                console.log(error)
                this.$snotify.error("Could not delete panel", "Failed")
            })
        },
        toggleShareStatus(){

            this.$store.dispatch("toggleShareStatus").then(response => {
                this.$snotify.success(response.data.MESSAGE, "Panel Updated")
            }).catch(error => {
                console.log(error)
                this.$snotify.error("Could not update panel", "Failed")
            })

        }

    }

}
</script>

<style lang="scss">
    .panel-detail-image-outer-container {
        width:100%;
        height:380px;
        display:flex;
        text-align:center;
        align-items: center;
        justify-content: center;
        background-color: #2f2f2f;
        position:relative;
    }

    .sd-panel-detail-title-wrapper {
        padding-left: 15px;
    }

    .sd-panel-detail-col {
        min-width: 0;
    }

    .panel-detail-image {
        max-width:100%;
        max-height:100%;

    }

    .sd-panel-detail-title,
    .sd-panel-figure-name {
        width:100%;
        height:36px;
    }
    .sd-panel-figure-name {
        margin-top: 16px;
    }
    .sd-panel-detail-title h3 {
        display: inline-block;
        margin-right: 6px;
        margin-bottom: 4px;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .sd-edit-icon {
        cursor: pointer;
        position:relative;
        color: #65dd65;
        font-size:0.85em;
    }

    .sd-panel-detail-title-edit-icon {
        top:-1em;
    }
    .sd-edit-icon:hover,
    .sd-edit-icon:focus,
    .sd-edit-icon:active {
        text-decoration:underline;
    }
    .panel-detail-caption-edit-container {
        height:32px;
        padding:6px 0;
    }

    .panel-detail-caption-wrapper {
        // height:130px;
        height:200px;
        max-width: 100%;
        border: solid 1px #eee;
        padding: 6px;
    }

    .panel-detail-caption-container {
        height:100%;
        padding-right:6px;
        overflow-Y:scroll;
    }

    .sd-panel-detail-tab-card {
        border: solid 1px #fff;
        border-radius: 6px 6px 0 0;
        max-height: 400px;
        margin-top: -1px;
    }
    .sd-panel-detail-tab-card .tab-panel.card-body {
        max-height: 400px;
    }
    .sd-panel-detail-tab-card .card-text {
        overflow-y: scroll;
        max-height: 360px;
        padding-right: 1rem;
    }

    .sd-panel-details-tabs .card-header {
        padding-top:0;
        color: #333;
    }


    .sd-panel-details-tabs .nav-tabs a.nav-link {
	color: #ddd;
	border: solid 1px #fff;
}
    .sd-panel-details-tabs .nav-tabs a.nav-link.active {
	color: #333;
}

.sd-panel-detail-title--editor {
    max-width:800px;
}

.sd-panel-detail--panel-actions {
    text-align:right;
    padding: 0.5em 0;
}

.sd-delete-panel-icon {
    margin-right: 3px;
}

.sd-panel-zoom-icon {
    color: #2a2a2a;
    position: absolute;
    bottom: 12px;
    right: 12px;
    opacity:0.7;
    background:#fff;
    padding:2px;
    border-radius: 3px;
    cursor:pointer;

}

.sd-sharing-toggle {
    margin-right:1em !important;
}

</style>