<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Costume;

class CostumeController extends Controller
{
    /**
     * Display a listing of the costumes owned by the current renter.
     */
    public function index()
    {
        $costumes = Auth::user()->costumes()->withCount('orders')->get();

        return view('renter.manage_costumes', compact('costumes'));
    }
}