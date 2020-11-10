import Axios from "axios"

//initial state
const state = {
    expandedPanelComments: [],
    commentReplyingToId: null,
}


const actions = {

    postComment({commit, state, rootState}, payload ) {

        let panelId = rootState.Panels.expandedPanelId

        let comment = {
            comment: payload
        }

        if(null !== state.commentReplyingToId) comment.reply_to = state.commentReplyingToId

        return Axios.post("/panels/" + panelId + "/comments", comment).then(response => {
            let newComment = response.data.DATA
            commit("addNewCommentToStore", newComment)
        })
    },

    deleteComment({commit, state, rootState}, payload ) {

        const panelId = rootState.Panels.expandedPanelId
        const commentId = payload

        return Axios.delete("/panels/" + panelId + "/comments/" + commentId).then(response => {
            commit('storeComments', response.data.DATA)
        })

    },


}

const mutations = {
    storeComments( state, payload ){
        state.expandedPanelComments = payload
    },
    clearComments( state ){
        state.expandedPanelComments = []
    },
    addNewCommentToStore( state, payload ){
        state.expandedPanelComments.push(payload)
    },
    setReplyingToId( state, payload ){
        state.commentReplyingToId = payload
    },
    clearReplyingToId(state){
        state.commentReplyingToId = null
    }
}

const getters = {
    comments(state){
        return state.expandedPanelComments
    },
    commentCount(state){
        return state.expandedPanelComments.length
    },
    replyingToId(state){
        return state.commentReplyingToId
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}