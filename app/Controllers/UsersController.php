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

use App\Models\Credential;
use App\Models\Category;
use App\Models\User;
use App\Models\Usage;
use App\Models\Ticket;
use Core\BaseController;
use Core\Auth;
use Core\Redirect;
use Core\Validator;
use Core\Authenticate;


/**
 * Description of UsersController
 *
 * @author Weverton
 */
class UsersController extends BaseController
{
    use Authenticate;

    private $user;
    private $credential;
    private $ticket;
    private $usage;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User;
        $this->credential = new Credential;
        $this->ticket = new Ticket;
        $this->usage = new Usage;
    }

    public function index()
    {
        $this->setPageTitle("Pessoas");
        $this->view->users = $this->user->All();
        if (Auth::admin()) {
            return $this->renderView("users/index", 'layout');
        }
        return Redirect::route('/login', ['errors' => ['Acesso administrativo requisitado.']]);
    }

    public function show($id)
    {
        $this->view->user = $this->user->find($id);
        $this->view->usage = $this->usage->All();
        if (Auth::admin() || (Auth::id() == $this->view->user->id)) {
            $this->view->users = $this->user->All();
            $this->view->credential = $this->credential->find($this->view->user->owner->id);
            $this->setPageTitle("{$this->view->user->name}");
            return $this->renderView("users/show", "layout");
        }
        return Redirect::route('/login', ['errors' => ['Acesso administrativo requisitado.']]);
    }

    public function create()
    {
        $this->setPageTitle('Novo usuário');
        $this->view->categories = Category::all();
        if (Auth::check() && (Auth::admin() == false)) {
            return Redirect::route('/users', ['errors' => ['Não possui privilégios necessários para criar um usuário.']]);
        }
        return $this->renderView("users/create", 'layout');
    }

    public function store($request)
    {
        if (Auth::id() != NULL) {
            $owner = Auth::id();
        } else {
            $owner = '1';
        }
        $data = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'phone' => $request->post->phone,
            'cep' => $request->post->cep,
            'rua' => $request->post->rua,
            'num' => $request->post->num,
            'bairro' => $request->post->bairro,
            'cidade' => $request->post->cidade,
            'uf' => $request->post->uf,
            'password' => $request->post->password,
            'owner_id' => $owner,
            'expires' => '0000-00-00'

        ];
        if (Validator::make($data, $this->user->rulesCreate())) {
            return Redirect::route('/user/create');
        }
        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);

        try {
            $user = $this->user->create($data);
            if (isset($request->post->category_id) && ($request->post->category_id[0] != '0')) {
                $user->category()->attach($request->post->category_id);
            }
            return Redirect::route('/users', [
                'success' => ['Usuário cadastrado com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function edit($id)
    {
        $this->view->user = $this->user->find($id);
        $this->view->categories = Category::all();
        if ((Auth::id() == $this->view->user->owner->id) || (Auth::id() == $this->view->user->id)) {
            $this->setPageTitle('Editar dados - ' . $this->view->user->name);
            return $this->renderView('users/edit', 'layout');
        }
        return Redirect::route('/users', ['errors' => ['Esses dados estão indisponiveis no momento.']]);
    }

    public function update($id, $request)
    {
        if (isset($request->post->owner)) {
            $owner = $request->post->owner;
        } else {
            $owner = Auth::id();
        }
        $data = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'phone' => $request->post->phone,
            'cep' => $request->post->cep,
            'rua' => $request->post->rua,
            'num' => $request->post->num,
            'bairro' => $request->post->bairro,
            'cidade' => $request->post->cidade,
            'uf' => $request->post->uf,
            'password' => $request->post->password,
            'owner_id' => $owner,
            'expires' => $request->post->expires
        ];
        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);
        if ($data['password'] == "") {
            $data['password'] = $this->user->find($id)->password;
        }
        if (Validator::make($data, $this->user->rulesUpdate($id))) {
            return Redirect::route("/user/{$id}/edit");
        }

        try {
            $user = $this->user->find($id);
            $user->update($data);

            if (isset($request->post->category_id) && ($request->post->category_id[0] != '0')) {
                $user->category()->sync($request->post->category_id);
            } else {
                $user->category()->detach();
            }
            return Redirect::route('/users', [
                'success' => ['Dados do usuário ' . $user->name . ' foram atualizados com sucesso<br />']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->user->find($id);
            if ((Auth::id() != $user->owner->id) && ($user->owner->id != 0)) {
                return Redirect::route('/users', [
                    'errors' => ['Você não pode excluir registros de outro usuário.']
                ]);
            }
            $user->delete();
            return Redirect::route('/users', [
                'success' => ['Usuário excluido com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function service($request)
    {
        $result = User::where('email', $request->post->email)->first();
        if ($result && password_verify($request->post->password, $result->password)) { //request user details for sync
            $user = [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'owner_id' => $result->owner_id
            ];
            if ($user['id'] == $user['owner_id']) {
                $this->view->check = true;
                $this->view->auth = $user['id'];
            }
        } elseif (isset($request->post)) { //data usage received
            /*
            $data = [
                'owner_id' => $request->post->id,
                'month' => $request->post->month,
                'GB_in' => $request->post->GB_in,
                'GB_out' => $request->post->GB_out
            ];
            $record = Usage::where('owner_id', $request->post->id)->where('month', $request->post->month)->first();
            try {
                if ($record) {
                    $usage = $this->usage->find($record->id);
                    $usage->update($data);
                } else {
                    $this->usage->create($data);
                }
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }*/
            $this->view->post = $request->post;
        } else {
            return Redirect::route('/system', [
                'errors' => ['autenticação nescessária']
            ]);
        }
        $this->view->credential = $this->credential->find($result->owner_id);
        if (isset($request->post->mikrotik) && ($request->post->mikrotik == 'true')) {
            $this->view->users = $this->user->All();
            $this->renderView('users/device', '');
        } else {
            $this->view->users = $this->user->All();
            $this->view->usage = $usage;
            $this->renderView('users/service', '');
        }
    }

    public function processar_pagamento($request)
    {
        if (isset($request)) {
            $this->view->user = $this->user->find($request->get->external_reference);
            $this->view->topic = $request->get->topic;
            $this->view->preference_id = $request->get->preference_id;
            $this->view->collection_id = $request->get->collection_id;
            $this->view->collection_status = $request->get->collection_status;
            $this->view->payment_id = $request->get->payment_id;
            $this->view->status = $request->get->status;
            $this->view->payment_type = $request->get->payment_type;
            $this->view->merchant_order_id = $request->get->merchant_order_id;
            $this->view->preference_id = $request->get->preference_id;
            $this->view->site_id = $request->get->site_id;
            $this->view->processing_mode = $request->get->aggregator;
            $this->view->merchant_account_id = $request->get->merchant_account_id;
            $this->setPageTitle('processar_pagamento');
            $this->renderView('users/processar_pagamento', 'layout');
        } else {
            return Redirect::route('/system', [
                'errors' => ['não há um link de pagamentos para processar']
            ]);
        }
    }

    public function system()
    {
        $this->view->user = $this->user->find(Auth::id());
        $this->view->credential = $this->credential->find(Auth::id());
        if (Auth::check() && (Auth::admin() == false)) {
            return Redirect::route('/client', ['errors' => ['Esses dados estão indisponiveis no momento.']]);
        }
        $this->setPageTitle('Sistema');
        return $this->renderView('users/system', 'layout');
    }

    public function cred_update($id, $request)
    {
        $data = [
            'id' => $id,
            'mode' => $request->post->mode,
            'install_password' => $request->post->install_password,
            'test_access_token' => $request->post->test_access_token,
            'access_token' => $request->post->access_token
        ];
        if (Validator::make($data, $this->credential->rules())) {
            return Redirect::route('/system');
        }

        try {
            $credential = $this->credential->find($id);
            if (!isset($credential)) {
                $this->credential->create($data);
            } else {
                $credential->update($data);
            }
            return Redirect::route('/system', [
                'success' => ['Credenciais registradas']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/system', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function support()
    {
        $this->setPageTitle('Suporte');
        $this->view->tickets = $this->ticket->All();
        $this->view->users = $this->user->All();
        $this->renderView('users/support', 'layout');
    }

    public function ticket()
    {
        $this->setPageTitle('Novo ticket de suporte');
        $this->view->categories = Category::all();
        if (!Auth::check()) {
            return Redirect::route('/support', ['errors' => ['Não é possivel criar o ticket de suporte sem estar logado.']]);
        }
        return $this->renderView("users/ticket", 'layout');
    }


    public function register($request)
    {
        $data = [
            'title' => $request->post->titulo,
            'owner_id' => Auth::id(),
            'description' => $request->post->description,
            'relative_to' => $request->post->relative_to,
            'status' => '0'
        ];
        if (Validator::make($data, $this->ticket->rules())) {
            return Redirect::route('/support/ticket');
        }

        try {
            $user = $this->ticket->create($data);
            if (isset($request->post->status) && ($request->post->status == '1')) {
                return Redirect::route('/ticket/' . $request->post->relative_to . '/close');
            }
            return Redirect::route('/support', [
                'success' => ['Ticket de suporte registrado, aguarde a resposta do setor técnico']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/support', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function close($id)
    {
        $data = [
            'status' => '1'
        ];
        $ticket = $this->ticket->find($id);
        if ((Auth::admin() != true) && ($ticket->owner_id != Auth::id())) {
            return Redirect::route('/support', [
                'errors' => ['Você não pode encerrar tickets de outro usuário.']
            ]);
        }
        try {
            $ticket->update($data);
            return Redirect::route('/support', [
                'success' => ['O ticket de suporte numero ' . $id . ' foi encerrado.<br />']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/support', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function display($id)
    {
        $this->view->tickets = $this->ticket->All();
        $this->view->ticket = $this->ticket->find($id);
        $this->view->user = $this->user;
        $this->setPageTitle("{$this->view->ticket->title}");
        return $this->renderView("users/ticket", "layout");
    }
}
