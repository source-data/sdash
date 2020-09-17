<template>
<multiselect
class="sd-author-multiselect"
@select="addUser"
:id="id"
label="name"
track-by="id"
placeholder="Type user name to search"
open-direction="bottom"
:options="users"
:multiple="false"
:searchable="true"
:loading="isLoading"
:internal-search="false"
:clear-on-select="true"
:close-on-select="true"
:reset-after="true"
:options-limit="20"
:max-height="600"
:show-no-results="true"
:hide-selected="true"
@search-change="asyncFind">
    <template slot="option" slot-scope="props">
        <div class="sd-group-user-option">
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
        initialUsers: Array,
    },
    data() {
        return {
            users: [],
            isLoading: false,
        }
    },
    methods: {
        ...mapActions(["findUsersByName"]),
        asyncFind: _.debounce(function(queryString){

            console.log("already", this.initialUsers);

            if(queryString.length > 0) {
                this.findUsersByName(queryString).then((result) => {
                    if(result.data.DATA && result.data.DATA.length > 0) {
                        const userResult = result.data.DATA;
                        const selectList = [];
                        const alreadySelected = this.initialUsers;
                        userResult.map(user => {
                            if (!_.find(alreadySelected, (already) => {
                                return (already.id === user.id);
                            })) {
                                console.log(user, alreadySelected);
                                selectList.push(user);
                            }
                        });

                        this.users = selectList;
                    } else {
                        this.users = []
                    }
                })
            } else {
                this.users = []
            }


        },300),
        addUser(userdata) {
          console.log("authormultiselect", userdata);
          this.$emit('select', userdata);
        }
    },

}

</script>

<style lang="scss">
    .sd-author-multiselect {
      margin: 1rem 0;
    }

    .sd-unconfirmed-user {
        color: red;
        font-weight: normal;
    }
    .multiselect.sd-author-multiselect .multiselect__tags {
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