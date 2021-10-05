<template>
  <section id="sd-registration-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols md="8">
                <b-card header="Register a new account">
                    <b-form
                        @submit.prevent="sendRegistration"
                    >
                        <b-row>
                            <b-col>
                                <h6>Mandatory information</h6>
                                <hr>
                            </b-col>
                        </b-row>

                        <!-- first name -->
                        <b-form-group
                            label="First name / given name*"
                            label-for="sd-register__firstname"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="firstName" :disabled="formDisabled" id="sd-register__firstname"></b-form-input>
                        </b-form-group>


                        <!-- surname -->
                        <b-form-group
                            label="Surname / family name*"
                            label-for="sd-register__surname"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="surname" :disabled="formDisabled" id="sd-register__surname"></b-form-input>
                        </b-form-group>

                        <!-- email -->
                        <b-form-group
                            label="Email address*"
                            label-for="sd-register__email"
                            label-cols-sm="4"
                            label-align-sm="right"
                            :invalid-feedback="emailFeedback"
                            :state="emailCheck"
                        >
                            <b-form-input
                            debounce="500"
                            v-model="email"
                            :disabled="formDisabled"
                            id="sd-register__email"
                            type="email"></b-form-input>
                        </b-form-group>

                        <!-- password 1 -->
                        <b-form-group
                            label="Password*"
                            label-for="sd-register__password"
                            label-cols-sm="4"
                            label-align-sm="right"
                            :invalid-feedback="password1Feedback"
                            :state="passwordCheck"
                        >
                            <b-form-input
                            v-model="password1"
                            autocomplete="new-password"
                            :disabled="formDisabled"
                            id="sd-register__password"
                            type="password"
                            debounce="500"
                            ></b-form-input>
                        </b-form-group>

                        <!-- password 2 -->
                        <b-form-group
                            label="Confirm password*"
                            label-for="sd-register__confirm-password"
                            label-cols-sm="4"
                            label-align-sm="right"
                            :invalid-feedback="password2Feedback"
                            :state="passwordRepeatCheck"
                        >
                            <b-form-input
                            v-model="password2"
                            :disabled="formDisabled"
                            autocomplete="new-password" id="sd-register__confirm-password"
                            type="password"
                            debounce="500"
                            ></b-form-input>
                        </b-form-group>
                        <b-row>
                            <b-col>
                                <h6>Optional information</h6>
                                <hr>
                            </b-col>
                        </b-row>

                        <!-- orcid -->
                        <b-form-group
                            label="ORCID"
                            label-for="sd-register__orcid"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="orcid" :disabled="formDisabled" id="sd-register__orcid"></b-form-input>
                        </b-form-group>

                        <!-- institution -->
                        <b-form-group
                            label="Institution name"
                            label-for="sd-register__institution"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="institutionName" :disabled="formDisabled" id="sd-register__institution"></b-form-input>
                        </b-form-group>

                        <!-- institution address -->
                        <b-form-group
                            label="Institution address"
                            label-for="sd-register__institution-address"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="institutionAddress" :disabled="formDisabled" id="sd-register__institution-address"></b-form-input>
                        </b-form-group>

                        <!-- department -->
                        <b-form-group
                            label="Department name"
                            label-for="sd-register__department"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="department" :disabled="formDisabled" id="sd-register__department"></b-form-input>
                        </b-form-group>

                        <!-- linkedin -->
                        <b-form-group
                            label="LinkIn profile"
                            label-for="sd-register__linkedin"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="linkedIn" :disabled="formDisabled" id="sd-register__linkedin"></b-form-input>
                        </b-form-group>

                        <!-- twitter -->
                        <b-form-group
                            label="Twitter feed"
                            label-for="sd-register__twitter"
                            label-cols-sm="4"
                            label-align-sm="right"
                        >
                            <b-form-input v-model="twitter" :disabled="formDisabled" id="sd-register__twitter"></b-form-input>
                        </b-form-group>
                        <b-row>
                            <b-col>
                                <h6>Privacy notification</h6>
                                <hr>
                                <p>This site is designed to facilitate the submission and sharing of scientific figures. It collects Personally Identifiable Information such as the names, e-mail addresses, organizational affiliations, IP addresses, and Open Researcher and Contributor IDs (ORC ID) of those submitting content (the "PI Information").</p>
                                <p>This PI Information is collected to facilitate communications regarding posted figures, to track/report on submissions and system usage. This PI information may be shared with the data controller (EMBO) and other registered users. The PI Information stored in relationship to a figure submission might be used in case of ethics concerns or violations.</p>
                                <p>Please confirm the following:</p>
                            </b-col>
                        </b-row>

                        <!-- accept conditions -->
                        <b-form-checkbox
                        :disabled="formDisabled"
                        id="sd-conditions"
                        v-model="acceptConditions"
                        name="sd-conditions"
                        >
                        I agree with the conditions above and confirm my personal information is correct.
                        </b-form-checkbox>

                        <!-- obtain consent -->
                        <b-form-checkbox
                        :disabled="formDisabled"
                        id="sd-consent"
                        v-model="acceptConsent"
                        name="sd-consent"
                        >
                        I will obtain consent from all persons and entities that may have intellectual property rights pertaining to the content I will post and share on this platform.
                        </b-form-checkbox>

                        <!-- obtain permissions -->
                        <b-form-checkbox
                        :disabled="formDisabled"
                        id="sd-permissions"
                        v-model="acceptPermissions"
                        name="sd-permissions"
                        >
                        I will obtain permission from any relevant co-authors before publicly posting or sharing content.
                        </b-form-checkbox>

                        <!-- submit -->
                        <b-row class="mt-4">
                            <b-col class="text-center">
                                <b-button variant="primary" :disabled="submitButtonIsDisabled" type="submit">Register</b-button>

                            </b-col>
                        </b-row>

                    </b-form>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
  </section>
