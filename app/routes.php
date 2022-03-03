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

$routes[] = ['/', 'HomeController@landing'];
$routes[] = ['/client', 'HomeController@index', 'auth'];
$routes[] = ['/isp', 'HomeController@isp'];
$routes[] = ['/development', 'HomeController@development'];
$routes[] = ['/notification/{id}', 'HomeController@notification'];
$routes[] = ['/contact', 'HomeController@contact'];
$routes[] = ['/checkin', 'HomeController@checkin'];
$routes[] = ['/send', 'HomeController@send'];

$routes[] = ['/products', 'ProductsController@index'];
$routes[] = ['/product/{id}/show', 'ProductsController@show'];
$routes[] = ['/product/create', 'ProductsController@create', 'auth'];
$routes[] = ['/product/store', 'ProductsController@store', 'auth'];
$routes[] = ['/product/uploadImage', 'ProductsController@uploadImage', 'auth'];
$routes[] = ['/product/{id}/edit', 'ProductsController@edit', 'auth'];
$routes[] = ['/product/{id}/update', 'ProductsController@update', 'auth'];
$routes[] = ['/product/{id}/delete', 'ProductsController@delete', 'auth'];

$routes[] = ['/users', 'UsersController@index', 'auth'];
$routes[] = ['/user/{id}/show', 'UsersController@show', 'auth'];
$routes[] = ['/user/create', 'UsersController@create'];
$routes[] = ['/user/store', 'UsersController@store'];
$routes[] = ['/user/service', 'UsersController@service'];
$routes[] = ['/processar_pagamento', 'UsersController@processar_pagamento'];
$routes[] = ['/user/{id}/edit', 'UsersController@edit', 'auth'];
$routes[] = ['/user/{id}/update', 'UsersController@update', 'auth'];
$routes[] = ['/user/{id}/delete', 'UsersController@delete', 'auth'];
$routes[] = ['/system', 'UsersController@system', 'auth'];
$routes[] = ['/credential/{id}/update', 'UsersController@cred_update', 'auth'];
$routes[] = ['/support', 'UsersController@support', 'auth'];
$routes[] = ['/support/ticket', 'UsersController@ticket', 'auth'];
$routes[] = ['/ticket/register', 'UsersController@register', 'auth'];
$routes[] = ['/ticket/{id}/close', 'UsersController@close', 'auth'];
$routes[] = ['/ticket/{id}/show', 'UsersController@display'];

$routes[] = ['/login', 'UsersController@login'];
$routes[] = ['/login/auth', 'UsersController@auth'];
$routes[] = ['/hotspot', 'UsersController@hotspot'];
$routes[] = ['/logout', 'UsersController@logout'];

return $routes;