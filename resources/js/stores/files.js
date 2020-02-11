import Axios from "axios"
import { deepStrictEqual } from "assert"

//initial state
const state = {
    expandedPanelFiles: [],
}



const actions = {
    storeUrl({commit, state, rootState}, payload) {

        let panelId = rootState.Panels.expandedPanelId

        return Axios.post("panels/" + panelId + "/files", payload).then(response => {
            commit("addToFiles", response.data.DATA)
            return response
        })
    },
    storeFile({commit, state, rootState}, payload){
        let panelId = rootState.Panels.expandedPanelId
        let form = new FormData()
        form.append('file', payload)

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

}

const getters = {

    getFiles(state){
        return state.expandedPanelFiles
    },
    fileCount(state){
        return state.expandedPanelFiles.length
    }

}


export default {
    state,
    getters,
    actions,
    mutations
}
