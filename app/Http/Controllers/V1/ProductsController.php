<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    protected $user;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');

        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Listamos todos los productos
        return Product::get();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('name', 'description', 'stock');
        $validator = Validator::make($data, [
            'name' => 'required|max:50|string',
            'description' => 'required|max:50|string',
            'stock' => 'required|numeric',
        ]);

        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Creamos el producto en la BD
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'stock' => $request->stock,
        ]);

        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Product created',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Bucamos el producto
        $product = Product::find($id);

        //Si el producto no existe devolvemos error no encontrado
        if (!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        //Si hay producto lo devolvemos
        return $product;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('name', 'description', 'stock');
        $validator = Validator::make($data, [
            'name' => 'required|max:50|string',
            'description' => 'required|max:50|string',
            'stock' => 'required|numeric',
        ]);

        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Buscamos el producto
        $product = Product::findOrfail($id);

        //Actualizamos el producto.
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'stock' => $request->stock,
        ]);

        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el producto
        $product = Product::findOrfail($id);

        //Eliminamos el producto
        $product->delete();

        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }
}
