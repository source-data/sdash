import Axios from 'axios';

const PasswordResetService = {
  async sendPasswordResetEmail(email) {
    return await Axios.post('/users/password/reset', {email});
  },
  async resetPassword(password, password_confirmation) {
    return await Axios.post('/users/password', {password, password_confirmation});
  },
};

export default PasswordResetService;