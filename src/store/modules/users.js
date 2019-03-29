import { Bus } from '@/bus'
import { HTTP } from '@/router/http'
// initial state
const state = {
	users: [],
	current: {
		user_id: '',
		username: '',
		fullname: '',
		permissions: [],
		jwt: '',
		email: ''
	}
}

// getters
const getters = {
	currentUser: state => state.current,
	allUsers: state => state.users
}

// actions
const actions = {
	login ({ commit }, userData) {
		return new Promise((resolve) => {
			var loggedUser = {
				username: `${userData.firstname.substr(0,1).toLowerCase()}${userData.lastname.replace(/ /g,'').toLowerCase()}`,
				jwt: `testJWT4${userData.firstname.replace(/ /g,'')}.${userData.lastname.replace(/ /g,'')}`,
				fullname: `${userData.firstname} ${userData.lastname}`,
				permissions: ['active'],
				email: `${userData.firstname.replace(/ /g,'').toLowerCase()}.${userData.lastname.replace(/ /g,'').toLowerCase()}@email.com`
			}
			HTTP.defaults.headers.common['authorization'] = 'Bearer ' + loggedUser.jwt
			localStorage.setItem('currentUser', JSON.stringify(loggedUser))
			commit('LOGIN', loggedUser)
			resolve(loggedUser)
		})
	},
	getUsers ({ state }){
		return state.users
	},
	getCredentials ({ commit, state }) {
		if (state.current.email) {
			return state.current
		} else {
			let user = localStorage.getItem('currentUser')
			if (user) {
				user = JSON.parse(user)
				if (user.jwt) {
					HTTP.defaults.headers.common['authorization'] = 'Bearer ' + user.jwt
				}
				commit('LOGIN', user)

				return true
			} else {
				return false
			}
		}
	},
	checkPermissions ( { state }, params) {
		let permissionsToCheck = params.permissions
		let condition = params.condition
		if (condition !== 'all') condition = 'any'
		if (!state.current.permissions) return false
		if (condition === 'any') return state.current.permissions.some(v => permissionsToCheck.includes(v))
		else if (condition === 'all') return _.difference(permissionsToCheck, state.current.permissions).length === 0

		return false
	},
	logout ({ commit }) {
		localStorage.removeItem('currentUser')
		HTTP.defaults.auth = {}
		commit('LOGOUT')
	},
	checkUser ({ state }, user) {
		let email = user.email.substr(0,user.email.indexOf('@')).concat('@email.com')
		return new Promise((resolve, reject) => {
			let user = _.filter(state.users, u => u.email === email);
			if (user.length) resolve({
				id: user[0].user_id,
				name: user[0].fullname,
				email: user[0].email
			}) 
			else {
				reject("unknwon user")
			}
		})
		// return HTTP.get('users?reference=' + user, { headers: { 'Accept': 'application/json' } }).then(res => {
		// 	if (res.status === 200) return res.data.sub
		// 	return false
		// }).catch(() => {
		// 	return false
		// })
	}
}

// mutations
const mutations = {
	ADD_USER (state, user){
		user.user_id = state.users.length+1
		user.fullname = user.firstname+" "+user.lastname
		state.users.push({
			user_id: user.user_id,
			fullname: user.fullname,
			firstname: user.firstname,
			lastname: user.lastname,
			email: user.email,
			permissions: ['active'],
			username: `${user.firstname.substr(0,1).toLowerCase()}${user.lastname.replace(/ /g,'').toLowerCase()}`
		})
		return user
	},
	SET_USERS (state, users) {
		state.users = users
	},
	LOGIN (state, user) {
		let userIdx = _.findIndex(state.users, u => u.email === user.email);
		if (userIdx === -1){
			userIdx = state.users.length+1
			user.user_id = userIdx
			state.users.push(user);
		} 
		state.current.user_id = state.users[userIdx].user_id
		state.current.username = state.users[userIdx].username
		state.current.fullname = state.users[userIdx].fullname
		state.current.permissions = state.users[userIdx].permissions
		state.current.jwt = state.users[userIdx].jwt
		state.current.email = state.users[userIdx].email
		Bus.$emit('user.updated', true)
		
	},
	LOGOUT (state) {
		state.current = {
			user_id: null,
			username: null,
			fullname: null,
			firstname: null,
			lastname: null,
			email: null,
			jwt: null,
			permissions: null
		}
		Bus.$emit('user.updated', true)
		
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
