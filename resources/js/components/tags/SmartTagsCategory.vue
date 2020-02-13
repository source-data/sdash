<template>
    <section class="sd-smarttags-category-container">
        <h4 class="sd-smarttags-category-container--title">{{ title }}</h4>
        <vue-tags-input
            placeholder="Enter tags or use SmartTag"
            :tags="combinedTags"
            v-model="newTag"
            :add-on-key="[13,',']"
            @before-adding-tag="createTag"
            @before-deleting-tag="deleteTag"
            :disabled="!iOwnThisPanel"
        >
        <div
            slot="tag-center"
            slot-scope="props"
        >
            <span class="sd-tag-clickable" @click.stop="tagSearch(props.tag)">
            {{props.tag.text}}
            </span>

        </div>
        <div
            class="sd-validate-suggested-tag"
            slot="tag-right"
            slot-scope="tag"

        >
            <font-awesome-icon @click="validateSuggestion(tag)" v-if="showValidate(tag)" class="sd-validate-suggested-tag-icon" icon="check" size="sm" />
        </div>
        </vue-tags-input>
    </section>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'

export default {

    name: 'SmartTagsCategory',
    components: { },
    props: ['title', 'type'],

    data(){

        return {
            tags:[],
            newTag:'',

        }

    }, /* end of data */

    computed: {
        ...mapGetters([
            'iOwnThisPanel',
            'userTags',
            'methodTags',
            'interventionTags',
            'assayTags',
            'otherTags',
            'suggestedMethodTags',
            'suggestedInterventionTags',
            'suggestedAssayTags',
            'suggestedOtherTags',
        ]),
        activeTags(){
            switch(this.type) {
                case 'user':
                    return this.userTags
                    break
                case 'method':
                    return this.methodTags
                    break
                case 'intervention':
                    return this.interventionTags
                    break
                case 'assay':
                    return this.assayTags
                    break
                default:
                    return this.otherTags
            }
        },
        suggestedTags(){
            switch(this.type) {
                case 'user':
                    return this.suggestedUserTags
                    break
                case 'method':
                    return this.suggestedMethodTags
                    break
                case 'intervention':
                    return this.suggestedInterventionTags
                    break
                case 'assay':
                    return this.suggestedAssayTags
                    break
                default:
                    return this.suggestedOtherTags
            }
        },
        combinedTags(){
            let combined = [];
            this.suggestedTags.forEach((tag, index) => {combined.push({ value:tag.name, text:tag.name, array_position:tag.array_position, status:"suggested", classes: "sd-suggested-tag"})})
            this.activeTags.forEach((tag, index) => {combined.push({ value:tag.name, text:tag.content, index:index, tag_id:tag.id, relationship_id:tag.meta.id, status:"confirmed"})})
            return combined
        }
    },

    methods:{ //run as event handlers, for example

        createTag(tag) {
            let createdTag = {name: tag.tag.text, origin: "smarttag", role: "", type: "", category: ""};
            switch(this.type) {
                case 'method':
                    createdTag.category = 'assay'
                    break
                case 'intervention':
                    createdTag.role = 'intervention'
                    break
                case 'assay':
                    createdTag.role = 'assayed'
                    break
            }
            this.$store.dispatch("createUserInputTag", createdTag).then( () => {
                this.newTag = ""
            }).catch(error => {
                this.$snotify.error("Could not store this tag", "Sorry!")
            })
        },
        validateSuggestion(tag) {
            this.$store.dispatch("validateSuggestedTag", {array_position: tag.tag.array_position, origin: "smarttag"}).catch(error => {
                this.$snotify.error("Could not store this tag", "Sorry!")
            })
        },
        showValidate(tag) {
            return tag.tag.status === 'suggested'
        },
        deleteTag(tag){
            let delTag = this.combinedTags[tag.index]
            if(delTag.status === 'suggested'){
                this.$store.commit('discardSuggestedTag', delTag)
            } else {
                this.$store.dispatch('deleteTag', delTag).catch(error => {
                    this.$snotify.error("Tag could not be removed", "Sorry!")
                })
            }

        },
        tagSearch(tag){
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("setSearchString", tag.text)
            this.$store.dispatch("fetchPanelList")
        }

    }

}
</script>

<style lang="scss">
.sd-smarttags-category-container {
    padding-bottom: 8px;
}

.sd-smarttags-category-container--title {
    font-size:1em;
}

.sd-suggested-tag {
    background-color: #717171 !important;
}

.svg-qm {
    width: 60px;
    height: 60px;
}

.sd-validate-suggested-tag {
    cursor: pointer;
}

.sd-validate-suggested-tag-icon {
     margin: 0 2px 0 4px;
}

.sd-tag-clickable {
    cursor: pointer;
    padding: 0 3px;
    border: solid 1px transparent;
    transition: border-color 200ms linear;
}

.sd-tag-clickable:hover {
    border: dotted 1px #cecece;
}

</style>