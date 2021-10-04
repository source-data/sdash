<template>
    <div class="main-navbar navbar navbar-expand-md">
        <ul v-if="isGuest" class="main-menu navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">SmartFigures</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about">About</a>
            </li>
        </ul>
        <ul v-else class="main-menu navbar-nav mr-auto">
            <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'dashboard'}">My Dashboard</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'groups'}">Groups</router-link>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about">About</a>
            </li>
        </ul>
        <ul v-if="isGuest" class="user-menu navbar-nav ml-auto">
            <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'login'}">Log in</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'register'}">Register</router-link>
            </li>
        </ul>
        <ul v-else class="user-menu navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a
                    id="navbarDropdown"
                    class="nav-link dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    {{ fullName }}
                    <span class="caret"></span>
                </a>

                <div
                    class="dropdown-menu dropdown-menu-right"
                    aria-labelledby="navbarDropdown"
                >
                <router-link class="dropdown-item" :to="{ name: 'dashboard'}">My dashboard</router-link>
                <router-link class="dropdown-item" :to="{ path: '/user/' + user.id }">My profile</router-link>
                    <a
                        class="dropdown-item"
                        href="#"
                        @click.prevent="logOut"
                    >
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import AuthService from '@/services/AuthService';
import {mapMutations} from 'vuex';

export default {
    name: "NavigationBar",

    props: {
        user: {
            type: Object
        }
    },

    data() {
        return {
            // csrf: document
            //     .querySelector('meta[name="csrf-token"]')
            //     .getAttribute("content")
        };
    },

    computed: {
        isGuest() {
            return this.user.id === null;
        },
        fullName() {
            return this.isGuest ? '' : (this.user.firstname + ' ' + this.user.surname);
        }
    },
    methods: {
        ...mapMutations(['expireCurrentUser', 'clearPanels', 'clearGroups']),
        logOut() {
            AuthService.logout().then(response => {
                this.expireCurrentUser();
                this.clearPanels();
                this.clearGroups();
                if(this.$route.path !== '/') this.$router.push('/');
            }).catch(error=>{
                console.log(error);
                this.$snotify.error("Could not log you out due to an error.","Error!");
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.main-navbar {
    margin: 0;
    padding: 0.5rem;
}

.main-navbar {
    background-color: #b5c8e9;

    .nav-item {
        line-height: 1.25;

        .nav-link {
            display: inline-block;
            padding: 0 0.5rem;
            color: white;
        }
        &:not(:last-child) {
            border-right: 1px solid white;
        }
    }
}

@media (max-width: 768px) {
    .main-navbar {
        .nav-item {
            line-height: 1.5;

            .nav-link {
                display: block;
            }
            &:not(:last-child) {
                border-right: none;
            }
        }
    }
}
</style>
