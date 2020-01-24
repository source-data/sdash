<template>
    <div>
        <li>
            <article class="sd-user-comment" :id="'sd-user-comment-' + thisComment.id">
                <header  class="sd-user-comment--header sd-user-comment--block">
                    <strong class="sd-user-comment--user-name">
                        {{ thisComment.user.firstname }} {{ thisComment.user.surname }}
                    </strong>
                     posted on {{ this.formattedPostDate }}
                </header>
                <div class="sd-user-comment--content sd-user-comment--block">
                    <div v-if="thisComment.reply_to" class="sd-user-comment--reply-quote-block">
                        <span class="sd-user-comment--reply-quote-meta">
                            @{{replyToComment.user.firstname}} {{ replyToComment.user.surname }} ({{ formattedReplyDate }})
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
        ...mapGetters(['comments']),
        thisComment(){
            return this.comments.filter(comment => comment.id === this.comment_id)[0]
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
        }
    },
    methods:{ //run as event handlers, for example

        setReply(){
            this.$store.commit("setReplyingToId", this.thisComment.id)
            document.getElementById("sd-post-content-area").focus()
        }

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

</style>