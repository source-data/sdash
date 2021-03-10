import Axios from "axios"

//initial state
const state = {
    userGroups: [],
    currentGroup: null,
}


const actions = {

    getGroupById({commit, state, rootState}, {group_id, unconfirmed_users}){

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
                is_public: response.data.DATA.is_public,
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
                    status: "confirmed",
                    token: null
                }
            };

            commit("addGroupToUserGroups", newGroup)

            return response
         })
    },
    modifyGroup({commit, state, rootState}, group){
        return Axios.put("/groups/" + group.id, group).then(response => {
            let modifiedGroup = {
                id: response.data.DATA.group.id,
                user_id: response.data.DATA.group.user_id,
                name: response.data.DATA.group.name,
                description: response.data.DATA.group.description,
                is_public: response.data.DATA.group.is_public,
                url: response.data.DATA.group.url,
                updated_at: response.data.DATA.group.updated_at,
                created_at: response.data.DATA.group.created_at,
                confirmed_users: response.data.DATA.group.confirmed_users,
                pivot: {
                    group_id: response.data.DATA.group.id,
                    role: "admin",
                    user_id: rootState.Users.user.id,
                    status: "confirmed",
                    token: null
                }
            };

            commit("updateUserGroup", modifiedGroup)

            return response
         })
    },

    manageGroupPanels({commit, state, rootState}, params) {
        let panels = [];
        switch (params.target) {
            case "selected":
                panels = rootState.Panels.selectedPanels;
                break;
            case "expanded":
                panels = [rootState.Panels.expandedPanelId];
                break;
        }
        return Axios.patch("/groups/" + params.groupId + "/panels", {
                panels: panels,
                action: params.action
            })
            .then(response => {
                let updatedGroup = Object.assign({},response.data.DATA.group)
                updatedGroup.pivot = Object.assign({},updatedGroup.confirmed_users[0].pivot)

                let updatedPanels = response.data.DATA.panels

                commit("updateUserGroup", updatedGroup)

                for (let i = 0; i < updatedPanels.length; i++) {
                    commit("updateLoadedPanel", updatedPanels[i])
                    if (updatedPanels[i].id === rootState.Panels.expandedPanelId) {
                        commit("storeExpandedPanelDetail", updatedPanels[i])
                    }
                }

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
    },
    deleteUserGroup({commit, state, rootState}){
        return Axios.delete("/groups/" + state.currentGroup.id).then(response => {
            commit("removeGroupFromUserGroups", state.currentGroup.id)
            commit("setCurrentGroup", null)
            return response
        })
    },
    acceptGroupMembership({commit, state}, payload) {
        return Axios.patch("/users/me/groups/" + payload.groupId + "/join/" + payload.token).then(response => {
            commit("updateUserGroup", response.data.DATA.group)
            return response
        })
    },
    declineGroupMembership({commit, state}, payload) {
        return Axios.delete("/users/me/groups/" + payload.groupId + "/join/" + payload.token)
    },

}

const mutations = {

    setUserGroups(state, groups){
        state.userGroups = groups
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
    confirmedUserGroups( state ) {
        return state.userGroups ? state.userGroups.filter( group => group.pivot.status === 'confirmed' ) : null
    },
    pendingUserGroups( state ) {
        return state.userGroups ? state.userGroups.filter( group => group.pivot.status === 'pending' ) : null
    },
    userAdminGroups (state) {
        return state.userGroups.filter( group => group.pivot.role === 'admin' )
    },
    currentGroup(state) {
        return state.currentGroup
    },
    isGroupAdmin(state, getters, rootState) {
        if(!state.currentGroup) return false
        if(state.currentGroup.users) return state.currentGroup.users.find(user => (user.id === rootState.Users.user.id && user.pivot.role==="admin")) ? true : false
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