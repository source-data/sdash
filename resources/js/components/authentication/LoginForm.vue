<template>
  <section id="sd-login-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols md="6">
                <b-card bg-variant="dark" text-variant="light" header="Log in">
                    <b-form
                        @submit.prevent="sendLogin"
                    >
                        <b-form-group
                            id="email-fieldset"
                            label="Email"
                            valid-feedback="Allowed"
                            :invalid-feedback="emailFeedback"
                            :state="emailCheck"
                            :disabled="loading"
                        >
                        <b-form-input
                        id="login-email"
                        v-model="email"
                        autocomplete="username"
                        :state="emailCheck"
                        debounce="500"
                        trim
                        ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            id="password-fieldset"
                            label="Password"
                            valid-feedback="Allowed"
                            :invalid-feedback="passwordFeedback"
                            :state="passwordCheck"
                            :disabled="loading"
                        >
                            <b-form-input id="login-password" v-model="password"
                            autocomplete="current-password"
                            :state="passwordCheck"                             type="password"
                            debounce="300" trim>
                            </b-form-input>
                        </b-form-group>
                        <b-form-group
                            id="remember-input-group"
                        >
                        <b-form-checkbox
                            id="sd-remember-checkbox"
                            v-model="remember"
                            name="remember"
                        >Remember me</b-form-checkbox>

                        </b-form-group>

                        <b-form-invalid-feedback force-show v-if="showInvalidFormFeedback">
                            {{loginFeedback}}
                        </b-form-invalid-feedback>

                        <b-button
                        :disabled="submitDisabled"
                        type="submit"
                        variant="primary"
                        >{{ loginButtonCaption }} <b-spinner small v-if="loading"></b-spinner></b-button>

                        <router-link class="btn btn-link" :to="{ name: 'passwordreset'}">Forgot your password?</router-link>
                    </b-form>
                </b-card>
            </b-col>

        </b-row>
    </b-container>
    <b-modal
    centered
    v-model="showEmailResend"
    hide-footer
    title="Please confirm your email address"
    >
    <EmailConfirmationNotice></EmailConfirmationNotice>
    </b-modal>
  </section>
</template>

<script>

import AuthService from '@/services/AuthService';
import EmailFormatValidator from '@/services/EmailFormatValidator';
import EmailConfirmationNotice from '@/components/authentication/EmailConfirmationNotice';
import store from '@/stores/store';
import { mapGetters, mapMutations } from 'vuex';

export default {

    name: 'LoginForm',
    components: { EmailConfirmationNotice },
    data(){

        return {
            email: '',
            password: '',
            remember: false,
            emailFeedback: '',
            passwordFeedback: '',
            loginFeedback: '',
            loading: false,
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['currentUser', 'showEmailConfirmationNotice', 'isLoggedIn']),
        emailCheck(){
            this.emailFeedback = '';
            this.loginFeedback = '';
            if(!this.email) return null;

            if(!EmailFormatValidator.validate(this.email)){
                this.emailFeedback = 'Invalid email address';
                return false;
            }

            return true;
        },
        passwordCheck(){
            this.passwordFeedback = '';
            this.loginFeedback = '';
            if(!this.password) return null;
            if(this.password.length < 8) {
                this.passwordFeedback = 'Passwords need at least 8 characters';
                return false;
            }
            return true;
        },
        submitDisabled() {
            return !(this.passwordCheck && this.emailCheck && !this.loading);
        },
        loginButtonCaption() {
            return (this.loading) ? 'Logging in' : 'Log in';
        },
        showInvalidFormFeedback() {
            return this.loginFeedback.length > 0;
        },
        showEmailResend: {
            get() {
                return this.showEmailConfirmationNotice;
            },
            set(value) {
                this.setEmailConfirmationNotice(value);
            },
        }
    },

    methods:{ //run as event handlers, for example
        ...mapMutations(['setEmailConfirmationNotice']),
        sendLogin() {
            this.loading = true;
            let destination = this.$route.query.next || '/';
            AuthService.login(this.email, this.password, this.remember).then((loginData) => {
                this.loading = false;
                this.$snotify.success(loginData.MESSAGE, "OK!");
                store.dispatch('fetchCurrentUser')
                    .then(() => {
                        this.setEmailConfirmationNotice(false);
                        this.$router.push({path:destination}).catch(error => {});
                    })
                    .catch((error) => {
                        console.log(error)
                        this.$snotify.error("We can't find your data. Please try again later.", "Sorry!")
                    });
            }).catch((errorData) => { console.log(errorData);
                this.loading = false;
                if(errorData.STATUS && errorData.STATUS === '403') {
                    this.setEmailConfirmationNotice(true);
                }
                if(errorData.STATUS === '401') {
                    this.$snotify.error(errorData.MESSAGE, "Login Failed");
                }
                if(errorData.message) {
                    this.$snotify.error(errorData.message, "Login Failed");
                }
                if(errorData.errors && errorData.errors.email) {
                    this.loginFeedback = errorData.errors.email.join(' ');
                }
                if(errorData.errors && errorData.errors.password) {
                    this.loginFeedback = errorData.errors.password.join(' ');
                }
            });


        },

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