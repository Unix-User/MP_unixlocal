<?php

/*
 * The MIT License
 *
 * Copyright 2020 Weverton.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Auth;
use Core\BaseController;
use Core\Redirect;
use Core\Validator;
use Illuminate\Support\Facades\Request;

/**
 * Description of ProductController
 *
 * @author Weverton
 */
class ProductsController extends BaseController
{

    private $product;

    public function __construct()
    {
        parent::__construct();
        $this->product = new Product;
    }

    public function index()
    {
        $this->setPageTitle("Produtos");
        $this->view->products = $this->product->All();
        return $this->renderView("products/index", 'layout');
    }

    public function show($id)
    {
        $this->view->product = $this->product->find($id);
        $this->setPageTitle("{$this->view->product->product}");
        return $this->renderView("products/show", "layout");
    }

    public function create()
    {
        $this->setPageTitle('Novo produto');
        $this->view->categories = Category::all();
        return $this->renderView("products/create", 'layout');
    }

    public function uploadImage(Request $request)
    {
        $response = null;
        $user = (object) ['image' => ""];

        if ($request->hasFile('image')) {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = './public_html/img/';
            $image = 'U-' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = '/public_html/img/' . $image;
                return $this->responseRequestSuccess($user);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
    }

    protected function responseRequestSuccess($ret)
    {
        return response()->json(['status' => 'success', 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['status' => 'error', 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }




    public function store($request)
    {
        $data = [
            'product' => $request->post->product,
            'description' => $request->post->description,
            'value' => $request->post->value,
            'image' => $request->post->image,
            'rate' => $request->post->rate,
        ];
        if (Validator::make($data, $this->product->rules())) {
            return Redirect::route('/product/create');
        }
        try {
            $product = $this->product->create($data);
            if (isset($request->post->category_id)) {
                $product->category()->attach($request->post->category_id);
            }
            return Redirect::route('/products', [
                'success' => ['Dados inseridos com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/products', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function edit($id)
    {
        $this->view->product = $this->product->find($id);
        if (Auth::admin() != true) {
            $prod = $this->view->product->user->id;
            $usr = Auth::admin();
            return Redirect::route('/products', [
                'errors' => ['Os dados de ' . $prod . ' estão indisponiveis no momento para ' . $usr . ' incidente sera reportado']
            ]);
        }
        $this->setPageTitle('Editar dados - ' . $this->view->product->product);
        return $this->renderView('products/edit', 'layout');
    }

    public function update($id, $request)
    {
        $data = [
            'product' => $request->post->product,
            'description' => $request->post->description,
            'value' => $request->post->value,
            'image' => $request->post->image,
            'rate' => $request->post->rate
        ];

        if (Validator::make($data, $this->product->rules())) {
            return Redirect::route("/product/{$id}/edit");
        }

        try {
            $product = $this->product->find($id);
            $product->update($data);
            if (isset($request->post->category_id)) {
                $product->category()->sync($request->post->category_id);
            } else {
                $product->category()->detach();
            }
            $this->product->find($id)->update($data);
            return Redirect::route('/products', [
                'success' => ['Dados atualizados com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/products', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $product = $this->product->find($id);
            if (Auth::admin() != true) {
                return Redirect::route('/products', [
                    'errors' => ['Você não pode excluir post de outro autor.']
                ]);
            }
            $product->delete();
            return Redirect::route('/products', [
                'success' => ['Item excluído com sucesso!']
            ]);
        } catch (\Exception $e) {
            return Redirect::route('/posts', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}
