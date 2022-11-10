<?php

/**
 * Date: Fri, 09 Mar 2018 09:07:39 +0000.
 */

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use App\Scopes\IsVisibleScope;
//use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Faq
 *
 * @property int $faq_id
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $last_updated
 * @property string $question
 * @property string $answer
 * @property int $item_order
 * @property int $is_hidden
 * @property int $category_id
 *
 * @package App\Models
 */
class Faq extends Eloquent implements Sortable, HasMedia
{
    use Searchable, SortableTrait, InteractsWithMedia/*, \Spiritix\LadaCache\Database\LadaCacheTrait*/;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'date_created';

    const UPDATED_AT = 'last_updated';

    public $asYouType = true;

    private $categories = [
        1 => [ // Allgemeines
            'icon' => 'wallet',
            'color' => 'indigo',
            'label' => 'Benutzerkonto', // trans_choice('faq.categories', 1),
        ],
        2 => [ // Benutzerkonto
            'icon' => 'classic-computer',
            'color' => 'yellow',
        ],
        3 => [ // Veröffentlichen -> Podcast
            'icon' => 'publish',
            'color' => 'orange',
        ],
        4 => [ // Blog
            'icon' => 'bookmark',
            'color' => 'pink',
        ],
        5 => [ // Statistiken
            'icon' => 'area-graph',
            'color' => 'red',
        ],
        6 => [ // Abrechnung
            'icon' => 'credit-card',
            'color' => 'green',
        ],
        7 => [ // Datenschutz
            'icon' => 'shield',
            'color' => 'cyan',
        ],
/*        8 => [ // API
            'icon' => 'power-plug',
            'color' => 'grey',
        ],*/
        9 => [ // Webplayer
            'icon' => 'sound',
            'color' => 'blue',
        ],
        10 => [ // Monetarisierung
            'icon' => 'credit',
            'color' => 'black',
        ],
        11 => [ // Produzieren
            'icon' => 'megaphone',
            'color' => 'red',
        ]
    ];
	protected $table = 'faq';
	protected $primaryKey = 'faq_id';

	public $incrementing = true;
	public $timestamps = true;

    public $sortable = [
        'order_column_name' => 'item_order',
        'sort_when_creating' => true,
    ];

	protected $casts = [
		'faq_id' => 'int',
		'item_order' => 'int',
		'is_hidden' => 'int',
		'category_id' => 'int'
	];

	protected $dates = [
		'date_created',
		'last_updated'
	];

	protected $fillable = [
		'date_created',
		'last_updated',
		'question',
		'answer',
		'item_order',
		'is_hidden',
		'category_id'
	];

	protected $attributes = [
	    'likes' => 0,
	    'dislikes' => 0,
        'is_hidden' => false,
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

	public function getCategories()
    {
        return $this->categories;
    }

	public function getCategoryAttributes($id)
    {
        return $this->categories[$id];
    }

    protected static function boot()
    {
        self::addGlobalScope(new IsVisibleScope());

        // auto-sets values on creation
/*        static::created(function(Faq $faq) {
            $faq->date_created = Carbon::now();
            $faq->last_updated = Carbon::now();
        });*/

        parent::boot();
    }

/*    public function categories()
    {
        return $this->hasOne(Faqcategory::class, )
    }*/

    public function getSlugAttribute()
    {
        return Str::slug($this->question, '-', config('app.locale'));
    }

    public function getIdAttribute()
    {
        return $this->faq_id;
    }
}
