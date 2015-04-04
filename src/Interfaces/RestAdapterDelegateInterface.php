<?php namespace Raffie\REST\Interfaces;

use Illuminate\Support\MessageBag;

interface RestAdapterDelegateInterface {

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