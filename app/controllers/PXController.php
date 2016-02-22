<?php

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class PXController extends BaseController {

    public function index(){
        $filters = [
            'feature'           => Input::get('feature', 'popular'),
            'sort'              => Input::get('sort', 'created_at'),
            'sort_direction'    => Input::get('sort_direction', 'desc'),
            'page'              => Input::get('page', 1)
        ];

        $result = $this->loadPhotos($filters);

        return View::make('index', ['photos' => $result['photos'], "inputs" => $filters]);
    }

    public function loadMore(){
        $filters = [
            'feature'           => Input::get('feature', 'popular'),
            'sort'              => Input::get('sort', 'created_at'),
            'sort_direction'    => Input::get('sort_direction', 'desc'),
            'page'              => Input::get('page', 1)
        ];

        $result = $this->loadPhotos($filters);

        return View::make('partials/photos', ['photos' => $result['photos']]);
    }

    private function loadPhotos($parameters){
        $parameters = array_merge(['image_size' => 3], $parameters);

        $px = App::make('pxoauth');
        $result = $px->get('photos', $parameters)->json();

        return $result;
    }

    public function photosByUser($uid){
        $px = App::make('pxoauth');

        $user = $px->get('users/show', ['id' => $uid])->json();
        $inputs = ['image_size' => 3, 'feature' => 'user', 'user_id' => $uid, 'rpp' => 100];
        $result = $this->loadPhotos($inputs);

        return View::make('user', ['photos' => $result['photos'], 'user' => $user['user']]);
    }

    public function favorite(){
        $photoId = Input::get("pid");

        $px = App::make('pxoauth');
        $url = "photos/{$photoId}/favorite";

        try {
            $result = $px->client->post($url);
        }
        catch(RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() === 403){
                return (string) $response->getBody();
            }

            return ["status" => 500, "error" => "A serious bug occurred."];
        }

        return (string) $result->getBody();
    }

    public function vote(){
        $photoId = Input::get("pid");
        //you can disable the link by using `voted` true
        $px = App::make('pxoauth');
        $url = "photos/{$photoId}/vote";
        try {
            $result = $px->client->post($url, ["body" => ['vote' => '1']]);
        }
        catch(RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() === 403){
                return (string) $response->getBody();
            }

            return ["status" => 500, "error" => "A serious bug occurred."];
        }

        return (string) $result->getBody();
    }

    public function show($id){
        $px = App::make('pxoauth');

        try {
            $photo = $px->client->get("photos/{$id}?image_size=4")->json();
            $comments = $px->client->get("photos/{$id}/comments?nested=true")->json();

            return View::make('single', ['photo' => $photo['photo'], 'comments' => $comments['comments']]);
        }
        catch(RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() === 404){
                // handle 404: photo not found
            }
        }
    }

    public function comment(){
        $photoId = Input::get('pid');
        $comment = Input::get('comment');

        $px = App::make('pxoauth');
        $result = $px->client->post("photos/{$photoId}/comments", ['body' => ['body' => $comment]])->json();

        if($result['status'] != 200){
            // handle 400: Bad request.
        }

        return Redirect::back();
    }
    
    public function upload(){
        try {
            $px = App::make('pxoauth');
            $result = $px->client->post('photos/upload', [
                'body'  => [
                    'name'          => Input::get('name'),
                    'description'   => Input::get('description'),
                    'file'          => fopen(Input::file('photo')->getPathname(), 'r'),
                ]
            ])->json();

            // you may want to pass a success message
            return Redirect::to("/photo/{$result['photo']['id']}");
        }
        catch(RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() === 422){
                // handle 422: Server error
            }
        }

    }//upload
}
