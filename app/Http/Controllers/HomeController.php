<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Promotion;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request, $id)
    {
        $promotions = Promotion::where('display_on_machine', 1)->get();
        $machine = Machine::with('slots.items')->where('id', $id)->first();
        return view('machine', compact('promotions', 'machine'));
    }

    public function list()
    {
        $machines = Machine::all();
        return view('welcome', compact('machines'));
    }
}
