<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImagenController extends Controller
{
    public function store(Request $request)
    {
        $imagen = $request->file('file');

        $nombreImagen = Str::uuid() . "." . $imagen->extension();

        $imagenServidor = new ImageManager(new Driver());
        $imagen = $imagenServidor->read($imagen);
        $imagen->cover(1000, 1000);
        $imagenPath = public_path('uploads') . '/' . $nombreImagen;
        $imagen->save($imagenPath);

        return response()->json(['imagen' => $nombreImagen]);
    }
}
