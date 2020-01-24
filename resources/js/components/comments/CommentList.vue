<template>
    <section class="sd-comment-list">
        <ul class="sd-comments list-unstyled">
                <comment v-for="thisComment in comments" :key="thisComment.id" :comment_id="thisComment.id"></comment>
        </ul>
        <post-comment></post-comment>
    </section>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'
import Comment from '@/components/comments/Comment'
import PostComment from '@/components/comments/PostComment'

export default {

    name: 'CommentList',
    components: { Comment, PostComment },
    props: [],
    computed:{
        ...mapGetters(['comments', 'commentCount']),
    },
    data(){

        return {

        }

    }, /* end of data */

    methods:{ //run as event handlers, for example

        methodName(){
            //do stuff here
        }

    }

}
</script>

<style lang="scss">

</style>