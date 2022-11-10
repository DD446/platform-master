<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::with('user')
            ->public()
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('review.index', ['reviews' => $reviews]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create()
    {
        $msg = ['success' => trans('review.message_success_create')];

        if (Review::owner()->first()) {
            return response()->redirectToRoute('home')->with($msg);
        }

        return view('review.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $msg = ['success' => trans('review.message_success_create')];

        if (Review::owner()->first()) {
            return response()->redirectToRoute('home')->with($msg);
        }

        $msg = ['success' => trans('review.message_success_create')];

        $validated = $this->validate($request, [
            'q1' => ['nullable', 'string'],
            'q2' => ['nullable', 'string'],
            'q3' => ['nullable', 'string'],
            'q4' => ['nullable', 'string'],
            'q5' => ['nullable', 'string'],
            'is_public' => ['boolean'],
        ]);

        $validated['usr_id'] = auth()->id();
        $validated['published_cite'] = '';

        if (!Review::create($validated)) {
            $msg = ['error' => trans('review.message_error_create')];
        }

        return response()->redirectToRoute('home')->with($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($request->method() == 'PUT') {
            $review->update(['published_cite' => $request->get('citation'), 'is_published' => $request->get('published', 0)]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
