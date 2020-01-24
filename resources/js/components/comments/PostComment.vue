<template>
 <div class="sd-post-comment" id="sd-post-comment">
    <header v-if="replyingTo" class="sd-post-comment-meta">
        Replying to {{ replyingTo.user.firstname }} {{ replyingTo.user.surname }} on {{ formattedReplyDate }} <b-button class="sd-cancel-reply" pill size="sm" variant="danger" @click="cancelReply">Cancel</b-button>
    </header>
    <b-form @submit.prevent="postComment">
        <b-form-textarea
        id="sd-post-content-area"
        v-model="postContent"
        placeholder="Write a comment"
        rows="3"
        max-rows="6"
        ></b-form-textarea>

        <div class="sd-post-commment-buttons">
            <b-button
                type="submit"
                size="sm"
                variant="light"
            >Post
            </b-button>
        </div>

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
                console.log(error)
            })
        },
        cancelReply(){
            this.$store.commit("clearReplyingToId")
        }


    }

}
</script>

<style lang="scss">


    .sd-post-commment-buttons{
        text-align:right;
        padding:6px 0;
    }

.sd-post-comment {
    border: solid 1px #eee;
    padding: 12px;
    margin-top: 6px;
    border-radius: 4px;
    background-color:#2e3746;
}

.sd-cancel-reply {
    font-size: 0.86em;
    padding: 2px 6px;
    margin-bottom: 4px;
}

</style>