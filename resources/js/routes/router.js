import Vue from 'vue'
import Router from 'vue-router'
import PanelGrid from '@/components/PanelGrid'
import UserProfile from '@/components/users/UserProfile'
import GroupInfo from '@/components/groups/GroupInfo'
import CreateGroup from '@/components/groups/CreateGroup'
import EditGroup from '@/components/groups/EditGroup'

Vue.use(Router)

export default new Router({
    mode: 'history',
    base: '/dashboard/',
    routes: [
      {
        path: '/',
        name: 'dashboard',
        component: PanelGrid
      },
      {
        path: '/user/:user_id',
        name: 'user',
        component: UserProfile,
        props: true,
      },
      {
        path: '/group/new',
        name: 'creategroup',
        component: CreateGroup,
      },
      {
        path: '/group/:group_id/edit',
        name: 'groupedit',
        component: EditGroup,
        props: true,
      },
      {
        path: '/group/:group_id',
        name: 'group',
        component: GroupInfo,
        props: true,
      },
    ]
});
