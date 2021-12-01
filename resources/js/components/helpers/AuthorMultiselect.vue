<template>
    <multiselect
        @select="addUser"
        :id="id"
        label="name"
        track-by="id"
        :placeholder="placeholder"
        open-direction="bottom"
        :options="users"
        :multiple="false"
        :loading="isLoading"
        :internal-search="false"
        :reset-after="true"
        :options-limit="20"
        :max-height="600"
        :hide-selected="true"
        @search-change="asyncFind"
    >
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
        placeholder: {
            type: String,
            default: "Type user name to search"
        },
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
          this.$emit('select', userdata);
        }
    },

}

</script>

<style lang="scss">
    .multiselect {
        margin: 1rem 0;
    }

    .sd-unconfirmed-user {
        color: red;
        font-weight: normal;
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