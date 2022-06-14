<template>
    <div>
        <div v-if="iCanEditThisPanel" class="add-source">
            <div class="form-group attach-link">
                <b-form-input
                    v-model="url"
                    size="sm"
                    type="url"
                    placeholder="Enter a URL"
                ></b-form-input>

                <b-button variant="primary" @click.prevent="sendUrl" :disabled="disableUrlSubmit">
                    <span>Link source file</span>
                </b-button>
            </div>

            <div class="form-group upload-file">
                <b-form-file
                    no-drop
                    single
                    size="sm"
                    v-model="file"
                    :disabled="pendingUpload"
                    @input="sendFile"
                    placeholder="Upload source file"
                ></b-form-file>
            </div>
        </div>

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
                <template v-if="showBatchActions" #head(action)>
                    <b-form-checkbox :checked="allItemsSelected" @change="toggleSelectAll"></b-form-checkbox>
                </template>

                <template v-slot:cell(action)="data">
                    <b-form-checkbox v-model="selectedItems" :value="data.item.id"></b-form-checkbox>
                </template>

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

                    <template v-if="!iCanEditThisPanel">
                        <span v-if="data.item.file_category_id">
                            {{ getFileCategoryName(data.item.file_category_id) }}
                        </span>

                        <span v-else>&mdash;</span>
                    </template>

                </template>

                <template v-slot:cell(description)="data">
                    <template v-if="!iCanEditThisPanel">
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                        <span v-else>&mdash;</span>
                    </template>

                    <template v-if="iCanEditThisPanel">
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
                        v-if="data.item.url"
                        class="source-url"
                        :href="data.item.url"
                        v-b-tooltip.hover.left
                        :title="data.item.url"
                        target="_blank"
                    >
                        {{data.item.url}}
                    </a>

                    <a
                        v-if="data.item.original_filename"
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

                <template v-slot:custom-foot>
                    <b-tr v-if="pendingUpload">
                        <b-td class="text-center">
                            <b-button variant="link">
                                <b-spinner small label="Uploading"></b-spinner>
                            </b-button>
                        </b-td>
                        <b-td colspan="4" v-if="file">Uploading <span class="font-italic">{{ file.name }}</span></b-td>
                    </b-tr>
                </template>
            </b-table>

            <b-button
                v-if="showBatchActions"
                variant="danger"
                class="text-light"
                id="delete-selected-sources"
                :disabled="selectedItems.length === 0"
            >
                <font-awesome-icon icon="trash-alt" /> Delete selected
            </b-button>

            <b-popover
                v-if="showBatchActions"
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
            fileToDelete: {},
            fileToUpdate: {},
            baseFields: [
                {key:'description', label:'Description', sortable: true, sortByFormatted:true, formatter:"distillDescription"},
                {key:'category', label:'Category', sortable: true, sortByFormatted:true, formatter:"distillCategoryName"},
                {key:'link', label: 'Filename / URL', sortable: true, sortByFormatted:true, formatter:"distillResourceLink"},
                {key:'size', label: 'Size', sortable: true, sortByFormatted:true, formatter:"distillFileSize"},
            ],
            batchActionField: {key:'action', label:'', sortable: false},
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
            'getFileCategoryById',
            'pendingUpload',
        ]),
        iCanEditThisPanel(){
            return (this.iOwnThisPanel || this.iHaveAuthorPrivileges)
        },
        disableUrlSubmit() {
            return this.pendingUpload || this.url === null
        },
        disableFileSubmit() {
            return this.pendingUpload || this.file === null
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
        showBatchActions() {
            return this.iCanEditThisPanel && this.getFiles.length > 0
        },
        fields() {
            if (this.showBatchActions) {
                // returns a new list and leaves baseFields unmodified
                return [this.batchActionField, ...this.baseFields]
            }
            return this.baseFields
        }
    },
    methods:{ //run as event handlers, for example
        sendFile(){
            if (!this.file) {
                return;
            }
            this.$store.commit('setPendingUpload', true);
            this.$store.dispatch("storeFile", {file:this.file, file_category_id:this.categoryId}).then(response => {
                this.$snotify.success(response.data.MESSAGE, "File Uploaded")
                this.$store.commit('setPendingUpload', false);
                this.file = null
            }).catch(error => {
                this.$snotify.error(error.data.errors.file[0], "Upload failed")
                this.$store.commit('setPendingUpload', false);
            })
        },
        sendUrl(){
            this.$store.dispatch("storeUrl", {url:this.url, file_category_id:this.categoryId}).then(response => {
                this.$snotify.success(response.data.MESSAGE, "URL Stored")
                this.url = null
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
@import 'resources/sass/_text.scss';

.add-source {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 0.5rem;

    .form-group {
        // let the form fields grow to take the available space.
        flex-grow: 1;
        flex-shrink: 0;

        margin-bottom: 0;

        // "append" the button directly to the input field.
        display: flex;
        ::v-deep input.form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        button {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            padding: 0 0.5rem;
            white-space: nowrap;
        }

        // trying to make sure the font-size set by the container sticks to all child components.
        button,
        ::v-deep input,
        ::v-deep .b-custom-control-sm.custom-file,
        ::v-deep .b-custom-control-sm .custom-file-input,
        ::v-deep .b-custom-control-sm .custom-file-label,
        ::v-deep .input-group-sm.custom-file,
        ::v-deep .input-group-sm .custom-file-input,
        ::v-deep .input-group-sm .custom-file-label {
            font-size: inherit;
        }
    }

    // The upload-file input needs less space than the attach-link part.
    .form-group.upload-file {
        flex-basis: 200px;
    }
    .form-group.attach-link {
        flex-basis: 250px;
    }
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