import Axios from "axios";
import AuthorTypes from "@/definitions/AuthorTypes";

//default state
const defaultState = {
    loadedPanels: [],
    expandedPanelId: null,
    expandedPanelDetail: {},
    paginate: true,
    searchGroup: null,
    searchText: null,
    searchTags: [],
    onlyMyPanels: false,
    loading: true,
    panelsLoaded: 0,
    panelsAvailable: 0,
    page: 0,
    nextPage: 1,
    lastPage: 1, // the default needs to be higher than the "page" variable above - to show that all pages haven't loaded
    selectedPanels: [],
    editingCaption: false
};

// default loaded panel
const defaultExpandedPanelState = {
    caption: null,
    clicks: null,
    created_at: null,
    downloads: null,
    id: null,
    made_public_at: null,
    subtype: null,
    title: null,
    type: null,
    updated_at: null,
    authors: [],
    external_authors: [],
    user: {},
    user_id: null,
    access_token: {},
    version: 0
};

defaultState.expandedPanelDetail = Object.assign({}, defaultExpandedPanelState);

//initial state
const state = Object.assign({}, defaultState);

const actions = {
    fetchPanelList({ commit, state, rootState }) {
        let params = { params: {} };

        let searchUrl =
            rootState.searchMode === "group"
                ? "/groups/" + rootState.Groups.currentGroup.id + "/panels"
                : "/users/me/panels";

        //pagination
        params.params.paginate = state.paginate;
        if (state.page < state.lastPage && state.page !== null)
            params.params.page = state.nextPage;
        if (state.searchText) params.params.search = state.searchText;
        if (state.searchTags) params.params.tags = state.searchTags;
        if (state.onlyMyPanels) params.params.private = true;
        console.table(params.params);

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
            commit("storeComments", response.data.DATA[0].comments);
            commit("storeFiles", response.data.DATA[0].files);
            commit("clearSuggestedTags");
            commit("storeTags", response.data.DATA[0].tags);
        });
    },
    updatePanel({ commit }, payload) {
        return Axios.patch("/panels/" + payload.id, payload).then(response => {
            commit("storeExpandedPanelDetail", response.data.DATA);
            return response;
        });
    },
    changeImage({ commit, state }, payload) {
        const data = new FormData();
        data.append("file", payload);
        data.append("_method", "PATCH");
        return Axios.post(
            "/panels/" + state.expandedPanelId + "/image",
            data
        ).then(response => {
            commit("updateExpandedPanelVersion", response.data.DATA);
            return response;
        });
    },
    deleteExpandedPanel({ commit, state, dispatch }) {
        return Axios.delete("/panels/" + state.expandedPanelId).then(
            response => {
                commit("removePanelFromStore", state.expandedPanelId);
                commit("closeExpandedPanels");
                commit("setPanelLoadingState", false);
                return response;
            }
        );
    },
    deleteSelectedPanels({ commit, state }) {
        if (!state.selectedPanels)
            throw { data: { MESSAGE: "You must select panels to delete." } };
        let toDelete = [].concat(state.selectedPanels);
        return Axios.delete("/panels/", {
            params: { id: toDelete }
        }).then(response => {
            for (let i = 0; i < toDelete.length; i++) {
                commit("removePanelFromStore", toDelete[i]);
                commit("removePanelFromSelections", toDelete[i]);
            }
            return response;
        });
    },
    generatePublicLink({ commit, state }) {
        if (!state.expandedPanelId)
            throw {
                data: {
                    MESSAGE:
                        "A panel must be expanded before generating a public link."
                }
            };
        return Axios.post("/panels/" + state.expandedPanelId + "/tokens").then(
            response => {
                commit("setPublicAccessToken", response.data.DATA);
                return response;
            }
        );
    },
    revokePublicLink({ commit, state }) {
        if (!state.expandedPanelId)
            throw {
                data: {
                    MESSAGE:
                        "A panel must be expanded before revoking a public link."
                }
            };
        return Axios.delete(
            "/panels/" + state.expandedPanelId + "/tokens"
        ).then(response => {
            commit("setPublicAccessToken", {});
            return response;
        });
    },
    updateExpandedPanelAuthors({state, commit}, payload) {
        return Axios.put('/panels/'+ state.expandedPanelId + "/authors", payload)
        .then( (response) => {
            commit("updateExpandedPanelAuthors", response.data.DATA);
            commit("updateLoadedPanelAuthors", {id: state.expandedPanelId, authors: response.data.DATA.authors, external_authors: response.data.DATA.external_authors})
            return response;
        });
    },
    expandPanel({ commit }, panelId) {
        commit("toggleEditingCaption", false);
        commit("updateExpandedPanelId", panelId);
    },
    closeExpandedPanels({ commit }) {
        commit("updateExpandedPanelId");
        commit("clearExpandedPanelDetail");
        commit("clearComments");
        commit("clearFiles");
        commit("clearTags");
        commit("clearSuggestedTags");
        commit("toggleEditingCaption", false);
    },
    uploadNewPanel({ commit }, newPanel) {
        return Axios.post("/panels", newPanel).then(response => {
            commit("addNewlyCreatedPanelToStore", response.data);
            return response;
        });
    },
    selectPanel({ commit }, id) {
        commit("addPanelToSelections", id);
    },
    deselectPanel({ commit }, id) {
        commit("removePanelFromSelections", id);
    },
    clearSelectedPanels({ commit }) {
        commit("clearSelectedPanels");
    },
    setSearchString({ commit }, payload) {
        commit("setSearchString", payload);
        commit("updateExpandedPanelId");
        commit("clearExpandedPanelDetail");
    },
    clearLoadedPanels({ commit }) {
        commit("clearLoadedPanels");
        commit("updateExpandedPanelId");
        commit("clearExpandedPanelDetail");
    },
    setPrivate({ commit }, payload) {
        commit("setPrivate", !!payload);
    },
    toggleShareStatus({ commit, state }, payload) {
        return Axios.patch("/panels/" + state.expandedPanelId + "/share").then(
            response => {
                commit("storeExpandedPanelDetail", response.data.DATA);
                commit("addGroupsToPanelById", response.data.DATA);
                return response;
            }
        );
    }
};

