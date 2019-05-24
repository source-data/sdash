// initial state
import {HTTP} from '@/router/http';
import {serverURL} from "@/app_config"

const getDefaultState = () => {
	return {
		figures: [],
		flags: {},
		totalItems: null,
		filterParams: {
			sortBy: 'date',
			sortDesc: true,
			limit: 10,
			pageNb: 1,
			filters: {
			}
		},
		request: ''
	}
}

const state = getDefaultState()

// getters
const getters = {
	figures: state => state.figures

}

// actions
const actions = {

	downloadDar ({ commit, state }, params) {
		self.location.href = serverURL+"/dar/"+params.figure_id+"?jwt="+params.jwt;
	},
	getFigures ({ commit, state }, params) {
		return HTTP.get('figures',{params:params}).then(function(response){
			commit('SET_FIGURES',response.data)
		})
	},
	postFigureComment ({ commit, state, dispatch }, params){
		HTTP.post('figure/'+params.id+"/comments",params).then(function(response){
			response.data.existing = (params.note_id) ? true : false;
			commit('SET_FIGURE_COMMENT',response.data)
		});
	},
	
	getPanels ({ commit }, params) {
		//NOT PROCESS YET
		return params
	},
	
	removeFigureFromProject ({ commit, state }, params){
		console.info(params);		
		let figure_idx = _.findIndex(state.figures, f => +f.figure_id === +params.figure_id);
		if (figure_idx === -1) { console.info("Sorry, the figure doesn't exist"); return; }

		let project_idx = _.findIndex(state.figures[figure_idx].projects, p => +p === +params.project_id);
		if (project_idx === -1) { console.info("Sorry, the figure is not in this project"); return; }

		return HTTP.delete("/figures/"+params.figure_id+"/projects/"+params.project_id).then(function(response){		
			commit("REMOVE_FIGURE_FROM_PROJECT",{project_idx: project_idx, figure_idx: figure_idx})
			return response.data
		})				
	},
	
	deleteFigure ({ commit }, params) {
		return HTTP.delete('figures/'+params.figure_id).then(function(response){
			commit('DELETE_FIGURE',response.data)
		})

		// if (params.project_id) {
		// 	 commit("REMOVE_FIGURE_FROM_PROJECT", params)
		// }
		// else{
		// 	return HTTP.delete('figures',{params:params}).then(function(response){
		// 		console.info(response.data);
		// 		commit('DELETE_FIGURE',{figure_id:response.data})
		// 	})
		//
		// 	commit("DELETE_FIGURE",params)
		//
		// }
	},
	getFigureComments ({commit, state}, params) {
		//NOT PROCESS YET
		return new Promise ((resolve, reject) => {
			let idx = _.findIndex(state.figures, f => +f.id === +params.id)
			if (idx === -1) reject("Sorry, figure is unknown")
			let notifications = []
			if (params.type === 'comments') {
				notifications = _.filter(state.figures[idx].notifications, n => n.event_type === 'comment')
			}
			else {
				notifications = state.figures[idx].notifications
			} 
			commit("SET_NOTIFICATIONS",notifications)
			resolve(notifications)
		})
	} ,
	addFigure({ commit, state, rootState }, figure_id){
		//NOT PROCESS YET
		HTTP.get('figures',{params:{figure_id:figure_id}}).then(function(response){
			commit('ADD_FIGURE',response.data[0])
		})
	}
	
}

// mutations
const mutations = {
	SET_FIGURES (state, params) {
		state.figures = _.map(params, f => {
			f._showDetails = false;
			f.is_selected = false
			f.view = 'panels'
			return f;
		})
	},
	TOGGLE_DETAILS (state,params){
		return params;
	},
	TOGGLE_SELECTED_FIGURE (state, params) {
		state.figures[params.index].is_selected = !state.figures[params.index].is_selected
	},
	RESET_FIGURE_FLAGS (state){
		state.figures = _.map(state.figures, f => {
			f.is_selected = false
			f._showDetails = false
			return f
		})
		
		state.flags = {}
	},
	REMOVE_FIGURE_FROM_PROJECT(state,params){
		console.info(params);
		state.figures[params.figure_idx].projects.splice(params[params.project_idx],1);
		console.info(state.figures[params.figure_idx]);
	},
	PUT_PROJECT_IN_FIGURE (state, project){
		let selectedFigureIds = project.figures;
		// let selectedFigureIds = _.map(project.figures, f =>  f.id);
		_.forEach(state.figures, (f,i) => {
			if (selectedFigureIds.indexOf(+f.id) > -1) {
				if (f.projects.indexOf(+project.project_id) === -1){
					state.figures[i].projects.push(+project.project_id)
				}
			}
		})
	},
	DELETE_FIGURE (state, params) {
		let figureIdx = _.findIndex(state.figures, f => +f.id === +params.figure_id)
		if (figureIdx > -1) state.figures.splice(figureIdx,1);
	},
	DELETE_PROJECT (state,project_id){
		_.forEach(state.figures, (f,i) => {
			_.remove(state.figures[i].projects, p => p == project_id)
		})
	},

	SET_FIGURE_COMMENT (state, params) {
		let figureIdx = _.findIndex(state.figures, f => +f.id === +params.id)
		if (params.existing){
			let idx = _.findIndex(state.figures[figureIdx].notifications, n => n.post_date === params.post_date && n.origin_name === params.origin_name)
			if (idx > -1){
				if (params.comment) {
					state.figures[figureIdx].notifications[idx].comment = params.comment
				}
				else {
					state.figures[figureIdx].notifications.splice(idx,1)
				}
			}
		}
		else {
			state.figures[figureIdx].notifications.push(params);			
		}
	},

	ADD_FIGURE (state, figure){
		figure._showDetails = false; figure.is_selected = false; figure.view = 'panels';
		state.figures.push(figure)
	},
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
