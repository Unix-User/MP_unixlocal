<?php

/*
 * The MIT License
 *
 * Copyright 2020 weverton.
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

return [
    /* Options (mysql, sqLite) */

    'driver' => 'mysql',
    'sqlite' => [
        'database' => 'database.db'],
    'mysql' => [
        'host' => 'localhost',
        'database' => 'databasename2',
        'user' => 'username',
        'pass' => 'password',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'],
        
    /* credenciais para email do PHPmailer */
    
    'mailer' => [
        'email' => 'any@email.com',
        'password' => 'password',
    ],
    
    /* chaves de acesso ao mercado pago, modes(test, prod) */
    'MPuser1' => [
        'mode' => 'prod',
        'test_access_token' => 'TEST-0000000000000000-000000-0000000000000000000000000000000-0000000000',
        'access_token' => 'APP_USR-0000000000000000-000000-0000000000000000000000000000000-0000000000'],
    'MPuser2' => [
        'mode' => 'test',
        'test_access_token' => 'TEST-0000000000000000-000000-0000000000000000000000000000000-0000000000',
        'access_token' => 'APP_USR-0000000000000000-000000-0000000000000000000000000000000-0000000000'],
];
