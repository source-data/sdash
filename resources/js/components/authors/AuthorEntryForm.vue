<template>
 <div class="sd-author-entry-form">
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
          required
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
          required
          placeholder="Dept. name"
        ></b-form-input>
      </b-form-group>

        <b-form-group
        id="sd-institution-name-input-group"
        label="institution name:"
        label-for="sd-institution-name-input"
      >
        <b-form-input
          size="sm"
          id="sd-institution-name-input"
          v-model="institution_name"
          required
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
          required
          placeholder="ORCID"
        ></b-form-input>
      </b-form-group>

      <b-button type="submit" variant="success">Create Author</b-button>
      <b-button type="reset">Cancel Entry</b-button>

  </b-form>
 </div>
</template>

<script>
import AuthorTypes from "@/definitions/AuthorTypes";

export default {

    name: 'AuthorEntryForm',
    components: { },
    props: [],
    data(){
        return {
          email: '',
          firstname: '',
          surname: '',
          department_name: '',
          institution_name: '',
          orcid: '',
        }

    }, /* end of data */

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

        this.$emit('created', newAuthor);
        this.resetForm();
      },
      resetForm() {
        this.email = '';
        this.firstname = '';
        this.surname = '';
        this.department_name = '';
        this.insitution_name = '';
        this.orcid = '';
      },
    }

}
</script>

<style lang="scss">

</style>