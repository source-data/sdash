<template>
    <div>
        <article v-if="user" id="user-profile">
            <header>
                <h2>
                    {{ user.firstname + ' ' + user.surname }}
                </h2>

                <router-link
                    v-if="isAuthorized"
                    :to="{name: 'useredit', params: {user_id: user.id}}"
                    id="link-to-edit-user-profile"
                    class="btn btn-desat-blue"
                >
                    <font-awesome-icon icon="edit" title="Edit user details" />
                    Edit profile
                </router-link>
            </header>

            <b-container fluid="lg" id="user-profile-info">
                <b-row v-if="user.email">
                    <b-col>
                        Email
                    </b-col>

                    <b-col md="auto">
                        <a class="text-info" :href="'mailto:' + user.email" target="_blank">
                            {{ user.email }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.orcid">
                    <b-col>
                        ORCID
                    </b-col>

                    <b-col md="auto">
                        <a class="text-info" :href="'https://orcid.org/' + user.orcid" target="_blank">
                            {{ user.orcid }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.institution_name">
                    <b-col>
                        Institution Name
                    </b-col>

                    <b-col md="auto">
                        {{ user.institution_name }}
                    </b-col>
                </b-row>

                <b-row v-if="user.institution_address">
                    <b-col>
                        Institution Address
                    </b-col>

                    <b-col md="auto">
                        {{ user.institution_address }}
                    </b-col>
                </b-row>

                <b-row v-if="user.department_name">
                    <b-col>
                        Department Name
                    </b-col>

                    <b-col md="auto">
                        {{ user.department_name }}
                    </b-col>
                </b-row>

                <b-row v-if="user.linkedin">
                    <b-col>
                        LinkedIn
                    </b-col>

                    <b-col md="auto">
                        <a class="text-info" :href="user.linkedin" target="_blank">
                            {{ user.linkedin }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.twitter">
                    <b-col>
                        Twitter
                    </b-col>

                    <b-col md="auto">
                        <a class="text-info" :href="user.twitter" target="_blank">
                            {{ user.twitter }}
                        </a>
                    </b-col>
                </b-row>
            </b-container>

            <footer v-if="isProfileOfLoggedInUser">
                To delete your account, please send an email to
                <a class="text-info" href="'mailto:sourcedata@embo.org'" target="_blank">
                    sourcedata@embo.org
                </a>.
            </footer>
        </article>

        <div class="spinner-container">
            <b-spinner
                v-if="loading"
                variant="primary"
                label="Spinning"
            ></b-spinner>
        </div>
    </div>
</template>

<script>

import Axios from "axios"
import { mapGetters } from 'vuex'

export default {
    name: 'UserProfile',
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
        isProfileOfLoggedInUser() {
            return this.currentUser.id === this.user.id;
        },
        isAuthorized() {
            return this.isProfileOfLoggedInUser || (this.currentUser.role === 'superadmin')
        },
    },
    watch: {
        'user_id': function updateDisplayedUser() {
            this.fetchData()
        },
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
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';

#user-profile {
    font-size: $font-size-md;
    margin: 2rem 5%;
}
@media (min-width: 768px) {
    #user-profile {
        margin: 6rem 5%;
    }
}

header {
    margin-bottom: 3rem;

    h2 {
        color: $vivid-orange;
        flex: auto;
        margin: 0;
        padding: 0.5rem 0;
    }

    #link-to-edit-user-profile {
        font-size: $font-size-sm;
        padding: 0.75rem 1rem;
    }
}
@media (min-width: 768px) {
    header {
        display: flex;
        margin-bottom: 6rem;
    }
}

#user-profile-info {
    padding: 0;
    margin-left: 0;

    .row:not(:last-child) {
        padding-bottom: 1.5rem;
    }

    .row .col:first-child {
        max-width: 250px;
    }
}

footer {
    margin-top: 100px;
}

.spinner-container {
    margin-top: 5rem;
    text-align: center;
}
</style>