import Vue from 'vue'
import Router from 'vue-router'
import Figures from '@/components/figures/list'
import FigureListGrid from '@/components/figures/FigureListGrid'
import Projects from '@/components/projects/list'
import Project from '@/components/projects/project'
import newProject from '@/components/projects/newProject'

import Login from '@/components/user/login'
import UserList from '@/components/user/UserList'
import User from '@/components/user/user'
import Admin from '@/components/user/Admin'
import store from '@/store'

import PermissionDenied from '@/components/user/permissionDenied'
import Register from '@/components/user/Register'
import AccountValidation from '@/components/user/AccountValidation'
import AccountActivation from '@/components/user/AccountActivation'
import forgetPassword from '@/components/user/forgetPassword'
import SetNewPassword from '@/components/user/setNewPassword'

import { publicPath } from '@/app_config'

Vue.use(Router)

const router = new Router({
	mode: 'history',
	base: publicPath,
	routes: [{
		path: '/',
		// redirect: '/'
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
		path: '/panels',
		name: 'panels',
		component: FigureListGrid,
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
		path: '/users',
		name: 'users',
		component: UserList,
		beforeEnter: requireAuth,
		meta: {permissions: 'active',condition: 'any'}
	},
	{
		path: '/admin',
		name: 'admin',
		component: Admin,
		beforeEnter: requireAuth,
		meta: {permissions: 'admin',condition: 'any'}
	},
	{
		path: '/user/:user_id',
		name: 'user',
		component: User,
		beforeEnter: requireAuth,
		meta: {permissions: 'active',condition: 'any'}
	},
	{
		path: '/permissionDenied',
		name: 'permissionDenied',
		component: PermissionDenied
	},
	{
		path: '/register',
		name: 'register',
		component: Register
	},
	{
		path: '/validationrequired',
		name: 'setCredentials',
		component: AccountValidation
	},
	{
		path: '/setnewpassword',
		name: 'setNewPassword',
		component: SetNewPassword
	},
	{
		path: '/activation/:param',
		name: 'accountActivation',
		component: AccountActivation,
		// beforeEnter: requireAuth,
		meta: {permissions: 'active',condition: 'any'}
	},
	{
		path: '/reject/:param',
		name: 'accountActivation',
		component: AccountActivation,
		// beforeEnter: requireAuth,
		meta: {permissions: 'active',condition: 'any'}
	},
	{
		path: '/forgetPassword',
		name: 'forgetPassword',
		component: forgetPassword
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
				path: '/login',
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
