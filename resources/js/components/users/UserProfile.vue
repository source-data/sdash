<template>
    <div>
        <info-bar v-if="user">
            <template v-slot:title>
                <h1 class="pb-0 mb-0">{{ user.firstname + ' ' + user.surname }}</h1>
                <p>{{ user.institution_name + ' &bull; ' + user.department_name }}</p>
            </template>
            <template v-slot:text>
                <dl class="row">
                    <dt class="col-sm-2">Email</dt>
                    <dd class="col-sm-10">
                        <a :href="'mailto:' + user.email" target="_blank" class="pb-2">{{ user.email }}</a>
                    </dd>

                    <dt class="col-sm-2">LinkedIn</dt>
                    <dd class="col-sm-10">
                        <a :href="user.linkedin" target="_blank" class="pb-2">{{ user.linkedin }}</a>
                    </dd>

                    <dt class="col-sm-2">Twitter</dt>
                    <dd class="col-sm-10">
                        <a :href="user.twitter" target="_blank" class="pb-2">{{ user.twitter }}</a>
                    </dd>

                    <dt class="col-sm-2">ORCID</dt>
                    <dd class="col-sm-10">
                        <a :href="'https://orcid.org/' + user.orcid" target="_blank" class="pb-2">{{ user.orcid }}</a>
                    </dd>
                </dl>
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

</style>