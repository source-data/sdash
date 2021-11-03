<template>
    <li>
        <article class="sd-user-comment" :id="'sd-user-comment-' + thisComment.id">
            <header  class="sd-user-comment--header sd-user-comment--block">
                <strong class="sd-user-comment--user-name">
                    {{ commentorName }}
                </strong>

                posted on {{ this.formattedPostDate }}
            </header>

            <div class="sd-user-comment--content sd-user-comment--block">
                <div v-if="thisComment.reply_to" class="sd-user-comment--reply-quote-block">
                    <span class="sd-user-comment--reply-quote-meta">
                        @{{ replyToCommentorName }} ({{ formattedReplyDate }})
                    </span>

                    <blockquote class="sd-user-comment--reply-quote-text">
                        {{ replyToComment.comment }}
                    </blockquote>
                </div>

                {{ thisComment.comment }}
            </div>

            <footer class="sd-user-comment--actions sd-user-comment--block">
                <b-button
                    v-if="!isDeleted"
                    size="xs"
                    @click="setReply"
                    v-b-tooltip.hover.top="{ customClass: 'sd-reply-to-tooltip' }" title="Reply to this comment"
                    v-scroll-to="{
                        el: '#sd-post-comment',
                        container: '.sd-panel-detail-tab-card .card-text'
                    }"
                >
                    Reply
                </b-button>

                <b-button
                    v-if="thisIsMyComment"
                    size="xs"
                    v-b-tooltip.hover.top="{ customClass: 'sd-remove-comment-tooltip' }" title="Delete your comment"
                    :id="'remove-comment-' + thisComment.id"
                >
                    Delete
                </b-button>

                <!-- remove me popover-->
                <b-popover
                    v-if="thisIsMyComment"
                    :ref="'remove-comment-popover-' + thisComment.id"
                    :target="'remove-comment-' + thisComment.id"
                    triggers="click blur"
                    placement="right"
                >
                    <template v-slot:title>
                            Delete Comment?
                        <b-button @click="closeDeleteCommentPopover" class="close" aria-label="Close">
                            <span class="d-inline-block" aria-hidden="true">&times;</span>
                        </b-button>
                    </template>

                    <div class="confirm-refresh-link">
                        <p>
                            Are you sure you want to delete your comment?
                        </p>

                        <div class="refresh-buttons">
                            <b-button variant="danger" small @click="deleteThisComment">Delete it!</b-button>
                            <b-button variant="outline-dark" small @click="closeDeleteCommentPopover">Cancel</b-button>
                        </div>
                    </div>
                </b-popover>
                <!-- end of remove me popover -->
            </footer>
        </article>
    </li>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import store from '@/stores/store'


export default {

    name: 'Comment',
    components: { },
    props: ['comment_id'],

    data(){

        return {

        }

    },
    computed: {
        ...mapGetters(['comments', 'currentUser',]),
        thisComment(){
            return this.comments.filter(comment => comment.id === this.comment_id)[0]
        },
        isDeleted() {
            return !!this.thisComment.deleted_at
        },
        replyToComment(){
            if(!this.thisComment.reply_to) return false
            return this.comments.filter(comment => comment.id === this.thisComment.reply_to)[0]
        },
        formattedPostDate(){
            return moment(this.thisComment.created_at).format("ddd D MMM YYYY HH:mm")
        },
        formattedReplyDate(){
            return moment(this.replyToComment.created_at).format("D MMM YY HH:mm")
        },
        thisIsMyComment() {
            return this.currentUser.id === this.thisComment.user_id
        },
        commentorName() {
            const comment = this.thisComment
            return comment.user ? comment.user.firstname + " " + comment.user.surname : "Comment Deleted"
        },
        replyToCommentorName() {
            const comment = this.replyToComment
            return comment.user ? comment.user.firstname + " " + comment.user.surname : "Comment Deleted"
        }
    },
    methods:{ //run as event handlers, for example

        setReply(){
            this.$store.commit("setReplyingToId", this.thisComment.id)
            document.getElementById("sd-post-content-area").focus()
        },
        deleteThisComment() {
            const commentId = this.thisComment.id
            if(this.thisIsMyComment) {
                this.$store.dispatch("deleteComment", commentId).then( response =>{
                    this.$snotify.success("Comment deleted", "Success!")
                }).catch(error => {
                    this.$snotify.error(error.data.message, "Sorry!")
                    this.closeDeleteCommentPopover()

                })
            }
        },
        closeDeleteCommentPopover() {
            const popupRef = 'remove-comment-popover-' + this.thisComment.id
            this.$refs[popupRef].$emit("close")
        },

    }

}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

.sd-user-comment {
    margin-bottom: 0.5rem;
}

.sd-user-comment--reply-quote-block {
    margin-bottom: 0.2rem;
    margin-left: 1rem;
    border-left: 0.3rem solid $mostly-white-gray-opaque;
    padding-left: 0.3rem;
}
.sd-user-comment--reply-quote-block blockquote {
    margin: 0;
}

.sd-user-comment--actions {
    line-height: 0.5rem;
}
.sd-user-comment--actions button {
    background: none;
    border: none;
    color: inherit;
    font-size: 0.65rem;
    padding: 0;
}

.sd-user-comment--actions button:active,
.sd-user-comment--actions button:focus,
.sd-user-comment--actions button:hover {
    text-decoration: underline;
}
</style>