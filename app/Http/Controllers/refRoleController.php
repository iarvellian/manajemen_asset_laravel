<?php

namespace App\Http\Controllers;

use App\Models\refRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class refRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ref_role = refRole::all();

        if(count($ref_role) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $ref_role
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = Validator::make($storeData, [
            'nama_role' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_role = refRole::create([
            'nama_role' => $request->nama_role,
        ]);

        $storeData = refRole::latest()->first();
 
        return response([
            'message' => 'Add Role Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_role)
    {
        $ref_role = refRole::find($id_role);

        if(!is_null($ref_role)){
            return response([
                'message' => 'Retrieve Role Success!',
                'data' => $ref_role
            ], 200);
        };

        return response([
            'message' => 'Role Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_role)
    {
        $ref_role = refRole::find($id_role);

        if(is_null($ref_role)){
            return response([
                'message' => 'Role Not Found!',
                'data' => null
            ], 404);
        };

        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'nama_role' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $ref_role->nama_role = $updateData['nama_role'];

        if($ref_role->save()){
            return response([
                'message' => 'Update Role Success!',
                'data' => $ref_role
            ], 200);
        }

        return response([
            'message' => 'Update Role Failed!',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_role)
    {
        $ref_role = refRole::find($id_role);

        if(is_null($ref_role)){
            return response([
                'message' => 'Role Not Found!',
                'data' => null
            ], 404);
        }

        if($ref_role->delete()){
            return response([
                'message' => 'Delete Role Success!',
                'data' => $ref_role
            ], 200);
        }

        return response([
            'message' => 'Delete Role Failed!',
            'data' => null
        ],400);
    }
}
