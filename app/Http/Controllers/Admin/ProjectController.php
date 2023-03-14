<?php

namespace App\Http\Controllers\Admin;

// CONTROLLER
use App\Http\Controllers\Controller;

// SUPPORT function str
use illuminate\Support\Str;

// MODELS
use App\Models\Project;
use App\Models\Technology;
use App\Models\Type;

// REQUEST for FORMS DATA
use Illuminate\Http\Request;
// Arr function
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
// Storage function
use Illuminate\Support\Facades\Storage;
// FOR VALIDATION UNIQUE IN UPDATE RULE
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Nella funzione metto il request per prendere il filtro published e lo prendo con la query
        $filterPublish = $request->query('filter-published');

        // Paginator imported in RouteServiceProvider and later use function here, the number is the number of elements into page
        $query = Project::orderBy('updated_at', 'DESC');

        // Controllo il filtro e se la value è draft sara flaso altrimenti vero
        if ($filterPublish) {
            $value = $filterPublish === 'drafts' ? 0 : 1;
            // Prenod la query where la colonna is publish sarà uguale alla value
            $query->where('is_published', $value);
        }

        $projects = $query->Paginate(10);

        $types = Type::all();

        $technologies = Technology::all();


        return view('admin.projects.index', compact('projects', 'types', 'technologies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // FAKE EMPTY MODEL FOR FORM
        $project = new Project();

        //MODEL FOR FORM tutte in ordine alfabetico (sottinteso)
        $types = Type::orderBy('label')->get();

        // Prendo le technologies dal DB
        $technologies = Technology::select('id', 'label')->orderBy('id')->get();

        return view('admin.projects.create', compact('project', 'types', 'technologies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|unique:projects|min:5|max:50',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png',
                // Controllo se esiste quell'id sulla tabella Types  (esiste:tabella,colonna)
                'type_id' => 'nullable|exists:types,id',
                'technologies' => 'nullable|exists:technologies,id'
            ],
            [
                // ERRORI
                'title.required' => 'Title field is required',
                'title.unique' => "Project\'s title : $request->title has already been taken.",
                'title.min' => 'The title field must have at least 5 characters',
                'title.max' => 'The title field must have at least 50 characters',
                'description.required' => 'Description field it cannot be empty',
                'image.image' => 'Image is not Valid.',
                'image.mimes' => 'Accepted extensions : jpeg,jpg,png.',
                'type_id' => 'Invalid Type',
                'technologies' => 'Invalid Technology'
            ]
        );

        $data = $request->all();
        // Prendo lo SLUG nella costruzione data
        $data['slug'] = Str::slug($data['title'], '-');

        $project = new Project();

        if (Arr::exists($data, 'image')) {
            // $img_url restituisce l'url finale
            $img_url = Storage::put('projects', $data['image']);
            $data['image'] = $img_url;
        };

        // CHECK DEL PUBLISH 
        // controllo se arriva il booleano (name del check) come chiave perche se non è checcato il form non la invia 
        $data['is_published'] = Arr::exists($data, 'is_published');

        $project->fill($data);

        // Assegno l'user loggato in quel momento
        // $project->user_id = Auth::user()->id;

        $project->save();

        // Releziono il project con i Technologies se esistono/arrivano in data  con attach!
        if (Arr::exists($data, 'technologies')) $project->technologies()->attach($data['technologies']);

        return to_route('admin.projects.show', $project->id)->with('type', 'success')->with('New project created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(project $project)
    {
        //MODEL FOR FORM tutte in ordine alfabetico (sottinteso)
        $types = Type::orderBy('label')->get();

        // Prendo le technologies dal DB
        $technologies = Technology::select('id', 'label')->orderBy('id')->get();

        // Assegniamo a $project_technologies da una collection ad un array di id, e lo passiamo giu
        $project_technologies = $project->technologies->pluck('id')->toArray();

        return view('admin.projects.edit', compact('project', 'types', 'technologies', 'project_technologies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, project $project)
    {
        $request->validate(
            [
                // UNIQUE IN THE PROJECTS TABLE and IGNORE PROJECT ID
                'title' => ['required', 'string', Rule::unique('projects')->ignore($project->id), 'min:5', 'max:50'],
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png',
                // Controllo se esiste quell'id sulla tabella Types  (esiste:tabella,colonna)
                'type_id' => 'nullable|exists:types,id'
            ],
            [
                // ERRORI
                'title.required' => 'Title field is required',
                'title.unique' => "Project\'s title : $request->title has already been taken.",
                'title.min' => 'The title field must have at least 5 characters',
                'title.max' => 'The title field must have at least 50 characters',
                'description.required' => 'Description field it cannot be empty',
                'image.url' => 'Url is not Valid.',
                'type_id' => 'Invalid Type'
            ]
        );

        $data = $request->all();

        $data['slug'] = Str::slug($data['title'], '-');

        if (Arr::exists($data, 'image')) {
            // Se esiste un'immagine nel project la cancello
            if ($project->image) Storage::delete($project->image);
            // $img_url restituisce l'url finale e la sostituisco
            $img_url = Storage::put('projects', $data['image']);
            $data['image'] = $img_url;
        };

        // CHECK DEL PUBLISH 
        // controllo se arriva il booleano (name del check) come chiave perche se non è checcato il form non la invia 
        $data['is_published'] = Arr::exists($data, 'is_published');

        $project->update($data);

        // Releziono il project con i Technologies se esistono/arrivano in data ma con sync !
        if (Arr::exists($data, 'technologies')) $project->technologies()->sync($data['technologies']);
        // Altrimenti se te li conto e ne hai (relazione tra $project->technologies) allora con il METODO ->technologies() le elimini ->detach();
        else if (count($project->technologies)) $project->technologies()->detach();


        return to_route('admin.projects.show', $project->id)->with('type', 'success')->with('message', "Project : $project->title updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(project $project)
    {
        // SE esisiste un'immagine del project la elimino dallo storage
        if ($project->image) Storage::delete($project->image);

        // Se te li conto e ne hai (relazione tra $project->technologies) allora con il METODO ->technologies() le elimini ->detach();
        if (count($project->technologies)) $project->technologies()->detach();

        $project->delete();
        return to_route('admin.projects.index')->with('type', 'success')->with('message', "Project : $project->title deleted successfully.");
    }


    // Nuova funzione personalizzata che "TOGGOLA" il is_published del project
    public function togglePublishProject(Project $project)
    {
        $project->is_published = !$project->is_published;

        // dopo il toggle imposto l'azione
        $action = $project->is_published ? 'successfully published.' : 'saved as draft.';

        $project->save();

        // redirect con messaggio e azione personalizzata
        return to_route('admin.projects.index')->with('type', 'success')->with('message', "The Project is $action");
    }
}
