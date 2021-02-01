<template>
    <div class="sd-file-uploads-container">
        <!-- file list -->
        <div class="sd-file-uploads--list-container">
            <b-table
                dark
                class="sd-file-uploads--list-table"
                striped
                :items="getFiles"
                :fields="fields"
                primary-key="id"
                ref="fileUploadsTable"
             ><!--end of table definition-->
                <template v-slot:cell(category)="data">
                    <template>
                        <span v-if="data.item.file_category_id">{{ getFileCategoryName(data.item.file_category_id) }}</span>
                        <span v-if="!data.item.file_category_id">&mdash;</span>
                    </template>
                </template>
                <template v-slot:cell(description)="data">
                    <template>
                        <span v-if="data.item.description">{{ data.item.description }}</span>
                    </template>
                </template>
                <template v-slot:cell(link)="data">
                    <a class="text-light" :href="data.item.url" v-b-tooltip.hover.left :title="data.item.url" target="_blank">{{data.item.url}}</a>
                    <a class="text-light" v-b-tooltip.hover.left :title="data.item.original_filename" :href="'/files/' + data.item.id">{{data.item.original_filename}}</a>
                </template>
                <template v-slot:cell(size)="data">
                    <span v-if="data.item.file_size">{{formatBytes(data.item.file_size)}}</span>
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
            fields:[
                {key:'category', label:'Category', sortable: true, sortByFormatted:true, formatter:"distillCategoryName"},
                {key:'description', label:'Description', sortable: true, sortByFormatted:true, formatter:"distillDescription"},
                {key:'link', label: 'Filename / URL', sortable: true, sortByFormatted:true, formatter:"distillResourceLink"},
                {key:'size', label: 'Size', sortable: true, sortByFormatted:true, formatter:"distillFileSize"},
            ],
        }
    }, /* end of data */
    mixins: [formatter],
    computed: {
        ...mapGetters([
            'getFiles',
            'getFileCategoryById',
        ]),
    },
    methods:{ //run as event handlers, for example
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
        getFileCategoryName(fileCategoryId){
            let category = this.getFileCategoryById(fileCategoryId);
            return (category ? category.name : "")
        },

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