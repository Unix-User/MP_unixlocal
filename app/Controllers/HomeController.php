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
use Core\Auth;
use Core\Hotspot;
use Core\BaseController;
use Core\Redirect;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 *
 *
 * @author Weverton
 */
class HomeController extends BaseController
{
    public function index()
    {
        if (Hotspot::checkin()) {
            $this->setPageTitle("Hotspot");
            $this->view->hotspot = [
                'mac'=> $this->hotspot->mac(),
                'ip' => $this->hotspot->ip(),
                'linklogin' => $this->hotspot->linklogin(),
                'linkorig' => $this->hotspot->linkorig(),
                'error' => $this->hotspot->error(),
                'chapid' => $this->hotspot->chapid(),
                'chapchallenge' => $this->hotspot->chapchallenge(),
                'linkloginonly' => $this->hotspot->linkloginonly(),
                'linkorigesc' => $this->hotspot->linkorigesc(),
                'macesc' => $this->hotspot->macesc(),
                'username' => Auth::name()
            ];
            $this->renderView('hotspot');
        }
        $this->setPageTitle('Home');
        $this->renderView('home/index', 'layout');
    }

    public function notification($id)
    {
        $this->view->status = $id;
        $this->setPageTitle('Notificações');
        $this->renderView('notification');
    }

    public function development()
    {
        $this->setPageTitle('Desenvolvimento');
        $this->renderView('home/development', 'layout');
    }

    public function landing()
    {
        $this->setPageTitle('Site');
        $this->renderView('landing');
    }

    public function isp()
    {
        $this->setPageTitle('ISP');
        $this->renderView('home/isp', 'layout');
    }

    public function contact()
    {
        $this->setPageTitle('Contato');
        $this->renderView('home/contato', 'layout');
    }

    public function send($request)
    {
        $conf = include __DIR__ . '/../database.php';
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'smtp.gmail.com';                             // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                     // Enable SMTP authentication
            $mail->Username = $conf['mailer']['email'];                 // SMTP username
            $mail->Password = $conf['mailer']['password'];              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587;                                          // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            // Define o remetente
            $mail->setFrom($request->post->email, $request->post->name);
            // Define o destinatário
            $mail->addAddress('wevertonslima@gmail.com', 'Weverton');
            $mail->addAddress('leonardo.85.rodrigues@gmail.com', 'Leonardo');
            // Conteúdo da mensagem
            $mail->isHTML(true);                                        // Seta o formato do e-mail para aceitar conteúdo HTML
            $mail->Subject = $request->post->category;
            $mail->Body = $request->post->message . '<br />Enviado por: ' . $request->post->email;
            $mail->AltBody = $request->post->message;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            // Enviar
            $mail->send();
            return Redirect::route('/login', [
                'success' => ['A mensagem foi enviada!']
            ]);
        } catch (Exception $e) {
            return Redirect::route('/login', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }
}
