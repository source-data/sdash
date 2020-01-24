import Axios from "axios"
import SmartTag from "../services/SmartTag"

//initial state
const state = {
    expandedPanelTags: [],
    suggestedSmartTags: [],

}


const actions = {
    fetchSmartTags({ commit, state }, payload) {

        return SmartTag(payload, true)

    },
    deleteTag({commit, state, rootState}, tag){

        let panelId = rootState.Panels.expandedPanelId

        return Axios.delete("/panels/" + panelId + "/tags/" + tag.tag_id, {params: { relationship_id: tag.relationship_id }}).then(response => {
            commit("storeTags", response.data.DATA)
            return response
        })
    },
    validateSuggestedTag({commit, state, rootState}, payload){

        let panelId = rootState.Panels.expandedPanelId
        let toCreate = state.suggestedSmartTags[payload.array_position]
        return Axios.post("/panels/" + panelId + "/tags/", { name: toCreate.name, category: toCreate.category, role: toCreate.role, type: toCreate.type, origin: payload.origin }).then(response => {
            commit("storeTags", response.data.DATA)
            commit("discardSuggestedTag", payload)
            return response
        })
    },
    createUserInputTag({commit, state, rootState}, payload){

        let panelId = rootState.Panels.expandedPanelId
        return Axios.post("/panels/" + panelId + "/tags/", { name: payload.name, category: payload.category, role: payload.role, type: payload.type, origin: payload.origin }).then(response => {
            commit("storeTags", response.data.DATA)
            return response
        })
    },
}

const mutations = {
    storeTags(state, payload){
        state.expandedPanelTags = payload
    },
    suggestedSmartTags(state, payload){
        state.suggestedSmartTags = payload //.map((tag, index) => { return {...tag, array_position:index} })
    },
    clearSuggestedTags(state){
        state.suggestedSmartTags = []
    },
    clearTags(state){
        state.expandedPanelTags = []
    },
    discardSuggestedTag(state, delTag){
        state.suggestedSmartTags.splice(delTag.array_position, 1)
    }
}

const getters = {
    userTags(state){
        return state.expandedPanelTags.filter(tag => tag.meta.origin === 'user')
    },
    methodTags(state){
        return state.expandedPanelTags.filter(tag => tag.meta.category === 'assay')
    },
    interventionTags(state){
        return state.expandedPanelTags.filter(tag => tag.meta.role === 'intervention')
    },
    assayTags(state){
        return state.expandedPanelTags.filter(tag => tag.meta.role === 'assayed')
    },
    otherTags(state){
        return state.expandedPanelTags.filter(tag => (tag.meta.origin !== 'user' && tag.meta.role !== 'assayed' && tag.meta.role !== 'intervention' && tag.meta.category !== 'assay'))
    },
    suggestedMethodTags(state){
        return state.suggestedSmartTags.map((tag, index) => { return {...tag, array_position:index} }).filter(tag => tag.category === 'assay')
    },
    suggestedInterventionTags(state){
        return state.suggestedSmartTags.map((tag, index) => { return {...tag, array_position:index} }).filter(tag => tag.role === 'intervention')
    },
    suggestedAssayTags(state){
        return state.suggestedSmartTags.map((tag, index) => { return {...tag, array_position:index} }).filter(tag => tag.role === 'assayed')
    },
    suggestedOtherTags(state){
        return state.suggestedSmartTags.map((tag, index) => { return {...tag, array_position:index} }).filter(tag => (tag.role !== 'assayed' && tag.role !== 'intervention' && tag.category !== 'assay'))
    },


}


export default {
    state,
    getters,
    actions,
    mutations
}