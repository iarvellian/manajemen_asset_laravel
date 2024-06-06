<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    public function register(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->id_role !== 1) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $customAttributes = [
                'id_role' => 'role',
            ];

            $validate = Validator::make($request->all(), [
                'nama_pegawai' => 'required|string|max:255',
                'jabatan' => 'required|string',
                'username' => 'required|string|max:255|unique:users',
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])/', 'confirmed'],
                'id_role' => 'required|exists:ref_role,id_role',
            ], [], $customAttributes);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);       
            }

            $user = User::create([
                'nama_pegawai' => $request->nama_pegawai,
                'jabatan' => $request->jabatan,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'id_role' => $request->id_role,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'username' => 'required|string|max:255|exists:users,username',
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])/'],
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);       
            }

            $user = User::where('username', $request->username)->first();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password mismatch'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'role'         => $user->role->nama_role
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            $user->load('role'); // Eager load the role relationship

            return response()->json($request->user());
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_pegawai' => 'required|string|max:255',
                'jabatan' => 'required|string',
                'username' => 'required|string|max:255|unique:users,username,'.$request->user()->id,
                'password' => ['nullable', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])/', 'confirmed'],
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);       
            }

            $userData = $request->only('nama_pegawai', 'jabatan', 'username');

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $request->user()->update($userData);

            $updatedUser = $request->user();

            return response()->json($updatedUser);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logged out'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
