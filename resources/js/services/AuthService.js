import Axios from 'axios';

const AuthService = {
  /**
   *
   * @param {string} email
   * @param {string} password
   * @returns Promise
   */
  async login(email, password) {
    try {
      let loginTransaction = await this.executeAuthMethod('/login', {email, password});
      return loginTransaction.data;
    } catch (error) {
      throw(error.data);
    }
  },

  async logout() {
      let logoutTransaction = await this.executeAuthMethod('/logout');
      return logoutTransaction.data;

  },

  async executeAuthMethod(apiEndpoint, params = {}) {
    await Axios.get("/csrf-cookie");
    let response = await Axios.post(apiEndpoint, params);
    return response;
  },

};

export default AuthService;