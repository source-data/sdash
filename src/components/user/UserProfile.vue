<template>
    <b-container>
        <b-row>
            <b-col>
                <div class="profile-header mb-5">
                    <user-icon :name="me.fullname" class="user-profile--user-icon" v-if="!me.profile_picture_filename"></user-icon>
                    <div class="user-profile--user-avatar"  v-if="me.profile_picture_filename">
                        <img :src="me.profile_picture" :alt="me.fullname">
                    </div>
                    <div class="user-profile-name">
                        <h2 class="user-profile--user-name">{{me.fullname}}</h2>
                        <h3 v-if="me.institution_name" class="user-profile--institution-name">{{me.institution_name}}</h3>
                    </div>
                </div >

            </b-col>

        </b-row>
        <b-row>
            <b-col class="pt3">
                <h3>My Profile</h3>

            </b-col>
        </b-row>
    </b-container>
</template>

<script>

import userIcon from '@/components/user/userIcon'
import { mapGetters } from 'vuex'

export default {

    name: 'UserProfile',
    components: { userIcon },
    props: [''],

    data(){

        return {
            //name: value accessed as this.name internally or name in template

        }

    }, /* end of data */

    computed: {
        ...mapGetters({me:'currentUser'}),
    },

    methods:{ //run as event handlers, for example

        methodName(){
            //do stuff here
        }

    }

}
</script>

<style scoped>

    .profile-header {
        background-color:#dadada;
        border: solid 1px #bfbfbf;
        position: relative;
        padding-left:130px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        color: #3c3c3c;
    }

    .user-profile--user-avatar {
        width: 120px;
        height: 120px;
        border-radius:60px;
        overflow: hidden;
        position: relative;
        left: -80px;
        transform: scale(1.25);
        box-shadow: 0 0 7px 0px #505050, 0px 0px 0px 1px #dad6d6;

    }
    .user-profile--user-avatar img {
        width:100%;
    }

    .user-profile--institution-name {
        font-size:1.4em;
        margin:0;
        color: #80509c;
    }


    .user-profile--user-name {
        margin-bottom: 0em;
    }
</style>