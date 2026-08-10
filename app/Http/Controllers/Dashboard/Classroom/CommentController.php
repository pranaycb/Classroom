<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Models\Reply;
use App\Models\Comment;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dashboard\Classroom\ClassroomController;

class CommentController extends ClassroomController
{
    /**
     * Get comments
     */
    protected function comments(Classroom $classroom, $slug, $id)
    {
        $model = match($slug){
            'stream' => $classroom->announcements()->findOrFail($id),
            'assignment' => $classroom->assignments()->findOrFail($id),
        };

        return $model->comments();
    }

    /**
     * Store comments
     */
    public function storeComment(Request $request, Classroom $classroom)
    {
        abort_if(Auth::user()->cannot('create', Comment::class), 401);

        $request->validate([
            'slug' => 'required|in:stream,assignment',
            'id' => 'required|numeric',
            'comment' => 'required',
        ]);

        $this->comments($classroom, $request->slug, $request->id)->create([
            'user_id' => Auth::id(),
            'content' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment has been posted successfully');
    }

    /**
     * Store product comment reply
     */
    public function storeReply(Request $request, Classroom $classroom, Comment $comment)
    {
        abort_if(Auth::user()->cannot('create', Reply::class), 401);

        $request->validate([
            'parent' => 'nullable|exists:replies,id',
            'reply' => 'required'
        ]);

        $comment->replies()->create([
            'parent_id' => $request->parent,
            'user_id' => Auth::id(),
            'content' => $request->reply,
        ]);

        return redirect()->back();
    }

    /**
     * Delete product comment
     */
    public function deleteComment(Classroom $classroom, Comment $comment)
    {
        abort_if(Auth::user()->cannot('delete', $comment), 401);

        $comment->delete();
        return redirect()->back();
    }

    /**
     * Delete product comment reply
     */
    public function deleteReply(Classroom $classroom, Comment $comment, Reply $reply)
    {
        abort_if(Auth::user()->cannot('delete', $reply), 401);

        $reply->delete();
        return redirect()->back();
    }
}