const mutations = {
    setPanelLoadingState(state, value) {
        state.loading = value;
    },
    toggleEditingCaption(state, value) {
        if (value === true || value === false) {
            state.editingCaption = value;
        } else {
            state.editingCaption = !state.editingCaption;
        }
    },

    addToLoadedPanels(state, payload) {
        state.loadedPanels.push(...payload.DATA.data); //panels
        state.panelsLoaded += payload.DATA.data.length;
        state.panelsAvailable = payload.DATA.total;
        state.page = payload.DATA.current_page;
        state.nextPage++;
        state.lastPage = payload.DATA.last_page;
    },
    updateLoadedPanel(state, updatedPanel) {
        const index = _.findIndex(state.loadedPanels, oldPanel => {
            return oldPanel.id === updatedPanel.id;
        });
        if (index > -1) state.loadedPanels.splice(index, 1, updatedPanel);
    },
    updateLoadedPanelAuthors(state, payload) {
        const index = _.findIndex(state.loadedPanels, oldPanel => {
            return oldPanel.id === payload.id;
        });

        if (index > -1) {
            const updated = Object.assign({},state.loadedPanels[index]);
            updated.authors = payload.authors;
            updated.external_authors = payload.external_authors;
            Object.assign(state.loadedPanels[index], updated);
        }
    },
    addNewlyCreatedPanelToStore(state, payload) {
        state.loadedPanels.unshift(payload.DATA);
        // state.panelsLoaded++
        // state.panelsAvailable++
    },
    clearLoadedPanels(state) {
        state.loadedPanels = [];
        state.panelsLoaded = 0;
        state.panelsAvailable = 0;
        state.page = 0;
        state.nextPage = 1;
        state.lastPage = 0;
    },
    updateExpandedPanelId(state, panelId = null) {
        state.expandedPanelId = panelId;
    },
    updateExpandedPanelVersion(state, version = 0) {
        state.expandedPanelDetail.version = version;
        let index = _.findIndex(state.loadedPanels, panel => {
            return panel.id === state.expandedPanelId;
        });
        if (index > -1) state.loadedPanels[index].version = version;
    },
    updateExpandedPanelAuthors(state, payload){
        const detail = Object.assign({}, state.expandedPanelDetail);
        detail.authors = payload.authors;
        detail.external_authors = payload.external_authors;
        state.expandedPanelDetail = detail;
    },
    storeExpandedPanelDetail(state, payload) {
        state.expandedPanelDetail = {
            caption: payload.caption,
            clicks: payload.clicks,
            authors: payload.authors,
            external_authors: payload.external_authors,
            created_at: payload.created_at,
            downloads: payload.downloads,
            id: payload.id,
            made_public_at: payload.made_public_at,
            subtype: payload.subtype,
            title: payload.title,
            type: payload.type,
            updated_at: payload.updated_at,
            user: payload.user,
            user_id: payload.user_id,
            groups: payload.groups,
            access_token: payload.access_token ? payload.access_token : {},
            version: payload.version
        };
    },
    removePanelFromStore(state, id) {
        let index = _.findIndex(state.loadedPanels, function(panel) {
            return panel.id == id;
        });
        if (index > -1) state.loadedPanels.splice(index, 1);
    },
    clearExpandedPanelDetail(state) {
        state.expandedPanelDetail = Object.assign(
            {},
            defaultExpandedPanelState
        );
    },
    addPanelToSelections(state, id) {
        state.selectedPanels.push(id);
    },
    removePanelFromSelections(state, id) {
        let index = state.selectedPanels.indexOf(id);
        if (index > -1) state.selectedPanels.splice(index, 1);
    },
    clearSelectedPanels(state) {
        state.selectedPanels = [];
    },
    setSearchString(state, payload) {
        state.searchText = payload;
    },
    clearSearchCriteria(state) {
        state.searchGroup = null;
        state.searchText = null;
        state.searchTags = [];
        state.onlyMyPanels = false;
        state.paginate = true;
    },
    setPrivate(state, payload) {
        state.onlyMyPanels = payload;
    },
    setPagination(state, payload) {
        state.paginate = payload;
    },
    addGroupsToPanelById(state, panelData) {
        let index = _.findIndex(state.loadedPanels, function(panel) {
            return panel.id == panelData.id;
        });
        if (index > -1) state.loadedPanels[index].groups = panelData.groups;
    },
    setPublicAccessToken(state, tokenObject) {
        state.expandedPanelDetail.access_token = tokenObject;
    }
};

