<template>
  <v-tour name="guided-tour" :steps="steps" :callbacks="callbacks"></v-tour>
</template>

<script>

import {setShowGuidedTour} from '@/services/GuidedTourService';

require('vue-tour/dist/vue-tour.css');

export default {

    name: 'GuidedTour',
    data(){

        return {
            steps: [
              {
                target: '#sd-upload-new-panel',
                header: {
                  title: 'Welcome to SDash!'
                },
                content: 'Upload an image to create your own SmartFigure.',
                params: {
                  placement: 'left',
                  enableScrolling: false,
                }
              },
              {
                target: '#sd-panel-privacy-group',
                header: {
                  title: 'See more SmartFigures!'
                },
                content: 'Switch to "Show all SmartFigures" to see work from other users.',
                params: {
                  placement: 'right',
                  enableScrolling: true,
                },
                before() {
                  return new Promise((resolve, reject) => {
                    if (localStorage.getItem("isSidebarExpanded") === null || localStorage.getItem("isSidebarExpanded") === 'false') {
                      document.getElementById('sd-panel-filters-toggle').click();
                    }
                    resolve();
                   });
                }
              },
            ],
            callbacks: {
              onSkip: this.rememberTourHasEnded,
              onStop: this.rememberTourHasEnded,
              onFinish: this.rememberTourHasEnded,
            }

        }

    }, /* end of data */
    methods: {
      rememberTourHasEnded() {
        setShowGuidedTour(false)
      }
    },
    mounted() {
      this.$tours['guided-tour'].start();
    },
}
</script>

<style lang="scss">

</style>