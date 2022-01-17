<template>
  <section id="sd-registration-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols sm="12" md="10">
                <b-card bg-variant="dark" text-variant="light">
                    <b-card-body>
                        <b-card-title title-tag="h1" class="h2 text-primary pb-4">Register a New Account</b-card-title>

                        <b-card-text>

                            <b-form
                                @submit.prevent="sendRegistration"
                            >
                                <!-- mandatory information block -->
                                <b-row class="pb-5">
                                    <b-col cols sm="12" lg="3">
                                        <h6 class="sd-header-alongside-input pb-4">Mandatory information</h6>
                                    </b-col>
                                    <b-col cols sm="12" lg="9">


                                        <!-- first name -->
                                        <b-form-group
                                            class="mb-4"
                                            label="First name / given name*"
                                            label-for="sd-register__firstname"
                                            label-cols-md="4"
                                            :invalid-feedback="firstNameFeedback"
                                            :state="firstNameCheck"
                                        >
                                            <b-form-input v-model="firstName"
                                            :disabled="formDisabled"
                                            id="sd-register__firstname"
                                            debounce="300"
                                            @input="clearErrors('firstname')"
                                            ></b-form-input>
                                        </b-form-group>


                                        <!-- surname -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Surname / family name*"
                                            label-for="sd-register__surname"
                                            label-cols-md="4"
                                            :invalid-feedback="surnameFeedback"
                                            :state="surnameCheck"
                                        >
                                            <b-form-input v-model="surname" :disabled="formDisabled"
                                            id="sd-register__surname"
                                            debounce="300"
                                            @input="clearErrors('surname')"
                                            ></b-form-input>
                                        </b-form-group>

                                        <!-- email -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Email address*"
                                            label-for="sd-register__email"
                                            label-cols-md="4"
                                            :invalid-feedback="emailFeedback"
                                            :state="emailCheck"
                                        >
                                            <b-form-input
                                            debounce="300"
                                            @input="clearErrors('email')"
                                            v-model="email"
                                            :disabled="formDisabled"
                                            id="sd-register__email"
                                            type="email"></b-form-input>
                                        </b-form-group>

                                        <!-- password 1 -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Password*"
                                            label-for="sd-register__password"
                                            label-cols-md="4"
                                            :invalid-feedback="passwordFeedback"
                                            :state="passwordCheck"
                                        >
                                            <b-form-input
                                            v-model="password1"
                                            autocomplete="new-password"
                                            :disabled="formDisabled"
                                            id="sd-register__password"
                                            type="password"
                                            debounce="300"
                                            @input="clearErrors('password')"
                                            ></b-form-input>
                                        </b-form-group>

                                        <!-- password 2 -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Confirm password*"
                                            label-for="sd-register__confirm-password"
                                            label-cols-md="4"
                                            :invalid-feedback="passwordRepeatFeedback"
                                            :state="passwordRepeatCheck"
                                        >
                                            <b-form-input
                                            v-model="password2"
                                            :disabled="formDisabled"
                                            autocomplete="new-password" id="sd-register__confirm-password"
                                            type="password"
                                            debounce="300"
                                            @input="clearErrors('password_confirmation')"
                                            ></b-form-input>
                                        </b-form-group>

                                    </b-col>
                                </b-row>

                                <!-- optional information block -->
                                <b-row class="pb-5">
                                    <b-col cols sm="12" lg="3">
                                        <h6 class="sd-header-alongside-input pb-4">Optional information</h6>
                                    </b-col>
                                    <b-col cols sm="12" lg="9">

                                        <!-- orcid -->
                                        <b-form-group
                                            class="mb-4"
                                            label="ORCID"
                                            label-for="sd-register__orcid"
                                            label-cols-md="4"
                                            :invalid-feedback="orcidFeedback"
                                            :state="orcidCheck"
                                        >
                                            <b-form-input v-model="orcid"
                                            :disabled="formDisabled"
                                            id="sd-register__orcid"
                                            debounce="300"
                                            @input="clearErrors('orcid')"
                                            ></b-form-input>
                                        </b-form-group>

                                        <!-- institution -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Institution name"
                                            label-for="sd-register__institution"
                                            label-cols-md="4"
                                        >
                                            <b-form-input v-model="institutionName" :disabled="formDisabled" id="sd-register__institution"></b-form-input>
                                        </b-form-group>

                                        <!-- institution address -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Institution address"
                                            label-for="sd-register__institution-address"
                                            label-cols-md="4"
                                        >
                                            <b-form-input v-model="institutionAddress" :disabled="formDisabled" id="sd-register__institution-address"></b-form-input>
                                        </b-form-group>

                                        <!-- department -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Department name"
                                            label-for="sd-register__department"
                                            label-cols-md="4"
                                        >
                                            <b-form-input v-model="department" :disabled="formDisabled" id="sd-register__department"></b-form-input>
                                        </b-form-group>

                                        <!-- linkedin -->
                                        <b-form-group
                                            class="mb-4"
                                            label="LinkIn profile"
                                            label-for="sd-register__linkedin"
                                            label-cols-md="4"
                                        >
                                            <b-form-input v-model="linkedIn" :disabled="formDisabled" id="sd-register__linkedin"></b-form-input>
                                        </b-form-group>

                                        <!-- twitter -->
                                        <b-form-group
                                            class="mb-4"
                                            label="Twitter feed"
                                            label-for="sd-register__twitter"
                                            label-cols-md="4"
                                        >
                                            <b-form-input v-model="twitter" :disabled="formDisabled" id="sd-register__twitter"></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row class="pb-5">
                                    <b-col cols sm="12" lg="3">
                                        <h6 class="pb-4">Privacy Notification</h6>
                                    </b-col>
                                    <b-col cols sm="12" lg="9">
                                        <p>This site is designed to facilitate the submission and sharing of scientific figures. It collects Personally Identifiable Information such as the names, e-mail addresses, organizational affiliations, IP addresses, and Open Researcher and Contributor IDs (ORC ID) of those submitting content (the "PI Information").</p>
                                        <p>This PI Information is collected to facilitate communications regarding posted figures, to track/report on submissions and system usage. This PI information may be shared with the data controller (EMBO) and other registered users. The PI Information stored in relationship to a figure submission might be used in case of ethics concerns or violations.</p>
                                        <p>Please confirm the following:</p>


                                        <!-- accept conditions -->
                                        <b-form-checkbox
                                        class="pb-4"
                                        :disabled="formDisabled"
                                        id="sd-conditions"
                                        v-model="acceptConditions"
                                        name="sd-conditions"
                                        :invalid-feedback="acceptConditionsFeedback"
                                        :state="acceptConditionsCheck"
                                        >
                                        I agree with the conditions above and confirm my personal information is correct.
                                        </b-form-checkbox>

                                        <!-- obtain consent -->
                                        <b-form-checkbox
                                        class="pb-4"
                                        :disabled="formDisabled"
                                        id="sd-consent"
                                        v-model="acceptConsent"
                                        name="sd-consent"
                                        :invalid-feedback="acceptConsentFeedback"
                                        :state="acceptConsentCheck"
                                        >
                                        I will obtain consent from all persons and entities that may have intellectual property rights pertaining to the content I will post and share on this platform.
                                        </b-form-checkbox>

                                        <!-- obtain permissions -->
                                        <b-form-checkbox
                                        class="pb-4"
                                        :disabled="formDisabled"
                                        id="sd-permissions"
                                        v-model="acceptPermissions"
                                        name="sd-permissions"
                                        :invalid-feedback="acceptPermissionFeedback"
                                        :state="acceptPermissionCheck"
                                        >
                                        I will obtain permission from any relevant co-authors before publicly posting or sharing content.
                                        </b-form-checkbox>

                                        <b-button variant="primary"
                                        class="sd-big-button wide"
                                        :disabled="submitButtonIsDisabled" type="submit">Register</b-button>
                                    </b-col>
                                </b-row>
                                <!-- submit -->

                            </b-form>
                        </b-card-text>
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
  </section>
