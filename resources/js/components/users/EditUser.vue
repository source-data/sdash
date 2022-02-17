<template>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark text-light">
                    <div class="card-header">Edit Profile</div>

                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <h6>Mandatory Information</h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="firstname" class="col-md-4 col-form-label text-md-right">First Name</label>

                                <div class="col-md-6">
                                    <input id="firstname" type="text" class="form-control" :class="{'is-invalid': errors.firstname}" name="firstname" v-model="user.firstname" required autocomplete="firstname">
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstname">
                                        <strong>{{ errors.firstname[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="surname" class="col-md-4 col-form-label text-md-right">Surname</label>

                                <div class="col-md-6">
                                    <input id="surname" type="text" class="form-control" :class="{'is-invalid': errors.surname}" name="surname" v-model="user.surname" required autocomplete="surname">
                                    <span class="invalid-feedback" role="alert" v-if="errors.surname">
                                        <strong>{{ errors.surname[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" :class="{'is-invalid': errors.email}" name="email" v-model="user.email" required autocomplete="email">
                                    <span class="invalid-feedback" role="alert" v-if="errors.email">
                                        <strong>{{ errors.email[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <h6>Optional Information</h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="orcid" class="col-md-4 col-form-label text-md-right">ORCID</label>

                                <div class="col-md-6">
                                    <input id="orcid" type="text" class="form-control" :class="{'is-invalid': errors.orcid}" name="orcid" v-model="user.orcid">
                                    <span class="invalid-feedback" role="alert" v-if="errors.orcid">
                                        <strong>{{ errors.orcid[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="institution_name" class="col-md-4 col-form-label text-md-right">Institution Name</label>

                                <div class="col-md-6">
                                    <input id="institution_name" type="text" class="form-control" :class="{'is-invalid': errors.institution_name}" name="institution_name" v-model="user.institution_name">
                                    <span class="invalid-feedback" role="alert" v-if="errors.institution_name">
                                        <strong>{{ errors.institution_name[0] }}</strong>
                                    </span>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="institution_address" class="col-md-4 col-form-label text-md-right">Institution Address</label>

                                <div class="col-md-6">
                                    <input id="institution_address" type="text" class="form-control" :class="{'is-invalid': errors.institution_address}" name="institution_address" v-model="user.institution_address">
                                    <span class="invalid-feedback" role="alert" v-if="errors.institution_address">
                                        <strong>{{ errors.institution_address[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="department_name" class="col-md-4 col-form-label text-md-right">Department Name</label>

                                <div class="col-md-6">
                                    <input id="department_name" type="text" class="form-control" :class="{'is-invalid': errors.department_name}" name="department_name" v-model="user.department_name">
                                    <span class="invalid-feedback" role="alert" v-if="errors.department_name">
                                        <strong>{{ errors.department_name[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="linkedin" class="col-md-4 col-form-label text-md-right">LinkedIn Profile</label>

                                <div class="col-md-6">
                                    <input id="linkedin" type="text" class="form-control" :class="{'is-invalid': errors.linkedin}" name="linkedin" v-model="user.linkedin">
                                    <span class="invalid-feedback" role="alert" v-if="errors.linkedin">
                                        <strong>{{ errors.linkedin[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="twitter" class="col-md-4 col-form-label text-md-right">Twitter Feed</label>

                                <div class="col-md-6">
                                    <input id="twitter" type="text" class="form-control" :class="{'is-invalid': errors.twitter}" name="twitter" v-model="user.twitter">
                                    <span class="invalid-feedback" role="alert" v-if="errors.twitter">
                                        <strong>{{ errors.twitter[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button class="btn btn-primary" type="button" :disabled="submiting" @click="updateUserData">
                                        <i class="fas fa-spinner fa-spin" v-if="submiting"></i> Save
                                    </button>
                                    <button class="btn btn-light" type="button" @click="cancelEdit">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <b-row class="justify-content-center">
            <b-col md="8">
                <b-card bg-variant="dark" text-variant="light" no-body class="mt-2">
                    <b-card-header>
                        Change Your Password
                    </b-card-header>
                    <b-card-body>
                        <b-form @submit.prevent>
                            <b-form-group
                            label-align-md="right"
                            label-cols-md="4"
                            content-cols-md="6"
                            id="change-password-1"
                            label="Current password"
                            label-for="existing-password-input"
                            valid-feedback="Password entered."
                            :invalid-feedback="invalidExistingPasswordFeedback"
                            :state="validExistingPassword"
                            >
                            <b-form-input
                            id="existing-password-input"
                            v-model="existingPassword"
                            :state="validExistingPassword"
                            type="password"
                            @change="clearValidationErrors('existingPassword')"
                            trim
                            ></b-form-input>
                            </b-form-group>

                            <b-form-group
                            label-cols-md="4"
                            content-cols-md="6"
                            label-align-md="right"
                            id="change-password-new-1"
                            label="Your new password"
                            label-for="new-password-input-1"
                            valid-feedback="OK."
                            :invalid-feedback="invalidNewPassword1Feedback"
                            :state="validNewPassword1"
                            >
                            <b-form-input
                            id="new-password-input-1"
                            v-model="newPassword1"
                            :state="validNewPassword1"
                            type="password"
                            @change="clearValidationErrors('newPassword1')"
                            trim></b-form-input>
                            </b-form-group>

                            <b-form-group
                            label-cols-md="4"
                            content-cols-md="6"
                            label-align-md="right"
                            id="change-password-new-2"
                            label="Your new password"
                            label-for="new-password-input-2"
                            valid-feedback="OK."
                            :invalid-feedback="invalidNewPassword2Feedback"
                            :state="validNewPassword2"
                            >
                            <b-form-input
                            id="new-password-input-2"
                            v-model="newPassword2"
                            :state="validNewPassword2"
                            type="password"
                            @change="clearValidationErrors('newPassword2')"
                            trim></b-form-input>
                            </b-form-group>
                            <div class="form-group row mb-0">
                                <b-col md="6" offset-md="4">
                                    <b-button variant="primary"  @click="updatePassword" :disabled="disablePasswordButton"><i class="fas fa-spinner fa-spin" v-if="submiting"></i> Change password</b-button>
                                    <b-button variant="light" @click="clearPasswords">Clear</b-button>

                                </b-col>
                            </div>
                        </b-form>
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row>
</div>
</template>

<script>

import Axios from "axios"
import { mapGetters, mapActions, mapMutations } from 'vuex'

export default {
    name: 'EditUser',
    props: ["user_slug"],
    data() {
        return {
            user: {},
            errors: {},
            passwordErrors: {},
            submiting: false,
            existingPassword: "",
            newPassword1: "",
            newPassword2: "",
        }
    },
    computed: {
        ...mapGetters([
            'currentUser',
        ]),
        validExistingPassword() {
            if(this.passwordErrors.existingPassword) return false;
            if(!this.existingPassword) return null;
            return this.existingPassword.length >= 8;
        },
        validNewPassword1() {
            if(this.passwordErrors.newPassword1) return false;
            if(!this.newPassword1) return null;
            return (this.newPassword1.length >= 8 && this.newPassword1 === this.newPassword2);
        },
        validNewPassword2() {
            if(this.passwordErrors.newPassword2) return false;
            if(!this.newPassword2) return null;
            return (this.newPassword2.length >= 8 && this.newPassword1 === this.newPassword2);
        },
        invalidExistingPasswordFeedback() {
            if(this.passwordErrors.existingPassword) return this.passwordErrors.existingPassword.join(' ');
            return 'Password must be at least 8 characters';
        },
        invalidNewPassword1Feedback() {
            if(this.passwordErrors.newPassword1) return this.passwordErrors.newPassword1.join(' ');
            if(this.newPassword1.length < 8) return "The password must be at least 8 characters";
            if(this.newPassword1 !== this.newPassword2) return "Enter the same new password to confirm."
        },
        invalidNewPassword2Feedback() {
            if(this.passwordErrors.newPassword2) return this.passwordErrors.newPassword2.join(' ');
            if(this.newPassword2.length < 8) return "The password must be at least 8 characters";
            if(this.newPassword1 !== this.newPassword2) return "Enter the same new password to confirm."
        },
        disablePasswordButton() {
            return !(!this.submiting && this.validExistingPassword && this.validNewPassword1 && this.validNewPassword2);
        },
    },
    created() {
        let user_slug = this.user_slug;
        if (!this.currentUser || ((this.currentUser.user_slug !== user_slug) && (this.currentUser.role !== 'superadmin'))) {
            this.$snotify.error("You don't have permission to do that", "Access Denied")
            this.$router.push({path: '/'})
            return
        }
        this.getUserData()
    },
    methods: {
        // set the global user record
        ...mapMutations(['setCurrentUser']),
        getUserData() {
            Axios.get("/users/" + this.user_slug)
                .then(response => {
                    this.user = response.data.DATA
                })
        },
        updateUserData() {
            this.submiting = true
            Axios.patch("/users/" + this.user_slug, this.user)
                .then(response => {
                    this.errors = {}
                    this.submiting = false
                    if(this.user_slug === this.currentUser.user_slug) {
                        this.setCurrentUser(response.data.DATA);
                    }
                    this.$router.push({path: `/user/${this.user_slug}`})
                })
                .catch(error => {
                    this.errors = error.data.errors
                    console.log(this.errors)
                    this.submiting = false
                })
        },
        updatePassword() {
            this.submitting = true;
            Axios.patch('/users/' + this.user_slug + '/password', {existingPassword: this.existingPassword, newPassword1: this.newPassword1, newPassword2: this.newPassword2}).then(response => {
                this.passwordErrors = {};
                this.submiting = false;
                this.clearPasswords();
                this.$snotify.success("Password changed.", "OK!");
                console.log(response);
            }).catch(error => {
                this.passwordErrors = error.data.errors;
                this.submiting = false;
                console.log(error);
            });

        },
        clearValidationErrors(toClear) {
            if(this.passwordErrors[toClear]) {
                let tempErrors = Object.assign({}, this.passwordErrors);
                delete tempErrors[toClear];
                this.passwordErrors = tempErrors;
            }
        },
        cancelEdit() {
            this.$router.push({path: `/user/${this.user_slug}`})
        },
        clearPasswords() {
            this.passwordErrors = {};
            this.existingPassword = '';
            this.newPassword1 = '';
            this.newPassword2 = '';

        },
    }
}
</script>
