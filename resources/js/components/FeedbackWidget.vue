<template>
  <div class="sd-feedback-widget" :class="{ 'sd-feedback-widget__visible' : isVisible }" >
    <button class="sd-feedback-widget--show" @click="isVisible=!isVisible">
      Give Feedback
    </button>
    <b-form @submit.stop.prevent :aria-hidden="!isVisible">
      <h4>Feedback</h4>
      <p>Please use this form to provide us with any feedback on SDash. Feedback is not anonymous - your name and email address will be sent to the site administrator.</p>
      <b-form-group
        id="sd-feedback-widget--message-wrapper"
        label="What would you like us to know?"
        label-for="sd-feedback-widget--message"
        :state="enableSubmission"
        :invalid-feedback="invalidMessage"
        :valid-feedback="validMessage"
      >
        <b-form-textarea
          id="sd-feedback-widget--message"
          v-model="message"
          placeholder="Write your feedback here."
          rows="6"
          :disabled="submitting"
          :state="enableSubmission"
        >

        </b-form-textarea>
      </b-form-group>
      <b-form-group>
        <b-button class="sd-feedback-widget--submit" :disabled="submitting || !enableSubmission" variant="success" @click.prevent="sendFeedback">Submit</b-button>
        <b-button class="sd-feedback-widget--cancel"  variant="secondary" @click.prevent="cancelFeedback" :disabled="submitting">Cancel</b-button>
      </b-form-group>
    </b-form>
  </div>

</template>

<script>

import submitFeedback from '@/services/submitFeedback'

export default {

    name: 'FeedbackWidget',
    data(){

        return {
            message: "",
            isVisible: false,
            submitting: false,
        }

    }, /* end of data */
    computed: {

      enableSubmission() {
        return (this.message.length >= 12 && this.message.length <=400 ) && (!/<[^>]+>/i.test(this.message))
      },

      invalidMessage() {
        if(!(this.message.length >= 12 && this.message.length <=400)) return "Min 12 characters, max 400 characters"
        if(/<[^>]+>/i.test(this.message)) return "No HTML tags"
        return ""
      },
      validMessage() {
        if (this.enableSubmission) return "Input valid."
      }

    },

    methods:{ //run as event handlers, for example
        sendFeedback () {
          this.submitting = true
          submitFeedback(this.message)
            .then((response) => {
              this.$snotify.success("Your feedback has been sent.", "Thank you!")
              this.clearInputs()
            })
            .catch(error => {
              this.$snotify.error("Could not send feedback.", "Sorry!")
            })
            .finally(() => { this.submitting = false })
        },

        cancelFeedback () {
            this.clearInputs()
        },
        clearInputs(){
          this.message = ""
          this.isVisible = false
          this.submitting = false
        }

    }

}
</script>

<style lang="scss">
  .sd-feedback-widget {
    position:fixed;
    box-sizing: border-box;
    border: solid 2px #0c93af;
    width: 340px;
    padding: 1.5rem;
    top:20%;
    right:-340px;
    z-index:999999;
    background-color: #efefef;
    transition: right 0.25s ease-in;
    box-shadow: 2px 2px 4px rgba(0,0,0,0.4);

    p, label {
      color: darken(#0c93af, 10%);
    }

  }

  .sd-feedback-widget.sd-feedback-widget__visible {
    right:0;
  }

  .sd-feedback-widget--show {
    position: absolute;
    transform: rotate(270deg);
    left:-102px;
    top: 5rem;
    font-size: 1.25rem;
    border: solid 2px #0c93af;
    color: #fff;
    border-radius: 0.25rem 0.25rem 0 0;
    border-bottom: none;
    background-color: #0c93af;
    padding: 0.125rem 1rem;
    z-index:-1;
  }

  .sd-feedback-widget--show:focus {
    outline:inherit;
    border:inherit;
    border-bottom: none;
  }

</style>