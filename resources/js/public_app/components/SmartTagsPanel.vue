<template>
    <article>
        <b-card title="Experimental Design" class="tag-group bg-dark">
          <b-card-text>
            <div class="sd-public-tags-wrapper" v-if="assayTags">
              <h4>Measured Variables</h4>
                <ul class="sd-public-tags-list">
                  <li class="sd-public-tags-list--item" v-for="tag in assayTags" :key="'tag-' + tag.id">
                    {{tag.content}}
                  </li>
                </ul>
            </div>
          </b-card-text>
        </b-card>
        <b-card title="General Keywords" class="tag-group bg-dark">
          <b-card-text>
            Tags 2
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

</style>