<template>
    <section class="sd-panel-access-links">
        <template v-if="iOwnThisPanel">
            <div class="sd-generate-panel-access-links">
                <b-button variant="secondary" class="py-2"><font-awesome-icon icon="users" /> Share with Group</b-button>
            </div>
            <template v-if="expandedPanel.groups && expandedPanel.groups.length > 0">
                <p>This figure is shared with:</p>
                <ul>
                    <li v-for="group in expandedPanel.groups" :key="group.id">
                        {{ group.name }}
                    </li>
                </ul>
            </template>
            <template v-if="!hasLinks">
                <div class="sd-generate-panel-access-links">
                    <b-button variant="secondary" class="py-2" @click="generateLink"><font-awesome-icon icon="link" /> Generate Public Link</b-button>
                </div>
                <p>This link will allow public access to this SmartFigure and create a QR code for this link.</p>
            </template>
        </template>
        <b-alert show variant="primary" v-if="!hasLinks && !iOwnThisPanel">
            The panel owner has not created a public link.
        </b-alert>
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
                    <a v-if="!loading" :href="'/panels/' + expandedPanel.id + '/token/qr'" download="qr_code.jpg">
                        <img class="sd-qr-code" :src="'/panels/' + expandedPanel.id + '/token/qr'" alt="QR code leading to the public panel link">
                    </a>
                </div>
            </b-col>
        </b-row>
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
.sd-generate-panel-access-links {
    margin-bottom: 0.5rem;
}

.sd-qr-code {
    height: 150px;
    width: auto;
}

.sd-qr-code-container {
    text-align: center;
    padding: 0.5rem 0 1.5rem;
}
</style>