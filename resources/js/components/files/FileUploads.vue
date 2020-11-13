<template>
    <div class="sd-file-uploads-container">
        <div v-if="iCanEditThisPanel" class="sd-file-uploads-header">
            <div class="sd-file-uploads--toggle-wrapper">
                <toggle-button v-model="uploadToggle" @change="clearUploads" :disabled="pendingUpload"
                    :color="{checked: '#666', unchecked: '#666'}" :labels="{checked:'URL', unchecked:'File'}" :width="80" :height="30" :font-size="14"/>
            </div>
            <div class="sd-file-uploads--category-wrapper">
                <b-form-select class="sd-file-uploads--category-selector" v-model="categoryId" :options="fileCategories">
                    <template v-slot:first>
                        <b-form-select-option value="null">Select category</b-form-select-option>
                    </template>
                </b-form-select>
            </div>
            <div class="sd-file-uploads--file-wrapper" v-if="!uploadToggle">
                <b-form-file @change="updatedFile" no-drop single v-model="file" :disabled="pendingUpload" placeholder="Select a file to attach"></b-form-file>
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
                striped
                :items="getFiles"
                :fields="fields"
                primary-key="id"
                ref="fileUploadsTable"
             ><!--end of table definition-->
                <template v-if="iCanEditThisPanel" v-slot:cell(action)="data">
                    <b-button variant="link" class="text-light" :id="'delete-button-' + data.item.id"><font-awesome-icon icon="trash-alt" size="lg"/></b-button>
                    <b-popover
                        :ref="'delete-popover-' + data.item.id"
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
                <template v-slot:cell(category)="data">
                    <template v-if="iCanEditThisPanel">
                        <a href="#" @click.prevent class="custom-styled-link" :id="'edit-category-' + data.item.id" title="Edit category">
                            <span v-if="data.item.file_category_id">{{ getFileCategoryName(data.item.file_category_id) }}</span>
                            <span class="sd-edit-icon" v-if="!data.item.file_category_id">
                                <font-awesome-icon icon="edit" title="Edit category" />
                                Edit category
                            </span>
                        </a>
                    </template>
                    <template v-if="!iCanEditThisPanel">
                        <span v-if="data.item.file_category_id">{{ getFileCategoryName(data.item.file_category_id) }}</span>
                        <span v-if="!data.item.file_category_id" class="text-info">&mdash;</span>
                    </template>
                    <b-popover
                        :ref="'edit-category-popover-' + data.item.id"
                        :target="'edit-category-' + data.item.id"
                        triggers="click"
                        placement="top"
                        @show="updateFileCategory(data.item.id, data.item.file_category_id)"
                        @hidden="clearUpdate"
                    >
                    <template v-slot:title>
                        Category
                        <b-button @click="closeCategoryPopover(data.item.id)" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">&times;</span>
                        </b-button>
                    </template>
                        <div class="update-file-category">
                            <b-form-group
                            :id="'update-category-form-group-' + data.item.id"
                            :label-for="'update-category-input-' + data.item.id"
                            >
                            <b-form-select v-model="selectedCategoryId" :options="fileCategories">
                                <template v-slot:first>
                                    <b-form-select-option value="">(none)</b-form-select-option>
                                </template>
                            </b-form-select>
                            </b-form-group>

                            <div class="update-buttons">
                                <b-button variant="success" small @click="saveFileCategory">Save</b-button>
                                <b-button variant="outline-dark" small @click="closeCategoryPopover(data.item.id)">Cancel</b-button>
                            </div>
                        </div>
                    </b-popover>
                </template>
                <template v-slot:cell(description)="data">
                    <template v-if="!iOwnThisPanel">
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                    </template>
                    <template v-if="iOwnThisPanel">
                        <a href="#" @click.prevent class="custom-styled-link" :id="'edit-description-' + data.item.id" title="Edit description">
                            <span v-if="data.item.description">{{ data.item.description }}</span>
                            <span class="sd-edit-icon" v-if="!data.item.description">
                                <font-awesome-icon icon="edit" title="Edit description" />
                                Edit description
                            </span>
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
                                Description
                                <b-button @click="closeDescriptionPopover(data.item.id)" class="close" aria-label="Close">
                                    <span class="d-inline-block" aria-hidden="true">&times;</span>
                                </b-button>
                            </template>
                            <div class="update-file-description">
                                <b-form-group
                                :id="'update-description-form-group-' + data.item.id"
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
                </template>
                <template v-slot:cell(link)="data">
                    <a class="text-light" :href="data.item.url" target="_blank">{{data.item.url}}</a>
                    <a class="text-light" :href="'/files/' + data.item.id">{{data.item.original_filename}}</a>
                </template>
                <template v-slot:cell(size)="data">
                    <span v-if="data.item.file_size">{{formatBytes(data.item.file_size)}}</span>
                </template>
                <template v-slot:custom-foot>
                    <b-tr v-if="pendingUpload">
                        <b-td class="text-center">
                            <b-button variant="link" class="text-light">
                                <b-spinner small label="Uploading"></b-spinner>
                            </b-button>
                        </b-td>
                        <b-td colspan="4">Uploading <span class="font-italic">{{ file.name }}</span></b-td>
                    </b-tr>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>

