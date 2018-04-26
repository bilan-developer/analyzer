<?php


namespace App\Exceptions;


class ModelException extends \Exception
{
    protected $code = 422;

    protected $message = 'The given data was invalid.';

    protected $errors = [];


    public function __construct($errors = []) {
        $this->errors = $errors;
    }


    public function getErrors(){
        return $this->errors;
    }
}