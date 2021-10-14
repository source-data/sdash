<template>
    <b-dropdown right variant="info" class="list-grid--download-controls" >
        <template v-slot:button-content><font-awesome-icon class="sd-panel-download-icon" icon="download" title="Download panel" /> Download</template>
        <b-dropdown-item @click.stop="download('pdf')">.pdf file</b-dropdown-item>
        <b-dropdown-item @click.stop="download('powerpoint')">.pptx (PowerPoint) file</b-dropdown-item>
        <b-dropdown-item @click.stop="download('dar')">SmartFigure Editor file</b-dropdown-item>
        <b-dropdown-item @click.stop="download('original')">Original image</b-dropdown-item>
        <b-dropdown-item @click.stop="download('zip')">Zip with all files</b-dropdown-item>
    </b-dropdown>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'

export default {

    name: 'DownloadBar',

    computed: {

        ...mapGetters(['expandedPanel', 'editingCaption']),

    },

    data(){

        return {

        }

    }, /* end of data */

    methods:{ //run as event handlers, for example

        download( downloadType = null ){

            let suffix = ''

            switch(downloadType) {
                case 'pdf':
                    suffix = 'pdf'
                    break
                case 'powerpoint':
                    suffix = 'powerpoint'
                    break
                case 'zip':
                    suffix = 'zip'
                    break
                case 'dar':
                    suffix = 'dar'
                    break
                default:
                    suffix = ''
                    break
            }

            window.location.href = '/panels/' + this.expandedPanel.id + '/' + suffix

        }

    }

}
</script>

<style lang="scss">
        .sd-panel-download-icon {
            margin-right: 3px;
        }
</style>