import Axios from "axios"
import { STATUS_CODES } from "http"
import Vue from "vue"
import panels from "./panels"

//initial state
const state = {
    userGroups: [],
    currentGroup: null,
}


const actions = {

    createNewGroup({commit, state, rootState}, group){
        return Axios.post("/groups", group).then(response => {
            let newGroup = {
                id: response.data.DATA.id,
                user_id: response.data.DATA.user_id,
                name: response.data.DATA.name,
                description: response.data.DATA.description,
                url: response.data.DATA.url,
                updated_at: response.data.DATA.updated_at,
                created_at: response.data.DATA.created_at,
                confirmed_users: response.data.DATA.confirmed_users,
                pivot: {
                    group_id: response.data.DATA.id,
                    role: "admin",
                    user_id: rootState.Users.user.id,
                }
            }

            commit("addGroupToUserGroups", newGroup)

            return response
         })
    },

    addSelectedPanelsToGroup({commit, state, rootState}, groupId) {

        return Axios.patch("/groups/" + groupId, {panels: rootState.Panels.selectedPanels}).then(response => {
            let updatedGroup = Object.assign({},response.data.DATA.group)
            updatedGroup.pivot = {
                group_id: response.data.DATA.group.users[0].pivot.group_id,
                user_id: response.data.DATA.group.users[0].pivot.user_id,
                role: response.data.DATA.group.users[0].pivot.role,
            }
            delete updatedGroup.users

            let updatedPanels = response.data.DATA.panels

            commit("updateUserGroup", updatedGroup)

            for(let i=0; i<updatedPanels.length; i++) {
                commit("updateLoadedPanel", updatedPanels[i])
            }
            console.log(updatedGroup, updatedPanels)

            return response
        })
    },
    removeUserFromGroup({commit, state, rootState}, userId){
        return Axios.delete("/groups/" + state.currentGroup.id + '/users/' + userId).then(response => {
            return response
        })
    }

}

const mutations = {

    setUserGroups(state, groups){
        state.userGroups = groups;
    },
    addGroupToUserGroups(state, group){

        state.userGroups.push(group)

    },
    updateUserGroup(state, group){
        let index = _.findIndex(state.userGroups, oldGroup => { return oldGroup.id === group.id })
        if(index > -1) state.userGroups.splice(index, 1, group)
    },
    setCurrentGroup(state, group_id){
        state.currentGroup = group_id ? state.userGroups.find((group) => group.id === parseInt(group_id)) : null
    },

}

const getters = {

    userGroups( state ){
        return state.userGroups
    },
    userAdminGroups (state) {
        return state.userGroups.filter( group => group.pivot.role === 'admin' )
    },
    currentGroup(state) {
        return state.currentGroup
    },
    isGroupAdmin(state, getters, rootState) {
        return state.currentGroup.confirmed_users.find(user => (user.id === rootState.Users.user.id && user.pivot.role==="admin")) ? true : false
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}