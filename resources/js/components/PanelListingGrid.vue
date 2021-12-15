<template>
    <div>
        <ul class="panel-grid list-unstyled">
            <panel-listing-grid-item
                v-for="panel in loadedPanels"
                :key="panel.id"
                :panel-id="panel.id"
            ></panel-listing-grid-item>
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
    </div>
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
        ]),
    }

}
</script>

<style lang="scss" scoped>
.panel-grid {
    position: relative;
    width: 100%;

    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
    column-gap: 16px;
    row-gap: 1.5rem;
}
</style>