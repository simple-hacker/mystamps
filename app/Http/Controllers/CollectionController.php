<?php

namespace App\Http\Controllers;

use App\User;
use App\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Adds a stamp to the auth user's collection.
     *  
     * @param Stamp $stamp
     * 
     * @return null
     */
    public function store(Stamp $stamp)
    {
        return auth()->user()->stamps()->attach($stamp);
    }

    /**
     * Removes a stamp from the auth user's collection.
     *  
     * @param Stamp $stamp
     * 
     * @return null
     */
    public function destroy(Stamp $stamp)
    {
        return auth()->user()->stamps()->detach($stamp);
    }
}