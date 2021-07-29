<template>
    <div>
        <b-navbar class="sd-action-bar" v-if="countSelectedPanels > 0">
            <!-- action bar controls -->
            <b-navbar-nav>
                <b-nav-form>
                    <b-button
                        class="sd-action-bar--clear-button"
                        @click="clearSelectedPanels"
                        v-b-tooltip.hover.top
                        title="Clear selection"
                    >
                        <font-awesome-icon icon="times" size="lg" />
                    </b-button>
                    <span> {{ countSelectedPanels }} selected </span>
                </b-nav-form>
            </b-navbar-nav>
            <b-navbar-nav class="ml-auto">
                <b-nav-form>
                    <b-button
                        class="sd-action-bar--delete-button"
                        id="sd-mass-delete-panels"
                        v-b-tooltip.hover.top
                        title="Delete selected panels"
                    >
                        <font-awesome-icon icon="trash-alt" size="lg" />
                    </b-button>
                    <b-button
                        class="sd-action-bar--group-button"
                        id="sd-add-panels-to-sharing-group"
                        v-b-tooltip.hover.top
                        title="Add panels to sharing group"
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
                <p>
                    <b-button @click="closeDeletePopover" size="sm"
                        >Cancel</b-button
                    >
                    <b-button
                        @click="deleteMultiplePanels"
                        size="sm"
                        variant="danger"
                    >
                        <font-awesome-icon icon="trash-alt" size="1x" />
                        Delete {{ countSelectedPanels }} panel(s)
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
            >
                <template v-slot:title>
                    Add to group
                    <b-button
                        @click="closeGroupsPopover"
                        class="close"
                        aria-label="Close"
                    >
                        <span class="d-inline-block" aria-hidden="true"
                            >&times;</span
                        >
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
                            <b-form-select-option :value="null"
                                >Select group</b-form-select-option
                            >
                        </template>
                    </b-form-select>
                </b-form-group>
                <p>
                    <b-button
                        @click="addPanelsToGroup"
                        size="sm"
                        variant="info"
                        :disabled="!selectedSharingGroupId"
                    >
                        <font-awesome-icon icon="users" size="1x" />
                        Add to Group
                    </b-button>
                </p>
                <p>
                    <label class="d-block">or create new sharing group</label>
                    <b-button @click="closeGroupsPopover" size="sm"
                        >Cancel</b-button
                    >
                    <b-button @click="createGroup" size="sm" variant="success">
                        <font-awesome-icon icon="users" size="1x" />
                        Create New Group
                    </b-button>
                </p>
            </b-popover>
        </b-navbar>

        <!-- upload controls -->
        <button
            class="panel-upload-button"
            @click="displayPanelUploader"
            v-b-tooltip.hover.top
            title="Upload new panel"
            v-if="countSelectedPanels === 0"
        >
            <font-awesome-icon icon="plus" />
        </button>
        <b-form-file
            ref="panelUploader"
            class="d-none"
            accept="image/jpeg, image/png, image/gif, image/tiff, application/png"
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
                    this.$snotify.success("New panel created", "Uploaded");
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
                        "Panels could not be added to group",
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
.sd-action-bar {
    padding: 10px 40px;
    background-color: #e9eef5;
}

.sd-action-bar--clear-button,
.sd-action-bar--delete-button,
.sd-action-bar--group-button {
    color: #383838;
    background: none;
    border: none;
}

.sd-action-bar--clear-button:hover,
.sd-action-bar--clear-button:focus,
.sd-action-bar--clear-button:active {
    color: #6e89aa;
}

.sd-action-bar--group-button:hover,
.sd-action-bar--group-button:focus,
.sd-action-bar--group-button:active {
    color: #28a745;
}

.sd-action-bar--delete-button:hover,
.sd-action-bar--delete-button:focus,
.sd-action-bar--delete-button:active {
    color: #dc3545;
}

.panel-upload-button {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 50px;
    height: 50px;
    font-size: 20px;
    line-height: 1;
    color: #fff;
    background: #dc3545;
    outline: none;
    border: none;
    border-radius: 100%;
    z-index: 100;
}
</style>
