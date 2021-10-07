<template>
    <nav class="navbar navbar-expand-md bg-light">
        <router-link class="navbar-brand" :to="{ name: 'dashboard'}">
            <img src="/images/SDash-Logo.svg" alt="SDash Logo" loading="lazy">
        </router-link>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <font-awesome-icon :icon="['fas', 'bars']" />
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav primary-nav ml-auto">
                <router-link :to="{ name: 'about'}" v-slot="{ href, navigate, isActive, isExactActive }" custom>
                    <div class="nav-item" :class="[isActive && 'router-link-active', isExactActive && 'router-link-exact-active']">
                        <a class="nav-link" :href="href" @click="navigate">About</a>
                    </div>
                </router-link>

                <router-link :to="{ name: 'groups'}" v-slot="{ href, navigate, isActive, isExactActive }" custom>
                    <div class="nav-item" :class="[isActive && 'router-link-active', isExactActive && 'router-link-exact-active']">
                        <a class="nav-link" :href="href" @click="navigate">Groups</a>
                    </div>
                </router-link>
            </div>

            <div class="navbar-nav secondary-nav ml-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarSearchMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Search
                        <font-awesome-icon :icon="['fas', 'search']" />
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarSearchMenuLink">
                        <search-bar :exclude-groups="isGuest"></search-bar>
                    </div>
                </div>

                <a v-if="isGuest" class="nav-link" href="/login">
                    Sign in
                    <font-awesome-icon :icon="['fas', 'user']" />
                </a>

                <div v-else class="nav-item dropdown">
                    <a class="dropdown-toggle nav-link" href="#" id="navbarUserProfileMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="profile-picture" src="/images/profile-picture-placeholder.jpeg" alt="User profile picture" loading="lazy">
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserProfileMenuLink">
                        <router-link class="dropdown-item" :to="{path: 'user/' + user.id}">
                            Profile
                        </router-link>

                        <a class="dropdown-item" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" :value="csrf" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import SearchBar from "@/components/SearchBar";

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
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
        };
    },

    computed: {
        isGuest() {
            return this.user === undefined;
        }
    }
};
</script>

<style lang="scss" scoped>
/*
 * The free space from any navbar content to the top and bottom is set this way
 * to allow the nav-links' active state background to encompass the whole navbar.
 */
.navbar {
    padding: 0;
}
.nav-item,
.navbar-brand {
    padding-top: 2rem;
    padding-bottom: 2rem;
}
.navbar-brand {
    margin-left: 4vw;
}

// height == line-height to vertically center the text inside the nav-links.
.navbar-brand > img,
.navbar-toggler,
.nav-item > .nav-link,
.profile-picture {
    height: 3rem;
    line-height: 3rem;
}

// The selector has to be this specific to override the styling for .nav-link.
.navbar-nav > .nav-item > .nav-link {
    color: var(--sdash-dark);
    // font-weight: bold;
    opacity: 1;
    padding: 0;
}
.navbar-nav > .nav-item > .nav-link:hover {
    color: var(--sdash-dark-hover);
}

// The secondary nav links' text should be slightly smaller than the primary ones.
.navbar-toggler,
.primary-nav {
    font-size: 2rem;
}
.secondary-nav {
    font-size: 1.5rem;
}

// Making the active state of the primary nav links look right.
.navbar-nav .nav-item {
    padding-left: 2vw;
    padding-right: 2vw;
}
.primary-nav .nav-item.router-link-exact-active {
    background-color: var(--sdash-blue);
}
.primary-nav .nav-item.router-link-exact-active .nav-link {
    color: white;
}

// Rounded profile picture.
img.profile-picture {
    border-radius: 50%;
}

@media (max-width: 767px) {
    // Reduce the amount of empty space on smaller screens.
    .navbar-brand {
        margin-left: 2vw;
    }
    .nav-item,
    .navbar-brand {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    /*
     * That the secondary nav links are smaller doesn't catch the eye as much
     * when they're far apart horizontally with a bigger screen. Once they're
     * right above each other in the expanded menu that we're using on small
     * screens they absolutely need to have the same font size.
     */
    .primary-nav {
        font-size: 1.5rem;
    }
}
</style>
