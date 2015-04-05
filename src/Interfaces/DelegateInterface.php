<?php namespace Raffie\REST\Adapter\Interfaces;

use Illuminate\Support\MessageBag;

interface DelegateInterface {

    /**
     * Request Fails
     * 
     * @param  Illuminate\Support\MessageBag  $errors
     * @return mixed
     */
    public function requestFails(MessageBag $errors);

    /**
     * Request Succeeds
     * 
     * @param  mixed $data
     * @return mixed
     */
    public function requestSucceeds($data);

}