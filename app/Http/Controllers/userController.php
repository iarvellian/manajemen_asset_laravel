<?php

namespace App\Http\Controllers;

use App\Models\changeLogs;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class userController extends Controller
{
    public function logActivity()
    {
        try {
            $user = Auth::user();
            
            if ($user->id_role == '1') {
                $log = changeLogs::with('user')->get();

                if(count($log) > 0){
                    $log->each(function ($item) {
                        $item->data_lama = json_decode($item->data_lama);
                        $item->data_baru = json_decode($item->data_baru);
                    });

                    return response([
                        'message' => 'Retrieve All Success',
                        'data' => $log
                    ], 200);    
                }
                
            } else {
                $logs = changeLogs::with('user')->where('user_id', $user->id)->get();

                if(count($logs) > 0){
                    $logs->each(function ($item) {
                        $item->data_lama = json_decode($item->data_lama);
                        $item->data_baru = json_decode($item->data_baru);
                    });

                    return response([
                        'message' => 'Retrieve Success',
                        'data' => $logs
                    ], 200);
                }
            }

            return response([
                'message' => 'Empty',
                'data' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getAllUsers()
    {
        try {
            $users = User::with('role')->get();

            if(count($users) > 0){
                return response([
                    'message' => 'Retrieve All Users Success',
                    'data' => $users
                ], 200);    
            } else {
                return response([
                    'message' => 'No users found',
                    'data' => []
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->id_role !== 1) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $usetarget = User::findOrFail($id);
            $usetarget->delete();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
