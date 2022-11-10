<?php

namespace App\Http\Controllers;

use App\Models\News;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Http\Request;
use App\Models\User;

class NewsController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $this->seo()
            ->setTitle(trans('news.page_title'))
            ->setDescription(trans('news.page_description', ['name' => config('app.name')]));

        $news = News::orderByDesc('created_at')->paginate(10);

        return view('news.index', ['news' => $news]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(News $news)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
        return view('news.create', ['news' => $news]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
        $news = new News();
        $news->fill($request->except(['_token', '_method']));

        if ($news->save()) {
            $msg = ['success' => 'Die Nachricht wurde erfolgreich gespeichert.'];
            return redirect()->route('news.show', ['id' => $news->slug])->with($msg);
        }

        $msg = ['error' => 'Es gab ein Fehler beim Speichern der Nachricht.'];
        return redirect()->back()->with($msg);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = News::where(['slug' => $id])->firstOrFail();

        return view('news.show', ['news' => $news]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
        $news = News::findOrFail($id);

        return view('news.create', ['news' => $news]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
        $news = News::findOrFail($id);
        $news->fill($request->except(['_token', '_method']));

        if ($news->save()) {
            $msg = ['success' => 'Die Nachricht wurde erfolgreich aktualisiert.'];
            return redirect()->route('news.show', ['id' => $news->slug])->with($msg);
        }

        $msg = ['error' => 'Es gab ein Fehler beim Speichern der Nachricht.'];
        return redirect()->back()->with($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
    }

    public function upload(Request $request)
    {
        if (auth()->guard('web')->role_id !== User::ROLE_ADMIN) {
            abort(403);
        }
    }

    public function like(News $news)
    {
        $news->increment('likes');

        return response()->json($news->likes);
    }

    public function dislike(News $news)
    {
        $news->increment('dislikes');

        return response()->json($news->dislikes);
    }
}
