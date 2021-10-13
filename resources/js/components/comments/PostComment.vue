<template>
    <div class="sd-post-comment" id="sd-post-comment">
        <header v-if="replyingTo" class="sd-post-comment-meta">
            Replying to {{ replyingTo.user.firstname }} {{ replyingTo.user.surname }} on {{ formattedReplyDate }}

            <b-button class="sd-cancel-reply" pill size="sm" variant="danger" @click="cancelReply">
                Cancel
            </b-button>
        </header>

        <b-form class="sd-post-comment-form" @submit.prevent="postComment">
            <b-form-textarea
                id="sd-post-content-area"
                v-model="postContent"
                placeholder="Write a comment"
                rows="3"
                max-rows="6"></b-form-textarea>

            <b-button class="sd-post-commment-button" type="submit" size="sm" variant="light">
                Post
            </b-button>
        </b-form>
    </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'

export default {

    name: 'PostComment',
    data(){

        return {
            postContent: "",
        }

    }, /* end of data */
    computed:{
        ...mapGetters(['comments', 'commentCount', 'replyingToId']),
        replyingTo(){
            let replyingToComment = this.comments.filter( cmnt => cmnt.id === this.replyingToId )
            if (replyingToComment.length === 0) return false
            if (!replyingToComment[0].user) return false
            return replyingToComment[0]

        },
        formattedReplyDate(){
            return moment(this.replyingTo.created_at).format("ddd D MMM YYYY HH:mm")
        }
    },

    methods:{ //run as event handlers, for example

        postComment(){
            this.$store.dispatch("postComment", this.postContent).then(response => {
                this.$snotify.success("Your comment has been posted", "Comment Posted")
                this.$store.commit("clearReplyingToId")
                this.postContent = ""
            }).catch(error => {
                this.$snotify.error(error.data.message, "Comment failed")
            })
        },
        cancelReply(){
            this.$store.commit("clearReplyingToId")
        }


    }

}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.sd-post-comment-form {
    background-color: $mostly-white-gray;
    border-radius: 0.5rem;
    position: relative;
}

.sd-post-comment-form textarea {
    overflow-y: auto !important;
}

.sd-post-commment-button {
    background-color: $vivid-orange;
    border-radius: 0.5rem;
    color: $mostly-black-blue;

    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
}
</style>