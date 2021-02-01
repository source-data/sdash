import Axios from "axios"
import AuthorTypes from "@/definitions/AuthorTypes"

export default {
  fetchPanelList({ commit, state, rootState }) {
      let params = { params: {} };

      let searchUrl = '/panels';
        //   rootState.searchMode === "group"
        //       ? "/groups/" + rootState.Groups.currentGroup.id + "/panels"
        //       : "/panels";

      //pagination
      params.params.paginate = state.paginate;
      if (state.page < state.lastPage && state.page !== null)
          params.params.page = state.nextPage;
      if (state.searchText) params.params.search = state.searchText;
      if (state.searchTags) params.params.tags = state.searchTags;
      if (state.filterAuthors) params.params.authors = state.filterAuthors.map(a => a.id);
      if (state.filterKeywords) params.params.keywords = state.filterKeywords.map(k => k.id);
      if (state.sortOrder) params.params.sortOrder = state.sortOrder;
      if (state.onlyMyPanels) params.params.private = true;

      return Axios.get(searchUrl, params).then(response => {
          commit("addToLoadedPanels", response.data);
          commit("setPanelLoadingState", false);
          return response;
      });
  },
  loadMorePanels({ commit, dispatch, state }) {
      return dispatch("fetchPanelList");
  },
  setLoadingState({ commit }, payload) {
      commit("setPanelLoadingState", payload);
  },
  loadPanelDetail({ commit, state }, panelId) {
      return Axios.get("/panels/" + panelId).then(response => {
          commit("storeExpandedPanelDetail", response.data.DATA[0]);
        //   commit("storeFiles", response.data.DATA[0].files);
        //   commit("storeTags", response.data.DATA[0].tags);
      });
  },
  expandPanel({ commit }, panelId) {
      commit("toggleEditingCaption", false);
      commit("updateExpandedPanelId", panelId);
  },
  closeExpandedPanels({ commit }) {
      commit("updateExpandedPanelId");
      commit("clearExpandedPanelDetail");
  },
  fetchFileCategories({commit}, payload) {
    return Axios.get("files/categories", payload).then(response => {
        commit("loadFileCategories", response.data.DATA)
        return response
    })
  },
  findTagsByName({ commit }, searchString){
    return Axios.get('/tags', { params: { name: searchString } }).then(response => {
        return response
    })
},
  findUsersByName({ commit }, searchString){
    return Axios.get('/users', { params: { name: searchString } }).then(response => {
        return response
    })
},
}