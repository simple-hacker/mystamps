<?php

namespace App\Http\Controllers;

use App\Issue;
use App\Stamp;
use App\Grading;
use App\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    /**
     * Load the user's Stamp Collection view.
     *
     * @return \Illuminate\View\View
     */
    public function index($year = null)
    {
        list($collection, $collectedStamps, $collectionValues) = $this->getCollection();

        // Obtain the Grading information.
        $gradings = Grading::all();
        $gradings = $gradings->keyBy('abbreviation');

        // Get total count of Stamps in catalogue.
        $stampsCount = Stamp::count();

        if ($year == null) {
            $year = $collection->keys()->first() ?? date("Y");
        }

        if (request()->wantsJson()) {
            return compact('collection', 'collectedStamps', 'collectionValues', 'stampsCount', 'gradings', 'year');
        }

        return view('collection.index', compact('collection', 'collectedStamps', 'collectionValues', 'stampsCount', 'gradings', 'year'));
    }

    /**
     * Shows details about the stamp, as well as what's in your collection.
     *
     * @param \App\Stamp $stamp
     * @param string $slug
     *
     * @return \Illuminate\View\View
     */
    public function show(Stamp $stamp)
    {
        $stampsInCollection = auth()->user()->collection()->where('stamp_id', $stamp->id)->get();
        $gradings = Grading::all();

        return compact('stampsInCollection', 'gradings');
    }

    /**
     * Adds a stamp to the auth user's collection.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Stamp $stamp
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $messages = [
            '*.grading_id.required' => 'Please select a grading type.',
            '*.grading_id.exists' => 'You have not selected a valid grading type.',
        ];

        $validator = Validator::make($request->stampsToAdd, [
            '*.grading_id' => 'required|integer|exists:gradings,id',
        ], $messages);


        if ($validator->validate()) {

            foreach($request->stampsToAdd as $attributes) {
                auth()->user()->stamps()->attach($request->stamp['id'], $attributes);
            }

            // Got an extra DB call here.  Ideally after adding we return the data that was posted and push to the collection
            // array in the modal instead of overwriting it completely with the DB call data.
            // Haven't done it yet because the data keys aren't matching.
            // This return returns id, user_id, stamp_id, grading_id, value etc WITH gradings and stamp data.
            // Posted data was only grading_id and value.  Missing grading_type to display.

            return auth()->user()->collection()->where('stamp_id', $request->stamp['id'])->get();
        }

        return "Error?";
    }

    /**
     * Removes a stamp from the auth user's collection.
     *
     * @param \App\Collection $collection
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Collection $collection)
    {
        if ($collection->user_id != auth()->user()->id) {
            abort(404);
        }

        if ($collection->delete()) {
            return "Success";
        }

        return abort(401);
    }

    public function print()
    {
        list($collection, $collectedStamps, $collectionValues) = $this->getCollection();

        return view('collection.table', compact('collectedStamps'));
    }

    public function missing()
    {
        // Users distinct stamps in their collection
        $stampIds = auth()->user()->collection->unique('stamp_id')->pluck('stamp_id');
        $missingStamps = Stamp::whereNotIn('id', $stampIds)->get();

        $missingStamps = $missingStamps->sort(function ($a, $b) {
            if (isset($a->issue) && isset($b->issue)) {
                if ($a->issue->release_date === $b->issue->release_date) {
                    if ($a->issue->title === $b->issue->title) {
                        // return $a->title > $b->title;
                        return $a->id > $b->id ? 1 : -1;
                    }
                    return $a->issue->title < $b->issue->title ? -1 : 1;
                }
                return $a->issue->release_date < $b->issue->release_date ? -1 : 1;
            }

            return 0;
        });

        return view('collection.missing', compact('missingStamps'));
    }

    protected function getCollection() {
        // Get all of the Collection model belonging to the auth user.
        $usersCollection = auth()->user()->collection;

        // Get array of stamp_ids in the user's collection.
        // This is used below to only obtain issue and stamp data of which belong in the user's collection.
        $stampsInCollection = $usersCollection->pluck('stamp_id')->unique()->toArray();

        // **
        // This just gets all the stamp and issue data of stamps you have collected.
        // This does not get the quantities and gradings of each stamp, that is done with $collectedStamp.
        // **
        // whereHas: Only grab the Issues where we have collected a stamp from.
        // withCount: Get the total number of stamps in this issue (so we can calculate missing stamps in collection)
        // with(['stamps']): Need to eager load the stamp data of only these collected stamps.
        // Sort issues by release date.
        // get() collection
        // Group collection by issue release year, otherwise My Collection could get very long.  Will only display one year at a time with navigation to choose year of your collection.
        $collection = Issue::whereHas('stamps', function ($query) use ($stampsInCollection) {
                            $query->whereIn('id', $stampsInCollection);
                        })
                        ->withCount('stamps')
                        ->with([
                            'stamps' => function ($query) use ($stampsInCollection) {
                                $query->whereIn('id', $stampsInCollection)->orderBy('sg_number');
                            }
                        ])
                        ->latest('release_date')
                        ->get()
                        ->groupBy('year');

        // Organise the collection by stamp and grading, because you could have multiple copies of the same stamp and grading and convert to array.
        // This is to make it easier to display data.
        // As we loop through the user's collection by stamps, it then refers to this array for the number of gradings.
        // I think I could somehow merge this in the mega collection above, but it's good enough for now.
        // $collectedStamps = $usersCollection->sortByDesc('stamp.issue.release_date')->groupBy(['stamp_id', 'grading_id']);

        $collectedStamps = $usersCollection->sort(function ($a, $b) {
            if ($a->stamp->issue->release_date === $b->stamp->issue->release_date) {
                if ($a->stamp->issue->title === $b->stamp->issue->title) {
                    // return $a->stamp->title > $b->stamp->title;
                    if ($a->stamp->sg_number === $b->stamp->sg_number) {
                        // return $a->stamp->title > $b->stamp->title;
                        return $b->grading->display_order < $a->grading->display_order ? -1 : 1;
                    }
                    return $a->stamp->sg_number > $b->stamp->sg_number ? 1 : -1;
                }
                return $a->stamp->issue->title < $b->stamp->issue->title ? -1 : 1;
            }
            return $a->stamp->issue->release_date < $b->stamp->issue->release_date ? -1 : 1;
        })
        ->groupBy(['stamp_id', 'grading.display_order']);

        // Organise the collection models by grading_id, then group by type (e.g. mint or used) and then further group by
        // abbreviation.  So we can get a total for all "mint" stamps which can they be further broken down values for each grading.
        $stampsByGradings = $usersCollection->sortBy('grading.display_order')->groupBy(['grading.type', 'grading.abbreviation']);

        // Set the array of values.
        $collectionValues = [
            'face_total' => 0,
            'mint_total' => 0,
            'used_total' => 0,
            'gradings' => []
        ];

        if (isset($stampsByGradings['mint'])) {
            $stampsByGradings['mint']->each(function($grading, $key) use (&$collectionValues) {
                $collectionValues['gradings'][$key] = $mint_value = $grading->sum('stamp.mint_value');
                $collectionValues['face_total'] += $grading->sum('stamp.face_value');
                $collectionValues['mint_total'] += $mint_value;
            });
        }
        if (isset($stampsByGradings['used'])) {
            $stampsByGradings['used']->each(function($grading, $key) use (&$collectionValues) {
                $collectionValues['gradings'][$key] = $used_value = $grading->sum('stamp.used_value');
                $collectionValues['used_total'] += $used_value;
            });
        }

        return [$collection, $collectedStamps, $collectionValues];
    }
}
