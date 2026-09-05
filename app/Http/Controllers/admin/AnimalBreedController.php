<?php
namespace App\Http\Controllers\Admin;

use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin master-data CRUD for animal breeds, each tied to a species (e.g.
 * German Shepherd -> Dog). Modeled directly on admin/CrewRoleController.php.
 * Permission module: p10 (shared with AnimalSpeciesController and the
 * animal audition list/detail/status controller).
 */
class AnimalBreedController extends AdminController
{
    public function list(Request $request)
    {
        if (checkAdminUserPermission('p10', 'view') == false) {
            return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p10', 'edit');
        $addPermission = checkAdminUserPermission('p10', 'add');
        $deletePermission = checkAdminUserPermission('p10', 'delete');

        $breed = AnimalBreed::leftJoin('animal_species', 'animal_species.id', '=', 'animal_breeds.animal_species_id')
            ->select('animal_breeds.*', 'animal_species.name as species_name')
            ->orderBy('animal_breeds.id', 'DESC')
            ->get();

        return \View::make("admin/animal-breed/list", compact('breed', 'editPermission', 'deletePermission', 'addPermission'));
    }

    public function add(Request $request)
    {
        if (checkAdminUserPermission('p10', 'add') == false) {
            return redirect("/");
        }

        $species = AnimalSpecies::where('status', 1)->orderBy('name')->get();

        return \View::make('admin/animal-breed/add', compact('species'));
    }

    public function edit(Request $request, $id = null)
    {
        if (checkAdminUserPermission('p10', 'edit') == false) {
            return redirect("/");
        }

        $breed = "";
        if (!empty($id)) {
            $breed = AnimalBreed::where('id', $id)->first();
        }
        $species = AnimalSpecies::where('status', 1)->orderBy('name')->get();

        return \View::make('admin/animal-breed/edit', compact('id', 'breed', 'species'));
    }

    public function store(Request $request)
    {
        if (checkAdminUserPermission('p10', 'add') == false) {
            return redirect("/");
        }

        $data = $request->all();
        $rules = [
            'name' => 'required|string|max:255',
            'animal_species_id' => 'required|exists:animal_species,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            AnimalBreed::create([
                'name' => $data['name'],
                'animal_species_id' => $data['animal_species_id'],
                'status' => isset($data['status']) ? 1 : 0,
            ]);

            return redirect("admin/animal-breed")
                ->with('success', "Breed has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {
        if (checkAdminUserPermission('p10', 'edit') == false) {
            return redirect("/");
        }

        $data = $request->all();
        $rules = [
            'name' => 'required|string|max:255',
            'animal_species_id' => 'required|exists:animal_species,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            $breed = AnimalBreed::find($data['breed_id']);
            $breed->name = $data['name'];
            $breed->animal_species_id = $data['animal_species_id'];
            $breed->status = isset($data['status']) ? 1 : 0;
            $breed->save();

            return redirect("admin/animal-breed")
                ->with('success', "Breed has been updated successfully.")
                ->send();
        }
    }

    public function delete($id)
    {
        if (checkAdminUserPermission('p10', 'delete') == false) {
            return redirect("/");
        }

        try {
            AnimalBreed::where('id', '=', $id)->delete();
            return redirect("admin/animal-breed")
                ->with('success', "Breed has been deleted successfully")
                ->send();
        } catch (\Exception $e) {
            return redirect("admin/animal-breed")->with('error', "Please try again")
                ->send();
        }
    }
}
