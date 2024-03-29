<template>
  <section id="sd-login-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols lg="4" md="6" sm="8">
                <b-card bg-variant="dark" text-variant="light">
                    <b-form
                        @submit.prevent="sendLogin"
                    >
                        <b-form-group
                            label-sr-only
                            id="email-fieldset"
                            label="Email"
                            valid-feedback="Allowed"
                            :invalid-feedback="emailFeedback"
                            :state="emailCheck"
                            :disabled="loading"
                            class="mb-4"
                        >
                            <b-form-input
                            size="lg"
                            id="login-email"
                            v-model="email"
                            autocomplete="username"
                            :state="emailCheck"
                            placeholder="E-Mail Address"
                            debounce="500"
                            trim
                            class="sd-text-input"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label-sr-only
                            id="password-fieldset"
                            label="Password"
                            valid-feedback="Allowed"
                            :invalid-feedback="passwordFeedback"
                            :state="passwordCheck"
                            :disabled="loading"
                            class="mb-4"
                        >
                            <b-form-input
                            size="lg"
                            placeholder="Password"
                            id="login-password"
                            v-model="password"
                            autocomplete="current-password"
                            :state="passwordCheck"
                            type="password"
                            debounce="300"
                            class="sd-text-input"
                            trim>
                            </b-form-input>
                        </b-form-group>
                        <b-form-group
                            id="remember-input-group"
                            class="mb-4"
                        >
                        <b-form-checkbox
                            id="sd-remember-checkbox"
                            v-model="remember"
                            name="remember"
                            class="sd-big-checkbox"
                        >Remember me</b-form-checkbox>

                        </b-form-group>

                        <b-form-invalid-feedback force-show v-if="showInvalidFormFeedback">
                            {{loginFeedback}}
                        </b-form-invalid-feedback>

                        <b-button
                        :disabled="submitDisabled"
                        type="submit"
                        variant="primary"
                        class="sd-big-button full-width"
                        >{{ loginButtonCaption }} <b-spinner small v-if="loading"></b-spinner></b-button>
                        <div class="text-right py-1">
                            <router-link class="text-white small" :to="{ name: 'passwordreset'}">Forgot your password?</router-link>
                        </div>
                    </b-form>
                </b-card>
            </b-col>

        </b-row>
    </b-container>
    <b-modal
        centered
        v-model="showEmailResend"
        hide-footer
        title="Please verify your email address"
    >
        <EmailConfirmationNotice :email="email" />
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