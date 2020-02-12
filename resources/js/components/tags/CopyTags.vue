<template>
    <div class="sd-copy-tags-wrapper">
        <b-button-group size="sm">
            <b-button @click="copyTags" variant="light"><font-awesome-icon icon="copy" title="Copy tags" /> Copy tags</b-button>
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
        ...mapGetters(['tagCache']),
        disablePaste(){
            return this.tagCache.length < 1
        }
    },
    methods:{
        copyTags(){
            this.$store.commit("copyTagsToCache")
            this.$snotify.info("Tags copied", "OK!")
        },
        pasteTags(){
            this.$store.commit("pasteTagsFromCache")
            this.$snotify.info("Tags pasted", "OK!")
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