import store from '@/stores/store'
import formatter from '@/services/formatter'
import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'FileUploads',
    data(){
        return {
            file: null,
            url:  null,
            categoryId: null,
            uploadToggle: true,
            pendingUpload: false,
            fileToDelete: {},
            fileToUpdate: {},
            fields:[
                {key:'action', label:'', sortable: false},
                {key:'category', label:'Category', sortable: true, sortByFormatted:true, formatter:"distillCategoryName"},
                {key:'description', label:'Description', sortable: true, sortByFormatted:true, formatter:"distillDescription"},
                {key:'link', label: 'Filename / URL', sortable: true, sortByFormatted:true, formatter:"distillResourceLink"},
                {key:'size', label: 'Size', sortable: true, sortByFormatted:true, formatter:"distillFileSize"},
            ],
            selectedCategoryId: null,
            fileDescriptionText: ""
        }
    }, /* end of data */
    mixins: [formatter],
    computed: {
        ...mapGetters([
            'getFiles',
            'iOwnThisPanel',
            'iHaveAuthorPrivileges',
            'getFileCategories',
            'getFileCategoryById'
        ]),
        iCanEditThisPanel(){
            return (this.iOwnThisPanel || this.iHaveAuthorPrivileges)
        },
        disableSubmit(){
            return (this.file===null && this.url===null) || this.pendingUpload
        },
        fileCategories(){
            let categories = this.getFileCategories.reduce((categories, category) => {
                categories.push({text: category.name, value: category.id})
                return categories
            },[])
            return categories
        }
    },
    methods:{ //run as event handlers, for example

        updatedFile(){
            this.url = null
        },
        updatedUrl(){
            this.file = null
        },
        clearUploads(){
            this.updatedFile()
            this.updatedUrl()
            this.categoryId = null
        },
        submitFile(){
            if(this.file) this.sendFile()
            if(this.url) this.sendUrl()
        },
        sendFile(){
            this.pendingUpload = true
            this.$store.dispatch("storeFile", {file:this.file, file_category_id:this.categoryId}).then(response => {
                this.$snotify.success(response.data.MESSAGE, "File Uploaded")
                this.clearUploads()
                this.pendingUpload = false
            }).catch(error => {
                this.$snotify.error(error.data.message, "Upload failed")
                this.pendingUpload = false
            })
        },
        sendUrl(){
            this.$store.dispatch("storeUrl", {url:this.url, file_category_id:this.categoryId}).then(response => {
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
            this.closeDeletePopover(this.fileToDelete.id)
            this.$store.dispatch("deleteFile", this.fileToDelete).then(response => {
                this.$snotify.success(response.data.MESSAGE, "File Deleted")
                this.clearDeletion()
            }).catch(error => {
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
            this.selectedCategoryId = null
            this.fileDescriptionText = ""
        },
        distillResourceLink(value, key, item){
            return item.original_filename || item.url
        },
        distillFileSize(value, key, item){
            return item.file_size || ""
        },
        distillCategoryName(value, key, item){
            return this.getFileCategoryName(item.file_category_id)
        },
        distillDescription(value, key, item){
            return item.description || ""
        },
        closeDeletePopover(id){
            if(this.$refs["delete-popover-" + id]) {
                this.$refs["delete-popover-" + id].$emit("close")
            }
        },
        closeCategoryPopover(id){
            if(this.$refs["edit-category-popover-" + id]) {
                this.$refs["edit-category-popover-" + id].$emit("close")
            }
        },
        getFileCategoryName(fileCategoryId){
            let category = this.getFileCategoryById(fileCategoryId);
            return (category ? category.name : "")
        },
        updateFileCategory(fileId, fileCategoryId){
            let toUpdate = this.getFiles.filter(file => file.id === fileId)[0]
            this.fileToUpdate = toUpdate
            this.selectedCategoryId = fileCategoryId
        },
        saveFileCategory(){
            if(!this.fileToUpdate){
                this.$snoftify.error("Please edit the category via the interface", "Update Failed")
                return;
            }

            let updatedFile = Object.assign({}, this.fileToUpdate)
            updatedFile.file_category_id = this.selectedCategoryId || null

            this.$store.dispatch("updateFileMeta", updatedFile).then(response => {
                this.closeCategoryPopover(this.fileToUpdate.id)
                this.$snotify.success(response.data.MESSAGE, "File Updated")
                this.clearUpdate()
                this.$refs.fileUploadsTable.refresh()
            }).catch(error => {
                this.closeCategoryPopover(this.fileToUpdate.id)
                this.$snotify.error(error.data.message, "Update failed")
                this.clearUpdate()
            })
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
            if (!this.fileToUpdate){
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
        }
    }
}
</script>

<style lang="scss">

.sd-file-uploads--list-container {
    overflow-x: auto;
}

.sd-file-uploads--list-table td {
    vertical-align: middle;
}

.sd-file-uploads--list-table td:first-child {
    width: 1%;
}

.sd-file-uploads--list-table td:nth-child(4) {
    max-width: 10rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sd-file-uploads--list-table th:nth-child(5),
.sd-file-uploads--list-table td:nth-child(5) {
    white-space: nowrap;
    text-align: right;
}

.sd-file-uploads--list-table td a .text-info {
    color: #65dd65 !important;
}

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

.sd-file-uploads--submit-wrapper {
    text-align:center;
    button {
        margin:0 3px;
    }
}

.sd-file-uploads--toggle-wrapper {
    display:flex;
    align-items: center;
}

.sd-file-uploads--category-wrapper {
    padding: 0 1rem;
}

label.vue-js-switch {
    margin: 0;
}

.custom-styled-link {
    color: #b0cddb;
    cursor: pointer;
}

.custom-styled-link:hover,
.custom-styled-link:focus,
.custom-styled-link:active {
    color: darken(#b0cddb, 15%);
}

</style>