import store from '@/stores/store';

const CheckUserLoginService = {

  async verifyLogin() {

    console.log("Checking for logged-in user");
    return await store.dispatch('fetchCurrentUser')
        .catch((error) => {
            if(error.status === 401) {
                console.log('No logged-in user found.');
            } else {
                console.log(error);
            }
        }).finally(() => {
            store.commit('setApplicationLoaded', true);
        });
  }


}

export default CheckUserLoginService;
