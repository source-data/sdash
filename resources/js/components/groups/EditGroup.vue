<template>
<div>
    <div v-if="!loaded">
        <b-row>
            <b-col class="text-center">
                <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
            </b-col>
        </b-row>
    </div>
    <div v-if="loaded">
        <b-container class="sd-edit-group py-4" v-if="currentGroup">
            <b-row>
                <b-col>
                    <h1 class="mb-4">Edit "{{ currentGroup.name }}"</h1>
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
                    </b-form-group>
                    <b-form-group
                    id="sd-new-group-members"
                    label-cols-sm="3"
                    label-cols-lg="2"
                    members="Select the group members."
                    label="Group members"
                    label-for="sd-new-group-members-input"
                    >
                        <user-multiselect id="sd-new-group-members-input" v-if="loaded" :initialusers="groupMembers" @userdataChange="updatedUserdata">
                        </user-multiselect>
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
                        <div v-for="panel in selectedPanelDetails" class="sd-group-panel-list-panel-wrapper">
                            <button class="remove-panel-from-group-button error" @click.prevent="deselectPanel(panel.id)">X</button>
                            <img class="sd-group-panel-list-grid-image" v-lazy="'/panels/' + panel.id + '/image/thumbnail'">
                        </div>

                    </section>
                    <section v-if="selectedPanels.length < 1" class="sd-group-panel-list--no-panels">
                        <p>No panels selected</p>
                    </section>
                </b-col>
            </b-row>

        </b-container>
    </div>
</div>


</template>

<script>

import store from '@/stores/store'
import { mapGetters, mapActions } from 'vuex'
import UserMultiselect from '@/components/helpers/UserMultiselect'

export default {

    name: 'EditGroup',
    components: {
        UserMultiselect
    },
    props: ["group_id"],
    data(){
        return {
            groupName: "",
            groupUrl: "",
            groupDescription: "",
            groupMembers:[],
            loaded: false,
        }
    },
    computed: {
        ...mapGetters([
            'selectedPanels',
            'loadedPanels',
            'currentGroup',
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
        groupDescriptionState(){
            return this.groupDescription.length > 0 ? true : false
        },
        groupDescriptionValid(){
            return  this.groupDescriptionState === true ? 'Group description is valid' : ''
        },
        groupDescriptionInvalid(){
            return (this.groupDescriptionState === false) ? 'Group description is required' : ''
        },
        disableSubmission(){
            return !(this.groupNameValid && this.groupDescriptionValid && this.groupUrlValid)
        }
    },

    methods:{
        ...mapActions([
            'modifyGroup',
            'setCurrentGroup',
            'getGroupById',
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
                members: members,
                panels: panels,

            }

            this.modifyGroup(group).then(response => {
                this.$router.push({path: `/group/${response.data.DATA.id}`})
            }).catch(err => {
                this.$snotify.error("Could not save sharing group", "Sorry!")
            })

        }


    },
    beforeMount() {
        // only allow authorized users to view the edit screen
        let group_id = this.group_id
        this.getGroupById({group_id, unconfirmed_users: true}).then(response => {
            let group = response.data.DATA
            this.groupName = group.name
            this.groupUrl =  group.url || ""
            this.groupDescription =  group.description
            this.groupMembers = group.users
            this.loaded = true

            this.$store.commit("clearLoadedPanels")
            this.$store.commit("updateExpandedPanelId")
            this.$store.commit("clearExpandedPanelDetail")
            this.$store.commit("clearSearchCriteria")
            // this.$store.commit("setCurrentGroup", this.group_id)
            this.$store.commit("setPagination", false)
            this.$store.commit("setSearchMode", "group")
            store.dispatch("fetchPanelList")
            .then((response) => {
                //select all the panels from this group, so they can be excluded from the group
                //by deselecting them
                for(let i = 0; i < response.data.DATA.data.length; i++){
                    this.$store.commit("addPanelToSelections", response.data.DATA.data[i].id)
                }

            })
            .catch((error) => { console.log(error)
                this.$store.commit("setPanelLoadingState", false)
            })

        }).catch(err => {
            if(err.status === 401) {
                this.$snotify.error("You don't have permission to do that", "Access Denied")
                this.$router.push({path: '/'})
            }
        })
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


</style>