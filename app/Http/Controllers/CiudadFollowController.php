<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Support\Facades\Auth;

class CiudadFollowController extends Controller
{
    public function toggle(Ciudad $ciudad)
    {
        $user = Auth::user();

        if ($user->isFollowingCiudad($ciudad)) {
            $user->followingCiudades()->detach($ciudad->id);
        } else {
            $user->followingCiudades()->attach($ciudad->id);
        }

        return back();
    }
}
