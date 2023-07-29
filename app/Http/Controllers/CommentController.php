<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Ticket $ticket)
    {

        $comments = $ticket->ticketComment()
            ->withTrashed()
            ->orderBy('created_at', 'asc')
            ->with(['user:id,name'])
            ->paginate(request('per_page') ?? 10);
        $comments->getCollection()->map(function ($comment) {
            if ($comment->deleted_at !== null) {
                $comment->comment = '<This comment has been deleted>';
            } else {
                unset($comment['deleted_at']);
            }
            unset($comment['updated_at']);
        });
        return $comments;
    }

    /**
     * Update the specified resource in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'string'
        ], ['comment.string' => 'comment cannot be empty']);
        $comment = new Comment(['comment' => $request->comment, 'ticket_id' => $ticket->id]);

        return auth()->user()->userComment()->save($comment);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id) return response(['error' => 'You do not have permission for this'], 403);

        $request->validate([
            'comment' => 'string'
        ], ['comment.string' => 'comment cannot be empty']);

        $comment->update($request->all());
        $comment->save();

        return response("updated", 202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) return response(['error' => 'You do not have permission for this'], 403);
        $comment->delete();
        return response()->noContent();
    }
}
