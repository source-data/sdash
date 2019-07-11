<template>
    <div>
        <b-tabs card content-class="figure-list-grid-card">
            <b-tab title="Caption" active>
                <b-card-text v-if="panel.caption && !editingCaption">
                    <div>{{panel.caption}}</div>
                </b-card-text>
                <div v-if="!panel.caption || editingCaption" class="editing-panel-caption">
                <textarea :value="panel.caption" name="panel-caption" id="panel-caption" rows="8" placeholder="Create a caption for this panel"></textarea>
                </div>
            </b-tab>
            <b-tab title="Metadata"><b-card-text></b-card-text></b-tab>
            <b-tab :title="commentCount"><b-card-text><slim-comments :id="panel.figure_id+''" scope="figures" /></b-card-text></b-tab>
            <b-tab title="File History"><b-card-text>No content at the moment.</b-card-text></b-tab>
        </b-tabs>
    </div>
</template>
 
<script>

import SlimComments from '@/components/notifications/SlimComments'

export default {
 
    name: 'FigureListGridDetail',
    components: {
        SlimComments,

    },
    data () {
        return {
            editingCaption: false,
        }
    },
    props: ["panel"],
 
    computed: {
 

        commentCount () {

            return "Comments (" + (this.panel.figure.notifications.reduce((n, note) => n + (note.event_type === "comment") ,0)).toString() + ")"

        }
         
    }, 
 
    methods:{ //run as event handlers, for example
 
        methodName(){
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
</style>