<template>
    <div class="sd-copy-tags-wrapper">
        <b-button-group size="sm">
            <b-button @click="copyTags" variant="light" :disabled="!hasTags"><font-awesome-icon icon="copy" title="Copy tags" /> Copy tags</b-button>
            <b-button @click="pasteTags" variant="light" :disabled="disablePaste"><font-awesome-icon icon="paste" title="Paste tags"/> Paste tags</b-button>
        </b-button-group>
    </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'

export default {

    name: 'CopyTags',
    computed: {
        ...mapGetters(['iOwnThisPanel', 'hasTags','tagCache']),
        disablePaste(){
            return (this.tagCache.length < 1 || !this.iOwnThisPanel)
        }
    },
    methods:{
        copyTags(){
            if(this.hasTags){
                this.$store.commit("copyTagsToCache")
                this.$snotify.info("Tags copied", "OK!")
            } else {
                this.$snotify.info("Nothing to copy", "No Action")
            }
        },
        pasteTags(){
            if(this.iOwnThisPanel){
                this.$store.commit("pasteTagsFromCache")
                this.$snotify.info("Tags pasted", "OK!")
            } else {
                this.$snotify.info("You can't modify another user's panel.", "No Action")
            }
        }
    }

}
</script>

<style lang="scss">
    .sd-copy-tags-wrapper {
        text-align:right;
        max-width: 450px;
    }
</style>