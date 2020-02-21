<template>
    <section class="sd-panel-access-links">
        <p>Links generated here will allow public access to the details of this single panel by following the generated link.</p>
        <b-row v-if="loading">
            <b-col class="text-center">
                <b-spinner variant="primary" label="Spinning" class="m-2" style="width: 2rem; height: 2rem;"></b-spinner>
            </b-col>
        </b-row>
        <b-row v-if="hasLinks">
            <b-col>
                <b-input-group class="my-3">
                    <b-form-input :value="linkUrl" id="sd-public-link" disabled></b-form-input>
                    <b-input-group-append>
                        <b-button variant="light" @click.prevent="copyLink"><font-awesome-icon icon="copy" /> Copy</b-button>
                    </b-input-group-append>
                </b-input-group>
                <div class="sd-qr-code-container">
                <img v-if="!loading" class="sd-qr-code" :src="'/panels/' + expandedPanel.id + '/token/qr'" alt="QR code leading to the public panel link">
                </div>
            </b-col>
        </b-row>
        <b-alert show variant="primary" v-if="!hasLinks && !iOwnThisPanel">
            The panel owner has not created a public link.
        </b-alert>
        <div class="sd-generate-panel-access-links text-right" v-if="!hasLinks && iOwnThisPanel">
            <b-button variant="success" class="py-2" size="sm" @click="generateLink"><font-awesome-icon icon="link" /> Generate Link</b-button>
        </div>
        <div class="sd-modify-panel-access-links text-right" v-if="hasLinks && iOwnThisPanel">
            <b-button variant="success" class="py-2" size="sm" @click="generateLink"><font-awesome-icon icon="link" /> Refresh Link</b-button>
            <b-button variant="danger" class="py-2" size="sm" @click="revokeLink"><font-awesome-icon icon="link" /> Revoke Link</b-button>
        </div>
    </section>
</template>

<script>

import { mapGetters } from 'vuex'

export default {

    name: 'PanelAccessLinks',

    data(){

        return {
            loading: false,
            link_base: process.env.MIX_API_PANEL_URL,
        }

    }, /* end of data */

    computed: {
        ...mapGetters(['expandedPanel', 'iOwnThisPanel']),
        hasLinks(){
            return this.expandedPanel.access_token.hasOwnProperty('token')
        },
        linkUrl(){
            return this.hasLinks ? this.link_base + "/" + this.expandedPanel.id + "?token=" + this.expandedPanel.access_token.token : false
        }
    },

    methods:{ //run as event handlers, for example

        generateLink () {
            this.loading = true
            this.$store.dispatch("generatePublicLink")
            .then(response => {
                this.loading = false
                this.$snotify.success("New public link generated.", "Success!")
            }).catch(error => {
                this.loading = false
                this.$snotify.error(error.data.MESSAGE, "Action Failed!")
            })
        },
        revokeLink () {
            this.loading = true
            this.$store.dispatch("revokePublicLink")
            .then(response => {
                this.loading = false
                this.$snotify.success("Public link removed.", "Success!")
            }).catch(error => {
                this.loading = false
                this.$snotify.error(error.data.MESSAGE, "Action Failed!")
            })
        },
        copyLink () {
            let target = document.getElementById("sd-public-link")
            target.disabled = false
            target.select()
            document.execCommand("copy")
            target.disabled="disabled"
            this.$snotify.info(this.linkUrl, "Copied Link")
        }

    }

}
</script>

<style lang="scss">
.sd-qr-code {
    height: 150px;
    width: auto;
}

.sd-qr-code-container {
    text-align: center;
    padding: 0.5rem 0 1.5rem;
}

</style>