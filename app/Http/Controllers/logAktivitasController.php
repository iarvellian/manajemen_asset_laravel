<?php

namespace App\Http\Controllers;

use App\Models\changeLogs;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class logAktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->id_role == '1') {
            $log = changeLogs::all();

            if(count($log) > 0){
                return response([
                    'message' => 'Retrieve All Success',
                    'data' => $log
                ], 200);    
            }
            
        }else{
            $logs = changeLogs::where('user_id', $user->id)->get();

            if(count($logs) > 0){
                return response([
                    'message' => 'Retrieve Success',
                    'data' => $logs
                ], 200);
            }
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
}
