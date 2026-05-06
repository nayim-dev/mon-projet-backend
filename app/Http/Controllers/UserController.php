<?php
namespace App\Http\Controllers;

use App\Models\User;

class UserController 
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}