<template>
    <div>
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
                    <hr>
                </div>
                <footer class="sd-user-comment--actions sd-user-comment--block">
                    <b-button
                        size="sm"
                        variant="danger"
                        v-b-tooltip.hover.left="{ customClass: 'sd-remove-comment-tooltip' }" title="Delete your comment"
                        v-if="thisIsMyComment"
                        :id="'remove-comment-' + thisComment.id"
                    >
                        <font-awesome-icon
                            class="sd-delete-comment-icon"
                            icon="trash-alt"
                            title="Delete panel"
                        />
                    </b-button>

                    <!-- remove me popover-->
                    <b-popover
                        v-if="thisIsMyComment"
                        :ref="'remove-comment-popover-' + thisComment.id"
                        :target="'remove-comment-' + thisComment.id"
                        triggers="click"
                        placement="topleft"
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

                    <b-button
                        v-if="!isDeleted"
                        type="submit"
                        size="sm"
                        variant="light"
                        @click="setReply"
                        v-scroll-to="{
                            el: '#sd-post-comment',
                            container: '.sd-panel-detail-tab-card .card-text'
                        }"
                        >Reply
                    </b-button>
                </footer>

            </article>
        </li>
    </div>
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
                }).finally(() => {
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

<style lang="scss">

    .sd-user-comment {
        background-color:#2f2f2f;
        margin-bottom: 0.5em;
    }

    .sd-user-comment--block {
        padding: 3px 6px;

        hr {
            margin: 6px 12px 0;
            border-top: solid 1px #6c6c6c;
        }
    }


    .sd-user-comment--user-name {
        color: #6e89aa;
    }

    .sd-user-comment--header {
        background-color: #2e3746;
    }

    .sd-user-comment--actions {
        text-align:right;
    }

    .sd-user-comment--reply-quote-block {
        margin: 0.5em;
        padding: 0.25em;
        background-color: #4f4f4f;
    }

    .sd-user-comment--reply-quote-meta {
        font-style:italic;
    }

.sd-remove-comment-tooltip {
    .tooltip-inner {
        background-color:#eee;
        color: #333;
    }
    .arrow:before {
        border-left-color:#eee;
    }

}

</style>