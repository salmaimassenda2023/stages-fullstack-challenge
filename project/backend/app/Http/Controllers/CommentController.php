<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Get comments for an article.
     */
    public function index($articleId)
    {
        $comments = Comment::where('article_id', $articleId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create($validated);
        $comment->load('user');

        return response()->json($comment, 201);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $articleId = $comment->article_id;

        $comment->delete();
        //  Récupère les commentaires restants du même article
        $remainingComments = Comment::where('article_id', $articleId)->get();

        // Sécurisé : retourne null s'il n'y a plus de commentaires
            $firstComment = $remainingComments->first();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'remaining_count' => $remainingComments->count(),
            'first_remaining' => $firstComment,
        ]);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }
}

