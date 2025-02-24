<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    function index(Trabajo $trabajo)
    {
        return view('articulos.index', compact('trabajo'));
    }
}
