import Vue from 'vue'
import Vuex from 'vuex'
import Users from './users'
import Panels from './panels'
import Comments from './comments'
import Files from './files'
import Tags from './tags'
import Groups from './groups'
import Axios from 'axios'

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
    applicationLoaded: false,
    showEmailConfirmationNotice: false,
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
    },
    applicationIsLoaded(state){
      return state.applicationLoaded
    },
    showEmailConfirmationNotice(state){
      return state.showEmailConfirmationNotice;
    },
    apiUrls(state, getters) {
      var panels, panelDetail;
      let panelThumbnail = panel => `/api/public/panels/${panel.id}/image/thumbnail?v=${panel.version}`;
      if (getters.isLoggedIn) {
        panelDetail = id => `/panels/${id}`;
        panels = getters.searchMode == 'group' ? `/groups/${state.Groups.currentGroup.id}/panels` : "/users/me/panels";
      } else {
        panelDetail = id => `/public/panels/${id}`;
        panels = "/public/panels";
      }
      return {
        panels: panels,
        panelDetail: panelDetail,
        panelThumbnail: panelThumbnail,
      }
    },
   },
  actions: {
    toggleLightbox({commit}){
      commit("toggleLightbox")
    },
    resendEmail({commit}){
      return Axios.post('emails');
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
    },
    setApplicationLoaded(state, value) {
      state.applicationLoaded = value;
    },
    setEmailConfirmationNotice(state, value) {
      state.showEmailConfirmationNotice = value;
    }

  }
})
