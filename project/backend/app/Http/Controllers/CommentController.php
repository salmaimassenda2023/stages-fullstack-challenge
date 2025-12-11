<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;
use HTMLPurifier;
use HTMLPurifier_Config;

class CommentController extends Controller
{
    /**
         * Purifie le contenu d'un commentaire pour éviter le XSS
         */
        private function sanitizeContent($content)
        {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', ''); // aucune balise HTML autorisée
            $config->set('Cache.DefinitionImpl', null); // désactive le cache

            $purifier = new HTMLPurifier($config);
            return $purifier->purify($content);
        }

    public function index($articleId)
    {
        $comments = Comment::where('article_id', $articleId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        // ✅ Sanitize XSS
        $validated['content'] = $this->sanitizeContent($validated['content']);

        $comment = Comment::create($validated);
        $comment->load('user');

        return response()->json($comment, 201);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $articleId = $comment->article_id;

        $comment->delete();

        $remainingComments = Comment::where('article_id', $articleId)->get();
        $firstComment = $remainingComments->first();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'remaining_count' => $remainingComments->count(),
            'first_remaining' => $firstComment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // ✅ Sanitize XSS
        $validated['content'] = $this->sanitizeContent($validated['content']);

        $comment->update($validated);

        return response()->json($comment);
    }
}
