<?php

namespace App\Http\Controllers;

use App\Issue;
use App\Year;
use App\Grading;

class CatalogueController extends Controller
{
    /**
     * Index view shows all the issues for the given year.
     *
     * @param integer $year
     *
     * @return Illuminate\View\View
     */
    public function index($year = null)
    {
        $catalogue = Issue::orderBy('release_date', 'desc')
                            ->with('stamps')
                            ->get()
                            ->keyBy('id')
                            ->groupBy('year', true)
                            ->sortByDesc('year');

        $years = Year::orderBy('year', 'desc')->pluck('year');

        if (!isset($year)) {
            // If not year in URL, then get the latest year we have entered.
            $year = $years[0];
        } else {
            // If year is in URL, check if it's a valid year by seeing if it's in the array of years.
            // dd($years);
            if (!in_array($year, $years->toArray())) {
                abort(404);
            }
        }

        $admin = auth()->user() ? auth()->user()->hasRole('admin') : false;

        return view('catalogue.index', compact('year', 'years', 'catalogue', 'admin'));
    }

    /**
     * Displays the given issue.
     *
     * @param \App\Issue $id
     * @param string $slug
     *
     * @return Illuminate\View\View
     */
    public function issue(Issue $issue, $slug)
    {
        // If the slug does not match the provided issue id then abort.
        if ($issue->slug !== $slug) {
            abort(404);
        }

        // If a user is logged in then fetch the user's stamp collection.
        // Else just pass an empty array for guests.
        $collection = auth()->check() ? auth()->user()->stamps : [];

        $gradings = Grading::all();

        return view('catalogue.issue', compact('issue', 'collection', 'gradings'));
    }
}
