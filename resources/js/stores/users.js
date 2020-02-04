import Axios from "axios"

//initial state
const state = {
    user: {
        id: null,
        email: null,
        firstname: null,
        surname: null,
        role: null,
        insitution_name: null,
        institution_address: null,
        department_name: null,
        linkedin: null,
        twitter: null,
        orcid: null,
    },

}


const actions = {

    fetchCurrentUser({ commit }){
        return Axios.get('/users/me')
        .then((response)=>{

            commit('setCurrentUser', response.data.DATA)
            commit('setUserGroups', response.data.DATA.confirmed_groups)

        })
    },
    findUsersByName({ commit }, searchString){

        console.log(searchString)

        return Axios.get('/users', { params: { name: searchString } }).then(response =>{
            console.log(response)
            return response
        })


    }


}

const mutations = {

    setCurrentUser(state, user){
        state.user = {
            id: user.id,
            email: user.email,
            firstname: user.firstname,
            surname: user.surname,
            role: user.role,
            insitution_name: user.insitution_name,
            institution_address: user.institution_address,
            department_name: user.department_name,
            linkedin: user.linkedin,
            twitter: user.twitter,
            orcid: user.orcid,
        }
    }
}

const getters = {
    currentUser( state ){
        return state.user
    },
    isLoggedIn( state ){
        return !!state.user.id
    },

}


export default {
    state,
    getters,
    actions,
    mutations
}