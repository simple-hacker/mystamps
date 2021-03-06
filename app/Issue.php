<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model implements Searchable
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'cgbs_issue' => 'integer',
        'year' => 'integer',
        'release_date' => 'date:Y-m-d',
    ];

    protected $with = ['monarch', 'category'];

    protected $appends = ['slug', 'path'];

    /**
     * An issue has many stamps;
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stamps()
    {
        return $this->hasMany('App\Stamp')
                    ->orderByRaw("CASE WHEN sg_number IS NULL THEN 0 ELSE 1 END DESC")
                    ->orderBy('sg_number')
                    ->orderBy('id');
    }

    /**
     * Returns a slug of the title
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }

    /**
     * Returns the path url for the issue.
     *
     * @return string
     */
    public function getPathAttribute()
    {
        return '/catalogue/' . $this->id . '/' . $this->slug;
    }

    /**
    * A issue belongs to one Monarch
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function monarch()
    {
        return $this->belongsTo('App\Monarch');
    }

    /**
    * A issue belongs to one issue category
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function category()
    {
        return $this->belongsTo('App\IssueCategory');
    }

    /**
     * Always convert the date to Y-m-d
     *
     * @param string $date
     * @return mixed
     */
    public function getReleaseDateAttribute($date)
    {
        if ($date !== null) {
            return ($date !== '0000-00-00') ? (new Carbon($date))->format('Y-m-d') : null;
        }
    }

    /**
    * Returns the search result for this model.
    *
    * @return \Spatie\Searchable\SearchResult
    */
    public function getSearchResult() : SearchResult
    {
        $url = route('catalogue.issue', ['issue' => $this, 'slug' => $this->slug]);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url
        );
    }
}
