<?php
namespace App\Http\Controllers\Admin;

use App\Models\AnimalSpecies;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin master-data CRUD for animal species (Dog, Cat, Horse, ...), used
 * to populate the dropdown on both the producer's animal audition post
 * form and the auditioner's animal profile form. Modeled directly on
 * admin/CrewRoleController.php. Permission module: p10 (Animal Audition
 * Management) — shared with AnimalBreedController and the animal audition
 * list/detail/status controller.
 */
class AnimalSpeciesController extends AdminController
{
    public function list(Request $request)
    {
        if (checkAdminUserPermission('p10', 'view') == false) {
            return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p10', 'edit');
        $addPermission = checkAdminUserPermission('p10', 'add');
        $deletePermission = checkAdminUserPermission('p10', 'delete');

        $species = AnimalSpecies::orderBy('id', 'DESC')->get();

        return \View::make("admin/animal-species/list", compact('species', 'editPermission', 'deletePermission', 'addPermission'));
    }

    public function add(Request $request)
    {
        if (checkAdminUserPermission('p10', 'add') == false) {
            return redirect("/");
        }

        return \View::make('admin/animal-species/add');
    }

    public function edit(Request $request, $id = null)
    {
        if (checkAdminUserPermission('p10', 'edit') == false) {
            return redirect("/");
        }

        $species = "";
        if (!empty($id)) {
            $species = AnimalSpecies::where('id', $id)->first();
        }

        return \View::make('admin/animal-species/edit', compact('id', 'species'));
    }

    public function store(Request $request)
    {
        if (checkAdminUserPermission('p10', 'add') == false) {
            return redirect("/");
        }

        $data = $request->all();
        $rules = ['name' => 'required|string|max:255'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            AnimalSpecies::create([
                'name' => $data['name'],
                'status' => isset($data['status']) ? 1 : 0,
            ]);

            return redirect("admin/animal-species")
                ->with('success', "Species has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {
        if (checkAdminUserPermission('p10', 'edit') == false) {
            return redirect("/");
        }

        $data = $request->all();
        $rules = ['name' => 'required|string|max:255'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            $species = AnimalSpecies::find($data['species_id']);
            $species->name = $data['name'];
            $species->status = isset($data['status']) ? 1 : 0;
            $species->save();

            return redirect("admin/animal-species")
                ->with('success', "Species has been updated successfully.")
                ->send();
        }
    }

    public function delete($id)
    {
        if (checkAdminUserPermission('p10', 'delete') == false) {
            return redirect("/");
        }

        try {
            AnimalSpecies::where('id', '=', $id)->delete();
            return redirect("admin/animal-species")
                ->with('success', "Species has been deleted successfully")
                ->send();
        } catch (\Exception $e) {
            return redirect("admin/animal-species")->with('error', "Please try again")
                ->send();
        }
    }
}
