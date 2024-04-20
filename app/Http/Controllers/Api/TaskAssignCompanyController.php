<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TaskAssignCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $result = DB::table('task_assign_company as tac')
            ->select('tac.id','users.full_name as user_name', 'tasks.name as task_name', 'company_masters.name as company_name','categories.name as category_name',)
            ->leftJoin('users', 'tac.user_id', '=', 'users.id')
            ->leftJoin('tasks', 'tac.task_id', '=', 'tasks.id')
            ->leftJoin('company_masters', 'tac.company_id', '=', 'company_masters.id')
            ->leftJoin('categories', 'tac.category_id', '=', 'categories.id')
            ->get();
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

            DB::table('task_assign_company')
                ->insert([
                    'user_id' => $request->user_id,
                    'company_id' => $request->company_id,
                    'category_id' => $request->category_id,
                    'task_id' => $request->task_id,
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
            $result = DB::table('task_assign_company')
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

            DB::table('task_assign_company')
                ->where('id', $id)
                ->update([
                    'user_id' => $request->user_id,
                    'company_id' => $request->company_id,
                    'category_id' => $request->category_id,
                    'task_id' => $request->task_id,
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

            DB::table('task_assign_company')
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
