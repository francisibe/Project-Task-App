<?php

namespace App\Http\Controllers;

use App\Project;
use App\Mail\ProjectCreated;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $projects = Project::where('owner_id', auth()->id())->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function show(Project $project)
    {  
        $this->authorize('update', $project);  

        return view('projects.show', compact('project'));
    }
    
    public function store()
    {
        $attributes = request()->validate([ 
            
            'title' => ['required', 'min:3'],

            'description' => ['required', 'min:3']
        ]);

        $attributes['owner_id'] = auth()->id();

        $project = Project::create($attributes);

        \Mail::to($project->owner->email)->send(
            new ProjectCreated($project)
        );

        return redirect('/projects');
    }

    public function edit(Project $project)
    {
        
        return view('projects.edit', compact('project'));

    }

    public function update(Project $project)
    {

        $this->authorize('update', $project);

        $attributes = request()->validate([ 
            
            'title' => ['required', 'min:3'],

            'description' => ['required', 'min:3']
        ]);

        $project->update(request(['title', 'description']));

        return redirect('/projects');
    }

    public function destroy(Project $project)
    {
        $this->authorize('update', $project);

        $project->delete();

        return redirect('/projects');
    }

}
