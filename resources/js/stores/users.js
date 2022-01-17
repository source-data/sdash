import Axios from "axios"

//default user state
const defaultUserState = {
    id: null,
    email: null,
    firstname: null,
    surname: null,
    role: null,
    avatar: null,
    insitution_name: null,
    institution_address: null,
    department_name: null,
    linkedin: null,
    twitter: null,
    orcid: null,
    has_consented: null,
    email_verified_at: null,
}

//initial state
const state = {
    user: Object.assign({}, defaultUserState),

}


const actions = {

    fetchCurrentUser({ commit }){
        return Axios.get('/users/me')
        .then((response)=>{
            commit('setCurrentUser', response.data.DATA)
            commit('setUserGroups', response.data.DATA.groups)

        })
    },
    findUsersByName({ rootGetters }, searchString){
        let userSearchUrl = rootGetters.apiUrls.userSearch();
        return Axios.get(userSearchUrl, { params: { name: searchString } }).then(response =>{
            return response
        })


    },


}

const mutations = {

    setCurrentUser(state, user){
        state.user = {
            id: user.id,
            email: user.email,
            firstname: user.firstname,
            surname: user.surname,
            role: user.role,
            avatar: user.avatar,
            insitution_name: user.insitution_name,
            institution_address: user.institution_address,
            department_name: user.department_name,
            linkedin: user.linkedin,
            twitter: user.twitter,
            orcid: user.orcid,
            has_consented: user.has_consented,
            email_verified_at: user.email_verified_at,
        };
    },
    expireCurrentUser(state) {
        state.user = Object.assign({}, defaultUserState);
    },
}

const getters = {
    currentUser(state) {
        return state.user;
    },
    isLoggedIn(state) {
        return !!state.user.id;
    },
    hasVerifiedEmail(state) {
        return !! state.user.email_verified_at;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
}