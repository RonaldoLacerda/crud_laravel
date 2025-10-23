<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {   
        $colors = Color::all();

        return view('usuarios.index', [
            'colors' => $colors
        ]);
    }

    public function usuarios()
    {
        $usuarios = User::with('colors')->get()->map(function($user){
            return [
                'id'        => $user->id,
                'nome'      => $user->name,
                'email'     => $user->email,
                'colors'    => $user->colors 
            ];
        });
        return response()->json($usuarios);
    }

    public function edit($id)
    {
        $user = User::with('colors')->findOrFail($id);
        return response()->json([
            'id'        => $user->id,
            'nome'      => $user->name,
            'email'     => $user->email,
            'colors'    => $user->colors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'      => 'required|string|min:3',
            'email'     => 'required|email|unique:users,email',
            'colors'    => 'required|array|min:1',
            'colors.*'  => 'exists:colors,id'
        ], [
            'nome.required'   => 'O nome é obrigatório.',
            'nome.min'        => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required'  => 'O e-mail é obrigatório.',
            'email.email'     => 'Informe um e-mail válido.',
            'email.unique'    => 'Este e-mail já está cadastrado.',
            'colors.required' => 'Selecione pelo menos uma cor.',
            'colors.min'      => 'Selecione pelo menos uma cor.',
            'colors.*.exists' => 'Uma das cores selecionadas é inválida.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'  => $validated['nome'],
                'email' => $validated['email'],
            ]);

            foreach ($validated['colors'] as $color_id) {
                DB::table('user_color')->insert([
                    'user_id'  => $user->id,
                    'color_id' => $color_id
                ]);
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nome'      => 'required|string|min:3',
            'email'     => "required|email|unique:users,email,{$id}",
            'colors'    => 'required|array|min:1',
            'colors.*'  => 'exists:colors,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'  => $validated['nome'],
                'email' => $validated['email']
            ]);

            DB::table('user_color')->where('user_id', $user->id)->delete();
            
            foreach ($validated['colors'] as $color_id) {
                DB::table('user_color')->insert([
                    'user_id'   => $user->id,
                    'color_id'  => $color_id
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            DB::table('user_color')->where('user_id', $user->id)->delete();

            $user->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}