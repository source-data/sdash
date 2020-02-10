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

    getGroupById({commit, state, rootState}, {group_id, unconfirmed_users}){ console.log("unconf", unconfirmed_users)

        if(!group_id) throw new Error("Group ID is required")

        return Axios.get("/groups/" + group_id, { params: (unconfirmed_users) ? { unconfirmed_users: true} : null }).then(response => {
            commit("setCurrentGroupFromApi",response.data.DATA)
            return response
        })

    },

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
                confirmed_users_count: response.data.DATA.confirmed_users_count,
                panels_count: response.data.DATA.panels_count,
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
    modifyGroup({commit, state, rootState}, group){
        console.table(group)
        return Axios.put("/groups/" + group.id, group).then(response => {
            let modifiedGroup = {
                id: response.data.DATA.group.id,
                user_id: response.data.DATA.group.user_id,
                name: response.data.DATA.group.name,
                description: response.data.DATA.group.description,
                url: response.data.DATA.group.url,
                updated_at: response.data.DATA.group.updated_at,
                created_at: response.data.DATA.group.created_at,
                confirmed_users: response.data.DATA.group.confirmed_users,
                pivot: {
                    group_id: response.data.DATA.group.id,
                    role: "admin",
                    user_id: rootState.Users.user.id,
                }
            }

            commit("updateUserGroup", modifiedGroup)

            return response
         })
    },

    addSelectedPanelsToGroup({commit, state, rootState}, groupId) {

        return Axios.patch("/groups/" + groupId, {panels: rootState.Panels.selectedPanels}).then(response => {
            let updatedGroup = Object.assign({},response.data.DATA.group)
            updatedGroup.pivot = {
                group_id: response.data.DATA.group.confirmed_users[0].pivot.group_id,
                user_id: response.data.DATA.group.confirmed_users[0].pivot.user_id,
                role: response.data.DATA.group.confirmed_users[0].pivot.role,
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
    // Remove logged-in user from group
    removeUserFromGroup({commit, state, rootState}){
        return Axios.delete("/groups/" + state.currentGroup.id + '/users/').then(response => {
            commit("removeGroupFromUserGroups", state.currentGroup.id)
            commit("setCurrentGroup", null)
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
    removeGroupFromUserGroups(state, group_id){
        let index = _.findIndex(state.userGroups, oldGroup => { return oldGroup.id === parseInt(group_id) })
        if(index > -1) state.userGroups.splice(index,1)
    },
    updateUserGroup(state, group){
        let index = _.findIndex(state.userGroups, oldGroup => { return oldGroup.id === group.id })
        if(index > -1) state.userGroups.splice(index, 1, group)
    },
    setCurrentGroup(state, group_id){
        state.currentGroup = group_id ? state.userGroups.find((group) => group.id === parseInt(group_id)) : null
    },
    setCurrentGroupFromApi(state, group){
        state.currentGroup = group
    }

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
        if(!state.currentGroup) return false
        return state.currentGroup.confirmed_users.find(user => (user.id === rootState.Users.user.id && user.pivot.role==="admin")) ? true : false
    },
    isGroupOwner(state, getters, rootState) {
        if(!state.currentGroup) return false
        return state.currentGroup.user_id === rootState.Users.user.id
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}