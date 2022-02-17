<template>
    <vue-full-screen-file-drop
        @drop='uploadPanel'
        formFieldName="file"
        v-if="panelDropEnabled">
        <div class="vue-full-screen-file-drop__content text-primary text-xl font-weight-bold">
            <p>
                Please drop a
                <br>
                JPG, PNG, GIF, TIFF or PDF file
                <br>
                or browse for files
            </p>

            <button class="btn btn-primary text-md font-weight-bold">
                + Upload SmartFigure
            </button>
        </div>

    </vue-full-screen-file-drop>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import VueFullScreenFileDrop from 'vue-full-screen-file-drop';

export default {
    name: 'PanelDropZone',
    components: {
        VueFullScreenFileDrop,
    },
    data() {
        return {};
    },
    computed: {
        ...mapGetters([
            "currentGroup",
            "isLoggedIn",
            "mayAddPanelToGroup",
            "showAuthorSidebar",
        ]),
        panelDropEnabled() {
            // no file upload if not logged in.
            if (!this.isLoggedIn) {
                return false;
            }
            // Disallow file dropping if the sidebar to edit a panel's authors is open.
            if (this.showAuthorSidebar) {
                return false;
            }
            // Disallow file dropping if we're on a group's page and not allowed to add panels to it.
            if (this.currentGroup && ! this.mayAddPanelToGroup) {
                return false;
            }
            return true;
        },
    },
    methods: {
        ...mapActions([
            'uploadNewPanel',
            'addSelectedPanelsToGroup',
        ]),
        uploadPanel(formData, files){
            this.uploadNewPanel(formData)
            .then(response => {
                this.$snotify.success("New SmartFigure created", "Uploaded")
                if(this.currentGroup) {
                    this.$store.commit("clearSelectedPanels")
                    this.$store.commit("addPanelToSelections", response.data.DATA.id)
                    this.$store.dispatch("manageGroupPanels", {
                        action: "add",
                        target: "selected",
                        groupId: this.currentGroup.id
                    }).then(response => {
                        this.$snotify.success("SmartFigure added to this group", "Group updated")
                    }).catch(error => {
                        console.log(error)
                        this.$snotify.error("Cannot add SmartFigure to this group", "Update failed")
                    })
                }
            })
            .catch(error => {
                this.$snotify.error(error.data.errors.file[0], "Upload failed")
            })
        },
    },
}
</script>

<style lang="scss">
@import 'resources/sass/_colors.scss';

.vue-full-screen-file-drop {
    background-color: $mostly-black-blue !important;
}

.vue-full-screen-file-drop__content {
    flex-direction: column;
    text-align: center;
}

.vue-full-screen-file-drop__content::before {
    border: 3px dashed $vivid-orange !important;
    border-radius: 2rem !important;
}
</style>