<template>
    <div>
        <ul class="sd-comments list-unstyled">
            <comment v-for="thisComment in comments" :key="thisComment.id" :comment_id="thisComment.id"></comment>
        </ul>
        <post-comment v-if="isLoggedIn"></post-comment>
    </div>
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
        ...mapGetters(['comments', 'commentCount', 'isLoggedIn']),
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

<style lang="scss" scoped>

</style>