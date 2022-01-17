<template>
    <b-container class="sd-create-group py-4">
        <b-row>
            <b-col>
                <h1 class="mb-4">Create a New User Group</h1>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-form-group
                id="sd-new-group-name"
                label-cols-sm="3"
                label-cols-lg="2"
                label="Group Name"
                label-for="sd-new-group-name-input"
                :valid-feedback="groupNameValid"
                :invalidFeedback="groupNameInvalid"
                >
                <b-form-input id="sd-new-group-name-input" placeholder="My new group" :state="groupNameState" v-model="groupName" trim></b-form-input>
                </b-form-group>

                <b-form-group
                id="sd-new-group-url"
                label-cols-sm="3"
                label-cols-lg="2"
                label="Group Website URL"
                label-for="sd-new-group-url-input"
                :valid-feedback="groupUrlValid"
                :invalidFeedback="groupUrlInvalid"
                >
                <b-form-input id="sd-new-group-url-input" placeholder="Website URL" :state="groupUrlState" v-model="groupUrl"></b-form-input>
                </b-form-group>

                <b-form-group
                id="sd-new-group-description"
                label-cols-sm="3"
                label-cols-lg="2"
                label="Group Description"
                label-for="sd-new-group-description-input"
                :valid-feedback="groupDescriptionValid"
                :invalidFeedback="groupDescriptionInvalid"
                >
                <b-form-textarea :state="groupDescriptionState" v-model="groupDescription" id="sd-new-group-description-input" placeholder="About this group"></b-form-textarea>
                </b-form-group>

                <b-form-group
                id="sd-new-group-members"
                label-cols-sm="3"
                label-cols-lg="2"
                members="Select the group members."
                label="Group members"
                label-for="sd-new-group-members-input"
                >
                    <user-multiselect id="sd-new-group-members-input" @userdataChange="updatedUserdata">
                    </user-multiselect>
                </b-form-group>

                <b-form-group
                id="sd-new-group-is-public"
                label-cols-sm="3"
                label-cols-lg="2"
                label="Public"
                label-for="sd-new-group-is-public-input"
                >
                    <b-form-checkbox v-model="isPublicGroup" id="sd-new-group-is-public-input" switch></b-form-checkbox>
                    <span class="text-muted" v-if="isPublicGroup">This group's information will be publicly available.</span>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="sd-create-group--buttons-wrapper">
                <b-button variant="success" @click.prevent="saveGroup" :disabled="disableSubmission">Save</b-button>
                <b-button @click.prevent="exitGroup">Cancel</b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <h2 class="py-4">Panels in Group</h2>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <section v-if="selectedPanels" class="sd-group-panel-list">
                    <div v-for="panel in selectedPanelDetails" class="sd-group-panel-list-panel-wrapper" :key="panel.id">
                        <button class="remove-panel-from-group-button error" @click.prevent="deselectPanel(panel.id)">X</button>
                        <img class="sd-group-panel-list-grid-image" v-lazy="thumbnailUrl(panel)">
                    </div>

                </section>
                <section v-if="selectedPanels.length < 1" class="sd-group-panel-list--no-panels">
                    <p>No panels selected</p>
                </section>
            </b-col>
        </b-row>

    </b-container>

</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import UserMultiselect from '@/components/helpers/UserMultiselect'

