<template>
<multiselect
class="sd-keyword-multiselect"
@select="addKeyword"
:id="id"
label="name"
track-by="id"
placeholder="Type keyword to search"
open-direction="bottom"
:options="keywords"
:multiple="false"
:searchable="true"
:loading="isLoading"
:internal-search="false"
:clear-on-select="true"
:close-on-select="true"
:reset-after="true"
:options-limit="20"
:max-height="600"
:show-no-results="true"
:hide-selected="true"
@search-change="asyncFind">
    <template slot="option" slot-scope="props">
        <div class="sd-group-keywords-option">
            <div class="sd-group-keywords--info">
                <div class="sd-group-keywords--name">
                    {{ props.option.content }}
                </div>
            </div>
        </div>
    </template>
    <span slot="noResult">No matching keywords found.</span>
</multiselect>

</template>

<script>

import Multiselect from 'vue-multiselect'
import store from '@/stores/store'
import { mapActions } from 'vuex'
import _ from 'lodash'

export default {

    name: 'KeywordMultiselect',
    components: {
        Multiselect,
    },
    props: {
        id: String,
        initialKeywords: Array,
    },
    data() {
        return {
            keywords: [],
            isLoading: false,
        }
    },
    methods: {
        ...mapActions(["findTagsByName"]),
        asyncFind: _.debounce(function(queryString){

            if(queryString.length > 0) {
                this.findTagsByName(queryString).then((result) => {
                    if(result.data.DATA && result.data.DATA.length > 0) {
                        const keywordResult = result.data.DATA;
                        const selectList = [];
                        const alreadySelected = this.initialKeywords;
                        keywordResult.map(keyword => {
                            if (!_.find(alreadySelected, (already) => {
                                return (already.id === keyword.id);
                            })) {
                                console.log(keyword, alreadySelected);
                                selectList.push(keyword);
                            }
                        });
                        this.keywords = selectList;
                    } else {
                        this.keywords = []
                    }
                })
            } else {
                this.keywords = []
            }

        }, 300),
        addKeyword(keywordData) {
          this.$emit('select', keywordData);
        }
    },

}

</script>

<style lang="scss">
    .sd-keyword-multiselect {
        margin: 1rem 0;
    }

    .multiselect.sd-keyword-multiselect .multiselect__tags {
        border: solid 1px #ced4da;
    }

    .sd-group-keywords-option {
        display: flex;
    }

    .sd-group-keywords--selected {
        display:flex;
        padding: 0.25rem;
        background: #e7edf7;
        margin-bottom:0.25rem;
    }

    .sd-group-keywords--selected > div {
        display: flex;
        flex-wrap: wrap;
        padding: 0.25rem;
    }

    .sd-group-keywords--selected-info {
        flex-grow: 1;
        flex-direction: vertical;
    }

    .sd-group-keywords--selected-name {
        font-size: 0.85rem;
        font-weight: bold;
        width: 100%;
    }

    .custom__remove {
        cursor: pointer;
    }
</style>