import Vue from 'vue'
import Router from 'vue-router'
import UserLevels from '@/definitions/UserLevels'
import authGuards from './authGuards'
import PanelGrid from '@/components/PanelGrid'
import EditUser from '@/components/users/EditUser'
import GroupGrid from '@/components/groups/GroupGrid'
import EditGroup from '@/components/groups/EditGroup'
import About from '@/views/About';
import Tutorial from '@/views/Tutorial';
import Api from '@/views/Api';
import UserProfile from '@/components/users/UserProfile'
import CreateGroup from '@/components/groups/CreateGroup'
import GroupListing from '@/components/groups/GroupListing'
import LoginForm from '@/components/authentication/LoginForm'
import PasswordReset from '@/components/authentication/PasswordReset'
import RegistrationForm from '@/components/authentication/RegistrationForm'
import PasswordUpdateForm from '@/components/authentication/PasswordUpdateForm'
import FourOhFourPage from '@/views/404';
import SinglePanel from '@/views/SinglePanel';

Vue.use(Router)

const router = new Router({
    mode: "history",
    base: "/",
    routes: [
        {
            path: "/",
            name: "dashboard",
            component: PanelGrid,
            props: route => ({
                query: route.query.q,
                // the detail view for the panel with this id will be opened
                idPanel: parseInt(route.query.panel, 10),
                page: parseInt(route.query.page, 10),
            }),
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/login",
            name: "login",
            component: LoginForm,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/password-reset",
            name: "passwordreset",
            component: PasswordReset,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/password-update/:token",
            name: "passwordupdate",
            component: PasswordUpdateForm,
            props: route => ({
                token: route.params.token,
                email: route.query.email
            }),
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/register",
            name: "register",
            component: RegistrationForm,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/user/:user_slug",
            name: "user",
            component: UserProfile,
            props: true,
            meta: {
                access: UserLevels.USER
            }
        },
        {
            path: "/user/:user_slug/edit",
            name: "useredit",
            component: EditUser,
            props: true,
            meta: {
                access: UserLevels.USER
            }
        },
        {
            path: "/groups",
            name: "groups",
            component: GroupGrid,
            props: route => ({ query: route.query.q }),
            meta: {
                access: UserLevels.USER
            }
        },
        {
            path: "/group/new",
            name: "creategroup",
            component: CreateGroup,
            meta: {
                access: UserLevels.USER
            }
        },
        {
            path: "/group/:group_id/edit",
            name: "groupedit",
            component: EditGroup,
            props: true,
            meta: {
                access: UserLevels.USER
            }
        },
        {
            path: "/group/:group_id",
            name: "group",
            component: GroupListing,
            props: true,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/panel/:panel_id",
            name: "singlepanel",
            component: SinglePanel,
            props: route => ({
                token: route.query.token
            }),
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/about",
            name: "about",
            component: About,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/tutorial",
            name: "tutorial",
            component: Tutorial,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "/docs/api",
            name: "api",
            component: Api,
            meta: {
                access: UserLevels.GUEST
            }
        },
        {
            path: "*",
            name: "404",
            component: FourOhFourPage,
            props: false,
            meta: {
                access: UserLevels.GUEST
            }
        }
    ]
});

// ADD AUTHORISATION GUARDS
router.beforeEach(authGuards);

export default router;