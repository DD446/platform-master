<?php

namespace App\Nova;

use App\Nova\Metrics\NewsCount;
use Carbon\Carbon;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Manogi\Tiptap\Tiptap;

class News extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\Models\\News';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title', 'teaser', 'body',
    ];

    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Title'),

            Textarea::make('Teaser'),

/*            Trix::make('Body')
                ->hideFromIndex()
                ->withFiles('public'),*/

            Tiptap::make('Body')
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

            Number::make('Likes')
                ->hideWhenCreating()
                ->min(0)
                ->step(1)
                ->sortable(),

            Number::make('Dislikes')
                ->hideWhenCreating()
                ->min(0)
                ->step(1)
                ->sortable(),

            Boolean::make('Public', 'is_public')
                ->trueValue(1)
                ->falseValue(0)
                ->sortable(),

            Boolean::make('Sticky', 'is_sticky')
                ->trueValue(1)
                ->falseValue(0)
                ->sortable(),

            DateTime::make('Published', 'created_at')
                ->withMeta(['value' => $this->created_at ?: Carbon::now()]),

            Text::make('Author')
                ->required()
                ->suggestions([
                    'Fabio',
                    'Max',
                    'Steffen',
                ]),

            Link::make('News Entry', 'id')
                ->url(function () {
                    if (isset($this->slug)) {
                        return route('news.show', ['id' => $this->slug]);
                    }
                })
                ->text("Go")
                ->exceptOnForms(),

/*            Images::make('Images', 'teaserimage') // second parameter is the media collection name
            ->conversionOnDetailView('detail') // conversion used to display the image
            ->conversionOnIndexView('thumb')
            ->conversionOnPreview('thumb')
            ->fullSize() // full size column
            //->rules('size:1') // validation rules for the collection of images
            // validation rules for the collection of images
            //->singleImageRules(['dimensions:min_width=100'])
            ,

            Images::make('Images', 'article') // second parameter is the media collection name
            ->conversionOnDetailView('detail') // conversion used to display the image
            ->conversionOnIndexView('thumb')
            ->conversionOnPreview('thumb')
            ->fullSize() // full size column
            //->rules('size:1') // validation rules for the collection of images
            // validation rules for the collection of images
            //->singleImageRules(['dimensions:min_width=100'])
            ,*/
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
            new NewsCount,
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

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->withoutGlobalScopes();

        return parent::indexQuery($request, $query);
    }
}
