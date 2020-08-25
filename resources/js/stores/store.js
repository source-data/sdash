import Vue from 'vue'
import Vuex from 'vuex'
import Users from './users'
import Panels from './panels'
import Comments from './comments'
import Files from './files'
import Tags from './tags'
import Groups from './groups'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
      Users,
      Groups,
      Panels,
      Comments,
      Files,
      Tags,
  },
  state: {
    lightboxOpen: false,
    searchMode: 'user',
    showAuthorSidebar: false,
  },
  getters: {
    isLightboxOpen(state) {
      return state.lightboxOpen
    },
    searchMode(state){
      return state.searchMode
    },
    showAuthorSidebar(state){
      return state.showAuthorSidebar
    }

   },
  actions: {
    toggleLightbox({commit}){
      commit("toggleLightbox")
    },

  },
  mutations: {
    toggleLightbox(state){
      state.lightboxOpen = !state.lightboxOpen
    },
    setSearchMode(state, mode) {
      state.searchMode = mode
    },
    setAuthorSidebar(state, value) {
      state.showAuthorSidebar = value
    }

  }
})
