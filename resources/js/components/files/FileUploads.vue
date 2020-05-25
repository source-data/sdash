<template>
    <div class="sd-file-uploads-container">
        <div v-if="iOwnThisPanel" class="sd-file-uploads-header">
            <div class="sd-file-uploads--toggle-wrapper">
                <toggle-button v-model="uploadToggle" @change="clearUploads" :color="{checked: '#666', unchecked: '#666'}" :labels="{checked:'URL', unchecked:'File'}" :width="80" :height="30" :font-size="14"/>
            </div>
            <div class="sd-file-uploads--file-wrapper" v-if="!uploadToggle">
                <b-form-file @change="updatedFile" no-drop single v-model="file" placeholder="Select a file to attach"></b-form-file>
            </div>
            <div class="sd-file-uploads--url-wrapper" v-if="uploadToggle">
                <b-form-input @change="updatedUrl" v-model="url" type="url" placeholder="Enter a URL to link remote resource"></b-form-input>
            </div>
            <div class="sd-file-uploads--submit-wrapper">
                <b-button variant="success" @click.prevent="submitFile" :disabled="disableSubmit">
                    <span v-if="!uploadToggle">Attach</span>
                    <span v-if="uploadToggle">Link</span>
                </b-button>
            </div>
        </div>
        <!-- file list -->
        <div class="sd-file-uploads--list-container">
            <b-table
                small
                dark
                class="sd-file-uploads--list-table"
                outlined
                striped
                :items="getFiles"
                :fields="fields"
                ref="fileUploadsTable"
             ><!--end of table definition-->
                <template v-if="iOwnThisPanel" v-slot:cell(action)="data">
                    <b-button variant="link" class="text-light" :id="'delete-button-' + data.item.id"><font-awesome-icon icon="trash-alt" size="lg"/></b-button>
                    <b-popover
                        :ref="'popover-' + data.item.id"
                        :target="'delete-button-' + data.item.id"
                        triggers="click"
                        placement="top"
                        @show="confirmDeletion(data.item.id)"
                        @hidden="clearDeletion"
                    >
                    <template v-slot:title>
                            Are you sure?
                        <b-button @click="closeDeletePopover(data.item.id)" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">&times;</span>
                        </b-button>
                    </template>
                        <div class="confirm-delete-content">
                            <div class="delete-buttons">
                                <b-button variant="danger" small @click="deleteFile">Delete it!</b-button>
                                <b-button variant="outline-dark" small @click="closeDeletePopover(data.item.id)">Cancel</b-button>
                            </div>
                        </div>
                    </b-popover>
                </template>
                <template v-if="!iOwnThisPanel" v-slot:cell(description)="data">
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                        <span v-if="!data.item.description" class="text-info">Add a description</span>
                </template>
                <template v-if="iOwnThisPanel" v-slot:cell(description)="data">
                    <a href="#" @click.prevent class="custom-styled-link" :id="'edit-description-' + data.item.id" title="Edit description">
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                        <span v-if="!data.item.description" class="text-info">Add a description</span>
                    </a>
                    <b-popover
                        :ref="'edit-description-popover-' + data.item.id"
                        :target="'edit-description-' + data.item.id"
                        triggers="click"
                        placement="top"
                        @show="updateFileDescription(data.item.id, data.item.description)"
                        @hidden="clearUpdate"
                    >
                    <template v-slot:title>
                            Update File Description
                        <b-button @click="closeDescriptionPopover(data.item.id)" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">&times;</span>
                        </b-button>
                    </template>
                        <div class="update-file-description">
                            <b-form-group
                            :id="'update-description-form-group-' + data.item.id"
                            description="Update your file descriptive text."
                            label="File description"
                            :label-for="'update-description-input-' + data.item.id"
                            >
                            <b-form-input id="input-1" v-model="fileDescriptionText" trim></b-form-input>
                            </b-form-group>

                            <div class="update-buttons">
                                <b-button variant="success" small @click="saveFileDescription">Save</b-button>
                                <b-button variant="outline-dark" small @click="closeDescriptionPopover(data.item.id)">Cancel</b-button>
                            </div>
                        </div>
                    </b-popover>
                </template>
                <template v-slot:cell(link)="data">
                    <a class="text-light" :href="data.item.url" target="_blank">{{data.item.url}}</a>
                    <a class="text-light" :href="'/files/' + data.item.id">{{data.item.original_filename}}</a>
                </template>


            </b-table>
        </div>
    </div>
