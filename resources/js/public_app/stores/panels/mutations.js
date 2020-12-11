import defaultExpandedPanelState from './defaultExpandedPanelState';

export default {
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
          version: payload.version,
          files: payload.files,
          tags: payload.tags,
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
  setAuthorFilter(state, payload) {
      state.filterAuthors = payload;
  },
  setKeywordFilter(state, payload) {
      state.filterKeywords = payload;
  },
  setSortOrder(state, payload) {
      state.sortOrder = payload;
  },
  resetAuthorFilter(state) {
      state.filterAuthors = [];
  },
  resetKeywordFilter(state) {
      state.filterKeywords = [];
  },
  resetSortOrder(state) {
      state.sortOrder = null;
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
  },
  removeAuthorFromPanel(state, params) {
      const authorId = params.author_id;
      const panelId = params.panel_id;

      // find the index of the removed author in the user-authors
      const index = _.findIndex(state.expandedPanelDetail.authors , author => {
          return author.id === authorId;
      });
      // remove the author from the  expanded panel detail
      if(index > -1) {
          state.expandedPanelDetail.authors.splice(index,1);
      }
      // find the index of the loaded panel with the removed author
      const loadedPanelIndex = _.findIndex(state.loadedPanels, panel => {
          return panel.id === panelId;
      })
      // find the index of the author within that panel
      if(loadedPanelIndex > -1) {
          const loadedPanelAuthorIndex = _.findIndex(state.loadedPanels[loadedPanelIndex].authors , author => {
              return author.id === authorId;
          });

          if(loadedPanelAuthorIndex > -1) {
              state.loadedPanels[loadedPanelIndex].authors.splice(loadedPanelAuthorIndex,1);
          }
      }
  },
  loadFileCategories(state, payload){
    state.categories = payload
  },
}