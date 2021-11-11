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
      return {
        panels() {
          if (getters.isLoggedIn) {
            if (getters.searchMode == 'group') {
              return `/groups/${state.Groups.currentGroup.id}/panels`;
            }
            return "/users/me/panels";
          }
          return "/public/panels";
        },
        panelDetail(id) {
          return getters.isLoggedIn ? `/panels/${id}` : `/public/panels/${id}`;
        },
        panelThumbnail(panel) {
          return `/api/public/panels/${panel.id}/image/thumbnail?v=${panel.version}`;
        },
        userSearch() {
          return getters.isLoggedIn ? '/users' : '/public/users';
        },
        tagSearch() {
          return getters.isLoggedIn ? '/tags' : '/public/tags';
        },
      }
    },
   },
  actions: {
    toggleLightbox({commit}){
      commit("toggleLightbox")
    },
    resendEmail({commit}, email){
      return Axios.post('emails', {email});
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
