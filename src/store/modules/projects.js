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
			number_of_comments: '',
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
	createProject ({ commit, state }, project) {
		let projectIdx = _.findIndex(state.projects, p => p.name === project.name)
		let newProject = project;
		newProject.project_id = state.projects.length + 1;
		newProject.create_date = new Date()
		newProject.last_event = new Date()
		newProject.figures = []
		newProject.comments = []
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
		}
	},
	DELETE_FIGURE (state, params) {
		_.forEach(state.projects, (p,i) => {
			state.projects[i].figures = _.remove(state.projects[i].figures, f => +f.id === +params.figure_id);
		})
	},
	REMOVE_FIGURE_FROM_PROJECT (state, params) {
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
		if (projectIdx > -1) {
			state.projects[projectIdx].figures = _.remove(state.projects[projectIdx].figures, f => +f.id === +params.figure_id);
		}
	},
	PATCH_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id);
		if (projectIdx > -1) {
			_.forEach(params, (v,k) => {
				if (k !== "project_id") state.projects[projectIdx][k] = v;
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
		}		
	},
	ADD_USER_TO_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			state.projects[projectIdx].users.push(params.user);
		}
	},
	REMOVE_USER_FROM_PROJECT (state, params){
		let projectIdx = _.findIndex(state.projects, p => +p.project_id === +params.project_id)
		if (projectIdx > -1) {
			state.projects[projectIdx].users.splice(params.user_idx,1);
		}
	}
	
		
}

export default {
	state,
	getters,
	actions,
	mutations
}
