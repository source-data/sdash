<template>
    <div>
        <form v-if="iCanEditThisPanel" class="add-source">
            <div class="form-row">
                <b-col cols="auto" class="source-type-toggle text-dark">
                    <toggle-button
                        v-model="uploadToggle"
                        @change="clearUploads"
                        :disabled="pendingUpload"
                        :css-colors="true"
                        :labels="{checked:'URL', unchecked:'File'}"
                        :width="80"
                        :height="38"
                    />
                </b-col>

                <b-col cols="auto">
                    <b-form-select v-model="categoryId" :options="fileCategories">
                        <template v-slot:first>
                            <b-form-select-option value="null">Category</b-form-select-option>
                        </template>
                    </b-form-select>
                </b-col>

                <b-col cols="auto" v-if="!uploadToggle">
                    <b-form-file
                        @change="updatedFile"
                        no-drop
                        single
                        v-model="file"
                        :disabled="pendingUpload"
                        placeholder="Select a file"
                    ></b-form-file>
                </b-col>

                <b-col cols="auto" v-if="uploadToggle">
                    <b-form-input
                        @change="updatedUrl"
                        v-model="url"
                        type="url"
                        placeholder="Enter a URL"
                    ></b-form-input>
                </b-col>

                <b-col cols="auto">
                    <b-button variant="primary" @click.prevent="submitFile" :disabled="disableSubmit">
                        <span v-if="!uploadToggle">Attach File</span>
                        <span v-if="uploadToggle">Add Link</span>
                    </b-button>
                </b-col>
            </div>
        </form>

        <!-- file list -->
        <div class="panel-sources-list">
            <b-table
                dark
                small
                :items="getFiles"
                :fields="fields"
                primary-key="id"
                ref="fileUploadsTable"
            >
                <template v-slot:cell(category)="data">
                    <template v-if="iCanEditThisPanel">
                        <a
                            href="#"
                            @click.prevent
                            class="custom-styled-link"
                            :id="'edit-category-' + data.item.id"
                            title="Edit category"
                        >
                            <span v-if="data.item.file_category_id">
                                {{ getFileCategoryName(data.item.file_category_id) }}
                            </span>

                            <span class="sd-edit-icon" v-if="!data.item.file_category_id">
                                <font-awesome-icon icon="edit" title="Edit category" />
                            </span>
                        </a>
                    </template>

                    <template v-if="!iCanEditThisPanel">
                        <span v-if="data.item.file_category_id">
                            {{ getFileCategoryName(data.item.file_category_id) }}
                        </span>

                        <span v-if="!data.item.file_category_id" class="text-info">&mdash;</span>
                    </template>

                    <b-popover
                        :ref="'edit-category-popover-' + data.item.id"
                        :target="'edit-category-' + data.item.id"
                        triggers="click blur"
                        placement="top"
                        @show="updateFileCategory(data.item.id, data.item.file_category_id)"
                        @hidden="clearUpdate"
                        custom-class="sd-custom-popover"
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
                                <b-button variant="primary" small @click="saveFileCategory">Save</b-button>

                                <b-button variant="outline-dark" small @click="closeCategoryPopover(data.item.id)">
                                    Cancel
                                </b-button>
                            </div>
                        </div>
                    </b-popover>
                </template>

                <template v-slot:cell(description)="data">
                    <template v-if="!iOwnThisPanel">
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                    </template>

                    <template v-if="iOwnThisPanel">
                        <a
                            href="#"
                            @click.prevent
                            class="custom-styled-link"
                            :id="'edit-description-' + data.item.id"
                            title="Edit description"
                        >
                            <span v-if="data.item.description">{{ data.item.description }}</span>

                            <span class="sd-edit-icon" v-if="!data.item.description">
                                <font-awesome-icon icon="edit" title="Edit description" />
                            </span>
                        </a>

                        <b-popover
                            :ref="'edit-description-popover-' + data.item.id"
                            :target="'edit-description-' + data.item.id"
                            triggers="click blur"
                            placement="top"
                            @show="updateFileDescription(data.item.id, data.item.description)"
                            @hidden="clearUpdate"
                            custom-class="sd-custom-popover"
                        >
                            <template v-slot:title>
                                Description
                                <b-button
                                    @click="closeDescriptionPopover(data.item.id)"
                                    class="close"
                                    aria-label="Close"
                                >
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
                                    <b-button variant="primary" small @click="saveFileDescription">
                                        Save
                                    </b-button>

                                    <b-button
                                        variant="outline-dark"
                                        small
                                        @click="closeDescriptionPopover(data.item.id)"
                                    >
                                        Cancel
                                    </b-button>
                                </div>
                            </div>
                        </b-popover>
                    </template>
                </template>

                <template v-slot:cell(link)="data">
                    <a
                        class="source-url"
                        :href="data.item.url"
                        v-b-tooltip.hover.left
                        :title="data.item.url"
                        target="_blank"
                    >
                        {{data.item.url}}
                    </a>

                    <a
                        class="source-file"
                        v-b-tooltip.hover.left
                        :title="data.item.original_filename"
                        :href="'/files/' + data.item.id"
                    >
                        {{data.item.original_filename}}
                    </a>
                </template>

                <template v-slot:cell(size)="data">
                    <span v-if="data.item.file_size">{{formatBytes(data.item.file_size, decimals=0)}}</span>
                </template>

                <template v-if="iCanEditThisPanel" v-slot:cell(action)="data">
                    <b-form-checkbox v-model="selectedItems" :value="data.item.id"></b-form-checkbox>
                </template>

                <template #head(action)>
                    <b-form-checkbox :checked="allItemsSelected" @change="toggleSelectAll"></b-form-checkbox>
                </template>

                <template v-slot:custom-foot>
                    <b-tr v-if="pendingUpload">
                        <b-td class="text-center">
                            <b-button variant="link">
                                <b-spinner small label="Uploading"></b-spinner>
                            </b-button>
                        </b-td>
                        <b-td colspan="4">Uploading <span class="font-italic">{{ file.name }}</span></b-td>
                    </b-tr>
                </template>
            </b-table>

            <b-button variant="danger" class="text-light" id="delete-selected-sources" :disabled="selectedItems.length === 0">
                <font-awesome-icon icon="trash-alt" /> Delete selected
            </b-button>

            <b-popover
                ref="delete-popover"
                target="delete-selected-sources"
                triggers="click blur"
                placement="top"
                custom-class="sd-custom-popover"
            >
                <template v-slot:title>
                    Are you sure?
                    <b-button @click="closeDeletePopover()" class="close" aria-label="Close">
                        <span class="d-inline-block" aria-hidden="true">&times;</span>
                    </b-button>
                </template>

                <div class="confirm-delete-content">
                    <div class="delete-buttons">
                        <b-button variant="danger" small @click="deleteFiles">
                            Delete it!
                        </b-button>

                        <b-button variant="outline-dark" small @click="closeDeletePopover()">
                            Cancel
                        </b-button>
                    </div>
                </div>
            </b-popover>
        </div>
    </div>
