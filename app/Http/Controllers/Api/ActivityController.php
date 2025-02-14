<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $result = DB::table('activities')->get();
            $data['result'] = $result;
            $data['status'] = 'success';
            $data['message'] = "Data Get Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {

            DB::table('activities')
                ->insert([
                    'com_master_id' => $request->com_master_id,
                    'user_id' => $request->user_id,
                    'created_at' => date("Y-m-d H:i:s")
                ]);

            $data['status'] = 'success';
            $data['message'] = "Data Insert Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        try {
            $result = DB::table('activities')
                ->where('id', $id)->first();
            $data['result'] = $result;
            $data['status'] = 'success';
            $data['message'] = "Data Get Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {

            DB::table('activities')
                ->where('id', $id)
                ->update([
                    'com_master_id' => $request->com_master_id,
                    'user_id' => $request->user_id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);

            $data['status'] = 'success';
            $data['message'] = "Data Updated Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {

            DB::table('activities')
                ->where('id', $id)->delete();

            $data['status'] = 'success';
            $data['message'] = "Data Deleted Successfully";

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            return response()->json($data, 400);
        }
    }
}
