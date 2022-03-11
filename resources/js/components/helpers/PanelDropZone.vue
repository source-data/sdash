<template>
    <vue-full-screen-file-drop
        @drop='uploadFile'
        formFieldName="file"
        v-if="panelDropEnabled">
        <div v-if="!expandedPanelId" class="vue-full-screen-file-drop__content text-primary text-xl font-weight-bold">
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
        <div v-if="expandedPanelId" class="vue-full-screen-file-drop__content text-primary text-xl font-weight-bold">
            <p>
                Please drop a file to attach to this SmartFigure
            </p>

            <button class="btn btn-primary text-md font-weight-bold">
                + Upload File
            </button>
        </div>

    </vue-full-screen-file-drop>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
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
            "expandedPanelId",
            "pendingUpload",
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
            // if a pending upload is in progress, don't allow another file drop
            if (this.pendingUpload) {
                return false;
            }
            // Disallow file dropping if we're on a group's page and not allowed to add panels to it.
            if (this.currentGroup && ! this.mayAddPanelToGroup) {
                return false;
            }
            return true;
        }
    },
    methods: {
        ...mapActions([
            'uploadNewPanel',
            'addSelectedPanelsToGroup',
            'storeFile',
        ]),

        ...mapMutations([ 'setPendingUpload', ]),
        uploadFile(formData, files){
            if(this.expandedPanelId) {
                this.attachFileToSmartFigure(formData, files);
            } else {
                this.uploadSmartFigure(formData, files);
            }
        },
        uploadSmartFigure(formData, files) {
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
        attachFileToSmartFigure(formData, files) {
            this.setPendingUpload(true);
            this.storeFile({file: files[0], file_category_id: null})
                .then(response => {
                    this.$snotify.success(response.data.MESSAGE, "File Uploaded")
                }).catch(error => {
                    this.$snotify.error(error.data.message, "Upload failed")
                }).finally(() => {
                   this.setPendingUpload(false);
                });
        }
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