<template>
    <article>
        <b-card title="Experimental Design" class="tag-group bg-dark">
            <smart-tags-category title="Measured Variables" type="assay"></smart-tags-category>
            <smart-tags-category title="Controlled Variables" type="intervention"></smart-tags-category>
            <smart-tags-category title="Instruments / Methods" type="method"></smart-tags-category>
        </b-card>
        <b-card title="General Keywords" class="tag-group bg-dark">
            <smart-tags-category type="other"></smart-tags-category>
        </b-card>
        <copy-tags></copy-tags>
    </article>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'
import SmartTagsCategory from '@/components/tags/SmartTagsCategory'
import CopyTags from '@/components/tags/CopyTags'

export default {

    name: 'SmartTagsPanel',
    components: { SmartTagsCategory, CopyTags },

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