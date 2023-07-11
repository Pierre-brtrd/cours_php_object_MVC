<?php

namespace App\Form;

use App\Core\Form;

class RegisterForm extends Form
{
    public function __construct()
    {
        $this
            ->startForm('POST', '#', [
                'class' => 'form card p-3 w-75 mx-auto'
            ])
            ->startDiv(['class' => 'row mb-3'])
            ->startDiv(['class' => 'col-md-6'])
            ->addLabel('nom', 'Nom:', ['class' => 'form-label'])
            ->addInput('text', 'nom', [
                'class' => 'form-control',
                'id' => 'nom',
                'placeholder' => 'Doe'
            ])
            ->endDiv()
            ->startDiv(['class' => 'col-md-6'])
            ->addLabel('prenom', 'Prénom:', ['class' => 'form-label'])
            ->addInput('text', 'prenom', [
                'class' => 'form-control',
                'id' => 'prenom',
                'placeholder' => 'John',
            ])
            ->endDiv()
            ->endDiv()
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('email', 'Email:', ['class' => 'form-label'])
            ->addInput('email', 'email', [
                'class' => 'form-control',
                'id' => 'email',
                'placeholder' => 'johndoe@exemple.com',
            ])
            ->endDiv()
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('password', 'Mot de passe:', ['class' => 'form-label'])
            ->addInput('password', 'password', [
                'class' => 'form-control',
                'id' => 'password',
                'placeholder' => 'S3CR3T',
            ])
            ->endDiv()
            ->addButton('S\'inscrire', ['class' => 'btn btn-primary'])
            ->endForm();
    }
}
