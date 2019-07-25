import {HTTP} from '@/router/http';

// initial state
const getDefaultState = () => {
	return {
		projects: [],
		flags: {},
		totalItems: null,
		filterParams: {
			sortBy: 'created_time',
			sortDesc: true,
			limit: 100,
			pageNb: 1,
			filters: {
				name: '',
				number_of_figures: '',
				number_of_users: '',
				number_of_notifications: '',
				created_time: ''
			}
		}
	}
}

const state = getDefaultState()

// getters
const getters = {
	projects: state => state.projects
}

// actions
const actions = {
	getProjects ({ commit, state }, params) {
		return HTTP.get('projects',{params:params}).then(function(response){			
			commit('SET_PROJECTS',response.data)
		})
	},
	createProject ({ commit, state, rootState }, project) {
		return HTTP.post('projects',project).then(function(response){		
			commit('CREATE_PROJECT',response.data)
			return response.data
		})
	},
		
	putFiguresInProject ({ commit, state, rootState }, params) {
		// let figureIds = _.map(params.figures, f => f.id).join(',');
		return HTTP.put('figure/'+params.figure_id+"/projects/"+params.project_id).then(function(response){		
			commit('PUT_FIGURES_IN_PROJECT',response.data)
			return response.data
		})
	},
}

// mutations
const mutations = {
	SET_PROJECTS (state, projects){
		state.projects = projects
	},
	SET_PROJECT_PERMISSION (state, permissions){
		_.forEach(permissions, p => {
			state.projects[p.idx].is_admin = p.is_admin
		})
	},
	RESET_PROJECTS (state) {
		state.projects.length = 0
	},
	CREATE_PROJECT (state,project){
		state.projects.push(project)
	},
	DELETE_PROJECT (state,project_id){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === project_id)
		if (projectIdx > -1) state.projects.splice(projectIdx,1)
	},
	PUT_FIGURES_IN_PROJECT (state, project){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +project.project_id)
		// let figureIds = _.map(params.figures, f => f.id);
		if (projectIdx > -1){
			state.projects[projectIdx] = project;
			state.projects[projectIdx].last_event = new Date();
		}
		// _.forEach(project.figures,function(fig){
		// 	console.info(state.figures);
		// })
		// 	state.projects[projectIdx].figures = _.uniq(state.projects[projectIdx].figures.concat(figureIds))
		// 	_.forEach(params.figures, f => {
		// 		state.projects[projectIdx].notifications.push({
		// 			origin_name: params.origin_name,
		// 			event_type: 'mutation',
		// 			mutation_type: 'IMPORT_FIGURE',
		// 			comment: f.filename,
		// 			post_date: new Date()
		// 		})
		// 	})
		// 	state.projects[projectIdx].last_event = new Date();
	},
	// DELETE_FIGURE (state, params) {
	// 	_.forEach(state.projects, (p,i) => {
	// 		let filename = _.first(_.filter(state.projects[i].figures, f => +f.id === +params.figure_id)).filename
	// 		_.remove(state.projects[i].figures, f => +f.id === +params.figure_id);
	//
	// 		state.projects[i].notifications.push({
	// 			origin_name: params.origin_name,
	// 			event_type: 'mutation',
	// 			mutation_type: 'REMOVE_FIGURE',
	// 			comment: filename,
	// 			post_date: new Date()
	// 		})
	// 		state.projects[i].last_event = new Date();
	// 	})
	// },
	// REMOVE_FIGURE_FROM_PROJECT (state, params) {
	// 	let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
	// 	if (projectIdx > -1) {
	// 		// let figureIdx = state.projects[projectIdx].figures.indexOf(params.figure_id);
	// 		_.remove(state.projects[projectIdx].figures, f => +f === +params.figure_id);
	// 		state.projects[projectIdx].notifications.push({
	// 			origin_name: params.origin_name,
	// 			event_type: 'mutation',
	// 			mutation_type: 'REMOVE_FIGURE',
	// 			comment: params.filename,
	// 			post_date: new Date()
	// 		})
	// 		state.projects[projectIdx].last_event = new Date();
	// 	}
	// },
	// PATCH_PROJECT (state, params){
	// 	let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
	// 	if (projectIdx > -1) {
	// 		_.forEach(params, (v,k) => {
	// 			if (k !== 'origin_name' && v !== state.projects[projectIdx][k]) {
	// 				state.projects[projectIdx].notifications.push({
	// 					origin_name: params.origin_name,
	// 					event_type: 'mutation',
	// 					mutation_type: 'EDIT_PROJECT',
	// 					comment: k+" edited to: "+v,
	// 					post_date: new Date()
	// 				})
	// 				state.projects[projectIdx].last_event = new Date();
	// 			}
	// 			if (k !== "project_id" && k !== 'origin_name') state.projects[projectIdx][k] = v;
	// 		})
	// 	}
	// 	else {
	// 		console.log("error no project")
	// 	}
	// },
	TOGGLE_PROJECT_USER_ADMIN (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			state.projects[projectIdx].users[params.user_idx].is_admin = !state.projects[projectIdx].users[params.user_idx].is_admin
			let comment = state.projects[projectIdx].users[params.user_idx].name
			let mutation_type = (state.projects[projectIdx].users[params.user_idx].is_admin) ? "PROMOTE_ADMIN" : "DEMOTE_ADMIN"
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: mutation_type,
				comment: comment,
				post_date: new Date()
			})
			state.projects[projectIdx].last_event = new Date();
		}
	},
	ADD_USER_TO_PROJECT (state, params){

		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			state.projects[projectIdx].users.push(params.user);
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: 'ADD_USER',
				comment: params.user.name+" has been added to the project",
				post_date: new Date()
			})
			state.projects[projectIdx].last_event = new Date();
		}
	},
	REMOVE_USER_FROM_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			let username = state.projects[projectIdx].users[params.user_idx].name
			state.projects[projectIdx].users.splice(params.user_idx,1);
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: 'ADD_USER',
				comment: username+" has been removed from the project",
				post_date: new Date()
			})
			state.projects[projectIdx].last_event = new Date();
		}
	},
	
	// SET_COMMENT (state, params){
	// 	let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
	// 	if (params.post_date){
	// 		let idx = _.findIndex(state.projects[projectIdx].notifications, n => n.post_date === params.post_date && n.origin_name === params.origin_name)
	// 		if (idx > -1){
	// 			if (params.comment) {
	// 				state.projects[projectIdx].notifications[idx].comment = params.comment
	// 			}
	// 			else {
	// 				state.projects[projectIdx].notifications.splice(idx,1)
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		state.projects[projectIdx].notifications.push({
	// 			origin_name: params.origin_name,
	// 			event_type: 'comment',
	// 			mutation_type: null,
	// 			comment: params.comment,
	// 			post_date: new Date()
	// 		})
	// 	}
	// 	state.projects[projectIdx].last_event = new Date();
	// },
	RESET_STATE (state) {
		Object.assign(state, getDefaultState())
	}
		
}

export default {
	state,
	getters,
	actions,
	mutations
}
