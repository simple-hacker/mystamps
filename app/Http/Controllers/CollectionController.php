<?php

namespace App\Http\Controllers;

use App\Issue;
use App\Stamp;
use App\Grading;
use App\Collection;
use Illuminate\Http\Request;
use Validator;

class CollectionController extends Controller
{
    /**
     * Load the user's Stamp Collection view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all of the Collection model belonging to the auth user.
        $usersCollection = auth()->user()->collection;

        // Get array of stamp_ids in the user's collection.
        // This is used below to only obtain issue and stamp data of which belong in the user's collection.
        $stampsInCollection = $usersCollection->pluck('stamp_id')->unique()->toArray();

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
                                $query->whereIn('id', $stampsInCollection);
                            }
                        ])
                        ->latest('release_date')
                        ->get()
                        ->groupBy('year');

        // Organise the collection by stamp and grading, because you could have multiple copies of the same stamp and grading and convert to array.
        // This is to make it easier to display data.
        // As we loop through the user's collection by stamps, it then refers to this array for the number of gradings.
        // I think I could somehow merge this in the the mega collection above, but it's good enough for now.
        $collectedStamps = $usersCollection->groupBy(['stamp_id', 'grading_id'])->toArray();

        // Organise the collection models by grading_id, then group by type (e.g. mint or used) and then further group by
        // abbreviation.  So we can get a total for all "mint" stamps which can they be further broken down values for each grading.
        $stampsByGradings = $usersCollection->sortBy('grading_id')->groupBy(['grading.type', 'grading.abbreviation']);

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
        if (isset($stampsByGradings['mint'])) {
            $stampsByGradings['used']->each(function($grading, $key) use (&$collectionValues) {
                $collectionValues['gradings'][$key] = $used_value = $grading->sum('stamp.used_value');
                $collectionValues['used_total'] += $used_value;
            });
        }
        // Obtain the Grading information.
        $gradings = Grading::all()->keyBy('abbreviation')->toArray();

        // // This will contain the total value of the collection, the face value of the collection, and the total value of each of the grading types.
        // $collectionValues = [];
        // $collectionValues['total'] = 0;
        // $collectionValues['face'] = 0;
        // // $collectionValues['total'] = $usersCollection->sum('value');
        // // $collectionValues['face'] = $usersCollection->sum('stamp.price');
        // $collectionValues['gradings'] = [];

        // // Loop through collection grouped by grading_id and calculate the sum of values for each type.
        // foreach($stampsByGradings as $grading_id => $stampsByGrading) {
        //     // The key here is the abbreviation of the grading obtain referenced by the id in the Grading information.
        //     // e.g.  MNH, AU, VLMM etc.
        //     $collectionValues['gradings'][$gradings[$grading_id]->abbreviation] = [
        //         'type' => $gradings[$grading_id]->type,
        //         'description' => $gradings[$grading_id]->description,
        //         'value' => $stampsByGrading->sum('value')
        //     ];
        // }

        // If wantsJSON then $collection->toArray();

        return view('collection.index', compact('collection', 'collectedStamps', 'collectionValues', 'gradings'));
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
        // return redirect(route('catalogue.issue', ['issue' => $collection->stamp->issue, 'slug' => $collection->stamp->issue->slug]))
        //         ->withToastWarning('Removed ' . $collection->stamp->title . ' from your collection.');

        return [
            'stampsInCollection' => $stampsInCollection,
            'gradings' => $gradings,
        ];
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
            // '*.value.required' => 'Please enter a value.',
            // '*.value.numeric' => 'Please enter a valid value, max two decimal places.',
            // '*.value.regex' => 'Please enter a valid value, max two decimal places.'
        ];

        $validator = Validator::make($request->stampsToAdd, [
            '*.grading_id' => 'required|integer|exists:gradings,id',
            // '*.value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
        ], $messages);

        
        if ($attributes = $validator->validate()) {

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
        
        // return redirect(route('catalogue.issue', ['issue' => $collection->stamp->issue, 'slug' => $collection->stamp->issue->slug]))
        //         ->withToastWarning('Removed ' . $collection->stamp->title . ' from your collection.');
    }








    
    public function table() {
                // Get all of the Collection model belonging to the auth user.
                $usersCollection = auth()->user()->collection;

                // Get array of stamp_ids in the user's collection.
                // This is used below to only obtain issue and stamp data of which belong in the user's collection.
                $stampsInCollection = $usersCollection->pluck('stamp_id')->unique()->toArray();
        
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
                                        $query->whereIn('id', $stampsInCollection);
                                    }
                                ])
                                ->latest('release_date')
                                ->get()
                                ->groupBy('year');
        
                // Organise the collection by stamp and grading, because you could have multiple copies of the same stamp and grading and convert to array.
                // This is to make it easier to display data.
                // As we loop through the user's collection by stamps, it then refers to this array for the number of gradings.
                // I think I could somehow merge this in the the mega collection above, but it's good enough for now.
                $collectedStamps = $usersCollection->groupBy(['stamp_id', 'grading_id'])->toArray();
        
                // Organise the collection models by grading_id, then group by type (e.g. mint or used) and then further group by
                // abbreviation.  So we can get a total for all "mint" stamps which can they be further broken down values for each grading.
                $stampsByGradings = $usersCollection->sortBy('grading_id')->groupBy(['grading.type', 'grading.abbreviation']);
        
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
                if (isset($stampsByGradings['mint'])) {
                    $stampsByGradings['used']->each(function($grading, $key) use (&$collectionValues) {
                        $collectionValues['gradings'][$key] = $used_value = $grading->sum('stamp.used_value');
                        $collectionValues['used_total'] += $used_value;
                    });
                }
                // Obtain the Grading information.
                $gradings = Grading::all()->keyBy('abbreviation')->toArray();
        
                // // This will contain the total value of the collection, the face value of the collection, and the total value of each of the grading types.
                // $collectionValues = [];
                // $collectionValues['total'] = 0;
                // $collectionValues['face'] = 0;
                // // $collectionValues['total'] = $usersCollection->sum('value');
                // // $collectionValues['face'] = $usersCollection->sum('stamp.price');
                // $collectionValues['gradings'] = [];
        
                // // Loop through collection grouped by grading_id and calculate the sum of values for each type.
                // foreach($stampsByGradings as $grading_id => $stampsByGrading) {
                //     // The key here is the abbreviation of the grading obtain referenced by the id in the Grading information.
                //     // e.g.  MNH, AU, VLMM etc.
                //     $collectionValues['gradings'][$gradings[$grading_id]->abbreviation] = [
                //         'type' => $gradings[$grading_id]->type,
                //         'description' => $gradings[$grading_id]->description,
                //         'value' => $stampsByGrading->sum('value')
                //     ];
                // }
        
                // If wantsJSON then $collection->toArray();
        
                return view('collection.table', compact('collection', 'collectedStamps', 'collectionValues', 'gradings'));
    }
}
