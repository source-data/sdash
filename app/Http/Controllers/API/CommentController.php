<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Notifications\NewCommentOnYourPanel;
use App\Notifications\NewReplyToYourPanelComment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Panel $panel, Request $request)
    {
        $user = auth()->user();

        //NOTE: at the moment anyone can comment on a public panel
        if (Gate::allows('view-panel', $panel)) {
            $request->validate([
                'comment'   => 'required|string',
                'reply_to'  => 'nullable|exists:comments,id'
            ]);

            $replyIsTo = $request->input('reply_to') ? $request->input('reply_to') : null;

            $comment = $panel->comments()->create([
                'user_id'   => $user->id,
                'comment'   => strip_tags($request->input('comment')),
                'reply_to'  =>  $replyIsTo,
            ]);

            $panelOwner = User::find($panel->user_id);

            if( $panelOwner->id !== $user->id){
                $panelOwner->notify(new NewCommentOnYourPanel($user, $panel, $comment));
            }


            if($replyIsTo){

                $replyComment = Comment::find($replyIsTo);
                $recipient = $replyComment->user;
                $recipient->notify(new NewReplyToYourPanelComment($user, $panel, $comment));

            }

            return API::response(200, 'Comment successfully posted.', $comment->load(['user']));


        } else {
            return API::response(401,"Access denied.",[]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        if(Gate::allows('modify-comment', $comment)){
            $request->validate([
                'comment'   => 'required|string'
            ]);

            $commentText = strip_tags($request->input("comment"));
            $comment->comment = $commentText;
            $comment->save();

            return API::response(200, 'Comment updated.', $comment->load(['user']));

        } else {
            return API::response(401,"Access denied.",[]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if(Gate::allows('modify-comment', $comment)){
            $comment->delete();
            return API::response(200, "Comment deleted", []);
        } else {
            return API::response(401,"Access denied.",[]);
        }
    }
}
