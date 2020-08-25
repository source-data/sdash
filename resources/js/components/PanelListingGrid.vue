<template>
    <article class="sd-panel-grid-container">
        <ul class="sd-panel-grid-container--inner list-unstyled py-4">
            <panel-listing-grid-item v-for="panel in loadedPanels" :key="panel.id" :panel-id="panel.id"></panel-listing-grid-item>
        </ul>

    <infinite-loading @infinite="addPanels">
        <div slot="spinner">
            <b-container>
                <b-row>
                    <b-col class="text-center">
                        <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
                    </b-col>
                </b-row>
            </b-container>
        </div>
        <div slot="no-more" class="font-weight-bold text-muted text-center">All results loaded</div>
        <div slot="no-results" class="font-weight-bold text-muted text-center">No more results</div>
    </infinite-loading>
    </article>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import InfiniteLoading from 'vue-infinite-loading'
import PanelListingGridItem from './PanelListingGridItem'

export default {

    name: 'PanelListingGrid',
    components: { PanelListingGridItem, InfiniteLoading },
    props: ['list_root'],
    data(){

        return {

        }

    }, /* end of data */

    methods:{ //run as event handlers, for example
        ...mapActions([
            'loadMorePanels',
            'loadMoreGroupPanels',
        ]),
        addPanels($state){
            if(this.hasLoadedAllResults){
                $state.complete()
            } else {

                if(this.list_root == 'group') {
                    this.loadMoreGroupPanels().then(()=>{
                        $state.loaded()
                    })
                } else {
                    this.loadMorePanels().then(()=>{
                        $state.loaded()
                    })
                }
            }

        }
    },

    computed:{
        ...mapGetters([
            'loadedPanels',
            'hasLoadedAllResults',
            'isLoadingPanels',
            'isInfiniteScrollPaused',
        ]),
        imageThumbnailUrl(id){
            return "/panels/" + id + "/image/thumbnail"
        }

    }

}
</script>

<style scoped lang="scss">
    .sd-panel-grid-container {
        width:100%;
        padding: 10px 40px;
        background-color: #d6dfee;
    }

    .sd-panel-grid-container--inner {
        display:flex;
        position: relative;
        flex-wrap:wrap;
		justify-content: stretch;
        width:100%;
        margin:0 auto;
    }

    .sd-panel-grid--info-bar {
        border: solid 1px red;
    }
</style>