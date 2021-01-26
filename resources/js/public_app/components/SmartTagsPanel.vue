<template>
    <article>
        <b-card title="Experimental Design" class="tag-group bg-dark" v-if="hasExperimentalDesignTags">
          <b-card-text>
            <div class="sd-public-tags-wrapper" v-if="assayTags.length > 0">
              <h4 class="sd-smarttags-category-container--title">Measured Variables</h4>
                <ul class="sd-public-tags-list list-inline">
                  <li class="sd-public-tags-list--item list-inline-item" v-for="tag in assayTags" :key="'tag-' + tag.id">
                    <b-badge tabindex="0" @click="tagSearch(tag.content)" variant="primary">{{tag.content}}</b-badge>
                  </li>
                </ul>
            </div>
            <div class="sd-public-tags-wrapper" v-if="interventionTags.length > 0">
              <h4 class="sd-smarttags-category-container--title">Controlled Variables</h4>
                <ul class="sd-public-tags-list list-inline">
                  <li class="sd-public-tags-list--item list-inline-item" v-for="tag in interventionTags" :key="'tag-' + tag.id">
                    <b-badge tabindex="0" @click="tagSearch(tag.content)" variant="primary">{{tag.content}}</b-badge>
                  </li>
                </ul>
            </div>
            <div class="sd-public-tags-wrapper" v-if="methodTags.length > 0">
              <h4 class="sd-smarttags-category-container--title">Instruments / Methods</h4>
                <ul class="sd-public-tags-list list-inline">
                  <li class="sd-public-tags-list--item list-inline-item" v-for="tag in methodTags" :key="'tag-' + tag.id">
                    <b-badge tabindex="0" @click="tagSearch(tag.content)" variant="primary">{{tag.content}}</b-badge>
                  </li>
                </ul>
            </div>
          </b-card-text>
        </b-card>
        <b-card title="General Keywords" class="tag-group bg-dark" v-if="otherTags.length > 0">
          <b-card-text>
            <div class="sd-public-tags-wrapper">
              <h4 class="sd-smarttags-category-container--title">General Keywords</h4>
                <ul class="sd-public-tags-list list-inline">
                  <li class="sd-public-tags-list--item list-inline-item" v-for="tag in otherTags" :key="'tag-' + tag.id">
                    <b-badge tabindex="0" @click="tagSearch(tag.content)" variant="primary">{{tag.content}}</b-badge>
                  </li>
                </ul>
            </div>
          </b-card-text>
        </b-card>
    </article>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'SmartTagsPanel',
    computed: {
      ...mapGetters([
        'hasTags',
        'userTags',
        'methodTags',
        'interventionTags',
        'assayTags',
        'otherTags',
      ]),
      hasExperimentalDesignTags() {
        return (this.interventionTags.length + this.methodTags.length + this.assayTags.length > 0)
      },
    },
    methods: {
      tagSearch(searchString){
          this.$store.commit("setPanelLoadingState", true)
          this.$store.commit("clearLoadedPanels")
          this.$store.commit("setSearchString", searchString)
          this.$store.dispatch("fetchPanelList")
      },


    }

}
</script>

<style lang="scss">

.tag-group {
    margin-bottom: 0.75rem;
}

.tag-group .card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: white;
}

.sd-smarttags-category-container--title {
    font-size:1em;
    margin-bottom: 0.25rem;
}

.sd-public-tags-wrapper {
  margin-top: 0.5rem;
}

.sd-public-tags-wrapper:nth-child(1) {
  margin-top:0;
}

.sd-public-tags-list--item {
  cursor: pointer;
}

</style>