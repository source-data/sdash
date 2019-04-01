// initial state
const state = {
	project: {
		project_id: '',
		name: '',
		description: '',
		url: '',
		users: [],
		notifications: [],
		figures: [],
		is_admin: false
	},
	notifications: [] // filtered list
}

// getters
const getters = {
	project: state => state.project,
	users: state => state.project.users,
	projectNotifications: state => state.notifications,
}

// actions
const actions = {
	getProject ({ commit, state, rootState }, params) {
		let project_id = params.project_id
		return new Promise ((resolve, reject) => {
			let projects = _.filter(rootState.projects.projects, p => p.project_id === +project_id)
			if (!projects.length){
				reject("Sorry, the project is unknown")
			}
			let project = projects[0];
			let userIdx = _.findIndex(project.users, u => u.email === rootState.users.current.email)
			if (userIdx === -1){
				reject("Sorry, you are not allowed to see this project")
			}
			else{
				project.is_admin = project.users[userIdx].is_admin 
				commit('SET_PROJECT',project)
				commit('RESET_FIGURE_FLAGS')
				resolve(project)
			}
		})
	},
	deleteProject ({ state, commit }){
		let project = state.project
		return new Promise ((resolve, reject) => {
			if ( !state.project.is_admin ){
				reject("Sorry, your are not allowed to delete the project")
			}
			else {
				commit("DELETE_PROJECT",project.project_id)
				commit("RESET_PROJECT")				
				resolve(true)
			}
		})
	},
	patchProject ( { commit, state }, params){
		return new Promise ((resolve, reject) => {
			if (!state.project.is_admin) {
				reject("permissiondenied");
			}
			else {
				params.project_id = state.project.project_id
				commit("PATCH_PROJECT",params);
			}
			resolve(params)
		})
	},
	toggleProjectUserAdmin ( { commit, state }, params){
		let userIdx = _.findIndex(state.project.users, u => +u.id === params.user.id)
		if (userIdx > -1) {
			commit("TOGGLE_PROJECT_USER_ADMIN", {project_id: params.project_id, user_idx: userIdx})
		}
	},
	addUserToProject ({ commit, rootState, state }, params){
		return new Promise ((resolve, reject) => {
			let userIdx = _.findIndex(state.project.users, u => u.email === params.email)
			if (userIdx > -1){
				reject("User already in the project");
				return;
			}
			let user = _.first(_.filter(rootState.users.users, u => u.email === params.email))
			if (!user){
				user = commit('ADD_USER',{email: params.email, firstname: params.firstname, lastname: params.lastname});
			}
			commit("ADD_USER_TO_PROJECT", {project_id: state.project.project_id, user: {email: user.email, id: user.user_id, is_admin: false, name: user.fullname}})
			resolve(true);
			return true;
		})
	},
	removeUserFromProject ({ commit, state }, params){
		return new Promise ((resolve, reject) => {
			if (state.project.users.length === 1) {
				reject("Sorry, you cannot delete the last member of the project");
				return;				
			}
			let idx = _.findIndex(state.project.users, u => +u.id === +params.id);
			if (idx === -1) {
				reject("Sorry, the user is not in the project");
				return;
			}
			commit("REMOVE_USER_FROM_PROJECT",{project_id: params.project_id, user_idx: idx})
			resolve(true)
		})	
	},
	getProjectComments ({commit, state}, params) {
		return new Promise ((resolve, reject) => {
			let notifications = []
			if (params.type === 'comments') {
				notifications = _.filter(state.project.notifications, n => n.event_type === 'comment')
			}
			else {
				notifications = state.project.notifications
			} 
			commit("SET_NOTIFICATIONS",notifications)
			resolve(notifications)
		})
	} ,
	postProjectComment ({ commit, state, dispatch }, params){
		params.project_id = state.project.project_id
		commit('SET_COMMENT',params)
		dispatch('getProjectComments',{type: 'comments'})
	}
	
}

// mutations
const mutations = {
	RESET_PROJECT (state){
		state.project.project_id = ''
		state.project.name = ''
		state.project.description = ''
		state.project.url = ''
		state.project.users = []
		state.project.notifications = []
		state.project.figures = []
		state.project.is_admin = false
	},
	SET_PROJECT (state, project){
		state.project = project
	},
	PATCH_PROJECT (state, params){
		_.forEach(params, (v,k) => {
			if (state.project[k] !== undefined && k !== 'origin_name') state.project[k] = v;
		})
	},
	TOGGLE_PROJECT_USER_ADMIN (state, params){
		state.project.users[params.user_idx].is_admin = !state.project.users[params.user_idx].is_admin
	},
	SET_NOTIFICATIONS (state, notifications) {
		state.notifications = notifications
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
