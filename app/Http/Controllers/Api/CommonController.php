<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    public function get_activities()
    {
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

    public function get_activity_details()
    {
        try {

            $result = DB::table('activity_details')->get();
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

    public function get_activity_detail_comments()
    {
        try {

            $result = DB::table('activity_detail_comments')->get();
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

    public function get_categories()
    {
        try {

            $result = DB::table('categories')->get();
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

    public function get_company_details()
    {
        try {

            $result = DB::table('company_details')->get();
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

    public function get_company_masters()
    {
        try {

            $result = DB::table('company_masters')->get();
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
}