const getters = {
    isLoadingPanels(state) {
        return state.loading;
    },

    hasPanels(state) {
        return state.panelsLoaded > 0;
    },
    loadedPanels(state) {
        return state.loadedPanels;
    },
    hasLoadedAllResults(state) {
        return state.panelsLoaded >= state.panelsAvailable;
    },
    expandedPanelId(state) {
        return state.expandedPanelId;
    },
    expandedPanel(state) {
        return state.expandedPanelDetail;
    },
    expandedPanelAuthors(state) {

        if (
            !state.expandedPanelDetail.authors
            &&
            !state.expandedPanelDetail.external_authors
            ) {
                return []
            };

        const userAuthors = state.expandedPanelDetail.authors.reduce(
                  (accumulator, author) => {
                      accumulator.push({
                          firstname: author.firstname,
                          surname: author.surname,
                          origin: 'users',
                          institution_name: author.institution_name,
                          department_name: author.department_name,
                          orcid: author.orcid,
                          email: author.email,
                          author_role: author.author_role.role,
                          order: author.author_role.order,
                          id: author.id,
                          corresponding:
                              author.author_role.role ===
                              AuthorTypes.CORRESPONDING
                      });
                      return accumulator;
                  },
                  []
              );
        const externalAuthors = state.expandedPanelDetail.external_authors.reduce(
                  (accumulator, author) => {
                      accumulator.push({
                          firstname: author.firstname,
                          surname: author.surname,
                          origin: 'external',
                          institution_name: author.institution_name,
                          department_name: author.department_name,
                          orcid: author.orcid,
                          email: author.email,
                          author_role: author.author_role.role,
                          order: author.author_role.order,
                          id: author.id,
                          corresponding:
                              author.author_role.role ===
                              AuthorTypes.CORRESPONDING
                      });
                      return accumulator;
                  },
                  []
              );
        return [...userAuthors, ...externalAuthors].sort((a,b) => a.order - b.order);
    },
    iOwnThisPanel(state, getters, rootState) {
        if (!state.expandedPanelDetail) return false;
        if (!rootState.Users.user) return false;
        return state.expandedPanelDetail.user_id === rootState.Users.user.id;
    },
    iHaveAuthorPrivileges(state, getters, rootState) {
        if (state.expandedPanelDetail.authors.length > 0 && getters.expandedPanelAuthors.filter((author) => (author.id===rootState.Users.user.id && (author.author_role===AuthorTypes.CORRESPONDING || author.author_role===AuthorTypes.CURATOR))).length > 0) return true

        return false
    },
    iCanEditTags(state, getters, rootState) {

        if (!state.expandedPanelDetail) return false

        if (state.expandedPanelDetail.user_id === rootState.Users.user.id) return true

        if (!rootState.Users.user) return false

        if (getters.iHaveAuthorPrivileges) return true

        if (
            !state.expandedPanelDetail.groups ||
            state.expandedPanelDetail.groups.length < 1
        ) return false

        return !!state.expandedPanelDetail.groups.find(group =>
            rootState.Groups.userGroups.find(
                userGroup =>
                    userGroup.id === group.id &&
                    userGroup.confirmed_users.find(
                        user =>
                            user.pivot.user_id === rootState.Users.user.id &&
                            user.pivot.role === "admin"
                    )
            )
        )
    },
    hasPanelDetail(state) {
        return (
            state.expandedPanelDetail.hasOwnProperty("id") &&
            state.expandedPanelDetail.id === state.expandedPanelId
        );
    },
    selectedPanels(state) {
        return state.selectedPanels;
    },
    countSelectedPanels(state) {
        return state.selectedPanels.length;
    },
    editingCaption(state) {
        return state.editingCaption;
    },
    searchString(state) {
        return state.searchText;
    },
    privatePanels(state) {
        return state.onlyMyPanels;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
