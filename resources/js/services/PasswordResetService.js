import Axios from 'axios';

const PasswordResetService = {
  async sendPasswordResetEmail(email) {
    return await Axios.post('/users/password', {email});
  }
};

export default PasswordResetService;