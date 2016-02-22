<?php

class PagesController extends BaseController {

    public function photoUpload(){
        return View::make('upload');
    }

    public function login(){
        return View::make('authorize');
    }
}