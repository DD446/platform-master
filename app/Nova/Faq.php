<?php

namespace App\Nova;

use App\Nova\Metrics\FaqCount;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Manogi\Tiptap\Tiptap;

class Faq extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Faq';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'question';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'faq_id',
        'question',
        'answer',
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content';

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = true;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $categories = array_combine(range(1,11), range(1,11));
        array_walk($categories, function(&$item, $key) {
            $item = trans_choice('faq.categories', $key);
        });

        return [
            ID::make('ID', 'faq_id')->sortable(),

            Text::make('Question')
                ->displayUsing(function ($q) {
                    return Str::limit($q, 80);
                })
                ->sortable()
                ->rules('required', 'max:255'),

/*            Code::make('Answer')
                ->options(['mode' => 'html']),*/

/*            Trix::make('Answer')
                ->withFiles('public'),*/

            Tiptap::make('Answer')
                ->buttons([
                    'heading',
                    '|',
                    'italic',
                    'bold',
                    '|',
                    'link',
                    'code',
                    'strike',
                    'underline',
                    'highlight',
                    '|',
                    'bulletList',
                    'orderedList',
                    'codeBlock',
                    'blockquote',
                    '|',
                    'horizontalRule',
                    '|',
                    'table',
                    '|',
                    'image',
                    '|',
                    'textAlign',
                    '|',
                    'history',
                    '|',
                    'editHtml',
                ])
                ->headingLevels([2, 3, 4])
                ->fileSettings([
                    'disk' => 'public',
                    'path' => '',
                ]),

            Select::make('Category', 'category_id')
                ->options($categories)
                ->displayUsingLabels()
                ->required()
                ->sortable(),

            Number::make('Likes')
                ->min(0)
                ->step(1)
                ->sortable(),

            Number::make('Dislikes')
                ->min(0)
                ->step(1)
                ->sortable(),

            Boolean::make('Hidden', 'is_hidden')
                ->trueValue(1)
                ->falseValue(0)
                ->sortable(),

            Date::make('Date created', 'date_created')
                ->withMeta([
                    'value' => $this->date_created ? $this->date_created->diffForHumans() : Carbon::now()->diffForHumans()
                ])
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Date::make('Last updated', 'last_updated')
                ->withMeta([
                    'value' =>  $this->last_updated ? $this->last_updated->diffForHumans() : Carbon::now()->diffForHumans()
                ])
                ->sortable()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Link::make('FAQ Entry', 'faq_id')
                ->url(function () {
                    // This prevents failure when creating a new entry
                    if (isset($this->id)) {
                        return route('faq.show', ['id' => $this->id, 'slug' => $this->slug]);
                    }
                })
                ->text("Go")
                ->exceptOnForms()
                /*->icon()*/,
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new FaqCount,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
/*    public function subtitle()
    {
        return "{$this->faq_id}";
    }*/

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->withoutGlobalScopes();

        return parent::indexQuery($request, $query);
    }

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     */
    public static function usesScout()
    {
        return false;
    }
}
