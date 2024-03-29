<template>
  <section id="sd-registration-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols lg="4" md="6" sm="8">
                <b-card bg-variant="dark" text-variant="light">
                    <b-card-title title-tag="h1" class="h2 text-primary pb-4">Reset your Password</b-card-title>
                    <b-form
                        @submit.prevent="sendPasswordReset"
                    >
                        <b-form-group
                            label-sr-only
                            id="email-fieldset"
                            label="Email"
                            valid-feedback="OK"
                            :invalid-feedback="emailFeedback"
                            :state="emailCheck"
                            :disabled="loading"
                        >
                        <b-form-input
                        placeholder="E-Mail Address"
                        class="sd-text-input"
                        id="login-email"
                        v-model="email"
                        autocomplete="username"
                        @input="clearEmailErrors"
                        :state="emailCheck"
                        debounce="500"
                        trim
                        ></b-form-input>
                        </b-form-group>
                        <b-form-row>
                          <b-col>
                            <b-button
                            class="sd-big-button full-width"
                            :disabled="submitDisabled"
                            type="submit"
                            variant="primary"
                            >{{ buttonCaption }} <b-spinner small v-if="loading"></b-spinner></b-button>
                          </b-col>
                        </b-form-row>

                    </b-form>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
  </section>
</template>

<script>

import EmailFormatValidator from '@/services/EmailFormatValidator';
import PasswordResetService from '@/services/PasswordResetService';

export default {

    name: 'PasswordReset',
    data(){

        return {
          email: '',
          loading: false,
          emailErrors: {},
        }

    }, /* end of data */

    methods:{

        sendPasswordReset(){
          this.loading = true;
          PasswordResetService.sendPasswordResetEmail(this.email)
          .then(response => {
            this.$router.push({name: 'login'});
            this.$snotify.success(response.data.message, "Email Sent");
          })
          .catch(error => {
            this.loading = false;
            this.$snotify.error("Password reset failed", "Sorry!");
            console.log(error);
            this.emailErrors = error.data.errors;

          });
        },
        clearEmailErrors(){
          this.emailErrors = {};
        }

    },

    computed: {
        buttonCaption() {
            return (this.loading) ? 'Sending' : 'Reset Password';
        },
        emailCheck() {
          if(!this.email) return null;
          if(this.emailErrors.email) return false;
          return EmailFormatValidator.validate(this.email);
        },
        submitDisabled() {
            return !(this.emailCheck && !this.loading);
        },
        emailFeedback(){
          if(this.emailErrors.email) return this.emailErrors.email.join(' ');
          return 'Invalid email format';
        },
    }

}
</script>

<style lang="scss">

</style>