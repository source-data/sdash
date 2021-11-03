<template>
<div class="sd-email-confirmation-warning">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols md="6">
                <b-card
                header="Please verify your email address!"
                >
                <b-card-text>
                  You cannot log in until you have verified your email address. Please check the email inbox for the registered email address.
                </b-card-text>
                <b-card-text>
                  If you cannot find the verification email, please check your spam folder. You can also re-send the email if needed.
                </b-card-text>
                <b-card-text>
                  <b-form-group
                    id="sd-email-confirmation-notice-fields"
                    valid-feedback="Allowed"
                    :invalid-feedback="invalidFeedback"
                    :state="state"
                  >
                  <b-input-group>
                    <b-form-input
                      :state="state"
                      placeholder="Email address"
                      debounce="300"
                      v-model="email"
                      type="email"
                    ></b-form-input>
                    <b-input-group-append>
                      <b-button variant="primary" @click.prevent="resendConfirmationEmail" :disabled="disableButton">Resend</b-button>
                    </b-input-group-append>
                  </b-input-group>
                  </b-form-group>
                </b-card-text>
                </b-card>
            </b-col>

        </b-row>
    </b-container>
  </div>
</template>

<script>

import EmailFormatValidator from '@/services/EmailFormatValidator';

export default {

    name: 'EmailConfirmationNotice',
    data(){
      return {
        email: '',
        sending: false,
      }
    },
    computed: {
      invalidFeedback() {
        if(!this.email.length) return '';
        if(!EmailFormatValidator.validate(this.email)) return "Invalid email format";
      },
      state() {
        if(!this.email.length) return null;
        return EmailFormatValidator.validate(this.email);
      },
      disableButton() {
        return (this.sending === true || this.state !== true);
      }
    },
    methods:{
        resendConfirmationEmail(){
          this.sending = true;
          this.$store.dispatch('resendEmail', this.email).then(response => {
            this.$snotify.success('Email resent', 'OK!');
          }).catch(error => {
            this.$snotify.error('Could not resend email.', 'Sorry!');
          }).finally(() => {
            this.sending = false;
          });
        }
    }

}
</script>

<style lang="scss">

</style>