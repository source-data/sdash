<template>
  <b-modal
      id="author-edit-sidebar"
      body-bg-variant="dark"
      body-text-variant="light"
      header-bg-variant="dark"
      header-text-variant="light"
      header-class="sd-author-modal-header"
      hide-footer
      scrollable
      title="Edit List of Authors"
      v-model="showAuthorSidebarModel"
      no-close-on-backdrop
  >
    <template #modal-header>
    <!-- header with close button -->
        <h5 id="author-edit-sidebar___BV_modal_title_" class="modal-title">Edit List of Authors</h5>
    </template>
    <panel-authors-edit-form></panel-authors-edit-form>
  </b-modal>
</template>

<script>

import PanelAuthorsEditForm from '@/components/authors/PanelAuthorsEditForm';
import { mapGetters, mapActions } from "vuex";

export default {

    name: 'PanelAuthorsEditModal',
    components: { PanelAuthorsEditForm },
    data(){
      return {

      }
    }, /* end of data */
    computed: {
      ...mapGetters([
        "showAuthorSidebar",
      ]),
      showAuthorSidebarModel: {
          set(value) {
              this.$store.commit('setAuthorSidebar', value)
          },
          get() {
              return this.showAuthorSidebar
          }
      },
    },
    methods:{ //run as event handlers, for example

    }

}
</script>

<style lang="scss">

</style>