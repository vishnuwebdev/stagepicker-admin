<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin CRUD for merchandise products (Store > Merchandise in the sidebar).
 * Follows the same pattern as ClassController / WebinarsController in this
 * folder: plain DB::table() queries, a file upload for the main image, and
 * redirect-with-flash-message responses. Multiple gallery images are handled
 * via the merchandise_images table (one row per image, tied to merchandise_id).
 */
class MerchandiseController extends AdminController
{
    public function list(Request $request)
    {
        $data['merchandise'] = DB::table('merchandise')->orderBy('id', 'desc')->get();
        return \View::make('admin/merchandise/list', $data);
    }

    public function add()
    {
        return \View::make('admin/merchandise/add');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'description' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }

        $photo_name = null;
        if ($request->hasFile('image')) {
            $path_original = public_path() . '/admin/uploads/merchandise';
            $file = $request->image;
            $photo_name = time() . '-' . $file->getClientOriginalName();
            $file->move($path_original, $photo_name);
        }

        $insertArr = [
            'title' => $request->title,
            'price' => $request->price,
            'sale_price' => $request->sale_price ?: null,
            'size' => $request->size,
            'description' => $request->description,
            'image' => $photo_name,
            'status' => $request->status ?: 1,
        ];

        $merchandiseId = DB::table('merchandise')->insertGetId($insertArr);

        if ($merchandiseId) {
            $this->storeGalleryImages($request, $merchandiseId);

            return redirect('admin/merchandise')
                ->with('success', 'Merchandise added successfully')
                ->send();
        }

        return redirect('admin/merchandise')
            ->with('error', 'Something went wrong')
            ->send();
    }

    public function view($id)
    {
        $data['merchandise'] = DB::table('merchandise')->where('id', $id)->first();
        $data['images'] = DB::table('merchandise_images')->where('merchandise_id', $id)->get();
        return \View::make('admin/merchandise/view', $data);
    }

    public function edit($id)
    {
        $data['merchandise'] = DB::table('merchandise')->where('id', $id)->first();
        $data['images'] = DB::table('merchandise_images')->where('merchandise_id', $id)->get();
        return \View::make('admin/merchandise/edit', $data);
    }

    public function update($id, Request $request)
    {
        $rules = [
            'title' => 'required',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'description' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }

        $updateArr = [
            'title' => $request->title,
            'price' => $request->price,
            'sale_price' => $request->sale_price ?: null,
            'size' => $request->size,
            'description' => $request->description,
            'status' => $request->status ?: 1,
        ];

        if ($request->hasFile('image')) {
            $path_original = public_path() . '/admin/uploads/merchandise';
            $file = $request->image;
            $photo_name = time() . '-' . $file->getClientOriginalName();
            $file->move($path_original, $photo_name);
            $updateArr['image'] = $photo_name;
        }

        $response = DB::table('merchandise')->where('id', $id)->update($updateArr);

        $this->storeGalleryImages($request, $id);

        if ($response !== false) {
            return redirect('admin/merchandise')
                ->with('success', 'Merchandise updated successfully')
                ->send();
        }

        return redirect('admin/merchandise')
            ->with('error', 'Something went wrong')
            ->send();
    }

    public function delete($id)
    {
        try {
            $images = DB::table('merchandise_images')->where('merchandise_id', $id)->get();
            foreach ($images as $img) {
                $path = public_path() . '/admin/uploads/merchandise/' . $img->image;
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            DB::table('merchandise_images')->where('merchandise_id', $id)->delete();
            DB::table('merchandise')->where('id', $id)->delete();

            return redirect('admin/merchandise')
                ->with('success', 'Merchandise deleted successfully')
                ->send();
        } catch (\Exception $e) {
            return redirect('admin/merchandise')
                ->with('error', 'Please try again')
                ->send();
        }
    }

    // Removes a single gallery image without deleting the whole product.
    public function deleteImage($id)
    {
        $image = DB::table('merchandise_images')->where('id', $id)->first();

        if ($image) {
            $path = public_path() . '/admin/uploads/merchandise/' . $image->image;
            if (file_exists($path)) {
                @unlink($path);
            }
            DB::table('merchandise_images')->where('id', $id)->delete();
        }

        return redirect()
            ->back()
            ->with('success', 'Image removed')
            ->send();
    }

    // Shared helper: saves any files submitted under the "images[]" input
    // as additional gallery rows in merchandise_images. Used by both store()
    // and update() so a product can pick up more images at any time.
    private function storeGalleryImages(Request $request, $merchandiseId)
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $path_original = public_path() . '/admin/uploads/merchandise';

        foreach ($request->file('images') as $file) {
            $photo_name = time() . '-' . $file->getClientOriginalName();
            $file->move($path_original, $photo_name);

            DB::table('merchandise_images')->insert([
                'merchandise_id' => $merchandiseId,
                'image' => $photo_name,
            ]);
        }
    }
}
