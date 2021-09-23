<template>
    <div class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <router-link class="nav-link navbar-brand" :to="{ name: 'dashboard'}">
            <font-awesome-icon icon="home" /> SDash
        </router-link>
        <search-bar :exclude-groups="isGuest"></search-bar>
    </div>
</template>

<script>
import SearchBar from "@/components/SearchBar";

export default {
    name: "TopBar",

    components: {
        SearchBar
    },

    props: {
        user: {
            type: Object
        }
    },

    data() {
        return {};
    },

    computed: {
        isGuest() {
            return this.user === undefined;
        }
    }
};
</script>

<style lang="scss" scoped>
.navbar {
    margin: 0;
    padding: 0.5rem 1rem;
}

.navbar-brand {
    margin-right: 1rem;
    padding-left: 0;
}
</style>
