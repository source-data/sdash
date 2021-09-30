import Vue from 'vue'
import Router from 'vue-router'
import PanelGrid from '@/components/PanelGrid'
import UserProfile from '@/components/users/UserProfile'
import EditUser from '@/components/users/EditUser'
import GroupGrid from "@/components/groups/GroupGrid";
import GroupListing from '@/components/groups/GroupListing'
import CreateGroup from '@/components/groups/CreateGroup'
import EditGroup from '@/components/groups/EditGroup'
import LoginForm from '@/components/authentication/LoginForm'

Vue.use(Router)

export default new Router({
    mode: "history",
    base: "/",
    routes: [
        {
            path: "/",
            name: "dashboard",
            component: PanelGrid
        },
        {
            path: "/login",
            name: "login",
            component: LoginForm
        },
        {
            path: "/user/:user_id",
            name: "user",
            component: UserProfile,
            props: true
        },
        {
            path: "/user/:user_id/edit",
            name: "useredit",
            component: EditUser,
            props: true
        },
        {
            path: "/groups",
            name: "groups",
            component: GroupGrid
        },
        {
            path: "/group/new",
            name: "creategroup",
            component: CreateGroup
        },
        {
            path: "/group/:group_id/edit",
            name: "groupedit",
            component: EditGroup,
            props: true
        },
        {
            path: "/group/:group_id",
            name: "group",
            component: GroupListing,
            props: true
        }
    ]
});
