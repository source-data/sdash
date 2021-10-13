import Vue from 'vue'
import Router from 'vue-router'
import UserLevels from './UserLevels'
import authGuards from './authGuards'
import PanelGrid from '@/components/PanelGrid'
import UserProfile from '@/components/users/UserProfile'
import EditUser from '@/components/users/EditUser'
import GroupGrid from '@/components/groups/GroupGrid'
import GroupListing from '@/components/groups/GroupListing'
import CreateGroup from '@/components/groups/CreateGroup'
import EditGroup from '@/components/groups/EditGroup'
import LoginForm from '@/components/authentication/LoginForm'
import RegistrationForm from '@/components/authentication/RegistrationForm'
import PasswordReset from '@/components/authentication/PasswordReset'

Vue.use(Router)

const router = new Router({
    mode: "history",
    base: "/",
    routes: [
        {
            path: "/",
            name: "dashboard",
            component: PanelGrid,
            meta: {
                access: UserLevels.GUEST
            },
        },
        {
            path: "/login",
            name: "login",
            component: LoginForm,
            meta: {
                access: UserLevels.GUEST
            },
        },
        {
            path: "/password-reset",
            name: "passwordreset",
            component: PasswordReset,
            meta: {
                access: UserLevels.GUEST
            },
        },
        {
            path: "/register",
            name: "register",
            component: RegistrationForm,
            meta: {
                access: UserLevels.GUEST
            },
        },
        {
            path: "/user/:user_id",
            name: "user",
            component: UserProfile,
            props: true,
            meta: {
                access: UserLevels.USER
            },
        },
        {
            path: "/user/:user_id/edit",
            name: "useredit",
            component: EditUser,
            props: true,
            meta: {
                access: UserLevels.USER
            },
        },
        {
            path: "/groups",
            name: "groups",
            component: GroupGrid,
            meta: {
                access: UserLevels.GUEST
            },
        },
        {
            path: "/group/new",
            name: "creategroup",
            component: CreateGroup,
            meta: {
                access: UserLevels.USER
            },
        },
        {
            path: "/group/:group_id/edit",
            name: "groupedit",
            component: EditGroup,
            props: true,
            meta: {
                access: UserLevels.USER
            },
        },
        {
            path: "/group/:group_id",
            name: "group",
            component: GroupListing,
            props: true,
            meta: {
                access: UserLevels.GUEST
            },
        }
    ]
});

// ADD AUTHORISATION GUARDS
router.beforeEach(authGuards);

export default router;