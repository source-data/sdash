<template>
  <div
    id="sd-feedback-widget"
    class="bg-dark-desat text-light"
    :class="{ 'visible' : isVisible }"
  >
    <button id="sd-show-feedback-widget" class="btn btn-primary" @click="isVisible=!isVisible">
      Give Feedback
    </button>

    <b-form @submit.stop.prevent :aria-hidden="!isVisible">
      <h4 hidden>
        Feedback
      </h4>

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
          placeholder="My feedback to this..."
          rows="6"
          :disabled="submitting"
          :state="enableSubmission"
        ></b-form-textarea>
      </b-form-group>

      <b-form-group>
        <b-button
          class="sd-feedback-widget--submit"
          :disabled="submitting || !enableSubmission"
          variant="info"
          @click.prevent="sendFeedback"
        >
          Submit
        </b-button>

        <b-button
          class="sd-feedback-widget--cancel"
          variant="secondary"
          @click.prevent="cancelFeedback"
          :disabled="submitting"
        >
          Cancel
        </b-button>
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

<style lang="scss" scoped>
@import 'resources/sass/_layout.scss';
@import 'resources/sass/_text.scss';

#sd-feedback-widget {
  position: fixed;
  width: 340px;
  padding: 1.5rem;
  top: 5%;
  right: -340px;
  z-index: $feedback-widget-z-index;
  transition: right 0.25s ease-in;
  box-shadow: 2px 2px 4px rgba(0,0,0,0.4);
  border-top-left-radius: 0.5rem;
}
#sd-feedback-widget.visible {
  right: 0;
}
@media (min-height: 450px) {
  #sd-feedback-widget {
    top: 20%;
  }
}

#sd-show-feedback-widget {
  box-shadow: 0 0 1px #555;
  font-size: $font-size-xs;
  padding: 0.125rem 1rem;

  /* Rotate the element so the text reads from bottom to top. Rotating the element around its own bottom-left corner
   * and positioning it at its parent's bottom-left corner lets it appear just outside and flush with the bottom and
   * left borders of its parent.
   */
  transform: rotate(270deg);
  transform-origin: bottom left;

  position: absolute;
  left: 0;
  bottom: 0;

  z-index: -1;

  /* this makes the top-left and top-right corners rounded, but these rounded corners end up on the bottom-left and
   * top-left due to the element's rotation.
   */
  border-radius: 0.5rem 0.5rem 0 0;
}
#sd-show-feedback-widget:focus {
  outline: inherit;
  border: inherit;
  border-bottom: none;
}
@media (min-width: 768px) {
  #sd-show-feedback-widget {
    font-size: 1.25rem;
    padding: 0.125rem 1rem;
  }
}
</style>