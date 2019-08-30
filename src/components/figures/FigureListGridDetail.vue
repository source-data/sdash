<template>
    <div>
        <b-tabs card content-class="figure-list-grid-card">
            <b-tab title="Caption" active>
                <b-card-text v-if="!editingCaption">
                    <div>{{panel.caption}}</div>
                    <button class="panel-caption--edit" @click="editCaption"><v-icon name="edit" class="edit-caption--edit-icon"></v-icon>Edit</button>
                </b-card-text>
                <div v-if="editingCaption" class="editing-panel-caption">
                    <textarea :value="panel.caption" name="panel-caption" id="panel-caption" rows="10" @input="updateTempCaptionText" placeholder="Create a caption for this panel"></textarea>
                    <button class="btn btn-success" @click.prevent="updateCaption"><v-icon name="save" class="list-grid--save-icon"></v-icon> Save</button>
                    <button class="btn btn-warning" @click.prevent="dontEditCaption"><v-icon name="ban" class="list-grid--save-icon"></v-icon> Cancel</button>
                </div>
            </b-tab>
            <b-tab title="Metadata"><b-card-text>
                <table class="list-grid--metadata-table">
                    <tr v-if="panel.figure.projects.length > 0"><td>Part of projects</td><td><a :href="createProjectUrl(proj)" class="panel-projects" v-for="(proj, index) in panel.figure.projects" :key="index">{{getProjectNameById(proj)}}</a></td></tr>
                    <tr><td>Creation date</td><td>{{ panel.figure.create_date }}</td></tr>
                    <tr v-if="interventions.length > 0"><td>Interventions:</td><td><span class="sd-tag intervention-tag" v-for="intervention in interventions" :key="intervention.panel_id + '_' + intervention.tag_id">{{intervention.kwd}}</span></td></tr>
                    <tr v-if="assays.length > 0"><td>Assays:</td><td><span class="sd-tag assay-tag" v-for="assay in assays" :key="assay.panel_id + '_' + assay.tag_id">{{assay.kwd}}</span></td></tr>
                    <tr v-if="components.length > 0"><td>Components:</td><td><span class="sd-tag component-tag" v-for="component in components" :key="component.panel_id + '_' + component.tag_id">{{component.kwd}}</span></td></tr>
                    <tr><td>Resource type</td><td>{{ panel.graphic.mimetype }}</td></tr>
                    <tr><td>File format</td><td>{{ panel.graphic["mime-subtype"] }}</td></tr>
                </table>
            </b-card-text></b-tab>
            <b-tab :title="commentCount"><b-card-text><slim-comments :id="panel.figure_id+''" scope="figures" /></b-card-text></b-tab>
            <b-tab title="File History"><b-card-text>
            <ul>
                <li>({{ panel.figure.create_date }}) - Version 1 created by {{ panel.figure.owner }}</li>
            </ul></b-card-text></b-tab>
            <b-tab title="Data Attachments">
                <b-card-text>
                </b-card-text>
                    <PanelAttachments :panel="panel"/>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>

import SlimComments from '@/components/notifications/SlimComments'
import PanelAttachments from '@/components/figures/PanelAttachments'
import { mapGetters } from 'vuex'
import {Bus} from '@/bus'


export default {

    name: 'FigureListGridDetail',
    components: {
        SlimComments,
        PanelAttachments,
    },
    data () {
        return {
            editingCaption: false,
            newCaption: "",
        }
    },
    props: ["panel"],

    computed: {

        commentCount () {
            return "Comments (" + (this.panel.figure.notifications.reduce((n, note) => n + (note.event_type === "comment") ,0)).toString() + ")"
        },
        ...mapGetters({
			projects: 'projects',
		}),
        assays() {
            return this.panel["kwd-group"].filter(item => item.label == "assayed")
        },
        interventions() {
            return this.panel["kwd-group"].filter(item => item.label == "intervention")
        },
        components() {
            return this.panel["kwd-group"].filter(item => item.label == "component")
        },

    },

    methods:{ //run as event handlers, for example

        editCaption () {
            debugger
            this.editingCaption = true;
            this.newCaption = this.panel.caption;
        },
        updateTempCaptionText(e){

            this.newCaption = e.target.value;
        },
        dontEditCaption () {
            this.editingCaption = false;
            this.newCaption = this.panel.caption;
        },
        updateCaption () {
            let vm = this;

            let updatePanelData = {
                panel_id: vm.panel.panel_id,
                caption: vm.newCaption,
                label: vm.panel.label,
            }

            vm.$store.dispatch("updatePanel", updatePanelData).then(() => {
                Bus.$emit("refreshData");
                vm.panel.caption = vm.newCaption;
                vm.$snotify.success("Panel caption updated");
                vm.editingCaption = false;
            });



        },
        getProjectNameById (project_id){
			return _.map(_.filter(this.projects, p => +p.project_id === +project_id), p => p.name)[0]
		},
        createProjectUrl(project_id) {
            return "/projects/" + project_id + "?view=figures";
        }

    }

}
</script>

<style>
    .figure-list-grid-card {
        border: solid 1px #fff;
        border-radius: 6px 6px 0 0;
        max-height: 400px;
        margin-top: -1px;
    }

    .figure-list-grid-card .tab-pane.card-body {
        max-height:400px;
    }

    .figure-list-grid-card .card-text {
        overflow-y:scroll;
        max-height: 360px;
        padding-right:1rem;
    }

    .list-grid-item .nav-tabs a.nav-link {
        color: #ddd;
        border: solid 1px #fff;
    }

    .list-grid-item .nav-tabs a.nav-link.active {
        color: #333;
    }

    #panel-caption {
        width:100%;
    }

    .list-grid--save-icon {
        position:relative;
        top: -2px;
        margin-right: 4px;
    }

    .panel-caption--edit {
        border:none;
        background-color:transparent;
        font-size: 0.85em;
        color: #01c59f;
        margin-top:6px;
    }

    .edit-caption--edit-icon {
        margin-right:4px;
        position: relative;
        top: -2px;
    }

    .editing-panel-caption .btn {
        margin-right:6px;
    }

    .list-grid--metadata-table td:first-child {
        padding-right:6px;
        color: #cb84ec;

    }

    .sd-tag {
        color: #fff;
        padding: 3px 6px;
        border-radius: 6px;
        background-color: #6b6b6b;
        margin-right: 3px;
    }

    .intervention-tag {
        background-color: #da4848;

    }

    .assay-tag {
        background-color: #6485ff;

    }

</style>