<template>
    <article>
        <smart-tags-category title="Instruments/Methods" type="method"></smart-tags-category>
        <smart-tags-category title="Measured Var" type="assay"></smart-tags-category>
        <smart-tags-category title="Controlled Var" type="intervention"></smart-tags-category>
        <smart-tags-category title="Other Entities" type="other"></smart-tags-category>
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

</style>