</template>

<script>
import { mapGetters } from 'vuex';

export default {

    name: 'RegistrationForm',
    components: {  },
    props: [],
    data(){
        return {
            formDisabled: false,
            firstName: '',
            surname: '',
            email: '',
            password1: '',
            password2: '',
            orcid: '',
            institutionName: '',
            institutionAddress: '',
            department: '',
            linkedIn: '',
            twitter: '',
            acceptConditions: false,
            acceptConsent: false,
            acceptPermissions: false,
            emailFeedback: '',
            password1Feedback: '',
            password2Feedback: '',
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['isLoggedIn']),
        submitButtonIsDisabled() {
            if(this.formDisabled) return true;
            if(
                this.firstName.length === 0
                || this.surname.length === 0
                || this.emailCheck !== true
                || this.passwordCheck !== true
                || this.passwordRepeatCheck !== true
                || this.acceptConditions !== true
                || this.acceptConsent !== true
                || this.acceptPermissions !== true
            ) return true;
            return false;
        },
        emailCheck(){
            this.emailFeedback = '';
            if(!this.email) return null;
            if(!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.email)){
                this.emailFeedback = 'Invalid email address';
                return false;
            }

            return true;
        },
        passwordCheck(){
            this.password1Feedback = '';
            if(!this.password1) return null;
            if(this.password1.length < 6){
                this.password1Feedback = 'Password must be at least 6 characters';
                return false;
            }
            return true;

        },
        passwordRepeatCheck(){
            this.password2Feedback = '';
            if(!this.password1 || !this.password2) return null;
            if(this.password1 !== this.password2){
                this.password2Feedback = 'The passwords do not match';
                return false;
            }
            return true;

        }

    },
    methods:{
        sendRegistration() {
            this.formDisabled = true;
            const newRegistration = {
                firstName: this.firstName,
                surname: this.surname,
                email: this.email,
                password1: this.password1,
                password2: this.password2,
                orcid: this.orcid,
                institutionName: this.institutionName,
                institutionAddress: this.institutionAddress,
                department: this.department,
                linkedIn: this.linkedIn,
                twitter: this.twitter,
                acceptConditions: this.acceptConditions,
                acceptConsent: this.acceptConsent,
                acceptPermissions: this.acceptPermissions,
            };



        }
    },
    created() {
        if(this.isLoggedIn) {
            this.$router.push('/');
        }
    }
}
</script>

<style lang="scss">

</style>