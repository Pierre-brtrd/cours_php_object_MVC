<?php

namespace App\Form;

use App\Core\Form;
use App\Models\PosteModel;

class PosteForm extends Form
{
    public function __construct(?PosteModel $poste = null)
    {
        $this
            ->startForm('POST', '#', [
                'class' => 'form card p-3 w-75 mx-auto',
                'enctype' => 'multipart/form-data',
            ])
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('titre', 'Titre:', ['class' => 'form-label'])
            ->addInput('text', 'titre', [
                'class' => 'form-control',
                'placeholder' => 'Titre de votre article',
                'id' => 'titre',
                'required' => true,
                'value' => $poste ? $poste->getTitre() : null,
            ])
            ->endDiv()
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('description', 'Description', ['class' => 'form-label'])
            ->addTextarea('description', $poste ?  $poste->getDescription() : '', [
                'class' => 'form-control',
                'id' => 'description',
                'placeholder' => 'Description de votre article',
                'rows' => 5,
                'required' => true,
            ])
            ->endDiv()
            ->startDiv(['class' => 'mb-3'])
            ->addLabel('image', 'Image:', ['class' => 'form-label'])
            ->addInput('file', 'image', [
                'class' => 'form-control',
                'id' => 'image',
            ])
            ->addImage($poste ? '/images/poste/' . $poste->getImage() : null, [
                'class' => 'img-form img-fluid rounded mt-2',
                'alt' => $poste ? $poste->getTitre() : null,
                'loading' => 'lazy',
            ])
            ->endDiv()
            ->startDiv(['class' => 'mb-3 form-check'])
            ->addInput('checkbox', 'actif', [
                'class' => 'form-check-input',
                'id' => 'actif',
                'checked' => $poste ? $poste->getActif() : false
            ])
            ->addLabel('actif', 'Actif', ['class' => 'form-check-label'])
            ->endDiv()
            ->addButton($poste ? 'Modifier' : 'Créer', ['class' => 'btn btn-primary'])
            ->endForm();
    }
}
