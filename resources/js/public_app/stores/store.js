import Vue from 'vue'
import Vuex from 'vuex'
import Panels from '@/public_app/stores/panels/panels'
import Groups from '@/public_app/stores/groups';

Vue.use(Vuex)

const actions = {
  toggleLightbox({commit}){
    commit("toggleLightbox")
  }
}

const mutations = {
  toggleLightbox(state){
    state.lightboxOpen = !state.lightboxOpen
  },
  setSearchMode(state, mode) {
    state.searchMode = mode
  }
}

const getters = {
  isLightboxOpen(state) {
    return state.lightboxOpen
  },
  searchMode(state){
    return state.searchMode
  }
}



export default new Vuex.Store({
  modules: {
      Panels,
      Groups,
  },
  state: {
    lightboxOpen: false,
    searchMode: 'user'
  },
  getters,
  actions,
  mutations,
})
