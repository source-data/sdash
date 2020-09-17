<template>
  <div class="panel-authors-edit--wrapper">
    <header class="panel-authors-edit--intro">
    <p>Edit the authors assigned to this panel.</p>
    <p>Note that adding an author assigns certain permissions to the user.</p>
    <ul class="panel-authors-edit--intro-roles">
      <li><strong>Author (Au)</strong> - can see the panel and its details.</li>
      <li><strong>Corresponding Author (Cor)</strong> - can edit the panel and details.</li>
      <li><strong>Curator (Cur)</strong> - can edit the panel and its details but is not listed in the author list.</li>
    </ul>
    </header>
    <section class="panel-authors-edit--add-author-wrapper">
      <strong class="panel-authors-edit--reorder-title">Search for Authors by Name.</strong>
      <author-multiselect @select="addUserAuthor" :initial-users="temporaryAuthorList"></author-multiselect>
    </section>
    <section class="panel-authors-edit--list-wrapper" v-if="temporaryAuthorList.length > 0">
        <strong class="panel-authors-edit--reorder-title">Drag authors to reorder.</strong>
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
          <div class="panel-authors-order-list-item--top-bar">
            <strong class="panel-authors-order-list-item--name">{{a.firstname}} {{a.surname}}</strong>
            <div class="panel-authors-order-list-item--remove">
              <b-button size="sm" variant="danger" class="panel-authors-order-list-item--remove-button"><font-awesome-icon icon="trash-alt" size="sm" @click="removeAuthor(a.order)"/></b-button>
            </div>
          </div>
          <em class="panel-authors-order-list-item--institution">{{a.institution_name}}</em>
          <b-form-radio-group
           :options="roleOptions"
            v-model="a.author_role"
            size="small"
            :disabled="a.origin==='external'"
            >
          </b-form-radio-group>
         </li>
        </transition-group>
        </draggable>
    </section>
    <div class="panel-authors-edit--save-wrapper">
      <b-button :disabled="expandedPanelAuthors.length < 1 && temporaryAuthorList.length <  1" variant="success" @click="saveAuthors">Save Authors</b-button>
      <b-button @click="closeSidebar">Cancel</b-button>
    </div>
    <section class="panel-authors-edit--add-external-author-wrapper">
      <strong class="panel-authors-edit--external-title">Add an external author.</strong>
      <p>You may add an author who is not a member of SDash. They will be notified by email and invited to join SDash.</p>
      <author-entry-form @created="addExternalAuthor"></author-entry-form>
    </section>
  </div>
</template>

<script>

import { mapGetters, mapActions } from "vuex";
import AuthorTypes from "@/definitions/AuthorTypes";
import draggable from 'vuedraggable';
import AuthorMultiselect from '@/components/helpers/AuthorMultiselect';
import AuthorEntryForm from './AuthorEntryForm';

export default {

    name: 'PanelAuthorsEditForm',
    components: { draggable, AuthorMultiselect, AuthorEntryForm },
    props: { },

    data(){

        return {
          temporaryAuthorList:[],
          drag: false,
          roleOptions: [
            {text: "Au.", value: AuthorTypes.AUTHOR},
            {text: "Corr.", value: AuthorTypes.CORRESPONDING},
            {text: "Cur.", value: AuthorTypes.CURATOR},
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

    methods:{
      ...mapActions([
        'updateExpandedPanelAuthors',
        'setLoadingState',
        ]),
      removeAuthor(order) {
        let index = this.temporaryAuthorList.findIndex(author => author.order === order);
        if (index > -1) this.temporaryAuthorList.splice(index,1);
      },
      saveAuthors(){

        let newAuthorList = [];

        for (let i = 0; i < this.temporaryAuthorList.length ; i++) {
          newAuthorList.push({
            id: this.temporaryAuthorList[i].id,
            author_role: this.temporaryAuthorList[i].author_role,
            institution_name: this.temporaryAuthorList[i].institution_name,
            department_name: this.temporaryAuthorList[i].department_name,
            orcid: this.temporaryAuthorList[i].orcid,
            email: this.temporaryAuthorList[i].email,
            firstname: this.temporaryAuthorList[i].firstname,
            surname: this.temporaryAuthorList[i].surname,
            order: i,
            origin: (this.temporaryAuthorList[i].origin) ? this.temporaryAuthorList[i].origin : 'users',
          })
        };

        this.updateExpandedPanelAuthors({authors: newAuthorList}).then(
          (response) => {
            this.$snotify.success("Authors updated.", "OK!")
          }
        ).catch(
          (error) => {
          const err = (error.data.hasOwnProperty('errors')) ? error.data.errors.authors[0] : error.data.MESSAGE;
          this.$snotify.error(err, "Error!");
        }).finally(()=>{
          this.closeSidebar();
        });
      },
      closeSidebar(){
        this.$store.commit("setAuthorSidebar", false);
      },
      addUserAuthor(userdata) {

        const newAuthor = {
          id: userdata.id,
          institution_name: userdata.institution_name,
          department_name: userdata.department_name,
          orcid: userdata.orcid,
          firstname: userdata.firstname,
          surname: userdata.surname,
          email: userdata.email,
          author_role: AuthorTypes.AUTHOR,
          order: this.temporaryAuthorList.length,
          };

        this.temporaryAuthorList.push(newAuthor);
      },
      addExternalAuthor(userdata) {
        const newAuthor = {
          institution_name: userdata.institution_name,
          department_name: userdata.department_name,
          orcid: userdata.orcid,
          firstname: userdata.firstname,
          surname: userdata.surname,
          email: userdata.email,
          author_role: userdata.author_role,
          origin: userdata.origin,
          order: this.temporaryAuthorList.length,
          };

        this.temporaryAuthorList.push(newAuthor);
      },
    },
    created() {
      this.temporaryAuthorList = this.expandedPanelAuthors.slice();
    },

}
</script>

<style lang="scss">
  .panel-authors-edit--wrapper {
    padding: 1rem 4rem 1rem 2rem;
  }

  .panel-authors-order-list {
    margin: 1rem 0;
    padding: 0.4rem;
    background-color: #666;
}

  .panel-authors-order-list-item {
    display: block;
    list-style-type:none;
    margin:0 0 0.5rem 0;
    background-color: #343a40;
    padding: 0.5rem;
    cursor:move;
  }
  .panel-authors-order-list-item:last-child {
    margin-bottom:0;
  }

  .panel-authors-order-list-item--top-bar {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    position: relative;
  }
  .panel-authors-order-list-item--remove-button {
    padding: 0.25rem 0.25rem;
    font-size: 0.875rem;
    line-height: 0.5;
  }

  .order-authors-move {
    transition: transform 0.5s;
  }

  .no-move {
  transition: transform 0s;
  }

  .ghost {
  opacity: 0.6;
  box-shadow:0 0 1rem blue;
}

.panel-authors-order-list-item--name {
    display: block;
    line-height: 1;
}

.panel-authors-order-list-item--institution {
  font-size:0.85em;
}

.panel-authors-order-list-item--remove {
    position: absolute;
    right: -4px;
    top: -5px;
}

.panel-authors-edit--add-external-author-wrapper {
  margin: 1rem 0;
}


</style>