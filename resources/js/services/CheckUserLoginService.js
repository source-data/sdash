import store from '@/stores/store';

const CheckUserLoginService = {

  async verifyLogin() {

    console.log("Checking for logged-in user");
    return await store.dispatch('fetchCurrentUser')
        .then(() => {
            store.commit('setApplicationLoaded', true);
        })
        .catch((error) => {
            if(error.status === 401) {
                console.log('No logged-in user found.');
            } else if (error.status === 403 && error.data.message === 'Your email address is not verified.') {
                store.commit('setEmailConfirmationNotice',true);
            } else {
                console.log(error);
            }
            store.commit('setApplicationLoaded', true);
        });
  }


}

export default CheckUserLoginService;
