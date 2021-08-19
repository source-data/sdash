import Vue from 'vue'
import Router from 'vue-router'
import PanelGrid from '@/public_app/components/PanelGrid'
import About from '@/views/About';
import Api from '@/views/Api';

Vue.use(Router)

export default new Router({
    mode: "history",
    base: "/public/",
    routes: [
        {
            path: "/",
            name: "dashboard",
            component: PanelGrid
        },
        {
            path: "/about",
            name: "about",
            component: About
        },
        {
            path: "/docs/api",
            name: "api",
            component: Api
        }
    ]
});
