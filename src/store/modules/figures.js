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
		state.figures = _.remove(state.figures, f => +f.id === +params.figure_id);
	},
	DELETE_PROJECT (state,project_id){
		_.forEach(state.figures, (f,i) => {
			state.figures[i].projects = _.remove(state.figures[i].projects, p => p == project_id)
		})
	},
	
}

export default {
	state,
	getters,
	actions,
	mutations
}
