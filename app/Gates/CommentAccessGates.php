<?php

namespace App\Gates;
use App\Models\Comment;
use App\User;

class CommentAccessGates
{
    public function canModifyComment(User $user, Comment $comment)
    {
        if($user->is_superadmin()) return true;

        if($comment->user_id === $user->id) return true;
    }
}
