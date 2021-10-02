<template>
  <section id="login-page">
    <b-container class="mt-5">
        <b-row align-h="center">
            <b-col cols md="6">
                <b-card header="Log in">
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
                            debounce="500" trim>
                            </b-form-input>
                        </b-form-group>
                        <b-form-invalid-feedback force-show v-if="showInvalidFormFeedback">
                            {{loginFeedback}}
                        </b-form-invalid-feedback>
                        <b-button
                        :disabled="submitDisabled"
                        type="submit"
                        variant="primary"
                        >{{ loginButtonCaption }} <b-spinner small v-if="loading"></b-spinner></b-button>
                    </b-form>
                </b-card>
            </b-col>

        </b-row>
    </b-container>
  </section>
</template>

<script>

import AuthService from '@/services/AuthService';
import store from '@/stores/store';
import { mapGetters } from 'vuex';

export default {

    name: 'LoginForm',
    components: { },
    props: [''],

    data(){

        return {
            email: '',
            password: '',
            emailFeedback: '',
            passwordFeedback: '',
            loginFeedback: '',
            loading: false,
        }

    }, /* end of data */
    computed: {
        ...mapGetters(['currentUser']),
        emailCheck(){
            this.emailFeedback = '';
            this.loginFeedback = '';
            if(!this.email) return null;

            if(!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.email)){
                this.emailFeedback = 'Invalid email address';
                return false;
            }

            return true;
        },
        passwordCheck(){
            this.passwordFeedback = '';
            this.loginFeedback = '';
            if(!this.password) return null;
            if(this.password.length < 6) {
                this.passwordFeedback = 'Passwords need at least 6 characters';
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
        }
    },

    methods:{ //run as event handlers, for example

        sendLogin() {
            this.loading = true;
            let destination = this.$route.query.next || '/';
            AuthService.login(this.email, this.password).then((loginData) => {
                this.loading = false;
                this.$snotify.success(loginData.MESSAGE, "OK!");
                store.dispatch('fetchCurrentUser')
                    .then(() => {
                        this.$router.push({path:destination}).catch(error => {});

                        if (!this.currentUser.has_consented) {
                            this.showConsentModal();
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        this.$snotify.error("We can't find your data. Please try again later.", "Sorry!")
                    });
            }).catch((errorData) => { console.log(errorData);
                this.loading = false;

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

    }

}
</script>

<style lang="scss">

</style>