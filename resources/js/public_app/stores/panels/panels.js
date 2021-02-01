import Axios from "axios";
import AuthorTypes from "@/definitions/AuthorTypes";
import defaultExpandedPanelState from './defaultExpandedPanelState';
import getters from './getters'
import actions from './actions'
import mutations from './mutations'

//default state
const defaultState = {
    loadedPanels: [],
    expandedPanelId: null,
    expandedPanelDetail: {},
    paginate: true,
    searchGroup: null,
    searchText: null,
    searchTags: [],
    filterAuthors: [],
    filterKeywords: [],
    sortOrder: null,
    onlyMyPanels: false,
    loading: true,
    panelsLoaded: 0,
    panelsAvailable: 0,
    page: 0,
    nextPage: 1,
    lastPage: 1, // the default needs to be higher than the "page" variable above - to show that all pages haven't loaded
    selectedPanels: [],
    editingCaption: false,
    categories: [],
};

//initial state
defaultState.expandedPanelDetail = Object.assign({}, defaultExpandedPanelState);
const state = Object.assign({}, defaultState);

export default {
    state,
    getters,
    actions,
    mutations
};
