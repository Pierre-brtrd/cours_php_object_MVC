<?php

namespace App\Core;

class Form
{
    private $formCode = '';

    /**
     * Génère le formulaire HTML
     *
     * @return string
     */
    public function create()
    {
        return $this->formCode;
    }

    /**
     * Validation du formulaire (si tous les champs sont remplis)
     *
     * @param array $form Tableau issu du formulaire ($_POST || $_GET)
     * @param array $champs Tableau listant les champs obligatoires
     * @return bool
     */
    public static function validate(array $form, array $champs)
    {
        // On parcours les champs
        foreach ($champs as $champ) {
            // Si le champ est absent ou vide dans le form
            if (!isset($form[$champ]) || empty($form[$champ]) || strlen(trim($form[$champ])) == 0) {
                // On sort en retournant false
                return false;
            }
        }
        return true;
    }

    /**
     * Ajoute les attributs à envoyer à la balise
     *
     * @param array $attributs
     * @return string
     */
    private function addAttributs(array $attributs): string
    {
        // On initialise un string
        $str = '';

        // On liste les attributs "courts"
        $courts = ['checked', 'disabled', 'readonly', 'multiple', 'required', 'autofocus', 'novalidate', 'formnovalidate'];

        // On boucle sur le tableau d'attributs
        foreach ($attributs as $attribut => $value) {
            // Vérification si c'est un attribut courts
            if (in_array($attribut, $courts) && $value == true) {
                $str .= " $attribut";
            } else {
                // On ajoute attribut="valeur"
                $str .= " $attribut=\"$value\"";
            }
        }

        return $str;
    }

    /**
     * Crée la balise d'ouverture du formulaire
     *
     * @param string $method methode du formulaire (POST, GET)
     * @param string $action action du formulaire
     * @param array $attributs 
     * @return Form
     */
    public function startForm(string $method = 'POST', string $action = '#', array $attributs = []): self
    {
        // On crée la balise form
        $this->formCode .= "<form action='$action' method='$method'";

        // On ajoute les attributs éventuels
        $this->formCode .= $attributs ? $this->addAttributs($attributs) . '>' : '>';

        return $this;
    }

    /**
     * Crée la balise de fermeture du formulaire
     *
     * @return Form
     */
    public function endForm(): self
    {
        $this->formCode .= '</form>';
        return $this;
    }

    /**
     * Crée une balise de form group
     *
     * @param array $attributs
     * @return self
     */
    public function startGroup(array $attributs = []): self
    {
        // On ajoute la balise
        $this->formCode .= '<div ';

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) . '>' : '>';

        return $this;
    }

    /**
     * Ferme la balise form group
     *
     * @return self
     */
    public function endGroup(): self
    {
        // On ajoute la balise de fermeture
        $this->formCode .= '</div>';

        return $this;
    }


    /**
     * Crée une balise Label avec un for
     *
     * @param string $for
     * @param string $text
     * @param array $attributs
     * @return Form
     */
    public function addLabelFor(string $for, string $text, array $attributs = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<label for=\"$for\"";

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) : '';

        // On ajoute le texte
        $this->formCode .= ">$text</label>";

        return $this;
    }

    /**
     * Crée un input pour un formulaire
     *
     * @param string $type
     * @param string $name
     * @param array $attributs
     * @return Form
     */
    public function addInput(string $type, string $name, array $attributs = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<input type='$type' name='$name' ";

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) . '>' : '>';

        return $this;
    }

    /**
     * Crée une balise TextArea pour un formulaire
     *
     * @param string $nom
     * @param string $valeur
     * @param array $attributs
     * @return Form
     */
    public function addTextArea(string $nom, string $valeur = '', array $attributs = []): self
    {

        // On ouvre la balise
        $this->formCode .= "<textarea name=\"$nom\"";

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) : '';

        // On ajoute le texte
        $this->formCode .= ">$valeur</textarea>";

        return $this;
    }

    /**
     * Crée une balise select pour un formulaire
     *
     * @param string $nom
     * @param array $options
     * @param array $attributs
     * @return Form
     */
    public function addSelectInput(string $nom, array $options, array $attributs = []): self
    {

        // On crée la balise
        $this->formCode .= "<select name='$nom'";

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) . '>' : '>';

        // On ajoute les options
        foreach ($options as $valeur => $text) {
            $this->formCode .= "<option value=\"$valeur\">$text</option>";
        }

        // On ferme la balise select
        $this->formCode .= "</select>";

        return $this;
    }

    /**
     * Crée un bouton pour un formulaire
     *
     * @param string $text
     * @param array $attributs
     * @return Form
     */
    public function addButton(string $text, array $attributs = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<button ";

        // On ajoute les attributs
        $this->formCode .= $attributs ? $this->addAttributs($attributs) : '';

        // On ferme la balise
        $this->formCode .= ">$text</button>";

        return $this;
    }
}
