<template>
    <div class="form-group sourcedata-tags" :class="'sourcedata-tags-' + type">
        <vue-tags-input
            v-if="iCanEditTags"
            placeholder="Enter tags or use SmartTag"
            :tags="combinedTags"
            v-model="newTag"
            :add-on-key="[13,',']"
            @before-adding-tag="createTag"
            @before-deleting-tag="deleteTag"
            :disabled="!iCanEditTags"
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

        <div class="panel-keywords">
            <span class="panel-keyword" v-for="tag in combinedTags" :key="tag.id">
                {{ tag.text }}
            </span>
        </div>
    </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {

    name: 'SmartTagsCategory',
    components: { },
    props: ['type'],

    data(){

        return {
            tags:[],
            newTag:'',

        }

    }, /* end of data */

    computed: {
        ...mapGetters([
            'iOwnThisPanel',
            'iCanEditTags',
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
            const keyWord = {id: tag.tag_id, name: tag.text}
            this.$store.commit("setKeywordFilter", [keyWord])
            this.$store.dispatch("setLoadingState", true)
            this.$store.dispatch("clearLoadedPanels")
            this.$store.dispatch("fetchPanelList")
        }

    }

}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.form-group::v-deep .vue-tags-input {
    background-color: $mostly-white-gray;
    max-width: 100%;
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


.sourcedata-tags::v-deep .ti-tag,
.sourcedata-tags .panel-keyword {
    color: $mostly-white-gray;
}
.sourcedata-tags-assay::v-deep .ti-tag,
.sourcedata-tags-assay .panel-keyword {
    background-color: $sourcedata-color-assay;
}
.sourcedata-tags-intervention::v-deep .ti-tag,
.sourcedata-tags-intervention .panel-keyword {
    background-color: $sourcedata-color-intervention;
}
.sourcedata-tags-method::v-deep .ti-tag,
.sourcedata-tags-method .panel-keyword {
    background-color: $sourcedata-color-method;
    color: $very-dark-blue;
}
.sourcedata-tags-other::v-deep .ti-tag,
.sourcedata-tags-other .panel-keyword {
    background-color: $sourcedata-color-other;
}

.panel-keywords {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.5rem;

    .panel-keyword {
        border-radius: 0.5rem;
    }
}
.panel-keyword-category,
.panel-keyword {
    line-height: 1rem;
    padding: 0.2rem 0.75rem 0.3rem;
}
</style>