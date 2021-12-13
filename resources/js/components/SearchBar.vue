<template>
    <div class="search" :class="{ active: isActive }">
        <label class="icon">
            <font-awesome-icon icon="search" />
        </label>
        <b-form-input
            type="search"
            placeholder="Smartfigures or groups"
            v-model="searchQuery"
            @focus="focusInput"
            @blur="defocusInput"
        ></b-form-input>
        <ul class="dropdown">
            <li @click.prevent.stop="search('panels')">
                <span class="icon">
                    <font-awesome-icon icon="images" />
                </span>
                Search smartfigures
            </li>
            <li @click.prevent.stop="search('groups')" v-if="!excludeGroups">
                <span class="icon">
                    <font-awesome-icon icon="users" />
                </span>
                Search groups
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "SearchBar",

    props: {
        excludeGroups: Boolean
    },

    data() {
        return {
            searchQuery: "",
            inputOnFocus: false
        };
    },

    computed: {
        isActive() {
            return /* this.inputOnFocus && */ this.searchQuery.length > 0;
        }
    },

    methods: {
        focusInput() {
            this.inputOnFocus = true;
        },
        defocusInput() {
            this.inputOnFocus = false;
        },
        search(category) {
            let name = "";
            if (category === "panels") {
                name = "dashboard";
            } else if (category === "groups") {
                name = "groups";
            } else {
                console.log("Invalid search category");
                return;
            }

            const query = {};
            if (this.searchQuery) {
                query.q = this.searchQuery;
            }

            this.$router.push({
                name,
                query,
            }).catch(err => {});
            this.searchQuery = "";
        },
    },
};
</script>

<style lang="scss" scoped>
.search {
    position: relative;
    border-radius: 3px;
}
@media (min-width: 768px) {
    .search {
        width: 480px;
    }
}

.search input {
    width: 100%;
    height: 30px;
    outline: none;
    border: none;
    border-radius: 3px;
    padding: 0 10px 0 calc(30px + 0.75rem);
    background: #f8f9fa;
    font-size: 1em;
}

.search.active input {
    border-radius: 5px 5px 0 0;
}

.search .dropdown {
    position: absolute;
    top: 30px;
    left: 0;
    width: 100%;
    margin: 0;
    padding: 0.25rem 0;
    opacity: 0;
    background: white;
    pointer-events: none;
    border-radius: 0 0 5px 5px;
    z-index: 100;
}

.search.active .dropdown {
    opacity: 1;
    pointer-events: auto;
}

.search .dropdown li {
    list-style: none;
    padding: 0.25rem;
    display: none;
    width: 100%;
    cursor: pointer;
    border-radius: 3px;
}

.search.active .dropdown li {
    display: block;
}

.search .dropdown li:hover {
    background: #efefef;
}

.search .dropdown li .icon {
    margin-left: 0.25rem;
    display: inline-block;
    color: #b9c2d2;
}

.search > .icon {
    position: absolute;
    left: 0.5rem;
    top: 0px;
    line-height: 30px;
    font-size: 1em;
    color: #235588;
    cursor: pointer;
}

.search .icon,
.search .dropdown li .icon {
    width: 30px;
    text-align: center;
}
</style>
