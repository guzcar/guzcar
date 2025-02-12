<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Muestra el formulario de edición del usuario autenticado.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    /**
     * Actualiza la información del usuario autenticado.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password_old' => 'nullable|string',
            'password_new' => 'nullable|string|min:8|required_with:password_confirm',
            'password_confirm' => 'nullable|string|same:password_new',
        ]);

        // Actualizar campos básicos
        $user->name = $request->name;
        $user->email = $request->email;

        // Actualizar contraseña si se proporciona
        if ($request->filled('password_old') && $request->filled('password_new')) {
            if (Hash::check($request->password_old, $user->password)) {
                $user->password = Hash::make($request->password_new);
            } else {
                return back()->withErrors(['password_old' => 'La contraseña actual no es correcta.']);
            }
        }

        $user->save();

        return redirect()->route('user.edit')->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Agrega un avatar al perfil del usuario.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar_url' => 'required|image',
        ]);

        // Subir y guardar la URL del avatar
        $path = $request->file('avatar_url')->store('avatar', 'public');
        $user->avatar_url = $path;
        $user->save();

        return redirect()->route('user.edit')->with('success', 'Avatar actualizado exitosamente.');
    }

    /**
     * Elimina el avatar del perfil del usuario.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar_url) {
            // Eliminar el archivo físico del almacenamiento si existe
            Storage::disk('public')->delete($user->avatar_url);

            // Eliminar la referencia en la base de datos
            $user->avatar_url = null;
            $user->save();
        }

        return redirect()->route('user.edit')->with('success', 'Avatar eliminado exitosamente.');
    }
}