</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'FileUploads',
    data(){

        return {
            file: null,
            url:  null,
            uploadToggle: true,
            fileToDelete: {},
            fileToUpdate: {},
            fileDescriptionText: "",
            fields:[
                {key:'action', label:'', sortable: false},
                {key:'description', label:'Description', sortable: true, sortByFormatted:true, formatter:"descriptionContents"},
                {key:'link', label: 'Filename / URL', sortable: true, sortByFormatted:true, formatter:"linkContents"},
                ]
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['getFiles', 'iOwnThisPanel']),
        disableSubmit(){
            return (this.file===null && this.url===null)
        }
    },
    methods:{ //run as event handlers, for example

        updatedFile(){
            this.url = null;
        },
        updatedUrl(){
            this.file = null;
        },
        clearUploads(){
            this.updatedFile()
            this.updatedUrl()
        },
        submitFile(){
            if(this.file) this.sendFile()
            if(this.url) this.sendUrl()
        },
        sendFile(){
            this.$store.dispatch("storeFile", this.file).then(response => {
                this.$snotify.success(response.data.MESSAGE, "File Uploaded")
                this.clearUploads()
            }).catch(error => {
                this.$snotify.error(error.data.message, "Upload failed")
            })
        },
        sendUrl(){
            this.$store.dispatch("storeUrl", {url:this.url}).then(response => {
                this.$snotify.success(response.data.MESSAGE, "URL Stored")
                this.clearUploads()
            }).catch(error => {
                this.$snotify.error("Not a valid URL - did you include http(s)://?", "Upload failed")
            })
        },
        deleteFile(){
            if(!this.fileToDelete){

                this.$snoftify.error("Select a file to delete", "Deletion Failed")
                return;

            }

            this.$store.dispatch("deleteFile", this.fileToDelete).then(response => {
                this.closeDeletePopover(this.fileToDelete.id)
                this.$snotify.success(response.data.MESSAGE, "File Deleted")
                this.clearDeletion()
            }).catch(error => {
                this.closeDeletePopover(this.fileToDelete.id)
                this.$snotify.error(error.data.message, "Deletion failed")
                this.clearDeletion()
            })

        },
        confirmDeletion(id){
            let toDelete = this.getFiles.filter(file => file.id === id)[0]
            this.fileToDelete = toDelete
        },
        clearDeletion(){
            this.fileToDelete = {}
        },
        clearUpdate(){
            this.fileToUpdate = {}
            this.fileDescriptionText = ""

        },
        linkContents(value, key, item){
            return item.original_filename || item.url
        },
        descriptionContents(value, key, item){
            return item.description || ""
        },
        closeDeletePopover(id){
            if(this.$refs["popover-" + id]) {
                this.$refs["popover-" + id].$emit("close")
            }
        },
        closeDescriptionPopover(id){
            if(this.$refs["edit-description-popover-" + id]) {
                this.$refs["edit-description-popover-" + id].$emit("close")
            }
        },
        updateFileDescription(id, text){
            let toUpdate = this.getFiles.filter(file => file.id === id)[0]
            this.fileToUpdate = toUpdate
            this.fileDescriptionText = text
        },
        saveFileDescription(){
            if(!this.fileToUpdate || !this.fileDescriptionText){

                this.$snoftify.error("Please edit the file description via the interface", "Update Failed")
                return;

            }

            let updatedFile = Object.assign({}, this.fileToUpdate)
            updatedFile.description = this.fileDescriptionText

            this.$store.dispatch("updateFileMeta", updatedFile).then(response => {
                this.closeDescriptionPopover(this.fileToUpdate.id)
                this.$snotify.success(response.data.MESSAGE, "File Updated")
                this.clearUpdate()
                this.$refs.fileUploadsTable.refresh()
            }).catch(error => {
                this.closeDescriptionPopover(this.fileToUpdate.id)
                this.$snotify.error(error.data.message, "Update failed")
                this.clearUpdate()
            })
        },

    }

}
</script>

<style lang="scss">

.sd-file-uploads-header {

    display: flex;
    flex-wrap: nowrap;
    padding-bottom: 1em;
    margin-bottom: 1em;
    border-bottom: solid 1px #eee;

}

.sd-file-uploads-header > div {

    flex-grow: 1;
    align-items: center;

}

.sd-file-uploads--file-wrapper .custom-file-label {
    padding-right: 79px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sd-file-uploads--or {

    padding: 0 1em;
    max-width: 45px;
    text-align: center;
    line-height: 2em;

}

.sd-file-uploads--submit-wrapper{
    text-align:center;

    button {
        margin:0 3px;
    }
}

.sd-file-uploads--toggle-wrapper {
    display:flex;
    align-items: center;
}

label.vue-js-switch {
    margin: 0;
}

.custom-styled-link{
    color: #65dd65;
    cursor: pointer;
}
.custom-styled-link:hover,
.custom-styled-link:focus,
.custom-styled-link:active
{
    color: darken(#65dd65, 15%);
}

</style>