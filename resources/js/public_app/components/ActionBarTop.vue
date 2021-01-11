<template>
    <b-navbar sticky type="light" class="sd-action-bar-top shadow-sm">
        <b-container>
                <b-nav-form>

                    <b-input-group class="mr-sm-2 my-2">
                        <b-form-input class="border border-dark" placeholder="Search" :value="searchString" @input="updateLocalSearchString"></b-form-input>
                        <b-input-group-append>
                            <b-button variant="outline-dark" type="submit" @click.prevent.stop="performTextSearch" >Search</b-button>
                        </b-input-group-append>
                    </b-input-group>
                            <b-button variant="outline-dark" class="my-2"  type="submit" @click.prevent.stop="clearTextSearch" >Clear</b-button>
                </b-nav-form>
        </b-container>
    </b-navbar>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'ActionBarTop',

    data(){

        return {
            localSearchString: "",
            selectedSharingGroupId: null,
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['searchString', 'searchMode'])
    },

    methods: { //run as event handlers, for example

        ...mapActions(['clearSelectedPanels']),
        updateLocalSearchString(value){
            this.localSearchString = value
        },
        performTextSearch(){
            this.$store.commit("setPanelLoadingState", true)
            this.$store.commit("clearLoadedPanels")
            this.$store.commit("setSearchString", this.localSearchString)
            this.$store.dispatch("fetchPanelList")

        },
        clearTextSearch(){
            this.localSearchString = ""
            this.$store.commit("setPanelLoadingState", true)
            this.$store.commit("clearLoadedPanels")
            this.$store.commit("setSearchString", this.localSearchString)
            this.$store.dispatch("fetchPanelList")
        },
    }

}
</script>

<style lang="scss">
.sd-action-bar-top {
    background-color: #b4c9ea;
}

.sd-action-bar-top--delete-button,
.sd-action-bar-top--group-button,
.sd-action-bar-top--figure-button,
{
    color: #383838;
    background: none;
    border:none;
}

.sd-action-bar-top--delete-button:hover,
.sd-action-bar-top--delete-button:focus,
.sd-action-bar-top--delete-button:active{
    background:none;
    color: red;

}

.sd-action-bar-top--group-button:hover,
.sd-action-bar-top--group-button:focus,
.sd-action-bar-top--group-button:active,
.sd-action-bar-top--figure-button:hover,
.sd-action-bar-top--figure-button:focus,
.sd-action-bar-top--figure-button:active,
{
    background:none;
    color: green;

}

.fade-enter-active, .fade-leave-active {
    transition: opacity .25s;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}

.sd-action-bar--file-upload .custom-file-label {
    border: solid 1px #343a40;
    color: #6d767e;
}

.sd-action-bar--file-upload .custom-file-label:after {
    background-color:#b4c9ea;
}

#sd-quit-group {
    margin-left:0.5rem;
}
</style>