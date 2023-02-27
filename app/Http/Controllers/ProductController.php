<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{

  //* -----------------------------------------------
  //* CRUD
  //* -----------------------------------------------
  public function create(Request $request)
  {
    try
    {
      //* -------------------------------------------------------
      //* Validations
      //* -------------------------------------------------------
      //* Ejecutar reglas de validación
      $validData = $this->validateDataStore($request);
      //* Validar resultado de validación
      if($validData->fails())
      {
        return $this->respondError($validData->errors(), 'Error validation.');
      }
      //* Crear recurso
      $product = $this->store($request);
      return $this->respondSuccess($product,'Product Created',201);
    }
    catch(\Exception $ex)
    {
      return $this->respondError(['exception', $ex],'Exception',500);
    }
  }
  public function index()
  {
    try
    {
      //* Obtener datos
      $products = $this->getAll();
      return Response()->json($products,200);
    }
    catch(\Exception $ex)
    {
      return $this->respondError(['exception', $ex],'Exception',500);
    }
  }
  public function show($id)
  {
    try
    {
      //* Obtener un solo registro
      $product = $this->getOne($id);
      return Response()->json($product,200);
    }
    catch(\Exception $ex)
    {
      return $this->respondError(['exception', $ex],'Exception',500);
    }
  }
  public function update(Request $request, int $id)
  {
    try
    {
      //* --------------------------------------------------------
      //* Validaciones
      //* --------------------------------------------------------
      $validData = $this->validateDataUpdate($request, $id);
      if($validData->fails())
      {
        return $this->respondError($validData->errors(),' Error validation', 400);
      }
      if(count($validData->validate()) == 0)
      {
        return $this->respondError(['general' => 'Operation reject'],'Error', 400);
      }

      $product = $this->edit($request, $id);
      return $this->respondSuccess($product,'Product updated',200);
    }
    catch(\Exception $ex)
    {
      return $this->respondError($ex, 'Exception', 500);
    }
  }
  public function delete(int $id)
  {
    try
    {
      $deleted = $this->destroy($id);
      if(!$deleted)
      {
        return $this->respondError([], 'Error operation');
      }
      return $this->respondSuccess([], 'Product Deleted',200);
    }
    catch(\Exception $ex)
    {
      return $this->respondError($ex, 'Exception',500);
    }
  }
  //* -----------------------------------------------
  //* End CRUD
  //* -----------------------------------------------
  //* Service
  //* -----------------------------------------------
  private function store(Request $request) : Product
  {
    //* Crear modelo
    $product = new Product();
    $product->code         = $request->code;
    $product->description  = $request->description;
    $product->category     = $request->category?? null;
    $product->brand        = $request->brand ?? null;
    $product->type_product = $request->type_product ?? 'Product';
    $product->unit         = $request->unit ?? 'Pieza';
    $product->color        = $request->color ?? null;
    $product->weight       = $request->weight ?? 0;
    $product->price        = $request->price ?? 0;
    $product->size         = $request->size ?? 0;
    //* Guardar modelo
    $product->save();
    return $product;
  }
  private function getAll() : Collection
  {
    $product = new Product();
    return $product::all();
  }
  private function getOne(int $id) : Product
  {
    $product = new Product();
    return $product::find($id);
  }
  private function edit(Request $request, int $id) : Product
  {
    //* Obtener producto
    $product = Product::find($id);
    if($request->has('code'))
    {
      $product->code         = $request->code;
    }
    if($request->has('description'))
    {
      $product->description  = $request->description;
    }
    if($request->has('category'))
    {
      $product->category     = $request->category;
    }
    if($request->has('brand'))
    {
      $product->brand        = $request->brand;
    }
    if($request->has('init'))
    {
      $product->unit         = $request->unit;
    }
    if($request->has('type_product'))
    {
      $product->type_product = $request->type_product;
    }
    if($request->has('color'))
    {
      $product->color        = $request->color;
    }
    if($request->has('weight'))
    {
      $product->weight       = $request->weight;
    }
    if($request->has('price'))
    {
      $product->price        = $request->price;
    }
    if($request->has('size'))
    {
      $product->size         = $request->size;
    }
    //* Guardar modelo
    $product->save();
    return $product;
  }
  private function destroy(int $id) : bool
  {
    //* Obtener producto
    $product = Product::find($id);
    //* Eliminar registro
    $deleted = $product->delete();
    return $deleted;
  }
  //* -----------------------------------------------
  //* End Service
  //* -----------------------------------------------
  //* Validation
  //* -----------------------------------------------
  public function validateDataStore(Request $request)
  {
    //* Obtener datos del request body
    $payload = $request->all();
    //* Definir Reglas
    $rules = [
      'code'         => 'required|string|unique:products|min:2|max:65',
      'description'  => 'required|string|unique:products|max:255',
      'category'     => 'nullable|string|max:255',
      'brand'        => 'nullable|string|max:255',
      'type_product' => 'nullable|string|max:15|in:product,service,kit',
      'unit'         => 'nullable|string|max:65',
      'color'        => 'nullable|string|max:65',
      'weight'       => 'nullable|number|decimal:10,2|gte:0',
      'price'        => 'nullable|number|decimal:10,2|gte:0',
      'size'         => 'nullable|number|decimal:10,2|gte:0',
    ];
    //* Mensajes de validaciones personalizados
    $messageRules = [
      'required' => 'The field :attribute, is required.',
      'unique'   => 'The field :attribute, must be unique.',
      'min'      => 'The field :attribute, must be greater that :min characters.',
      'max'      => 'The field :attribute, must be less that :max characters.',
      'in'       => 'The field :attribute, must be Product, Service or kit.',
      'decimal'  => 'The field :attribute, must be decimal.',
      'gte'      => 'The field :attribute, must be greater that or equal to :value.',
    ];
    //* Personalizar nombre de atributos
    $attributes = [
      'type_product' => 'product type',
    ];
    //* Construir validator personalizado
    return Validator::make($payload,$rules, $messageRules, $attributes);
  }
  public function validateDataUpdate(Request $request, int $id)
  {
    //* Obtener datos del request body
    $payload = $request->all();
    //* Definir Reglas
    $rules = [
      'code'         => 'nullable|string|min:2|max:65|unique:products,code,'.$id,
      'description'  => 'nullable|string|max:255|unique:products,description,'.$id,
      'category'     => 'nullable|string|max:255',
      'brand'        => 'nullable|string|max:255',
      'type_product' => 'nullable|string|max:15|in:product,service,kit',
      'unit'         => 'nullable|string|max:65',
      'color'        => 'nullable|string|max:65',
      'weight'       => 'nullable|number|decimal:10,2|gte:0',
      'price'        => 'nullable|number|decimal:10,2|gte:0',
      'size'         => 'nullable|number|decimal:10,2|gte:0',
    ];
    //* Mensajes de validaciones personalizados
    $messageRules = [
      'unique'   => 'The field :attribute, must be unique.',
      'min'      => 'The field :attribute, must be greater that :min characters.',
      'max'      => 'The field :attribute, must be less that :max characters.',
      'in'       => 'The field :attribute, must be Product, Service or kit.',
      'decimal'  => 'The field :attribute, must be decimal.',
      'gte'      => 'The field :attribute, must be greater that or equal to :value.',
    ];
    //* Personalizar nombre de atributos
    $attributes = [
      'type_product' => 'product type',
    ];
    //* Construir validator personalizado
    return Validator::make($payload,$rules, $messageRules, $attributes);
  }
  //* -----------------------------------------------
  //* End Validation
  //* -----------------------------------------------

}
