<?php

namespace App\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        // précaution si notre objet date ne contient rien

        if ($date === null){
          return '';
        }
        return $date->format('d/m/Y');
    }


    public function reverseTransform($frenchDate)
    {
        // ce que nous sousentendant par frenchDate c'est une format 21/09/2019

        if ($frenchDate === null){
            throw new TransformationFailedException("Vous devez fournir une date !");
        }
        // sinon on formate la chine de caractère frenchDate en une DateTime

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        // si createFromFormat n'arrive pas à nous rendre un objet DateTime ( rend dans ce cas False) ==> on lance une exception
        if ($date === false){
            throw new TransformationFailedException("Le format de la date n'est pas le bon !");
        }

        return $date;
    }
}