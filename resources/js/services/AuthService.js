import Axios from 'axios';

const AuthService = {

  async login(email, password) {

    try {
      await Axios.get("/sanctum/csrf-cookie");
      let loginTransaction = await Axios.post("/login", {email, password});
      return loginTransaction.data;
    } catch (error) {
      throw(error.data);
    }
  },
  logout() {

  }

};

export default AuthService;