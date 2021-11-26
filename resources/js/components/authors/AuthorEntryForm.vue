<template>
 <div class="sd-author-entry-form" id="sd-author-entry-form">
  <b-form
    @submit.prevent="createAuthor"
    @reset.prevent="resetForm"
  >
        <b-form-group
        id="sd-firstname-input-group"
        label="First name:"
        label-for="sd-firstname-input"
      >
        <b-form-input
          size="sm"
          id="sd-firstname-input"
          v-model="firstname"
          required
          placeholder="First name"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-surname-input-group"
        label="Surname:"
        label-for="sd-surname-input"
      >
        <b-form-input
          size="sm"
          id="sd-surname-input"
          v-model="surname"
          required
          placeholder="Surname"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-email-input-group"
        label="Email address:"
        label-for="sd-email-input"
      >
        <b-form-input
          size="sm"
          id="sd-email-input"
          v-model="email"
          type="email"
          placeholder="Author's email address"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-department-name-input-group"
        label="Department name:"
        label-for="sd-department-name-input"
      >
        <b-form-input
          size="sm"
          id="sd-department-name-input"
          v-model="department_name"
          placeholder="Dept. name"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-institution-name-input-group"
        label="Institution name:"
        label-for="sd-institution-name-input"
      >
        <b-form-input
          size="sm"
          id="sd-institution-name-input"
          v-model="institution_name"
          placeholder="Institution name"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-orcid-input-group"
        label="Author's ORCID:"
        label-for="sd-orcid-input"
      >
        <b-form-input
          size="sm"
          id="sd-orcid-input"
          v-model="orcid"
          placeholder="ORCID"
        ></b-form-input>
      </b-form-group>

      <div class="sd-author-entry-form--button-wrapper">
        <b-button type="reset">Cancel Entry</b-button>
        <b-button type="submit" variant="success">{{ isEditing ? 'Save Edits' : 'Confirm Author' }}</b-button>
      </div>

  </b-form>
 </div>
</template>

<script>
import AuthorTypes from "@/definitions/AuthorTypes";

export default {

    name: 'AuthorEntryForm',
    components: { },
    props: {'details': Object},
    data(){
        return {
          editing: false,
          id: '',
          email: '',
          firstname: '',
          surname: '',
          department_name: '',
          institution_name: '',
          orcid: '',
        }

    }, /* end of data */
    computed: {
      isEditing(){
        return (this.editing === true)
      },
    },
    methods:{ //run as event handlers, for example
      createAuthor() {
        const newAuthor = {
          author_role: AuthorTypes.AUTHOR,
          email: this.email,
          firstname: this.firstname,
          surname: this.surname,
          department_name: this.department_name,
          institution_name: this.institution_name,
          orcid: this.orcid,
          origin: "external",
        };

        const id = this.id;

        if(this.isEditing) {
          this.$emit('modified', {id: id, author: newAuthor});
        } else {
          this.$emit('created', newAuthor);
        }

        this.resetForm();
      },
      resetForm() {
        this.editing = false;
        this.id = '';
        this.email = '';
        this.firstname = '';
        this.surname = '';
        this.department_name = '';
        this.institution_name = '';
        this.orcid = '';

        this.$emit('cancel', {});
      },
    },
    watch: {
      details(values) {
        this.editing = (values.hasOwnProperty('editing') && values.editing===true) ? true : false;
        this.id = values.id;
        this.email = values.email;
        this.firstname = values.firstname;
        this.surname = values.surname;
        this.department_name = values.department_name;
        this.institution_name = values.institution_name;
        this.orcid = values.orcid;
      },
    }

}
</script>

<style lang="scss" scoped>
  .sd-author-entry-form--button-wrapper {
    text-align: right;
  }
</style>