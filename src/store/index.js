import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import users from './modules/users'
import figures from './modules/figures'
import projects from './modules/projects'
import project from './modules/project'

Vue.use(Vuex)

export default new Vuex.Store({
	plugins: [createPersistedState()],
	modules: {
		users,
		figures,
		projects,
		project
	}
})
