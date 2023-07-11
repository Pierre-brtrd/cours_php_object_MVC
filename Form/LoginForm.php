<?php

namespace App\Form;

use App\Core\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        $this
            ->startForm('POST', '#', [
                'class' => 'form card p-3 w-50 mx-auto',
                'id' => 'form-login',
                'enctype' => 'multipart/form-data'
            ])
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('email', 'Email:', [
                'class' => 'form-label'
            ])
            ->addInput('email', 'email', [
                'class' => 'form-control',
                'placeholder' => 'john@example.com',
            ])
            ->endDiv()
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('password', 'Mot de passe:', [
                'class' => 'form-label',
            ])
            ->addInput('password', 'password', [
                'class' => 'form-control',
                'placeholder' => 'S3CR3T',
            ])
            ->endDiv()
            ->addButton('Se connecter', [
                'class' => 'btn btn-primary'
            ])
            ->endForm();
    }
}
