import Vue from 'vue'
import Router from 'vue-router'
import Figures from '@/components/figures/list'
import Projects from '@/components/projects/list'
import Project from '@/components/projects/project'
import newProject from '@/components/projects/newProject'
import store from '@/store'
import Login from '@/components/user/login'


Vue.use(Router)

const router = new Router({
	mode: 'history',
	routes: [{
		path: '/',
		redirect: '/figures'
	},
	{
		path: '/figures',
		name: 'figures',
		component: Figures,
		beforeEnter: requireAuth,
		meta: { permissions: 'active', condition: 'any' }
	},
	{
		path: '/projects',
		name: 'projects',
		component: Projects,
		beforeEnter: requireAuth,
		meta: { permissions: 'active', condition: 'any' }
	},
	{
		path: '/projects/new',
		name: 'newproject',
		component: newProject,
		beforeEnter: requireAuth,
		meta: { permissions: 'active', condition: 'any' }
	},
	{
		path: '/projects/:project_id',
		name: 'project',
		component: Project,
		beforeEnter: requireAuth,
		meta: { permissions: 'active', condition: 'any' }
	},
	{
		path: '/login',
		name: 'login',
		component: Login
	},
	{
		path: '*',
		redirect: '/login'
	}
	]
})

router.beforeEach((to, from, next) => {
	document.title = 'SDash'
	next()
})

function requireAuth (to, from, next) {
	store.dispatch('getCredentials').then(test => {
		if (!test) {
			next({
				path: '/',
				query: { redirect: to.fullPath }
			})
		} else {
			
			if (to.matched.some(record => record.meta.permissions.length > 0)) {
				store.dispatch('checkPermissions', { permissions: to.meta.permissions, condition: to.meta.condition }).then(res => {
					if (res) {
						next()
					} else {
						next({
							path: '/login'
						})
					}
				})
			} else {
				next()
			}
		}
	})
}
export default router
