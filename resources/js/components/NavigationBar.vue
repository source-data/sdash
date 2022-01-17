<template>
    <nav class="navbar navbar-expand-md bg-light">
        <router-link class="navbar-brand" :to="{ name: 'dashboard'}">
            <img src="/images/logos/sdash.svg" alt="SDash" loading="lazy">
        </router-link>

        <button
            class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation"
        >
            <font-awesome-icon :icon="['fas', 'bars']" />
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav primary-nav">
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

                <router-link :to="{ name: 'tutorial'}" v-slot="{ href, navigate, isActive, isExactActive }" custom>
                    <div
                        class="nav-item"
                        :class="[isActive && 'router-link-active', isExactActive && 'router-link-exact-active']"
                    >
                        <a class="nav-link" :href="href" @click="navigate">Tutorial</a>
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

            <div class="navbar-nav secondary-nav">
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
                            class="profile-picture" :src="avatarUrl"
                            :alt="'Avatar of ' + fullName" loading="lazy"
                        >
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserProfileMenuLink">
                        <router-link class="dropdown-item" :to="{name: 'user', params: { user_id: user.id }}">
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

            <div v-if="isGuest" class="navbar-nav register-nav">
                <div class="nav-item">
                    <router-link class="nav-link" :to="{name: 'register'}">
                        Register
                        <br>
                        <span class="register-secondary-text invisible">to contribute</span>
                    </router-link>
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
        },
        avatarUrl() {
            return this.user.avatar
                ? "/storage/avatars/" + this.user.avatar
                : "/images/default_avatar.jpg";
        },
    },
    methods: {
        ...mapMutations(['expireCurrentUser', 'clearPanels', 'clearGroups']),
        logOut() {
            AuthService.logout().then(response => {
                this.clearGroups();
                this.clearPanels();
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
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_layout.scss';

$navbar-box-shadow-size: 1px;
.navbar {
    box-shadow: 0 $navbar-box-shadow-size $navbar-box-shadow-size #bbb;
    font-size: 1.5rem;
    /* The free space from any navbar content to the top and bottom is set via the content's padding or margin to allow
     * the primary nav-items' active state background to encompass the whole navbar.
     */
    margin: 0;
    padding: 0;
    width: 100vw;
    z-index: $navbar-z-index;
}
@media (min-height: 500px) {
    /* Make the navbar stay fixed while scrolling on screens with enough height */
    .navbar {
        position: fixed;
    }
}

/* This defines the free space around the always-visible items in the nav bar, the SDash logo and the burger button. */
.navbar-brand,
.navbar-toggler {
    margin-bottom: $navbar-padding-bottom;
    margin-top: $navbar-padding-top;
    padding: 0;
}
.navbar-brand {
    cursor: pointer;
    font-size: 1rem;
    margin-left: $navbar-padding-left;
    margin-right: 0;
}
.navbar-toggler {
    font-size: 2rem;
    margin-left: 0;
    margin-right: $navbar-padding-right;
}
@media (min-width: 768px) {
    .navbar-brand {
        margin-bottom: $navbar-padding-bottom-md;
        margin-left: $navbar-padding-left-md;
        margin-top: $navbar-padding-top-md;
    }
}

/* The nav-items start out in the collapsible section and should be slightly indented compared to the SDash logo. */
.nav-item {
    margin: 0;
    padding-bottom: $navbar-padding-bottom * 0.5;
    padding-left: $navbar-padding-left * 2;
    padding-top: $navbar-padding-top * 0.5;
}
.nav-item.router-link-active {
    background-color: $very-dark-blue;
    box-shadow: 0 $navbar-box-shadow-size+1 $very-dark-blue;
}
@media (min-width: 768px) {
    .nav-item {
        padding-bottom: $navbar-padding-bottom-md;
        padding-top: $navbar-padding-top-md;
        padding-left: 2vw;
        padding-right: 2vw;
    }
    /* The primary nav links should be a bit larger on larger screens */
    .primary-nav {
        font-size: 1.75rem;
    }

    .secondary-nav > *:last-child {
        padding-right: $navbar-padding-right-md;
    }

    /* Dropdown nav-items need margins instead of padding to correctly position their dropdown menus close to them. */
    .nav-item.dropdown {
        padding: 0;
        margin-bottom: $navbar-padding-bottom-md;
        margin-left: 2vw;
        margin-right: 2vw;
        margin-top: $navbar-padding-top-md;
    }
    .nav-item.dropdown:last-child {
        padding: 0;
        margin-right: $navbar-padding-right-md;
    }
}


/* height == line-height to vertically center the text inside the nav-links. */
.navbar-brand > img,
.navbar-toggler,
.nav-item > .nav-link,
.profile-picture {
    height: $navbar-content-height;
    line-height: $navbar-content-height;
}

/* Styling for the links in the navbar. */
.navbar-nav {
    margin-left: auto;

    /* The selectors have to be this specific to override the styling for .nav-link. */
    > .nav-item > .nav-link {
        color: $mostly-black-blue;
        font-weight: bold;
        opacity: 1;
        padding: 0;
    }
    > .nav-item > .nav-link:hover {
        color: $mostly-black-blue-hover;
    }

    > .nav-item.router-link-active > .nav-link {
        color: $mostly-white-gray;
    }
    > .nav-item.router-link-active > .nav-link:hover {
        color: $very-light-gray;
    }

}

/* The search & login icons are a bit too large compared to the text if they have the same font size. */
.secondary-nav .nav-link > svg {
    font-size: 1.25rem;
}

/* Rounded profile picture. */
img.profile-picture {
    border-radius: 50%;
}

/* If we're in the big-screen view, the link to the registration page is positioned just below the navbar on the right.
 * If not, it's in the collapsible menu with the standard positioning.
 */
@media (min-width: 768px) {
    .register-nav {
        background-color: $vivid-orange;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        box-shadow: 0 0 1px #555;
        margin-left: 0;
        margin-right: $navbar-padding-right-md - 1; /* This roughly aligns the registration link with the login link. */
        padding: 0;
        position: absolute;
        /* The registration link is positioned right below the navbar. For that we have to use absolute positioning and
         * calculate the correct positioning relative to the navbar itself.
         */
        top: $navbar-height-md;
        right: 0;
        z-index: 10; /* Lets the registration link appear in front of the panels. */
    }
    .register-nav .nav-item {
        padding-bottom: 2.5rem;
        padding-top: 1rem;
    }
    .register-secondary-text {
        display: block;
        font-weight: normal;
        font-size: 1.125rem;
        line-height: 1rem;
        visibility: visible !important;
    }
}

/* The search & profile dropdowns need some right margin in the collapsed view on smaller screens. */
.dropdown-menu {
    margin-right: 10%;
}
@media (min-width: 768px) {
    .dropdown-menu {
        margin-right: 0;
    }
}
</style>
