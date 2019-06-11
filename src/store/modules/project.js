import {HTTP} from '@/router/http';

// initial state
const getDefaultState = () => {
	return {
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
}

const state = getDefaultState()

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
			let userIdx = _.findIndex(project.users, u => u.id === +rootState.users.current.user_id)
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
		if ( !state.project.is_admin ){ console.info("no admin, could not delete"); return;}
		return HTTP.delete('projects/'+state.project.project_id).then(function(response){		
			commit("DELETE_PROJECT",response.data)
			commit("RESET_PROJECT")
			return response.data
		})
	},

	patchProject ( { commit, state }, project){
		return HTTP.patch('projects/'+project.project_id,project).then(function(response){		
			commit('PATCH_PROJECT',response.data)
			return response.data
		})
	},

	toggleProjectUserAdmin ( { commit, state }, params){	
		let userIdx = _.findIndex(state.project.users, u => +u.id === params.user.id)
		if (userIdx > -1) {
			if (params.user.is_admin){
				return HTTP.put("/projects/"+params.project_id+"/users/"+params.user.id+"/admin").then(function(response){		
					commit("TOGGLE_PROJECT_USER_ADMIN", {project_id: params.project_id, user_idx: userIdx})
					return response.data
				})				
			}
			else{
				return HTTP.delete("/projects/"+params.project_id+"/users/"+params.user.id+"/admin").then(function(response){		
					commit("TOGGLE_PROJECT_USER_ADMIN", {project_id: params.project_id, user_idx: userIdx})
					return response.data
				})				
			}
		}
	},

	addUserToProject ({ commit, rootState, state }, params){
		let userIdx = _.findIndex(state.project.users, u => u.email === params.email)
		if (userIdx > -1){ console.info("User already in the project"); return; }

		return HTTP.put("/projects/"+state.project.project_id+"/users/",params).then(function(response){		
			commit("ADD_USER_TO_PROJECT", response.data)
			return response.data
		})		
	},

	removeUserFromProject ({ commit, state }, params){
		if (state.project.users.length === 1) { console.info("Sorry, you cannot delete the last member of the project"); return; }
		let idx = _.findIndex(state.project.users, u => +u.id === +params.id);
		if (idx === -1) { console.info("Sorry, the user is not in the project"); return; }

		return HTTP.delete("/projects/"+params.project_id+"/users/"+params.id).then(function(response){		
			commit("REMOVE_USER_FROM_PROJECT",{project_id: params.project_id, user_idx: idx})
			return response.data
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
		console.log(params)
		HTTP.post("/projects/"+state.project.project_id+"/comments", params).then( () => {
			commit('SET_COMMENT',params)	
			dispatch('getProjectComments',{type: 'comments'})
		})
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
	ADD_USER_TO_PROJECT (state, user){
		if (user){
			state.project.users.push(user);
		}
	},
	REMOVE_FIGURE_FROM_PROJECT(state,idx){
		if (idx)
			state.project.figures.splice(idx,1);
	},
	PATCH_PROJECT (state, project){
		_.forEach(project, (v,k) => {
			if (state.project[k] !== undefined && k !== 'origin_name') state.project[k] = v;
		})
	},
	TOGGLE_PROJECT_USER_ADMIN (state, params){
		state.project.users[params.user_idx].is_admin = !state.project.users[params.user_idx].is_admin
	},
	SET_NOTIFICATIONS (state, notifications) {
		state.notifications = notifications
	},
	RESET_STATE (state) {
		Object.assign(state, getDefaultState())
	},
	SET_COMMENT (state, params){
		if (params.post_date){
			let idx = _.findIndex(state.project.notifications, n => n.post_date === params.post_date && n.origin_name === params.origin_name)
			if (idx > -1){
				if (params.comment) {
					state.project.notifications[idx].comment = params.comment
				}
				else {
					state.project.notifications.splice(idx,1)
				}
			}
		}
		else {
			state.project.notifications.push({
				origin_name: params.origin_name,
				note_id:params.note_id,
				event_type: 'comment',
				mutation_type: null,
				comment: params.comment,
				post_date: new Date()
			})
		}
		state.project.last_event = new Date();
	}
	
}

export default {
	state,
	getters,
	actions,
	mutations
}
