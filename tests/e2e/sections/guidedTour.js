var tourCommands = {
  clickNext: function () {
    return this.verify.visible('@nextButton')
      .click('@nextButton')
  },
  clickSkip: function () {
    return this.verify.visible('@skipButton')
      .click('@skipButton')
  },
  clickPrev: function () {
    return this.verify.visible('@prevButton')
      .click('@prevButton')
  },
  clickStop: function () {
    return this.verify.visible('@stopButton')
      .click('@stopButton')
  },
};

module.exports = {
  selector: '.v-tour',
  commands: [tourCommands],
  elements: {
    nextButton: 'button.v-step__button-next',
    skipButton: 'button.v-step__button-skip',
    prevButton: 'button.v-step__button-previous',
    stopButton: 'button.v-step__button-stop',
  }
};