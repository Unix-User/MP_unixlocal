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

namespace App\Models;

use \Core\BaseModelElloquent;

class User extends BaseModelElloquent {

    public $table = "users";
    public $timestamps = false;
    protected $fillable = ['name', 'email', 'phone', 'cep','rua', 'num', 'bairro', 'cidade', 'uf', 'password', 'owner_id', 'expires'];

    public function rulesCreate() {
        return [
            'name' => 'min:2|max:255|unique:User:name',
            'email' => 'email|unique:User:email',
            'password' => 'required|min:4|max:60',
            'phone' => 'min:9|max:14|unique:User:phone',
            'cep' => 'max:10'
        ];
    }

    public function rulesUpdate($id) {
        return [
            'name' => 'min:2|max:255|unique:User:name:'.$id,
            'email' => 'email|unique:User:email:'.$id,
            'password' => 'min:4|max:60',
            'phone' => 'min:9|max:14|unique:User:phone:'.$id,
            'cep' => 'max:10'
        ];
    }

    public function user() {
        return $this->hasMany(User::class);
    }

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsToMany(Category::class);
    }

}
