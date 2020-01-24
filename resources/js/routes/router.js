import Vue from 'vue'
import Router from 'vue-router'
import PanelGrid from '@/components/PanelGrid'
import GroupInfo from '@/components/groups/GroupInfo'
import CreateGroup from '@/components/groups/CreateGroup'

Vue.use(Router)

export default new Router({
    mode: 'history',
    base: process.env.MIX_DASHBOARD_URL,
    routes: [
      {
        path: '/',
        name: 'dashboard',
        component: PanelGrid
      },
      {
        path: '/group/new',
        name: 'creategroup',
        component: CreateGroup,
      },
      {
        path: '/group/:group_id',
        name: 'group',
        component: GroupInfo,
        props: true,
      },
    ]
});