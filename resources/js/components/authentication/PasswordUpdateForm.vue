<template>
  <section id="sd-login-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols md="6">
                <b-card bg-variant="dark" text-variant="light" header="Reset your password">
                    <b-form
                        @submit.prevent="sendPasswordUpdate"
                    >
                          <b-form-invalid-feedback :state="emailCheck">
                          {{emailFeedback}}
                          </b-form-invalid-feedback>
                        <b-form-group
                            id="sd-new-password-fieldset"
                            label="Enter your new password"
                            valid-feedback="Allowed"
                            :invalid-feedback="passwordFeedback"
                            :state="passwordCheck"
                            :disabled="loading"
                        >
                        <b-form-input
                          id="sd-new-password"
                          type="password"
                          v-model="password"
                          autocomplete="username"
                          :state="passwordCheck"
                          debounce="300"
                          trim
                        ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            id="sd-confirm-new-password-fieldset"
                            label="Confirm password"
                            valid-feedback="Allowed"
                            :invalid-feedback="confirmPasswordFeedback"
                            :state="confirmPasswordCheck"
                            :disabled="loading"
                        >
                            <b-form-input id="sd-confirm-new-password"
                              v-model="confirmPassword"
                              autocomplete="current-password"
                              :state="confirmPasswordCheck"
                              type="password"
                              debounce="300"
                              trim
                            >
                            </b-form-input>
                        </b-form-group>
                        <b-button
                        :disabled="submitDisabled"
                        type="submit"
                        variant="primary"
                        >{{ submitButtonCaption }} <b-spinner small v-if="loading"></b-spinner></b-button>
                    </b-form>
                </b-card>
            </b-col>

        </b-row>
    </b-container>
  </section>
</template>

<script>

import PasswordResetService from '@/services/PasswordResetService';

export default {

    name: 'PasswordUpdateForm',
    props: {
      token: String,
      email: String,
       },
    data(){
        return {
          loading: false,
          password: '',
          confirmPassword: '',
          errors: {},

        }
    }, /* end of data */

    methods:{

        sendPasswordUpdate(){
          this.loading = true;
          PasswordResetService.resetPassword(this.email, this.password, this.confirmPassword, this.token).then(response => {
            this.$snotify.success(response.data.message, "Password Reset");
            this.errors = {};
            this.$router.push({name:'dashboard'});
          }).catch(error => {
            console.log(error);
            this.errors = error.data.errors;
            this.password = '';
            this.confirmPassword = '';
            this.$snotify.error(error.data.message, "Error");
          }).finally(() => {
            this.loading = false;
          });

        }

    },
    computed: {
      submitDisabled() {
        return !(this.passwordCheck && this.confirmPasswordCheck && !this.loading);
      },
      passwordCheck() {
        if(this.errors.password) return false;
        if(!this.password) return null;
        if(this.password.length < 8) return false;
        return true;
      },
      emailCheck() {
        if(this.errors.email) return false;
        return null;
      },
      passwordFeedback() {
        if(this.errors.password) return this.errors.password.join(' ');
        if(this.password.length < 8) {
            return 'Password must be at least 8 characters';
        }
      },
      confirmPasswordCheck() {
        if(!this.password || !this.confirmPassword) return null;
        if(this.password !== this.confirmPassword) return false;
        return true;
      },
      confirmPasswordFeedback() {
        if(this.password1 !== this.confirmPassword) {
            return 'The passwords do not match';
        }
      },
      emailFeedback() {
        if(this.errors.email) return this.errors.email.join(' ');
      },
      submitButtonCaption() {
          return (this.loading) ? 'Submitting' : 'Submit Password';
      },
    }

}
</script>

<style lang="scss">

</style>