<template>
<multiselect
v-model="selectedUsers"
class="sd-user-multiselect"
:id="id"
label="name"
track-by="id"
placeholder="Type to search"
open-direction="bottom"
:options="users"
:multiple="true"
:searchable="true"
:loading="isLoading"
:internal-search="false"
:clear-on-select="true"
:close-on-select="true"
:options-limit="300"
:max-height="600"
:show-no-results="true"
:hide-selected="true"
@search-change="asyncFind">
    <template slot="option" slot-scope="props">
        <div class="sd-group-user-option">
            <div class="sd-group-user-avatar-wrapper">
                <avatar :username="props.option.firstname + ' ' + props.option.surname" :size="32" class="sd-group-user-avatar"></avatar>
            </div>
            <div class="sd-group-user--info">
                <div class="sd-group-user--name">
                    {{ props.option.firstname + ' ' + props.option.surname }}
                </div>
                <div class="sd-group-user--affiliation">
                    {{ props.option.institution_name }}
                </div>
            </div>
        </div>
    </template>
    <template
    slot="tag"
    slot-scope="{ option, remove }"
    >
        <div class="sd-group-user--selected">
            <div class="sd-group-user-avatar-wrapper">
                <avatar :username="option.firstname + ' ' + option.surname" :size="32" class="sd-group-user-avatar"></avatar>
            </div>
            <div class="sd-group-user--selected-info">
                <div class="sd-group-user--selected-name">
                    {{ option.firstname + ' ' + option.surname }}
                    <span class="sd-unconfirmed-user" v-if="option.pivot && option.pivot.status && option.pivot.status=='pending'">(user has not opted-in)</span>
                </div>
                <div class="sd-group-user--selected-affiliation">
                    {{ option.institution_name }}
                </div>
            </div>
                <div class="sd-group-user--make-admin" @mousedown.stop.prevent>
                                    Make admin&nbsp;

                <b-form-checkbox switch @click.stop.prevent :checked="option.isGroupAdmin" @change="updateAdminUser(option.id)" title="Remove from group">
                </b-form-checkbox>
            </div>
            <div class="sd-group-user--remove">
                <span class="custom__remove" @mousedown.stop.prevent="remove(option)">Remove ❌</span>
            </div>

        </div>
    </template>
    <template slot="clear" slot-scope="props">
        <div class="multiselect__clear" v-if="selectedUsers.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
    </template>
    <span slot="noResult">No matching users found.</span>
</multiselect>

</template>

<script>

import Multiselect from 'vue-multiselect'
import store from '@/stores/store'
import { mapActions } from 'vuex'
import _ from 'lodash'

export default {

    name: 'UserMultiselect',
    components: {
        Multiselect,
    },
    props: {
        id: String,
        initialusers: Array,
    },
    data() {
        return {
            users: [],
            selectedUsers: [],
            isLoading: false,
        }
    },
    methods: {
        ...mapActions(["findUsersByName"]),
        asyncFind: _.debounce(function(queryString){

            if(queryString.length > 0) {
                this.findUsersByName(queryString).then((result) => {

                    if(result.data.DATA && result.data.DATA.length > 0) {
                        for(let i = 0; i < result.data.DATA.length; i++) {
                            result.data.DATA[i].isGroupAdmin = false
                        }

                        this.users = result.data.DATA
                    } else {
                        this.users = []
                    }


                })
            } else {
                this.users = []
            }


        },300),
        clearAll () {
            this.selectedUsers = []
        },
        updateAdminUser (user_id) {
            let userIndex = _.findIndex(this.selectedUsers, (user) => user.id === user_id)
            if(userIndex >=0) {
                this.selectedUsers[userIndex].isGroupAdmin = !this.selectedUsers[userIndex].isGroupAdmin
            }
        }
    },
    watch: {
        selectedUsers: {
            handler() {
                this.$emit("userdataChange", this.selectedUsers)
            },
            deep: true
        }
    },
    mounted(){
        if(this.initialusers){
            let tempUser = _.clone(this.initialusers, true)
            for(let i = 0; i < tempUser.length; i++){
                if(tempUser[i].pivot.role == 'admin') {
                    tempUser[i].isGroupAdmin = true;
                } else {
                    tempUser[i].isGroupAdmin = false;
                }
            }

            this.selectedUsers = tempUser
        }
    }


}

</script>

<style lang="scss">
    .sd-unconfirmed-user {
        color: red;
        font-weight: normal;
    }
    .multiselect.sd-user-multiselect .multiselect__tags {
        border: solid 1px #ced4da;
    }

    .sd-group-user-option
     {
        display:flex;
    }

    .sd-group-user--selected
     {
        display:flex;
        padding: 0.25rem;
        background: #e7edf7;
        margin-bottom:0.25rem;
    }

    .sd-group-user--selected > div {
        display: flex;
        flex-wrap:wrap;
        padding:0.25rem;
    }

    .sd-group-user--selected-info {
        flex-grow:1;
        flex-direction:vertical;
    }

    .sd-group-user--selected-name {
        font-size:0.85rem;
        font-weight: bold;
    }

    .sd-group-user--selected-name,
    .sd-group-user--selected-affiliation {
        width: 100%;
    }

    .sd-group-user--selected-affiliation {
        position: relative;
        top: -4px;
    }
    .sd-group-user-avatar-wrapper,
     {
        width: 64px;
        flex: 0 0 64px;
    }

    .custom__remove {
        cursor: pointer;
    }
</style>