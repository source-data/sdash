<template>
<div class="sd-email-confirmation-warning bg-warning">
 <b-container>
  <b-row>
    <b-col class="pt-3 pb-3">
      Your access to SDash is heavily restricted until you verify your email address. Please check your email inbox and follow the link in the email.
    </b-col>
    <b-col md="2" class="pt-3 pb-3 text-md-right">
      <b-button variant="primary" @click="resendConfirmationEmail">Resend email</b-button>
    </b-col>
  </b-row>
 </b-container>
</div>
</template>

<script>

export default {

    name: 'EmailConfirmationNotice',
    methods:{
        resendConfirmationEmail(){
          this.$store.dispatch('resendEmail').then(response => {
            this.$snotify.success('Email resent', 'OK!');
          }).catch(error => {
            this.$snotify.error('Could not resend email.', 'Sorry!');
          });
        }
    }

}
</script>

<style lang="scss">

</style>