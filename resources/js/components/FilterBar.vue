<template>
    <aside class="sd-filter-bar">
        <section class="pb-4">
            <h4 class="pb-1">Filter Panels</h4>
            <div>
                <b-form-radio-group stacked name="toggle-panel-list-privacy" v-model="privacyLevel">
                    <b-form-radio value="all">Show all Panels</b-form-radio>
                    <b-form-radio value="private">Show My Own Panels</b-form-radio>
                </b-form-radio-group>
            </div>
        </section>

        <h5 class="pb-2">My Groups</h5>
        <div role="tablist" class="sd-group-list-wrapper">
            <b-card v-for="group in userGroups" :key="group.id" no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button block href="#" v-b-toggle="'group-'+group.id" class="sd-filter-accordion-header" variant="light">{{ group.name }} <br>| <font-awesome-icon icon="users" /> {{group.confirmed_users_count}} | <font-awesome-icon icon="layer-group" /> {{group.panels_count}} </b-button>
            </b-card-header>
            <b-collapse :id="'group-' + group.id" accordion="sd-filter-accordion" role="tabpanel">
                <b-card-body>
                <b-card-text>
                    <router-link :to="{path:'/group/' + group.id}">Go to group</router-link>
                </b-card-text>
                <b-card-text>
                    {{ group.description }}
                </b-card-text>
                </b-card-body>
            </b-collapse>
            </b-card>
        </div>
    </aside>
</template>

<script>

import store from '@/stores/store'
import { mapGetters } from 'vuex'

            //     <b-button variant="link" @click="showAllPanels">Show All</b-button>
            // </div>
            // <div>
            //     <b-button variant="link" @click="showMyPanels">Show My Panels</b-button>
export default {

    computed: {
        ...mapGetters([
            'userGroups',
            'privatePanels',
        ]),
        privacyLevel:{

            get() {
                return (this.privatePanels===true) ? "private" : "all"
            },
            set(value) {
                let privacy = (value === "private") ? true : false
                this.toggleAccess(privacy)
            }
        }
    },
    methods: {
        toggleAccess(value){
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("setSearchString", "")
            this.$store.dispatch("setPrivate", value)
            this.$store.dispatch("fetchPanelList")
        }
    }

}
</script>

<style>
  .sd-filter-accordion-header {
      text-align:left;
      background-color: none;
  }

  .sd-group-list-wrapper {
      max-height: 600px;
      overflow-y: scroll;
      border: solid 1px #e0e0e0;
      padding: 1rem 0.5rem;

  }
</style>