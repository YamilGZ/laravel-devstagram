<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Routing\Controllers\HasMiddleware;

class PerfilController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            //new Middleware('auth', only: ['create']),
        ];
    }

    public function index()
    {
        return view('perfil.index');
    }
    public function store(Request $request)
    {
        $request->request->add(['username' => Str::slug($request->username)]);

        $request->validate([
            'username' => ['required', 'unique:users,username, ' . auth()->user()->id, 'min:3', 'max:20', 'not_in:twitter,editar-perfil'],
        ]);

        if ($request->imagen) {
            $imagen = $request->file('imagen');

            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            $imagenServidor = new ImageManager(new Driver());
            $imagen = $imagenServidor->read($imagen);
            $imagen->cover(1000, 1000);

            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagen->save($imagenPath);
        }

        // Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->id ?? null;
        $usuario->save();

        // Redireccionar
        return redirect()->route('posts.index', $usuario->username);
    }
}
