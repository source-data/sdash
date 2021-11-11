<template>
    <div>
        <info-bar v-if="user">
            <template v-slot:above-title v-if="isAuthorized">
                <router-link :to="{name: 'useredit', params: {user_id: user.id}}" class="sd-edit-icon sd-user-edit-link">
                    <font-awesome-icon icon="edit" title="Edit user details" />
                    Edit profile
                </router-link>
            </template>
            <template v-slot:title>
                <h1 class="pb-0 mb-0">{{ user.firstname + ' ' + user.surname }}</h1>
                <p v-if="user.institution_name">{{ user.institution_name }}</p>
            </template>
            <template v-slot:text>
                <dl class="row mt-3">
                    <template v-if="user.email">
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">
                            <a :href="'mailto:' + user.email" target="_blank" class="pb-2">{{ user.email }}</a>
                        </dd>
                    </template>
                    <template v-if="user.orcid">
                        <dt class="col-sm-4">ORCID</dt>
                        <dd class="col-sm-8">
                            <a :href="'https://orcid.org/' + user.orcid" target="_blank" class="pb-2">{{ user.orcid }}</a>
                        </dd>
                    </template>
                    <template v-if="user.institution_name">
                        <dt class="col-sm-4">Institution Name</dt>
                        <dd class="col-sm-8">{{ user.institution_name }}</dd>
                    </template>
                    <template v-if="user.institution_address">
                        <dt class="col-sm-4">Institution Address</dt>
                        <dd class="col-sm-8">{{ user.institution_address }}</dd>
                    </template>
                    <template v-if="user.department_name">
                        <dt class="col-sm-4">Department Name</dt>
                        <dd class="col-sm-8">{{ user.department_name }}</dd>
                    </template>
                    <template v-if="user.linkedin">
                        <dt class="col-sm-4">LinkedIn</dt>
                        <dd class="col-sm-8">
                            <a :href="user.linkedin" target="_blank" class="pb-2">{{ user.linkedin }}</a>
                        </dd>
                    </template>
                    <template v-if="user.twitter">
                        <dt class="col-sm-4">Twitter</dt>
                        <dd class="col-sm-8">
                            <a :href="user.twitter" target="_blank" class="pb-2">{{ user.twitter }}</a>
                        </dd>
                    </template>
                </dl>
            </template>
            <template v-slot:footer>
                <p>To delete your account, please send an email to <a :href="'mailto:sourcedata@embo.org'" target="_blank" class="pb-2">sourcedata@embo.org</a>.</p>
            </template>
        </info-bar>
        <b-container v-if="loading" fluid class="mt-3">
            <b-row>
                <b-col class="text-center">
                    <b-spinner variant="primary" label="Spinning" class="m-5" style="width: 4rem; height: 4rem;"></b-spinner>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>

import Axios from "axios"
import InfoBar from '../InfoBar'
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'UserProfile',
    components: {
        InfoBar,
    },
    props: ['user_id'],
    data() {
        return {
            loading: false,
            user: null,
            error: null
        }
    },
    computed: {
        ...mapGetters([
            'currentUser'
        ]),
        isAuthorized() {
            return (this.currentUser.id === this.user.id) || (this.currentUser.role === 'superadmin')
        }
    },
    created() {
        this.fetchData()
    },
    methods: {
        fetchData() {
            this.error = this.user = null
            this.loading = true
            return Axios.get("/users/" + this.user_id)
                .then(response => {
                    return this.user = response.data.DATA;
                })
                .catch(error => {
                    this.error = error
                })
                .then(() => {
                    this.loading = false
                })
        }
    }
}

</script>

<style lang="scss">
    .sd-user-edit-link {
        font-size: 1rem;
        padding: 0.5rem 0;
        color: #459939;
    }
</style>