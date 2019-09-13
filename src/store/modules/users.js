import {HTTP} from '@/router/http';

// initial state
const state = {
	all: [],
	current: {
		user_id: null,
		username: null,
		authdata: null,
		jwt: null,
		permissions: null,
		role: null,
		validator: null,
		projects: null,
		institution_name: null,
		department_name: null,
		institution_address: null,
		profile_picture_filename: null,
		profile_picture: null,
	}

}

// getters
const getters = {
	allUsers: state => state.all,
	currentUser: state => state.current
}

// actions
const actions = {
	getAllUsers ({ commit }) {
		return HTTP.get('/users').then(res => {
			commit('SET_USERS', res.data)
			return res.data;
		});
	},
	getUsers ({ state }){
		return state.users
	},
	checkUser ({ state }, user) {
		return HTTP.get('/check_user',{params:{mail:user.email}}).then(res => {
			return res.data;
			// if (res.data)

			// commit('SET_USERS', res.data)
		});

		// let email = user.email.substr(0,user.email.indexOf('@')).concat('@email.com')
		// return new Promise((resolve, reject) => {
		// 	let user = _.filter(state.users, u => u.email === email);
		// 	if (user.length) resolve({
		// 		id: user[0].user_id,
		// 		name: user[0].fullname,
		// 		email: user[0].email
		// 	})
		// 	else {
		// 		reject("unknown user")
		// 	}
		// })
	},

	register ({ commit },user){
		return new Promise((resolve, reject) => {
			HTTP.post('user',user).then(res => {
				var userData = res.data;
				var loggedUser = {
					user_id: userData.user_id,
					username: userData.username,
					firstname: userData.firstname,
					lastname: userData.lastname,
					authdata: userData.code,
					jwt: userData.jwt,
					permissions: userData.permissions,
					role: userData.role,
					validator: userData.validator,
					projects: userData.projects,
					institution_name: userData.institution_name,
					department_name: userData.department_name,
					institution_address: userData.institution_address,
					profile_picture_filename: userData.profile_picture_filename,
					profile_picture: userData.profile_picture,
				}
				commit("LOGIN",loggedUser);
				resolve(loggedUser);
			})
				.catch(function(err){reject(err)})
		})
	},
	login ({ commit }, user) {
		return new Promise((resolve, reject) => {
			// HTTP.get('/test', user)
			HTTP.post('/authenticate', user)
				.then(function(res){

					var userData = res.data; console.log(userData);

					// google authenticator //
					var loggedUser;
					var authdata = userData.code;
					HTTP.defaults.headers.common['authorization'] = 'Bearer '+userData.jwt;
					loggedUser = {
						user_id: userData.user_id,
						username: userData.login,
						fullname: userData.fullname,
						authdata: authdata,
						jwt: userData.jwt,
						permissions: userData.permissions,
						role: userData.role,
						projects: userData.projects,
						institution_name: userData.institution_name,
						department_name: userData.department_name,
						institution_address: userData.institution_address,
						profile_picture_filename: userData.profile_picture_filename,
						profile_picture: userData.profile_picture,
					};
					localStorage.setItem('currentUser',JSON.stringify(loggedUser));
					commit('LOGIN',loggedUser);
					resolve(userData);
				})
				.catch(function (err) { reject(err) })
		})
	},

	getCredentials({ commit }){
		if (state.current.user_id){
			return state.current
		}
		else {
			let user = localStorage.getItem('currentUser');
			if (user){
				user = JSON.parse(user);
				if (user.jwt){
					// var parts = Base64.decode(user.authdata).split(":");
					// HTTP.defaults.auth = {username: parts[0],password: parts[1]};
					HTTP.defaults.headers.common['authorization'] = 'Bearer '+user.jwt;
				}
				commit('LOGIN',user);
				return true;
			}
			else {
				return false;
			}
		}
	},
	checkPermissions(ctx,params){
		let permissionsToCheck = params.permissions
		let condition = params.condition
		if (condition != 'all') condition = 'any';
		if (!state.current.permissions) return false;
		if (condition == 'any') return state.current.permissions.some(v => permissionsToCheck.includes(v));
		else if (condition == 'all') return _.difference(permissionsToCheck, state.current.permissions).length === 0;
		return false;
	},
	logout ({ commit }){
		localStorage.removeItem('currentUser');
		HTTP.defaults.headers.common['authorization'] = ''
		commit('LOGOUT');
	}


}

// mutations
const mutations = {
	SET_USERS (state, users) {
		state.all = users
	},
	LOGIN (state, user){
		state.current = user;
	},
	LOGOUT (state){
		state.current = {
			user_id: null,
			username: null,
			authdata: null,
			jwt: null,
			permissions: null,
			role: null,
			validator: null
		}

	}
}

export default {
	state,
	getters,
	actions,
	mutations
}