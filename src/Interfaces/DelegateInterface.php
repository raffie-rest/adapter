<?php namespace Raffie\REST\Adapter\Interfaces;

use Illuminate\Support\MessageBag;

interface DelegateInterface {

    /**
     * Model Create Fails
     * 
     * @param  Illuminate\Support\MessageBag  $errors
     * @return mixed
     */
    public function requestFails(MessageBag $errors);

    /**
     * Model Create Succeeds
     * 
     * @param  mixed $data
     * @return mixed
     */
    public function requestSucceeds($data);

}