<template>
  <div class="panel-authors-edit--wrapper" id="panel-authors-edit--wrapper">
  <span v-if="modified">Author list has been modified, please save before closing.</span>
    <header class="panel-authors-edit--intro">
      <h6 class="panel-authors-edit--main-title h6">Add registered author</h6>
    </header>
    <section class="panel-authors-edit--add-author-wrapper">
      <author-multiselect @select="addUserAuthor" :initial-users="temporaryAuthorList"></author-multiselect>
      <author-types-table @close="closeAuthorTypesTable" v-if="showAuthorTypesTable"></author-types-table>
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
              <b-button v-if="a.origin==='external'" size="sm" variant="success" class="panel-authors-order-list-item--edit-button"><font-awesome-icon icon="edit" size="sm" @click="editExternalAuthor(a)"/></b-button>
              <b-button size="sm" variant="danger" class="panel-authors-order-list-item--remove-button"><font-awesome-icon icon="trash-alt" size="sm" @click="removeAuthor(a.order)"/></b-button>
            </div>
          </div>
          <em class="panel-authors-order-list-item--institution">{{a.institution_name}}</em>
          <b-form-radio-group
           :options="roleOptions"
            v-model="a.author_role"
            size="small"
            :disabled="a.origin==='external'"
            v-b-tooltip.hover="(a.origin==='external' ? 'External authors may only hold the author role.' : false)"
            >
          </b-form-radio-group>
         </li>
        </transition-group>
        </draggable>
    </section>
    <div class="panel-authors-edit--error-wrapper" v-if="correspondingAuthorCount<1">
      A minimum of 1 corresponding author is required.
    </div>
    <div class="panel-authors-edit--save-wrapper">
      <b-button
      :disabled="(expandedPanelAuthors.length < 1 && temporaryAuthorList.length <  1)
      || correspondingAuthorCount < 1 || !modified"
      variant="success"
      @click="saveAuthors"
      >Save Authors</b-button>
      <b-button @click="closeSidebar">Cancel</b-button>
    </div>
    <section class="panel-authors-edit--add-external-author-wrapper">
      <strong class="panel-authors-edit--external-title">Add an external author.</strong>
      <p>You may add an author who is not a member of SDash. They will be notified by email and invited to join SDash.</p>
      <author-entry-form @created="addExternalAuthor" @modified="saveExternalAuthorEdits" @cancel="cancelExternalAuthorEdits" :details="editedAuthor"></author-entry-form>
    </section>
  </div>
</template>

<script>

import { mapGetters, mapActions } from "vuex";
import AuthorTypes from "@/definitions/AuthorTypes";
import draggable from 'vuedraggable';
import AuthorMultiselect from '@/components/helpers/AuthorMultiselect';
import AuthorEntryForm from './AuthorEntryForm';
import AuthorTypesTable from './AuthorTypesTable';

export default {

    name: 'PanelAuthorsEditForm',
    components: { draggable, AuthorMultiselect, AuthorEntryForm, AuthorTypesTable },
    props: { },

    data(){

        return {
          temporaryAuthorList:[],
          editedAuthor:{},
          drag: false,
          roleOptions: [
            {text: "Author", value: AuthorTypes.AUTHOR},
            {text: "Corresponding author", value: AuthorTypes.CORRESPONDING},
            {text: "Curator", value: AuthorTypes.CURATOR},
          ],
          showAuthorTypesTable: true,
          modified: false,
          enableOnChangeHandler: false,
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
      correspondingAuthorCount() {
        return this.temporaryAuthorList.filter( author => author.author_role === AuthorTypes.CORRESPONDING).length;
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
      saveAuthors(closeAfterSave = true){

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
            if(closeAfterSave) this.closeSidebar();
          }
        ).catch(
          (error) => {
          const err = (error.data.hasOwnProperty('errors')) ? error.data.errors.authors[0] : error.data.MESSAGE;
          this.$snotify.error(err, "Error!");
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
      saveExternalAuthorEdits(userdata) {
        const authorIndex = this.temporaryAuthorList.findIndex( item => item.editing === true);
        const author = Object.assign({},this.temporaryAuthorList[authorIndex]);
        author.institution_name = userdata.author.institution_name;
        author.department_name = userdata.author.department_name;
        author.orcid = userdata.author.orcid;
        author.firstname = userdata.author.firstname;
        author.surname = userdata.author.surname;
        author.email = userdata.author.email;
        delete author.editing;
        this.temporaryAuthorList.splice(authorIndex,1,author);
        this.editedAuthor = {};
        this.scrollToForm();
        this.saveAuthors(false);

      },
      cancelExternalAuthorEdits() {
        const authorIndex = this.temporaryAuthorList.findIndex( item => item.editing === true);
        if(authorIndex >= 0) delete this.temporaryAuthorList[authorIndex].editing;
        this.scrollToForm();

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

        this.scrollToForm();

        this.saveAuthors(false);
      },
      editExternalAuthor(authorData) {

        document.getElementById('sd-author-entry-form').scrollIntoView({behavior: 'smooth'});

        for(let i = 0; i < this.temporaryAuthorList.length; i++) {
          if (this.temporaryAuthorList[i].id === authorData.id && this.temporaryAuthorList[i].order === authorData.order && this.temporaryAuthorList[i].origin === 'external') {
            this.temporaryAuthorList[i].editing = true;
          } else {
            if(this.temporaryAuthorList[i].hasOwnProperty('editing')) {
              delete this.temporaryAuthorList[i].editing;
            }
          }
        }


        this.editedAuthor = {
          editing: true,
          id: authorData.id,
          email: authorData.email,
          firstname: authorData.firstname,
          surname: authorData.surname,
          department_name: authorData.department_name,
          institution_name: authorData.institution_name,
          orcid: authorData.orcid,
        }
      },
      scrollToForm(){
        document.querySelector('#author-edit-sidebar .b-sidebar-body').scrollTo({top:0, behavior: 'smooth'});
      },
      closeAuthorTypesTable() {
        this.showAuthorTypesTable = false;
      }
    },
    created() {
      this.temporaryAuthorList = this.expandedPanelAuthors.map(author => ({...author}));
    },
    watch: {
      temporaryAuthorList: {
        handler() {
          if(this.enableOnChangeHandler) {
            this.modified = true;
          }
          this.enableOnChangeHandler = true;
        },
        deep: false,
      }
    },

}
</script>

<style lang="scss">
  .panel-authors-edit--add-author-wrapper {
    margin-bottom: 1rem;
  }
  .panel-authors-edit--wrapper {
    padding: 0;
  }

  .panel-authors-order-list {
    margin: 1rem 0;
    padding: 0;
    background-color: transparent;
}

  .panel-authors-order-list-item {
    display: block;
    list-style-type:none;
    margin:0 0 0.75rem 0;
    background-color: #2f3150;
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
  .panel-authors-order-list-item--remove-button,
  .panel-authors-order-list-item--edit-button
   {
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
  opacity: 0.45;
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

.panel-authors-edit--error-wrapper {
    border: dashed 2px #983a3a;
    line-height: 1.2;
    padding: 0.15rem 0.25rem;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
    background-color: #dab1b1;
    color: #983a3a;
}
</style>