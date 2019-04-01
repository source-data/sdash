// initial state

const state = {
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

// getters
const getters = {
	projects: state => state.projects
}

// actions
const actions = {
	getProjects ({ state, rootState, commit }) {
		let permissions = [];
		_.forEach(state.projects, (p,pidx) => {
			let userIdx = _.findIndex(p.users, u => u.email === rootState.users.current.email)
			if (userIdx === -1){
				permissions.push({idx: pidx, is_admin: false})
			}
			else{
				let is_admin = p.users[userIdx].is_admin 
				permissions.push({idx: pidx, is_admin: is_admin})
			}
		})
		commit("SET_PROJECT_PERMISSION",permissions)
	},
	createProject ({ commit, state, rootState }, project) {
		let projectIdx = _.findIndex(state.projects, p => p.name === project.name)
		let newProject = project;
		newProject.user_id = rootState.users.current.user_id
		newProject.user_name = rootState.users.current.fullname
		newProject.project_id = state.projects.length + 1
		newProject.create_date = new Date()
		newProject.last_event = new Date()
		newProject.figures = []
		newProject.notifications = []
		newProject.is_admin = true
		return new Promise ((resolve, reject) => {
			if (projectIdx > -1) {
				reject("projectalreadyexists")	
			}
			else {
				commit("CREATE_PROJECT",newProject)		
				resolve (newProject)
			}
		});
	}
}

// mutations
const mutations = {
	SET_PROJECT_PERMISSION (state, permissions){
		_.forEach(permissions, p => {
			state.projects[p.idx].is_admin = p.is_admin
		})
	},
	RESET_PROJECTS (state) {
		state.projects.length = 0
	},
	CREATE_PROJECT (state,project){
		project.notifications.push({
			origin_name: project.user_name,
			event_type: 'mutation',
			mutation_type: 'CREATE_PROJECT',
			comment: "Project created",
			post_date: new Date()
		})
		state.projects.push(project)
	},
	DELETE_PROJECT (state,project_id){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === project_id)
		if (projectIdx > -1) state.projects.splice(projectIdx,1)
	},
	PUT_FIGURES_IN_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		let figureIds = _.map(params.figures, f => f.id);
		if (projectIdx > -1){
			state.projects[projectIdx].figures = _.uniq(state.projects[projectIdx].figures.concat(figureIds))
			_.forEach(params.figures, f => {
				state.projects[projectIdx].notifications.push({
					origin_name: params.origin_name,
					event_type: 'mutation',
					mutation_type: 'IMPORT_FIGURE',
					comment: f.filename,
					post_date: new Date()
				})
			})
			
		}
	},
	DELETE_FIGURE (state, params) {
		_.forEach(state.projects, (p,i) => {
			let filename = _.first(_.filter(state.projects[i].figures, f => +f.id === +params.figure_id)).filename
			state.projects[i].figures = _.remove(state.projects[i].figures, f => +f.id === +params.figure_id);

			state.projects[i].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: 'REMOVE_FIGURE',
				comment: filename,
				post_date: new Date()
			})
			
		})
	},
	REMOVE_FIGURE_FROM_PROJECT (state, params) {
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
		if (projectIdx > -1) {
			// let figureIdx = state.projects[projectIdx].figures.indexOf(params.figure_id);
			state.projects[projectIdx].figures = _.remove(state.projects[projectIdx].figures, f => +f === +params.figure_id);
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: 'REMOVE_FIGURE',
				comment: params.filename,
				post_date: new Date()
			})

		}
	},
	PATCH_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
		if (projectIdx > -1) {
			_.forEach(params, (v,k) => {
				if (k !== 'origin_name' && v !== state.projects[projectIdx][k]) {
					state.projects[projectIdx].notifications.push({
						origin_name: params.origin_name,
						event_type: 'mutation',
						mutation_type: 'EDIT_PROJECT',
						comment: k+" edited to: "+v,
						post_date: new Date()
					})
				}
				if (k !== "project_id" && k !== 'origin_name') state.projects[projectIdx][k] = v;
			})			
		}
		else {
			console.log("error no project")
		}
	},
	TOGGLE_PROJECT_USER_ADMIN (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			state.projects[projectIdx].users[params.user_idx].is_admin = !state.projects[projectIdx].users[params.user_idx].is_admin	
			let comment = (state.projects[projectIdx].users[params.user_idx].is_admin) ? state.projects[projectIdx].users[params.user_idx].name+" has admin privileges" : state.projects[projectIdx].users[params.user_idx].name+" has not anymore admin privileges";
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'mutation',
				mutation_type: 'PROMOTE_ADMIN',
				comment: comment,
				post_date: new Date()
			})
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

		}
	},
	SET_COMMENT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (params.post_date){
			let idx = _.findIndex(state.projects[projectIdx].notifications, n => n.post_date === params.post_date && n.origin_name === params.origin_name)
			if (idx > -1){
				if (params.comment) {
					state.projects[projectIdx].notifications[idx].comment = params.comment
				}
				else {
					state.projects[projectIdx].notifications.splice(idx,1)
				}
			}
		}
		else {
			state.projects[projectIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'comment',
				mutation_type: null,
				comment: params.comment,
				post_date: new Date()
			})			
		}

	}
		
}

export default {
	state,
	getters,
	actions,
	mutations
}
