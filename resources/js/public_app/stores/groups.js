import Axios from "axios"

//initial state
const state = {
    groups: [],
}

const actions = {
    fetchGroups({ commit }, payload) {
        return Axios.get("groups", payload).then(response => {
            commit("setGroups", response.data.DATA);
            return response;
        });
    }
};

const mutations = {

    setGroups(state, groups) {
        state.groups = groups;
    },
}

const getters = {

    groups(state) {
        return state.groups;
    },

}

export default {
    state,
    getters,
    actions,
    mutations
}