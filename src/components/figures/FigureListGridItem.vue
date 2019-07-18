<template>
    <section class="list-grid-item" :class="{ 'list-grid__expanded': isExpanded }">
        <div class="list-grid-image-container" @click.prevent="toggleExpanded" ref="imageContainer" tabindex="0">
            <img :src="panel.graphic.imgsrc" :alt="panel.label" class="list-grid-image">
            <div class="css_arrow"></div>
            <div class="list-grid-caption">
                <div class="list-grid-caption--owner">
                    <user-icon :name="panel.figure.owner"></user-icon>
                    <span class="list-grid-caption--owner-name">{{panel.figure.owner}}</span>
                </div>
                <div class="list-grid-caption--label">{{ panel.label }}</div>
            </div>
            <span class="list-grid-file-type" v-if="panel.subtype">
                {{ panel.subtype }}
            </span>
        </div>
        <div class="list-grid-extra" ref="listGridExtra">
            <button type="button" aria-label="Close" class="close list-grid-extra--close" @click.prevent="toggleExpanded"><span aria-hidden="true">&times;</span></button>
            <div class="list-grid-extra--wrapper">
                <div class="list-grid-extra--image-container">
                    <div class="list-grid-extra--image-wrapper">
                        <img :src="panel.graphic.imgsrc" :alt="panel.label" class="list-grid-extra--image" ref="listGridImage">
                    </div>
                </div>
                <div class="list-grid-extra--info-container">
                    <h2 v-if="!editingLabel">{{panel.label}} <button class="edit-label-button" @click.stop="isEditingLabel"><v-icon name="edit" class="edit-label--edit-icon"></v-icon> Edit</button></h2>
                    <div v-if="editingLabel" class="edit-label--input-container">
                        <input type="text" class="form-control" :value="panel.label" @input="updateTempLabelText">
                        <button class="btn btn-success" @click.prevent="updateLabel"><v-icon name="save" class="list-grid--save-icon"></v-icon> Save</button>
                        <button class="btn btn-warning" @click.prevent="dontEditLabel"><v-icon name="ban" class="list-grid--save-icon"></v-icon> Cancel</button>
                    </div>
                    <h4>Part of Figure: {{ panel.figure.dar.caption.title }}</h4>
                    <figure-list-grid-detail :panel="panel"></figure-list-grid-detail>
                    <footer class="list-grid-actions">
                        <b-button variant="light">Open SmartFigure</b-button>
                        <b-dropdown right dropup variant="light" >
                            <span slot="text"><v-icon name="file-download" class="list-grid--download-icon"></v-icon> Download</span>
                            <b-dropdown-item @click.stop="downloadDar(panel.figure.id)">.dar file</b-dropdown-item>
                            <b-dropdown-item @click.stop="downloadPdf(panel.figure.id)">.pdf file</b-dropdown-item>
                            <b-dropdown-item @click.stop="downloadPowerpoint(panel.figure.id)">.pptx (PowerPoint) file</b-dropdown-item>
                        </b-dropdown>
                        <b-button variant="danger" @click.stop="deleteFigure"><v-icon name="trash-alt" class="list-grid--delete-icon"></v-icon>Delete File</b-button>
                        <p class="confirmDeletionMessage" v-if="confirmDeletion"> Are you sure? <button @click.stop="reallyDeleteFigure">YES</button> / <button @click.stop="dontReallyDeleteFigure">NO</button></p>
                    </footer>
                </div>

            </div>
        </div>
    </section>
</template>

<script>

import {Bus} from '@/bus';
import userIcon from '@/components/user/userIcon'
import FigureListGridDetail from '@/components/figures/FigureListGridDetail'
import { mapGetters } from 'vuex'

export default {

    name: 'FigureListGridItem',
    components: { userIcon, FigureListGridDetail },
    props: ['panel'],

    data(){

        return {
            isExpanded: false,
            editingLabel: false,
            newLabel: null,
            confirmDeletion: false,

        }

    },
    computed: {
		...mapGetters({
			user: 'currentUser',
		}),
    },

    methods:{

        toggleExpanded(){

            let vm = this;

            vm.isExpanded = !vm.isExpanded;
            if(vm.isExpanded){

                vm.$emit('expanded', {id:vm.panel.panel_id});
                vm.$scrollTo(vm.$refs.listGridExtra, 300, {offset:-120} );
                setTimeout(function(){ vm.$refs.listGridImage.style.display="inline-block" }, 500);
            } else {
                vm.$refs.listGridImage.style.display="none";
            }

        },
        downloadDar (figure_id) {
			this.$store.dispatch('downloadDar',{figure_id:figure_id,jwt:this.user.jwt})
		},
        downloadPdf (figure_id) {
			this.$store.dispatch('downloadPdf',{figure_id:figure_id,jwt:this.user.jwt})
		},
        downloadPowerpoint (figure_id) {
			this.$store.dispatch('downloadPowerpoint',{figure_id:figure_id,jwt:this.user.jwt})
		},
        deleteFigure() {
            this.confirmDeletion = true
        },
        reallyDeleteFigure(figure_id) {
            this.$store.dispatch('deleteFigure',{ figure_id: this.panel.figure.id }).then(() => {
                this.$snotify.success("Figure permanently deleted ");
            })
        },
        dontReallyDeleteFigure() {
            this.confirmDeletion = false;
        },
        isEditingLabel() {
            this.editingLabel = true;
        },
        dontEditLabel() {
            this.editingLabel = false;
            this.newLabel = null;
        },
        updateTempLabelText(e){

            this.newLabel = e.target.value;
        },
        updateLabel() {
            let vm = this;

            let updateLabelData = {
                panel_id: vm.panel.panel_id,
                caption: vm.panel.caption,
                label: vm.newLabel,
            }

            vm.$store.dispatch("updatePanel", updateLabelData).then(() => {
                vm.$snotify.success("Panel label updated");
                vm.panel.label = vm.newLabel;
                vm.editingLabel = false;
            });
        },

    },
    mounted: function () {
        let vm = this;
        Bus.$on("close-panels", (obj) => { if(vm.isExpanded && obj.id !== vm.panel.panel_id) vm.isExpanded = false; });
    }


}
</script>

