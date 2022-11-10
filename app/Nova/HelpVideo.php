<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Manogi\Tiptap\Tiptap;

class HelpVideo extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\HelpVideo::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'page_title',
        'title',
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
    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('Page Title')
                ->displayUsing(function ($q) {
                    return Str::limit($q, 80);
                })
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Page Description')
                ->displayUsing(function ($q) {
                    return Str::limit($q, 80);
                })
                ->sortable()
                ->rules('required', 'max:400'),

            Text::make('Title')
                ->displayUsing(function ($q) {
                    return Str::limit($q, 80);
                })
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Subtitle')
                ->displayUsing(function ($q) {
                    return Str::limit($q, 80);
                })
                ->sortable()
                ->rules('max:255'),

            Tiptap::make('Content')
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
                    'path' => 'lp',
                ])
                ->rules(['required']),

            Text::make('Content')
                ->displayUsing(function ($q) {
                    return Str::limit(strip_tags($q), 400);
                })
                ->onlyOnDetail(),

            Text::make('Username')
                ->required(),

            Number::make('Poster'),
            Number::make('Mp4'),
            Number::make('Webm'),
            Number::make('Ogv'),

            Boolean::make('Is public'),

            Link::make('Entry', 'id')
                ->url(function () {
                    // This prevents failure when creating a new entry
                    if (isset($this->id)) {
                        return route('lp.video', ['video' => $this->id, 'slug' => $this->slug]);
                    }
                })
                ->text("Go")
                ->exceptOnForms()
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
        return [];
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
}
