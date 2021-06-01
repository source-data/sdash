import Vue from 'vue'
import Router from 'vue-router'
import PanelGrid from '@/public_app/components/PanelGrid'
import GroupGrid from "@/public_app/components/groups/GroupGrid";

Vue.use(Router)

export default new Router({
    mode: 'history',
    base: '/public/',
    routes: [
      {
        path: '/',
        name: 'dashboard',
        component: PanelGrid
      },
      {
        path: '/groups',
        name: 'groups',
        component: GroupGrid,
      },
    ]
});
