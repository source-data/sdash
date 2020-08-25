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
        <draggable
        tag="ul"
        class="panel-authors-order-list"
        v-model="temporaryAuthorList"
        v-bind="dragOptions"
        @start="drag=true"
        @end="drag=false"

        >
        <transition-group type="transition" :name="!drag ? 'order-authors' : null">
         <li v-for="a in temporaryAuthorList" :key="a.order" class="panel-authors-order-list-item">
          <strong class="panel-authors-order-list-item--name">{{a.firstname}} {{a.surname}}</strong>
          <em class="panel-authors-order-list-item--institution">{{a.institution_name}}</em>
          <b-form-radio-group :options="roleOptions"></b-form-radio-group>
           <!-- order: {{a.order}}, role: {{a.author_role}} -->
         </li>
        </transition-group>
        </draggable>
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
          drag: false,
          roleOptions: [
            {text: "Author", value: AuthorTypes.AUTHOR},
            {text: "Corresponding Author", value: AuthorTypes.CORRESPONDING},
            {text: "Curator", value: AuthorTypes.CURATOR},
          ],
        }

    }, /* end of data */
    computed: {
      ...mapGetters([
        "expandedPanelAuthors"
      ]),
    dragOptions() {
      return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      };
    },
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

  .panel-authors-order-list-item {
    display: block;
    list-style-type:none;
    margin:0 0 0.5rem 0;
    background-color: #4a538c;
    padding: 0.5rem;
    cursor:move;
  }

  .order-authors-move {
    transition: transform 0.5s;
  }

  .no-move {
  transition: transform 0s;
  }

  .ghost {
  opacity: 0.6;
  box-shadow:0 2px 1rem -2px yellow;
}

.panel-authors-order-list-item--name {
    display: block;
    line-height: 1;
    color: #d6dfee;
}
</style>