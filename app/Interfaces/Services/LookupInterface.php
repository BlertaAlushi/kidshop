<?php

namespace App\Interfaces\Services;

interface LookupInterface
{
 public function index();
 public function store($data);

 public function update($data,$item);

}
