<template>
    <nav class="navbar navbar-expand-md bg-light">
        <router-link class="navbar-brand" :to="{ name: 'dashboard'}">
            <img src="/images/SDash-Logo.svg" alt="SDash Logo" loading="lazy">
        </router-link>

        <button
            class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation"
        >
            <font-awesome-icon :icon="['fas', 'bars']" />
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav primary-nav ml-auto">
                <!-- Only the link text itself should be clickable, but the whole nav-item should display the active
                state. Therefore we need to apply the vue-router's classes for the active state to the nav-item instead
                of the link as usual. That's possible with the v-slot API:
                https://router.vuejs.org/api/#example-applying-active-class-to-outer-element -->
                <router-link :to="{ name: 'about'}" v-slot="{ href, navigate, isActive, isExactActive }" custom>
                    <div
                        class="nav-item"
                        :class="[isActive && 'router-link-active', isExactActive && 'router-link-exact-active']"
                    >
                        <a class="nav-link" :href="href" @click="navigate">About</a>
                    </div>
                </router-link>

                <router-link v-if="!isGuest" :to="{ name: 'groups'}" v-slot="{ href, navigate, isActive, isExactActive }" custom>
                    <div
                        class="nav-item"
                        :class="[isActive && 'router-link-active', isExactActive && 'router-link-exact-active']"
                    >
                        <a class="nav-link" :href="href" @click="navigate">Groups</a>
                    </div>
                </router-link>
            </div>

            <div class="navbar-nav secondary-nav ml-auto">
                <div class="nav-item dropdown">
                    <a
                        class="nav-link" href="#" id="navbarSearchMenuLink" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"
                    >
                        Search
                        <font-awesome-icon :icon="['fas', 'search']" />
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarSearchMenuLink">
                        <search-bar :exclude-groups="isGuest"></search-bar>
                    </div>
                </div>

                <div v-if="isGuest" class="nav-item">
                    <router-link class="nav-link" :to="{name: 'login'}">
                        Sign in
                        <font-awesome-icon :icon="['fas', 'user']" />
                    </router-link>
                </div>

                <div v-else class="nav-item dropdown">
                    <a
                        class="dropdown-toggle nav-link" href="#" id="navbarUserProfileMenuLink" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    >
                        <img
                            class="profile-picture" src="/images/profile-picture-placeholder.jpeg"
                            alt="User profile picture" loading="lazy"
                        >
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserProfileMenuLink">
                        <router-link class="dropdown-item" :to="{path: 'user/' + user.id}">
                            Profile
                        </router-link>

                        <a
                            class="dropdown-item" href="#"
                            @click.prevent="logOut"
                        >
                            Logout
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="isGuest" class="navbar-nav secondary-nav register-nav">
                <div class="nav-item">
                    <a class="nav-link" href="/register">
                        Register
                        <br>
                        <span class="register-secondary-text invisible">to contribute</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import SearchBar from "@/components/SearchBar";
import AuthService from '@/services/AuthService';
import {mapMutations} from 'vuex';

export default {
    name: "NavigationBar",

    components: {
        SearchBar
    },

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
@use "sass:math";
@import 'resources/sass/_colors.scss';

$navbar-content-height: 3rem;
$navbar-padding-bottom: 2rem;
$navbar-padding-left: 4vw;
$navbar-padding-right: 2vw;
$navbar-padding-top: 2rem;

/* The free space from any navbar content to the top and bottom is set this way to allow the nav-items' active state
 * background to encompass the whole navbar.
 */
.navbar {
    padding: 0;
}
.nav-item,
.navbar-brand {
    padding-top: $navbar-padding-bottom;
    padding-bottom: $navbar-padding-top;
}
.navbar-brand {
    margin-left: $navbar-padding-left;
    margin-right: 0;
    font-size: 1rem;
}

/* height == line-height to vertically center the text inside the nav-links. */
.navbar-brand > img,
.navbar-toggler,
.nav-item > .nav-link,
.profile-picture {
    height: $navbar-content-height;
    line-height: $navbar-content-height;
}

/* The selector has to be this specific to override the styling for .nav-link. */
.navbar-nav > .nav-item > .nav-link {
    color: $mostly-black-blue;
    font-weight: bold;
    opacity: 1;
    padding: 0;
}
.navbar-nav > .nav-item > .nav-link:hover {
    color: $mostly-black-blue-hover;
}

/* The secondary nav links' text should be slightly smaller than the primary ones. */
.navbar-toggler,
.primary-nav {
    font-size: 2rem;
}
.secondary-nav {
    font-size: 1.5rem;
    margin-right: $navbar-padding-right;
}
/* The search & login icons are a bit too large compared to the text if they have the same font size. */
.secondary-nav .nav-link > svg {
    font-size: 1.25rem;
}


/* Making the active state of the primary nav links look right. */
.navbar-nav .nav-item {
    padding-left: 2vw;
    padding-right: 2vw;
}
.primary-nav .nav-item.router-link-exact-active {
    background-color: $very-dark-blue;
}
.primary-nav .nav-item.router-link-exact-active .nav-link {
    color: white;
}

/* Rounded profile picture. */
img.profile-picture {
    border-radius: 50%;
}

@media (max-width: 767px) {
    /* Reduce the amount of empty space on smaller screens. */
    .navbar-brand {
        margin-left: math.div($navbar-padding-left, 2);
    }
    .nav-item,
    .navbar-brand {
        padding-bottom: math.div($navbar-padding-bottom, 2);
        padding-top: math.div($navbar-padding-top, 2);
    }

    /* That the secondary nav links are smaller doesn't catch the eye as much when they're far apart horizontally with
     * a bigger screen. Once they're right above each other in the collapsible menu that we're using on small screens
     * they absolutely need to have the same font size.
     */
    .primary-nav {
        font-size: 1.5rem;
    }
}

/* If we're in the big-screen view, the link to the registration page is positioned just below the navbar on the right.
 * If not, it's in the collapsible menu with the standard positioning.
 */
@media (min-width: 768px) {
    .register-nav {
        background-color: $vivid-orange;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        margin-right: 1vw; /* This roughly aligns the registration link with the login link. */
        padding: 0;
        position: absolute;
        /* The registration link is positioned right below the navbar. For that we have to use absolute positioning and
         * calculate the correct positioning relative to the navbar itself.
         */
        top: $navbar-padding-top + $navbar-content-height + $navbar-padding-bottom;
        right: 0;
        z-index: 10; /* Lets the registration link appear in front of the panels. */
    }
    .register-nav .nav-item {
        padding-top: 0.5rem;
    }
    .register-secondary-text {
        display: block;
        font-weight: normal;
        font-size: 1.125rem;
        line-height: 1rem;
        visibility: visible !important;
    }
}
</style>
