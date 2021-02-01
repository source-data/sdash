import AuthorTypes from "@/definitions/AuthorTypes"

export default {
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
  },
  getFiles(state) {
      return state.expandedPanelDetail.files;
  },
  getFileCategoryById: (state) => (id) => {
    return state.categories.find(category => category.id === id)
},
fileCount(state){
    return state.expandedPanelDetail.files.length
},
hasTags(state) {
    return state.expandedPanelDetail.tags.length > 0
},
userTags(state){
    return state.expandedPanelDetail.tags.filter(tag => tag.meta.origin === 'user')
},
methodTags(state){
    return state.expandedPanelDetail.tags.filter(tag => tag.meta.category === 'assay')
},
interventionTags(state){
    return state.expandedPanelDetail.tags.filter(tag => tag.meta.role === 'intervention')
},
assayTags(state){
    return state.expandedPanelDetail.tags.filter(tag => tag.meta.role === 'assayed')
},
otherTags(state){
    return state.expandedPanelDetail.tags.filter(tag => (tag.meta.origin !== 'user' && tag.meta.role !== 'assayed' && tag.meta.role !== 'intervention' && tag.meta.category !== 'assay'))
},
}