<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Affiche le formulaire de création d'un nouveau workflow
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('workflows.create');
    }
}
