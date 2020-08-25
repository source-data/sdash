<template>
  <div class="panel-authors-edit--wrapper">
    <header class="panel-authors-edit--intro">
    <p>Edit the authors assigned to this panel.</p>
    <p>Note that adding an author assigns certain permissions to the user.</p>
    <ul class="panel-authors-edit--intro-roles">
      <li><strong>Author</strong> - can see the panel and its details.</li>
      <li><strong>Corresponding Author</strong> - can edit the panel and details.</li>
      <li><strong>Curator</strong> - can see the panel and its details but is not listed in the author list.</li>
    </ul>
    </header>
    <section class="panel-authors-edit--list-wrapper" v-if="temporaryAuthorList">
      <ul class="panel-authors-order-list">
        <draggable v-model="temporaryAuthorList">
        <li v-for="a in temporaryAuthorList">{{a.firstname}} {{a.surname}} [order: {{a.order}}, role: {{a.author_role}}]</li>
        </draggable>
      </ul>
    </section>
  </div>
</template>

<script>

import { mapGetters, mapActions } from "vuex";
import AuthorTypes from "@/definitions/AuthorTypes";
import draggable from 'vuedraggable';

export default {

    name: 'PanelAuthorsEditForm',
    components: { draggable },
    props: { },

    data(){

        return {
          temporaryAuthorList:[],
        }

    }, /* end of data */
    computed: {
      ...mapGetters([
        "expandedPanelAuthors"
      ]),
    },

    methods:{ //run as event handlers, for example

    },
    created() {
      this.temporaryAuthorList = this.expandedPanelAuthors;
    },

}
</script>

<style lang="scss">
  .panel-authors-edit--wrapper {
    padding: 1rem 4rem 1rem 2rem;
  }

  .panel-authors-edit--intro-roles {
    strong {
    }
  }
</style>