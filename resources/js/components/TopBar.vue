<template>
    <div class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <a class="navbar-brand" :href="homeUrl">
            <font-awesome-icon icon="home" /> SDash
        </a>
        <search-bar :exclude-groups="isPublic"></search-bar>
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
        homeUrl: {
            type: String,
            required: true
        },
        isPublic: {
            type: Boolean
        }
    },

    data() {
        return {};
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
}
</style>