</template>

<script>

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
                {key:'description', label:'Description', sortable: true, sortByFormatted:true, formatter:"distillDescription"},
                {key:'category', label:'Category', sortable: true, sortByFormatted:true, formatter:"distillCategoryName"},
                {key:'link', label: 'Filename / URL', sortable: true, sortByFormatted:true, formatter:"distillResourceLink"},
                {key:'size', label: 'Size', sortable: true, sortByFormatted:true, formatter:"distillFileSize"},
            ],
            selectedCategoryId: null,
            fileDescriptionText: "",
            selectedItems: [],
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
        },
        selectedFiles() {
            let files = this.getFiles;
            let selectedFiles = this.selectedItems.map(function getFile(id) {
                let matchingFiles = files.filter(function idMatches(file) {
                    return file.id === id;
                });
                if (matchingFiles.length === 1) {
                    return matchingFiles[0];
                } else {
                    console.log('no attachment (or multiple) found for id', id)
                }
            });
            return selectedFiles;
        },
        allItemsSelected() {
            let numSelected = this.selectedItems.length;
            return numSelected > 0 && numSelected === this.getFiles.length;
        },
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
        deleteFiles(){
            if(!this.selectedFiles) {
                this.$snoftify.error("Select a file to delete", "Deletion Failed")
                return;
            }
            this.closeDeletePopover()
            this.selectedFiles.forEach(file => {
                this.$store.dispatch("deleteFile", file).then(response => {
                    this.clearSelection()
                }).catch(error => {
                    this.$snotify.error(error.data.message, "Deletion failed")
                    this.clearSelection()
                })
            });

        },
        clearSelection() {
            this.selectedItems = []
        },
        toggleSelectAll() {
            this.selectedItems = this.selectedItems.length === this.getFiles.length
                ? [] // clear the selections if all attachments are selected
                : this.getFiles.map(file => file.id); // select all files if none or only some are selected
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
        closeDeletePopover(){
            if(this.$refs["delete-popover"]) {
                this.$refs["delete-popover"].$emit("close")
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

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.add-source {
    margin-bottom: 0.75rem;
}
.add-source > * {
    padding: 0;
}
.add-source > *:not(:last-child) {
    padding-right: 1rem;
}
.add-source > .form-row > .col-auto > * {
    border-radius: 0.65rem;
}

/* To influence the style of the URL/File toggle button in a scoped block, we need to use deep selectors that apply
 * to child elements: https://vue-loader.vuejs.org/guide/scoped-css.html#child-component-root-elements
 */
.add-source::v-deep .v-switch-core {
    background-color: $mostly-white-gray;
}
.add-source::v-deep .v-switch-button {
    background-color: $very-dark-desaturated-blue !important;
}
.add-source::v-deep .v-switch-label {
    color: $mostly-black-blue !important;
}
.add-source::v-deep .vue-js-switch {
    font-size: inherit;
    font-weight: inherit;
}

.panel-sources-list {
    overflow-x: auto;
}
.panel-sources-list::v-deep table {
    background-color: inherit;
    color: inherit;
}
.panel-sources-list::v-deep .table thead th {
    border: none;
}
.panel-sources-list::v-deep .table th,
.panel-sources-list::v-deep .table td {
    border-top: solid 1px $mostly-white-gray;
}

@media (max-width: 600px) {
    .panel-sources-list::v-deep td a {
        max-width: 10vw;
    }
}
.panel-sources-list::v-deep td a {
    display: inline-block;
    max-width: 30vw;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
@media (min-width: 1200px) {
    .panel-sources-list::v-deep td a {
        max-width: 10vw;
    }
}

</style>