export default {

    name: 'CreateGroup',
    components: {
        UserMultiselect
    },
    data(){
        return {
            groupName: "",
            groupUrl: "",
            groupDescription: "",
            groupMembers: [],
            isPublicGroup: false,
        }
    },
    computed: {
        ...mapGetters([
            'apiUrls',
            'selectedPanels',
            'loadedPanels',
            'isGroupDescriptionValid',
            'groupDescriptionMaxLength',
        ]),
        selectedPanelDetails(){
            return this.loadedPanels.filter((item) => this.selectedPanels.indexOf(item.id) >= 0)
        },
        groupNameState(){
            return this.groupName.length >= 5 ? true : false
        },
        groupNameValid(){
            return  this.groupNameState === true ? 'Group name is valid' : ''
        },
        groupNameInvalid(){
            if(this.groupName.length >= 5) {
                return ''
            } else if (this.groupName.length > 0) {
                return 'Group names must be at least five characters long'
            } else {
                return 'Group name is mandatory'
            }
        },
        groupUrlState(){
            if(this.groupUrl.length < 1) return true
            if(this.groupUrl.length > 0 && this.groupUrlCorrectFormat === true) return true
            return false
        },
        groupUrlValid(){
            if(this.groupUrl.length < 1) return 'Optional field'
            if(this.groupUrl.length > 0 && this.groupUrlCorrectFormat === true) return 'URL format accepted'
        },
        groupUrlInvalid(){
            if(this.groupUrl.length < 1) return ''
            if(this.groupUrl.length > 0 && this.groupUrlCorrectFormat === false) return 'Please enter a valid URL including the protocal (http[s])'
        },
        groupUrlCorrectFormat(){
            let regex = new RegExp(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/)
            return this.groupUrl.match(regex) ? true : false
        },
        groupDescriptionState() {
            return this.isGroupDescriptionValid(this.groupDescription);
        },
        groupDescriptionValid() {
            return  this.groupDescriptionState === true ? 'Group description is valid' : ''
        },
        groupDescriptionInvalid(){
            if (this.groupDescriptionState === true) {
                return ''
            }
            if (this.groupDescription) {
                return `Group description must be ${this.groupDescriptionMaxLength} characters or fewer`
            }
            return 'Group description is required'
        },
        disableSubmission(){
            return !(this.groupNameValid && this.groupDescriptionValid && this.groupUrlValid)
        }
    },

    methods:{
        ...mapActions([
            'createNewGroup'
        ]),
        deselectPanel(id){
            this.$store.commit("removePanelFromSelections", id)
        },
        exitGroup(){
            this.$router.go(-1)
        },
        updatedUserdata(users) {
            this.groupMembers = users
        },
        saveGroup(){

            let members = [], panels = []

            this.groupMembers.map(member => {
                members.push({
                    id: member.id,
                    admin: member.isGroupAdmin,
                })
            })

            this.selectedPanels.map(panel => {
                panels.push(panel)
            })

            let group = {
                name: this.groupName,
                url: this.groupUrl,
                description: this.groupDescription,
                is_public: this.isPublicGroup,
                members: members,
                panels: panels,

            }

            this.createNewGroup(group).then(response => {
                this.$router.push({path: `/group/${response.data.DATA.id}`})
            }).catch(err => {
                this.$snotify.error("Could not save new sharing group", "Sorry!")
            })
        },
        thumbnailUrl(panel) {
            return this.apiUrls.panelThumbnail(panel);
        },
    }

}
</script>

<style lang="scss">
.sd-group-panel-list {
    display:flex;
    flex-wrap:wrap;
}

.sd-group-panel-list-panel-wrapper {
    display:flex;
    box-sizing: border-box;
    flex: 0 1 20%;
    margin:0.25rem;
    align-items: center;
    justify-content: center;
    position: relative;
    border: solid 1px #ced4da;

}

.sd-group-panel-list-grid-image {
    width: 100%;
    height: auto;
}

.remove-panel-from-group-button {
    position: absolute;
    top: 0.5rem;
    right:0.5rem;
    padding:0;
    margin: 0;
    vertical-align: middle;
    text-align: center;
    width: 1.5rem;
    height: 1.5rem;
    border: none;
    border-radius: 50%;
    cursor:pointer;
    background: #dc3545;
    color: #fff;
}

.sd-create-group--buttons-wrapper {
    text-align:right;
}

.btn.disabled,
.btn:disabled {
    cursor: not-allowed;
}

#sd-new-group-is-public .custom-switch {
    display: inline-block;
    margin-top: 7px;
}

</style>