<?php

namespace App\Http\Controllers;

use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $this->seo()
            ->setCanonical(route('faq.index'))
            ->setTitle(trans('faq.title_seo_faq'))
            ->setDescription(trans('faq.description_seo_faq'));

        $mainEntity = [];
        foreach (Faq::all() as $qa) {
            $c = new \stdClass();
            $c->{'@type'} = "Question";
            $c->name = $qa->question;
            $a = new \stdClass();
            $a->{'@type'} = 'Answer';
            $a->text = $qa->answer;
            $c->acceptedAnswer = $a;
            $mainEntity[] = $c;
        }

        $this->seo()->jsonLd()->setType('FAQPage')->addValues(['mainEntity' => $mainEntity]);

        $aFaq = DB::select(DB::raw('select faq_id, category_id, question, likes from ( select faq_id, category_id, question, likes, (@rn:=if(@prev = category_id, @rn +1, 1)) as rownumb, @prev:= category_id from ( select faq_id, category_id, question, likes from faq order by category_id ,  likes desc, faq_id ) as sortedlist JOIN (select @prev:=NULL, @rn :=0) as vars ) as groupedlist where rownumb<=3 order by category_id, likes desc, faq_id'));

        $aSorted = [];

        foreach($aFaq as $oFaq) {
            if (!array_key_exists($oFaq->category_id, $aSorted)) {
                $aSorted[$oFaq->category_id] = [];
            }
            $aSorted[$oFaq->category_id][] = $oFaq;
        }

        $page = Page::where('title', '=', 'faq')->first();

        return view('faq.index', ['aFaq' => $aSorted, 'aCategories' => (new Faq())->getCategories(), 'page' => $page]);
    }

    public function category($slug, $id)
    {
        $seoDescription = trans('faq.description_seo_category', ['name' => trans_choice('faq.categories', $id)]);
        $this->seo()
            ->setCanonical(route('faq.category', ['slug' => $slug, 'id' => $id]))
            ->setTitle(trans('faq.title_seo_category', ['name' => trans_choice('faq.categories', $id)]))
            ->setDescription($seoDescription);
        //$this->seo()->metatags()->addMeta(['robots' => 'noindex']);

        $mainEntity = [];
        foreach (Faq::where('category_id', '=', $id)->get() as $qa) {
            $c = new \stdClass();
            $c->{'@type'} = "Question";
            $c->name = $qa->question;
            $a = new \stdClass();
            $a->{'@type'} = 'Answer';
            $a->text = $qa->answer;
            $a->url = route('faq.show' , ['id' => $qa->faq_id, 'slug' => $qa->slug]);
            $c->acceptedAnswer = $a;
            $mainEntity[] = $c;
        }

        $this->seo()->jsonLd()->setType('FAQPage')->setDescription($seoDescription)->addValues(['mainEntity' => $mainEntity]);

        $aFaq = Faq::where('category_id', '=', $id)->orderBy('question', 'asc')->paginate();

        return view('faq.category', ['id' => $id, 'aFaq' => $aFaq]);
    }

    public function search(Request $request)
    {
        $this->seo()
            ->setCanonical(route('faq.search'))
            ->metatags()->addMeta(['robots' => 'noindex']);

        $this->validate($request, [
            'q' => 'required',
        ]);
        $query = $request->get('q');

        $this->seo()->setTitle(trans('faq.title_seo_search', ['query' => $query]));

        //$aFaq = Faq::whereRaw("match(question,answer) against (? in boolean mode)", [$query])->paginate();
        $faqs = Faq::search($query)->get();
        $aFaq = $faqs->paginate();

        return view('faq.search', ['query' => $query, 'aFaq' => $aFaq]);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id, $slug)
    {
        $faq = Faq::findOrFail($id);

        if ($slug != $faq->slug) {
            return redirect()->route('faq.show', ['id' => $id, 'slug' => $faq->slug], 301);
        }

        $aFaq = Faq::where('category_id', '=', $faq->category_id)->where('faq_id', '!=', $faq->faq_id)->orderBy('likes', 'desc')->get();
        $cFaq = $aFaq->count()+1;
        $pageDescription = Str::limit(strip_tags($faq->question), 300);

        $this->seo()
            ->setTitle($faq->question)
            ->setDescription($pageDescription)
            ->setCanonical(route('faq.show', ['id' => $id, 'slug' => $faq->slug]));

        $mainEntity = [];
        $c = new \stdClass();
        $c->{'@type'} = "WebPage";
        $c->{'@id'} = route('faq.show', ['id' => $id, 'slug' => $faq->slug]);
        $c->image = asset('images1/podcaster_logo.svg');
        $c->name = $faq->question;
        $mainEntity[] = $c;

        $author = [];
        $a = new \stdClass();
        $a->{'@type'} = "Author";
        $a->name = "Fabio Bacigalupo & Team";
        $author[] = $a;

        $publisher = [];
        $p = new \stdClass();
        $p->{'@type'} = "Organization";
        $p->name = config('app.name');
        $l = new \stdClass();
        $l->{'@type'} = 'ImageObject';
        $l->url = asset('images1/podcaster_logo.svg');
        $p->logo = $l;
        $publisher[] = $p;

        $this->seo()->jsonLdMulti()
            ->setType('Article')
            ->addValue('headline', $faq->question)
            ->addValue('datePublished', $faq->date_created)
            ->addValue('dateModified', $faq->last_updated)
            ->addImage(asset('images1/podcaster_logo.svg'))
            ->setDescription($pageDescription)
            ->addValues(['mainEntity' => $mainEntity, 'author' => $author, 'publisher' => $publisher]);

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $itemList = [];
            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 1;
            $i = new \stdClass();
            $i->{'@id'} = route('faq.index');
            $i->name = trans('nav.faq');
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $c = new \stdClass();
            $c->{'@type'} = "ListItem";
            $c->position = 2;
            $i = new \stdClass();
            $i->{'@id'} = route('faq.show', ['id' => $id, 'slug' => $faq->slug]);
            $i->name = $faq->question;
            $i->image = asset('images1/podcaster_logo.svg');
            $c->item = $i;
            $itemList[] = $c;

            $this->seo()->jsonLdMulti()->newJsonLd()->setType('BreadcrumbList')->setTitle('Breadcrumb')->addValues(['itemListElement' => $itemList]);
        }

        if (!$this->seo()->jsonLdMulti()->isEmpty()) {
            $mainEntity = [];
            $c = new \stdClass();
            $c->{'@type'} = "Question";
            $c->name = $faq->question;
            $a = new \stdClass();
            $a->{'@type'} = 'Answer';
            $a->text = $faq->answer;
            $c->acceptedAnswer = $a;
            $mainEntity[] = $c;

            $this->seo()->jsonLd()->setType('FAQPage')->addValues(['mainEntity' => $mainEntity]);
        }

        return view('faq.show', ['faq' => $faq, 'aFaq' => $aFaq->take(5), 'cFaq' => $cFaq]);
    }

    public function like($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->increment('likes');

        return response()->json($faq->likes);
    }

    public function dislike($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->increment('dislikes');

        return response()->json($faq->dislikes);
    }
}
