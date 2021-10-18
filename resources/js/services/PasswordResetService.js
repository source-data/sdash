import Axios from 'axios';

const PasswordResetService = {
  async sendPasswordResetEmail(email) {
    return await Axios.post('/users/password/reset', {email});
  },
  async resetPassword(email, password, password_confirmation,  token) {
    return await Axios.post('/users/password', {email, password, password_confirmation, token});
  },
};

export default PasswordResetService;