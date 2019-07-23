// initial state
import {HTTP} from '@/router/http';
import {serverURL} from "@/app_config"
import moment from 'moment'
const getDefaultState = () => {
	return {
		figures: [],
		flags: {},
		totalItems: null,
		filterParams: {
			sortBy: 'create_date',
			sortDesc: true,
			limit: 15,
			pageNb: 1,
			filters: {
				owner: '',
				filename: '',
				title: '',
				figureLabel: '',
				projects: '',
				createdFrom: '',
				createdTo: '',
				modifiedFrom: '',
				modifiedTo: ''
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
	downloadPdf ({ commit, state }, params) {
		self.location.href = serverURL+"/pdf/"+params.figure_id+"?jwt="+params.jwt;
	},
	downloadPowerpoint ({ commit, state }, params) {
		self.location.href = serverURL+"/powerpoint/"+params.figure_id+"?jwt="+params.jwt;
	},
	downloadImage ({ commit, state }, params) {
		self.location.href = serverURL+"/image/"+params.figure_id+"?jwt="+params.jwt;
	},
	getFigures ({ commit , dispatch, state }, params) {
		if (state.totalItems !== null && state.figures.length >= state.totalItems && state.filterParams.sortBy === params.sortBy && state.filterParams.sortDesc === params.sortDesc && _.isEqual(state.filterParams.filters, params.filters)) {
			return
		}
		var reset = false
		if (params.resetDisplay){
			commit('RESET_FLAGS')
		}
		let requestParams = ''
		_.forEach(params.filters, function (value, filterName) {
			if (filterName === 'project_id') {
				if (value) {
					requestParams += '&project_id=' + value
				}
			} else if (filterName.indexOf('created') === -1 && filterName.indexOf('modified') === -1) {
				if (value) {
					if (value.indexOf('*') === -1) { requestParams += '&' + filterName + '=*' + value + '*' }
					else { requestParams += '&' + filterName + '=' + value  }
				}
			}
		})
		if (params.filters.createdFrom || params.filters.createdTo) {
			let fromDate = ''; let toDate = ''
			if (params.filters.createdFrom) {
				fromDate = moment(params.filters.createdFrom).format('YYYYMMDD')
			}
			if (params.filters.createdTo) {
				toDate = moment(params.filters.createdTo).format('YYYYMMDD')
			}
			requestParams += '&create_date=' + fromDate + '-' + toDate
		}
		if (params.filters.modifiedFrom || params.filters.modifiedTo) {
			let fromDate = ''; let toDate = ''
			if (params.filters.modifiedFrom) {
				fromDate = moment(params.filters.modifiedFrom).format('YYYYMMDD')
			}
			if (params.filters.modifiedTo) {
				toDate = moment(params.filters.modifiedTo).format('YYYYMMDD')
			}
			requestParams += '&modified_date=' + fromDate + '-' + toDate
		}
		let offset = 0
		if (state.filterParams.sortBy !== params.sortBy || state.filterParams.sortDesc !== params.sortDesc || state.request !== requestParams) {
			offset = 0
			params.limit = (state.figures.length > state.filterParams.limit) ? state.figures.length : state.filterParams.limit
			reset = true
		} else offset = (params.pageNb - 1) * params.limit
		let sortSense = (params.sortDesc) ? '-' : ''
		var request = 'figures?limit=' + params.limit + '&offset=' + offset + (params.sortBy ? '&sort=' + sortSense + params.sortBy : '') + requestParams
		return HTTP.get(request, { headers: { 'Accept': 'application/json' } }).then(res => {
			commit('SET_TOTAL', res.headers['x-total-count'])
			let data = []
			_.forEach(res.data, d => {
				let t = { panels: [], comments: [] }
				_.forEach(d, (v, k) => {
					t[k] = v
				})

				let showDetails = (state.flags[t.figure_id] !== undefined) ? state.flags[t.figure_id].show_details : false
				if (t.figure_id !== undefined) {
					let flag = {
						id: t.figure_id,
						is_selected: false,
						show_details: showDetails,
						comment: false
					}
					commit('SET_FLAG', flag)
					t._showDetails = showDetails
					data.push(t)
				}
			})
			commit('SET_FIGURES', { data: data, reset: reset })
			commit('SET_FIGURES_FILTER_PARAMS', params)
			commit('SET_REQUEST_PARAMS', requestParams)
			return res
		})
	},

	getFiguresOld ({ commit, state }, params) {
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
	deleteFigureComment ({ commit, state, dispatch }, params) {
		if (!params.note_id || !params.figure_id)  return
		return new Promise ((resolve, reject) => {
			HTTP.delete("/figures/"+params.figure_id+"/comments/"+params.note_id).then( () => {
				commit("DELETE_FIGURE_COMMENT",params)
				dispatch('getFigureComments',{type: 'comments',id: params.figure_id})
				resolve(params.note_id)
			}).catch(err => {
				reject(err)
			})
		})
	},

	removeFigureFromProject ({ commit, state }, params){
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
			commit('DELETE_FIGURE',{figure_id: params.figure_id})
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
	},
	updatePanel({commit, state, dispatch}, params ){

		let updatedPanel = {
			caption: params.caption,
			label: params.label,
			panel_id: params.panel_id,
		}

		return new Promise((resolve, reject) => {
			HTTP.patch('panel/' + params.panel_id, updatedPanel).then((data) => {
				commit('UPDATE_PANEL', data.data);
				resolve(data);
			}).catch(err => reject(err))
		});
	}

}

// mutations
const mutations = {
	SET_FIGURES (state, data) {
		let figures = data.data
		let reset = data.reset
		_.forEach(figures, (d, i) => {
			if (d.is_selected === undefined) d.is_selected = false
			if (d._showDetails === undefined) d._showDetails = false
			if (d.comment === undefined) d.comment = null
			d.view = 'panels'
			d.comments = []
			_.forEach(state.flags, (flag, figure_id) => {
				if (d.figure_id === figure_id) {
					figures[i].is_selected = flag.is_selected
					figures[i]._showDetails = flag.show_details
					figures[i].comment = flag.comment
				}
			})
		})
		if (reset) {
			state.figures = figures
		}
		else {
			state.figures = _.uniqBy(state.figures.concat(figures), function (d) { return d.figure_id })
		}
	},

	SET_FIGURES_OLD (state, params) {
		state.figures = _.map(params, f => {
			f._showDetails = false;
			f.is_selected = false
			f.view = 'panels'
			return f;
		})
	},
	TOGGLE_DETAILS (state, params) {
		state.flags[params.figure_id].show_details = !state.flags[params.figure_id].show_details
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
		state.figures.splice(params.figure_idx,1);
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
		state.figures.unshift(figure)
		let flag = {
			is_selected: false,
			show_details: false,
			comment: ''
		}
		state.flags[figure.figure_id] = flag;
	},
	RESET_STATE (state) {
		Object.assign(state, getDefaultState())
	},
	SET_FLAG (state, flag) {
		state.flags[flag.id] = {
			is_selected: flag.is_selected,
			comment: flag.comment
		}
		if (flag.show_details !== undefined) state.flags[flag.id].show_details = flag.show_details
	},
	RESET_FLAGS (state) {
		state.flags = {}
	},
	SET_FIGURES_FILTER_PARAMS (state, params) {
		state.filterParams.sortBy = params.sortBy
		state.filterParams.sortDesc = params.sortDesc
		state.filterParams.limit = params.limit
		state.filterParams.pageNb = params.pageNb
		state.filterParams.owner = params.filters.owner
		state.filterParams.filename = params.filters.filename
		state.filterParams.title = params.filters.title
		state.filterParams.figureLabel = params.filters.figureLabel
		state.filterParams.projects = params.filters.projects
		state.filterParams.createdFrom = params.filters.createdFrom
		state.filterParams.createdTo = params.filters.createdTo
		state.filterParams.modifiedFrom = params.filters.modifiedFrom
		state.filterParams.modifiedTo = params.filters.modifiedTo
	},
	SET_REQUEST_PARAMS (state, request) {
		state.request = request
	},
	SET_TOTAL (state, value) {
		state.totalItems = value
	},
	DELETE_FIGURE_COMMENT (state, params) {
		let figureIdx = _.findIndex(state.figures, f => +f.id === +params.figure_id)
		let nidx = _.findIndex(state.figures[figureIdx].notifications, n => n.note_id === params.note_id);
		state.figures[figureIdx].notifications.splice(nidx,1)
	},
	UPDATE_PANEL (state, params) {
		let figureIndex = _.findIndex(state.figures, f => +f.id === +params.figure_id);
		let panelIndex = _.findIndex(state.figures[figureIndex].dar.fig, f => +f.panel_id === +params.panel_id);
		state.figures[figureIndex].dar.fig[panelIndex] = params;
	}

}

export default {
	state,
	getters,
	actions,
	mutations
}
