// initial state
import mockFigures from '@/mock/figures'

const state = {
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

// getters
const getters = {
	figures: state => state.figures

}

// actions
const actions = {
	getFigures ({ commit, state }) {
		return new Promise((resolve) => {
			setTimeout(() => {
				if (!state.figures.length) {
					resolve(mockFigures)
					commit('SET_FIGURES',mockFigures)
				}
				else resolve(state.figures)
			})
		})
	},
	getPanels ({ commit }, params) {
		// console.log(params)
		return params
	},
	deleteFigure ({ commit }, params) {
		if (params.project_id) { commit("REMOVE_FIGURE_FROM_PROJECT", params) }
		else commit("DELETE_FIGURE",params)
	},
	getFigureComments ({commit, state}, params) {
		
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
	postFigureComment ({ commit, state, dispatch }, params){
		commit('SET_FIGURE_COMMENT',params)
	},
	addFigure({ commit, state, rootState }, figure){
		let newId = 0;
		_.forEach(state.figures, f => {
			if (+f.id > newId) newId = +f.id;
		})
		figure.id = newId+1
		commit('ADD_FIGURE',figure);
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
	PUT_FIGURES_IN_PROJECT (state, params){
		let selectedFigureIds = _.map(params.figures, f =>  f.id);
		_.forEach(state.figures, (f,i) => {
			if (selectedFigureIds.indexOf(+f.id) > -1) {
				if (f.projects.indexOf(+params.project_id) === -1){
					state.figures[i].projects.push(+params.project_id)
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
	REMOVE_FIGURE_FROM_PROJECT (state, params) {
		let figureIdx = _.findIndex(state.figures, f => +f.id === +params.figure_id);
		if (figureIdx > -1) {
			let projectIdx = state.figures[figureIdx].projects.indexOf(+params.project_id);
			if (projectIdx > -1) state.figures[figureIdx].projects.splice(projectIdx,1);
		}
	},
	SET_FIGURE_COMMENT (state, params) {
		let figureIdx = _.findIndex(state.figures, f => +f.id === +params.id)
		if (params.post_date){
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
			state.figures[figureIdx].notifications.push({
				origin_name: params.origin_name,
				event_type: 'comment',
				mutation_type: null,
				comment: params.comment,
				post_date: new Date()
			})			
		}
	},
	ADD_FIGURE (state, figure){
		state.figures.push(figure)
	}
	
}

export default {
	state,
	getters,
	actions,
	mutations
}
