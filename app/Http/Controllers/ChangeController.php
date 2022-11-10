<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Change;

class ChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $changes = Change::where('is_public', '=', true)->orderByDesc('created_at')->take(15)->paginate();

        return response()->json($changes);
    }

    public function like(Change $change)
    {
        $change->increment('likes');

        return response()->json($change->likes);
    }

    public function dislike(Change $change)
    {
        $change->increment('dislikes');

        return response()->json($change->dislikes);
    }
}
