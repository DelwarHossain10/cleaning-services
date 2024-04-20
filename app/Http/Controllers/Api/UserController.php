<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpEmail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register', 'password_recovery', 'verify_and_reset_password']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|between:2,100',
            'phone_number' => 'required|unique:users,phone_number|regex:/^(\+)?[0-9]{10,20}$/',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ], [
            'phone_number.regex' => 'The phone number format is invalid. Please provide a valid phone number.',
        ]);


        if ($validator->fails()) {
            return response(
                [
                    "message" => 'Input field data required !!',
                    "status" => "error",
                ],
                403
            );
        }

        DB::beginTransaction();
        try {
            $user = User::create(array_merge(
                $validator->validated(),
                [
                    'password' => Hash::make($request->password),
                    'state_name' => $request->state_name ?? null,
                    'address' => $request->address ?? null
                ]
            ));
            DB::commit();
            return response(
                [
                    "user" => $user,
                    "message" => "Registration Success",
                    "status" => "success",
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function login(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validated->fails()) {
            return response([
                'message' => $validated->errors(),
                'status' => 'error'
            ], 200);
        }


        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {

            $token = $user->createToken($request->email)->plainTextToken;

            return response([
                "user" => $user,
                'token' => $token,
                'message' => 'Login Success',
                'status' => 'success'
            ], 200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status' => 'failed'
        ], 401);
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response(
                [
                    "message" => "Logout Success",
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {

            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function logged_user()
    {
        try {
            $role = null;
            $permissions = null;
            $loggeduser = auth()->user();
            $extraPermission = collect(auth()->user()->permissions)->pluck(
                "name"
            );

            if (isset(auth()->user()->roles[0])) {
                $roleID = auth()->user()->roles[0]->id;
                $role = Role::find($roleID);

                $rolePermissions = Permission::join(
                    "role_has_permissions",
                    "role_has_permissions.permission_id",
                    "=",
                    "permissions.id"
                )
                    ->whereIn(
                        "role_has_permissions.role_id",
                        auth()
                            ->user()
                            ->roles->pluck("id")
                    )
                    ->get();

                $permissionsList = collect($rolePermissions)->pluck("name");
                $permissionsList = $permissionsList
                    ->merge($extraPermission)
                    ->unique();
            }
            return response(
                [
                    "user" => $loggeduser,
                    "Permissions List" => $permissionsList,
                    "message" => "Logged User Data",
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function change_password(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        $user = auth()->user();
        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                403
            );
        } else {

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'message' => "Old Password Doesn't match",
                    'status' => 'error',
                ], 403);
            }
        }
        DB::beginTransaction();
        try {
            $user->password = Hash::make($request->new_password);
            $user->update();
            DB::commit();
            return response(
                [
                    "message" => "Password Changed Successfully",
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }
    public function password_recovery(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^(\+)?[0-9]{10,20}$/',
        ], [
            'phone_number.regex' => 'The phone number format is invalid. Please provide a valid phone number.',
        ]);

        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                403
            );
        } else {
            $user = User::where("phone_number", $request->phone_number)->first();
            if (!$user) {
                return response(
                    [
                        "message" => "The phone number is not registered.",
                        "status" => "error",
                    ],
                    403
                );
            }
        }
        DB::beginTransaction();
        try {
            $otp = new Otp;
            $otp->user_id = $user->id;
            $otp->otp_no = rand(1000, 9999);
            $otp->save();

            // Send OTP to email
            Mail::to($user->email)->send(new OtpEmail($otp->otp_no));
            // Send OTP to sms

            $phoneNumber = $user->phone_number;

            // Check if the phone number starts with "+88"
            if (strpos($phoneNumber, "+88") === 0) {
                // Remove "+88" prefix
                $recipient = substr($phoneNumber, 3);
            } else {
                // No need to remove anything
                $recipient = $phoneNumber;
            }


            $SMSText = 'Your OTP is: ' . $otp->otp_no . '.Please use this OTP to complete your verification. This OTP will expire in 60 seconds.';
            $smsUrl = "http://192.168.100.213/httpapi/sendsms?userId=aci_cb&password=Asdf1234&smsText=" . urlencode($SMSText) . "&commaSeperatedReceiverNumbers=" . $recipient;
            $response = file_get_contents($smsUrl);


            DB::commit();
            return response(
                [
                    "message" => 'OTP sent to email and Phone. This OTP will expire in 60 seconds.',
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function verify_and_reset_password(Request $request)
    {
        // Validate the request data
        $validated = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^(\+)?[0-9]{10,20}$/',
            'otp' => 'required|numeric',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // If validation fails, return error response
        if ($validated->fails()) {
            return response()->json([
                'message' => $validated->errors(),
                'status' => 'error',
            ], 403);
        }

        // Find the user by phone number
        $user = User::where("phone_number", $request->phone_number)->first();

        // If user not found, return error response
        if (!$user) {
            return response()->json([
                'message' => 'The phone number is not registered.',
                'status' => 'error',
            ], 404);
        }

        // Find the latest OTP for the user
        $latestOtp = Otp::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        // If OTP not found or mismatch, return error response
        if (!$latestOtp || $latestOtp->otp_no != $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP.',
                'status' => 'error',
            ], 400);
        }
        // Check if OTP is expired (more than 60 seconds old)
        if (now()->diffInSeconds($latestOtp->created_at) > 60) {
            return response()->json([
                'message' => 'OTP expired.',
                'status' => 'error',
            ], 400);
        }
        DB::beginTransaction();
        try {
            // Update user's password
            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();
            return response()->json([
                'message' => 'Password updated successfully.',
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }


    public function role_list()
    {
        try {
            $roles = Role::pluck("name", "name")->all();
            return response(
                [
                    "roles" => $roles,
                    "message" => "All Role List",
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function permission_list()
    {
        try {
            $permission = Permission::select("name", "id")->get();
            return response(
                [
                    "permission" => $permission,
                    "message" => "All Permission List",
                    "status" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function user_list()
    {
        try {
            $userList = User::get();
            $userList = $userList->map(function ($data) {
                $data->roles = $data->roles;
                $data->permissions = $data->permissions;
                return $data;
            });
            return response([
                'users' => $userList,
                'message' => 'All User List',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function user_update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email," . $id,
        ]);

        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                403
            );
        }

        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                $user->update([
                    "name" => $request->name,
                    "email" => $request->email,
                ]);

                $user->syncRoles($request->roles);
                DB::commit();
                return response(
                    [
                        "user" => $user,
                        "message" => "User Update successfully",
                        "status" => "success",
                    ],
                    201
                );
            }
            return response(
                [
                    "message" => "User Not Found",
                    "status" => "error",
                ],
                403
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }

    public function assign_permission(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            "permissions" => "required",
        ]);

        if ($validated->fails()) {
            return response(
                [
                    "message" => $validated->errors(),
                    "status" => "error",
                ],
                403
            );
        }

        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {

                $user->givePermissionTo($request->permissions);

                DB::commit();
                return response(
                    [
                        "permissions" => $request->permissions,
                        "message" => "Assign Permission Successfully",
                        "status" => "success",
                    ],
                    201
                );
            }
            return response(
                [
                    "message" => "User Not Found!",
                    "status" => "success",
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response(
                [
                    "message" => $e->getMessage(),
                    "status" => "error",
                ],
                403
            );
        }
    }
}
