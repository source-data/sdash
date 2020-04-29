<template>
    <div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

import Axios from "axios"

export default {
    name: 'EditUser',
    props: ["user_id"],
    data() {
        return {
            user: {},
            errors: {},
            submiting: false
        }
    },
    created() {
        this.getUserData()
    },
    methods: {
        getUserData() {
            Axios.get("/users/" + this.user_id)
                .then(response => {
                    this.user = response.data.DATA
                })
        },
        updateUserData() {
            this.submiting = true
            Axios.patch("/users/" + this.user_id, this.user)
                .then(response => {
                    this.errors = {}
                    this.submiting = false
                })
                .catch(error => {
                    this.errors = error.data.errors
                    console.log(this.errors)
                    this.submiting = false
                })
        }
    }
}
</script>