</template>

<script>
import { mapGetters } from 'vuex';
import AuthService from '@/services/AuthService';
import EmailFormatValidator from '@/services/EmailFormatValidator';

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
            errors: {}

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
        firstNameFeedback(){
            if(this.errors.firstname) return this.errors.firstname.join(' ');
        },
        surnameFeedback(){
            if(this.errors.surname) return this.errors.surname.join(' ');
        },
        emailFeedback(){
            if(this.errors.email) return this.errors.email.join(' ');
            if(!EmailFormatValidator.validate(this.email)) {
                return 'Invalid email address format';
            };

        },
        passwordFeedback(){
            if(this.errors.password) return this.errors.password.join(' ');
            if(this.password1.length < 8) {
                return 'Password must be at least 8 characters';
            }

        },
        passwordRepeatFeedback(){
            if(this.password1 !== this.password2) {
                return 'The passwords do not match';
            }
        },
        orcidFeedback(){
            if(this.errors.orcid) return this.errors.orcid.join(' ');
        },
        acceptConditionsFeedback(){
            if(this.errors['confirmation.0']) return this.errors['confirmation.0'].join(' ');
        },
        acceptConsentFeedback(){
            if(this.errors['confirmation.1']) return this.errors['confirmation.1'].join(' ');
        },
        acceptPermissionFeedback(){
            if(this.errors['confirmation.2']) return this.errors['confirmation.2'].join(' ');
        },
        emailCheck(){
            if(this.errors.email) return false;
            if(!this.email) return null;
            if(!EmailFormatValidator.validate(this.email)) return false;
            return true;
        },
        passwordCheck(){
            if(this.errors.password) return false;
            if(!this.password1) return null;
            if(this.password1.length < 8) return false;
            return true;

        },
        passwordRepeatCheck(){
            if(!this.password1 || !this.password2) return null;
            if(this.password1 !== this.password2) return false;
            return true;
        },
        orcidCheck(){
            if(this.errors.orcid) return false;
            if(this.orcid.length===0) return null;
        },
        firstNameCheck(){
            if(this.errors.firstname) return false;
            if(this.firstName.length===0) return null;
        },
        surnameCheck(){
            if(this.errors.surname) return false;
            if(this.surname.length===0) return null;
        },
        acceptConditionsCheck(){
            if(this.errors['confirmation.0']) return false;
        },
        acceptConsentCheck(){
            if(this.errors['confirmation.1']) return false;
        },
        acceptPermissionCheck(){
            if(this.errors['confirmation.2']) return false;
        },

    },
    methods:{
        sendRegistration() {
            this.formDisabled = true;
            const newRegistration = {
                firstname: this.firstName,
                surname: this.surname,
                email: this.email,
                password: this.password1,
                password_confirmation: this.password2,
                orcid: this.orcid,
                institution_name: this.institutionName,
                institution_address: this.institutionAddress,
                department_name: this.department,
                linkedin: this.linkedIn,
                twitter: this.twitter,
                confirmation: [
                    this.acceptConditions,
                    this.acceptConsent,
                    this.acceptPermissions,
                ]
            };
            AuthService.register(newRegistration).then(response => {
                this.errors = {};
                this.formDisabled = false;
                console.log(response);
                this.$store.commit('setEmailConfirmationNotice', true);
                this.$snotify.success("Email confirmation sent.", "Account created.");
                this.$router.push({name: 'login'});
            }).catch(error => {
                window.scroll(0,0);
                this.formDisabled = false;
                this.errors = error.errors;
                this.$snotify.error("See the form for details", "Failed");
                console.log(error);
            });

        },
        clearErrors(fieldName) {
            if(this.errors.hasOwnProperty(fieldName)) {
                const modifiedErrors = Object.assign({}, this.errors);
                delete modifiedErrors[fieldName];
                this.errors = modifiedErrors;
            }
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
 .sd-header-alongside-input {
     padding-top: 0.565rem;
 }
</style>