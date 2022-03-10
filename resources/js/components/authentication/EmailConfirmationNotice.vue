<template>
  <div class="sd-email-confirmation-warning">
    <p>
      To use SDash, please click the verification button in the email we sent to <b>{{ this.email }}</b>.
    </p>
    <p>
      Email not received? Check your spam folder or <b-button id="sd-resend-verification-email" variant="link" @click.prevent="resendConfirmationEmail" :disabled="disableButton">re-send it!</b-button>
    </p>
  </div>
</template>

<script>
import { mapActions, mapMutations } from 'vuex';
export default {

    name: 'EmailConfirmationNotice',
    props: {
      email: {
        type: String,
        required: true,
      }
    },
    data(){
      return {
        sending: false,
      }
    },
    computed: {
      disableButton() {
        return this.sending === true;
      }
    },
    methods:{
        ...mapActions(['resendEmail']),
        ...mapMutations(['setEmailConfirmationNotice']),
        resendConfirmationEmail(){
          this.sending = true;
          this.resendEmail(this.email).then(response => {
            this.$snotify.success(`Verification email resent to ${this.email}`, 'OK!');
          }).catch(error => {
            this.$snotify.error('Could not resend verification email.', 'Sorry!');
          }).finally(() => {
            this.sending = false;
            this.setEmailConfirmationNotice(false);
          });
        }
    }

}
</script>

<style lang="scss">
#sd-resend-verification-email {
  border: none;
  padding: 0;
  vertical-align: baseline;
}
</style>