<style scoped>

    *, *:before, *:after {

        box-sizing: border-box;

    }

    .list-grid-item {
        box-sizing: border-box;
        height: 280px;
        min-width: 240px;
        margin:8px;
        cursor:pointer;
        transition: all 0.3s ease-in;
    }

    .list-grid-item.list-grid__expanded {

        margin-bottom:710px;

    }

    .list-grid-caption {
        position: absolute;
        top: 0;
        width: 100%;
        padding: 11px;
        background-color: rgb(51, 51, 51);
        color: #fff;
        opacity: 0.4;
        transition: opacity 150ms linear;
    }

    .list-grid-caption--owner {
        display: flex;
    }

    .list-grid-caption--owner .userIcon {
        margin-right: 1em;
    }

    .list-grid-caption--owner-name {
        vertical-align: middle;
        font-weight: bold;
        line-height: 35px;
    }

    .list-grid-caption--label {
        padding-left: 54px;
        margin-top: -12px;
    }

    .list-grid-item:hover .list-grid-caption,
    .list-grid-item:focus .list-grid-caption,
    .list-grid-item:active .list-grid-caption {
        opacity: 0.9;
    }

    .list-grid-image-container {
        height:100%;
        background-color:#3c3c3c;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .list-grid-image {
        display:block;
        max-height: 100%;
        width: auto;


    }

    .list-grid-file-type {
        position: absolute;
        bottom: 6px;
        right: 6px;
        padding: 3px 12px;
        background-color: #9e33a5;
        color: #fff;
        border-radius: 16px;
        line-height: 18px;
        opacity: 0.4;
        transition: all 200ms linear;
    }

    .list-grid-item:hover .list-grid-file-type,
    .list-grid-item:focus .list-grid-file-type,
    .list-grid-item:active .list-grid-file-type {
        opacity:0.8;
    }

    .list-grid-extra {
        cursor: default;
        position: absolute;
        left:0;
        width: 100%;
        background-color: #222;
        color: #ddd;
        font-size: 16px;
        margin-top: 6px;
        max-height: 0;
        height: 0;
        overflow: hidden;
        transition: all 0.3s ease-in;

    }

    .list-grid__expanded .list-grid-extra {
        max-height: 700px;
        height:700px;
    }

    .list-grid-extra--close {
        position: absolute;
        font-size: 2em;
        top:4px;
        right:8px;
    }

    .list-grid-extra--wrapper {
        height: 100%;
        display:flex;
        flex-wrap: nowrap;

    }

    .list-grid-extra--wrapper > div {
        padding: 48px;

    }

    .list-grid-extra--image-container {
        flex: 0 0 50%;
        display: flex;

    }
    .list-grid-extra--info-container {
        flex: 0 0 50%;

    }

    .list-grid-extra--image-wrapper {

        height: 100%;
        width: 100%;
        text-align:center;

    }

    .list-grid-extra--image {
        max-width: 100%;
        max-height: 100%;
        display:none;
    }



    .css_arrow {
        display: none;
    }

    .list-grid__expanded .css_arrow {
        display: block;
        width: 0px;
        height: 0px;
        border: solid 15px transparent;
        border-bottom: solid 20px #222;
        border-top: none;
        position: absolute;
        bottom: -6px;
    }

    .list-grid-actions {
        margin-top: 1em;
    }

    .list-grid-actions .btn {
        margin-right: 4px;
    }

    .list-grid--download-icon {
        color:#444;
        margin-right:6px;
        position:relative;
        top:-2px;
    }

    .list-grid--delete-icon {
        color:#eee;
        margin-right:6px;
        position:relative;
        top:-2px;
    }

    .confirmDeletionMessage {
        display: inline-block;
        padding-left:1em;
    }

    .confirmDeletionMessage button {
        border: none;
        background: transparent;
        color: orange;
        text-decoration: underline;
        cursor: pointer;
    }

    .edit-label-button {
        border: none;
        position: relative;
        top:-1em;
        background-color: transparent;
        font-size: 0.85rem;
        color: #01c59f;
        margin-top: 6px;
    }

    .edit-label--input-container {
        margin: 0 0 6px 0;
    }

    .edit-label--input-container .btn {
        margin: 6px 6px 0 0;
    }

    .edit-label--input-container .form-control {
        font-size: 1.8rem;
    }

</style>