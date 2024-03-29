import Axios from "axios"
import { deepStrictEqual } from "assert"

//initial state
const state = {
    categories: [],
    expandedPanelFiles: [],
    pendingUpload: false,
}



const actions = {
    fetchFileCategories({commit}, payload) {
        return Axios.get("/public/files/categories", payload).then(response => {
            commit("loadFileCategories", response.data.DATA)
            return response
        })
    },
    storeUrl({commit, state, rootState}, payload) {

        let panelId = rootState.Panels.expandedPanelId

        return Axios.post("panels/" + panelId + "/files", payload).then(response => {
            commit("addToFiles", response.data.DATA)
            return response
        })
    },
    storeFile({ commit, rootState, rootGetters }, payload) {
        let fileSizeInBytes = payload.file.size,
            validationFailed = rootGetters.validateFileUpload(
                fileSizeInBytes,
                (maxFileSizeInMegaBytes) => `Source files may not be larger than ${maxFileSizeInMegaBytes} MB`
            );
        if (validationFailed) {
            return validationFailed;
        }
        let panelId = rootState.Panels.expandedPanelId
        let form = new FormData()
        form.append('file', payload.file)
        form.append('file_category_id', payload.file_category_id)

        return Axios.post("panels/" + panelId + "/files", form, {headers: { 'Content-Type' : 'multipart/form-data' }}).then(response => {
            commit("addToFiles", response.data.DATA)
            return response
        })
    },
    deleteFile({commit}, file){

        return Axios.delete("files/" + file.id).then(response => {
            commit("removeFileFromStore", file.id)
            return response
        })

    },
    updateFileMeta({commit}, file){

        return Axios.patch("files/" + file.id, file).then(response => {
            commit("updateFile", response.data.DATA)
            return response
        })

    }

}

const mutations = {
    loadFileCategories(state, payload){
        state.categories = payload
    },
    storeFiles(state, payload){
        state.expandedPanelFiles = payload
    },
    clearFiles(state){
        state.expandedPanelFiles = []
    },
    addToFiles(state, payload){
        state.expandedPanelFiles.push(payload)
    },
    removeFileFromStore(state, id){
        let index = _.findIndex(state.expandedPanelFiles, function(file){ return file.id == id })
        if(index > -1) state.expandedPanelFiles.splice(index,1)
    },
    updateFile(state, file){
        let index = _.findIndex(state.expandedPanelFiles, function(oldFile){ return oldFile.id == file.id })
        state.expandedPanelFiles[index] = file
    },
    setPendingUpload(state, value) {
        state.pendingUpload = !!value;
    }

}

const getters = {
    getFileCategories(state){
        return state.categories
    },
    getFileCategoryById: (state) => (id) => {
        return state.categories.find(category => category.id === id)
    },
    getFiles(state){
        return state.expandedPanelFiles
    },
    fileCount(state){
        return state.expandedPanelFiles.length
    },
    pendingUpload(state) {
        return state.pendingUpload;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
