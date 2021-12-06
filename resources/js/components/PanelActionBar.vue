<template>
    <div id="panel-action-bar">
        <b-navbar v-if="countSelectedPanels > 0">
            <!-- action bar controls -->
            <b-navbar-nav>
                <b-nav-form>
                    <b-button
                        id="sd-clear-selected-panels"
                        class="text-light"
                        variant="link"
                        @click="clearSelectedPanels"
                        v-b-tooltip.hover.top
                        title="Clear selection"
                    >
                        <font-awesome-icon icon="times" size="lg" />
                    </b-button>

                    <span>{{ countSelectedPanels }} selected</span>
                </b-nav-form>
            </b-navbar-nav>

            <b-navbar-nav class="ml-auto">
                <b-nav-form>
                    <b-button
                        id="sd-mass-delete-panels"
                        variant="link"
                        v-b-tooltip.hover.top
                        title="Delete selected SmartFigures"
                    >
                        <font-awesome-icon icon="trash-alt" size="lg" />
                    </b-button>

                    <b-button
                        id="sd-add-panels-to-sharing-group"
                        variant="link"
                        v-b-tooltip.hover.top
                        title="Add SmartFigures to sharing group"
                    >
                        <font-awesome-icon icon="users" size="lg" />
                    </b-button>
                </b-nav-form>
            </b-navbar-nav>

            <!-- deletion confirmation popover -->
            <b-popover
                ref="delete-popover"
                target="sd-mass-delete-panels"
                triggers="click"
                placement="bottom"
                custom-class="sd-custom-popover"
            >
                <template v-slot:title>
                    Are you sure?
                    <b-button
                        @click="closeDeletePopover"
                        class="close"
                        aria-label="Close"
                    >
                        <span class="d-inline-block" aria-hidden="true"
                            >&times;</span
                        >
                    </b-button>
                </template>
                <p class="mb-2">
                    <b-button @click="closeDeletePopover" size="sm"
                        >Cancel</b-button
                    >
                </p>
                <p>
                    <b-button
                        @click="deleteMultiplePanels"
                        size="sm"
                        variant="danger"
                    >
                        <font-awesome-icon icon="trash-alt" size="1x" />
                        Delete {{ countSelectedPanels }} SmartFigure(s)
                    </b-button>
                </p>
            </b-popover>

            <!-- add to group popover -->
            <b-popover
                ref="sharing-group-popover"
                target="sd-add-panels-to-sharing-group"
                triggers="click"
                placement="bottom"
                @hidden="onGroupsPopoverHide"
                custom-class="sd-custom-popover"
            >
                <template v-slot:title>
                    Add to group
                    <b-button
                        @click="closeGroupsPopover"
                        class="close"
                        aria-label="Close"
                    >
                        <span class="d-inline-block" aria-hidden="true">
                            &times;
                        </span>
                    </b-button>
                </template>

                <b-form-group
                    id="sd-group-fieldset-1"
                    label="Add to existing group"
                    label-for="sd-group-select"
                >
                    <b-form-select
                        size="sm"
                        id="sd-group-select"
                        :options="myGroups"
                        v-model="selectedSharingGroupId"
                        trim
                    >
                        <template #first>
                            <b-form-select-option :value="null">
                                Select group
                            </b-form-select-option>
                        </template>
                    </b-form-select>
                </b-form-group>

                <p class="mb-2">
                    <b-button
                        @click="addPanelsToGroup"
                        size="sm"
                        variant="primary"
                        :disabled="!selectedSharingGroupId"
                    >
                        <font-awesome-icon icon="users" size="1x" />
                        Add to Group
                    </b-button>
                </p>

                <p class="mb-2">
                    <label class="d-block">or create new sharing group</label>
                    <b-button @click="closeGroupsPopover" size="sm">
                        Cancel
                    </b-button>
                </p>
                <p>
                    <b-button @click="createGroup" size="sm" variant="success">
                        <font-awesome-icon icon="users" size="1x" />
                        Create New Group
                    </b-button>
                </p>
            </b-popover>
        </b-navbar>

        <!-- upload controls -->
        <b-button
            v-if="countSelectedPanels === 0"
            id="sd-upload-new-panel"
            variant="primary"
            class="panel-upload-button text-md float-right"
            @click="displayPanelUploader"
            v-b-tooltip.hover.top
            title="Upload new SmartFigure"
        >
            <font-awesome-icon icon="plus" />
        </b-button>

        <b-form-file
            ref="panelUploader"
            class="d-none"
            accept="application/pdf, image/jpeg, image/png, image/gif, image/tiff, application/png"
            v-model="file"
            @input="attemptPanelUpload"
        ></b-form-file>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    name: "PanelActionBar",

    data() {
        return {
            file: null,
            selectedSharingGroupId: null
        };
    },
    computed: {
        ...mapGetters(["countSelectedPanels", "userGroups"]),
        myGroups() {
            let groups = this.userGroups.reduce((myGroups, group) => {
                myGroups.push({ text: group.name, value: group.id });
                return myGroups;
            }, []);
            return groups;
        }
    },

    methods: {
        ...mapActions(["clearSelectedPanels", "deleteSelectedPanels"]),
        displayPanelUploader() {
            this.$refs.panelUploader.$el.childNodes[0].click();
        },
        attemptPanelUpload() {
            if (this.file == null) {
                return;
            }
            let submission = new FormData();
            submission.append("file", this.file);
            this.$store
                .dispatch("uploadNewPanel", submission)
                .then(response => {
                    this.$snotify.success("New SmartFigure created", "Uploaded");
                    this.file = null;
                })
                .catch(error => {
                    this.$snotify.error(
                        error.data.errors.file[0],
                        "Upload failed"
                    );
                    this.file = null;
                });
        },
        addPanelsToGroup() {
            this.$store
                .dispatch("manageGroupPanels", {
                    action: "add",
                    target: "selected",
                    groupId: this.selectedSharingGroupId
                })
                .then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Success");
                    this.closeGroupsPopover();
                })
                .catch(error => {
                    this.$snotify.error(
                        "SmartFigures could not be added to group",
                        "Failure"
                    );
                });
        },
        closeGroupsPopover() {
            if (this.$refs["sharing-group-popover"]) {
                this.$refs["sharing-group-popover"].$emit("close");
            }
        },
        onGroupsPopoverHide() {
            this.selectedSharingGroupId = null;
        },
        createGroup() {
            this.closeGroupsPopover();
            this.$router.push({ name: "creategroup" });
        },
        closeDeletePopover() {
            if (this.$refs["delete-popover"]) {
                this.$refs["delete-popover"].$emit("close");
            }
        },
        deleteMultiplePanels() {
            this.deleteSelectedPanels()
                .then(response => {
                    this.$snotify.success(response.data.MESSAGE, "Success");
                })
                .catch(error => {
                    this.$snotify.error(error.data.MESSAGE, "Deletion Failed!");
                });
        }
    }
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

$action-bar-height: 55px;
#panel-action-bar {
    height: $action-bar-height;
}
nav .btn-link {
    color: $mostly-white-gray;
}
nav .btn-link:active,
nav .btn-link:focus,
nav .btn-link:hover {
    color: $mostly-white-gray-opaque;
}

.panel-upload-button {
    border: none;
    border-radius: 100%;
    width: $action-bar-height;
    height: $action-bar-height;
    margin-right: 2vw;
    outline: none;
    z-index: 100;
}
</style>
