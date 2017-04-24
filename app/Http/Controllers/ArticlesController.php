<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
class ArticlesController extends Controller
{
  public function __construct()//aka a constructor but in php form.this ensures that only logged in users may store, update, and destroy.
  {
    $this->middleware('jwt.auth', ['only'=>['store','update','destroy']]);
  }
  //List of Articles

  public function index()
  {
    $articles = Article::orderBy("id","desc")->take(3)->get();
    foreach($articles as $key => $article)
    {
      if(strlen($article->body) > 100)
      {
        $article->body = substr($article->body, 0, 100)."...";
      }
    }
    return Response::json($articles);
  }
  //Stores Our Articles
  public function store(Request $request)
  //store function
  //validate and stores blog post
  {
    $rules = [//rules array. states what needs to be required from our store array in the front end
      'title' => 'required',
      'body' => 'required',
      'image' => 'required'
    ];


    $validator = Validator::make(Purifier::clean($request->all()), $rules);//pass in data

    if($validator->fails())//check to see if validation has failed. if a return statement artivates then anything after will not activate. unless you choose to do an 'else if' statement
    {
      return Response::json(["error" => "Please fill out all fields."]);
    }

    $user = Auth::user();//$user is being formatted here, so it can be referenced below

    if($user->roleID != 1)//states what user id has access and what does not.
    {
      return Response::json(["error" => "You can't enter here."]);
    }

    $article = new Article;

    $article->title = $request->input('title');
    $article->body = $request->input('body');

    $image = $request->file('image');
    $imageName= $image->getClientOriginalName();
    $image->move('storage/',$imageName);
    $article->image = $request->root()."/storage/".$imageName;
    $article->save();//always include save

    return Response::json(["success" => "You beastmoded it!"]);

  }

  //Update Our Articles
  public function update($id, Request $request)
  {
    $article = Article::find($id);

    //Saves the title
    $article->title = $request->input('title');

    //Saves the body
    $article->body = $request->input('body');

    //Saves the image to the server and save the image URL to the DB.
    $image = $request->file('image');
    $imageName = $image->getClientOriginalName();
    $image->move("storage/",$imageName);
    $article->image = $request->root(). "/storage/".$imageName;

    //Comits the saves to the DB
    $article->save();

    return Response::json(["success"=>"Article Updated."]);
  }
  //Show Single Article
  public function show($id)
  {
    $article = Article::find($id);

    return Response::json($article);
  }


//Delete a Single Function, ie deletes and article
  public function destroy($id)
  {
    $article = Article::find($id);

    $article->delete();

    return Response::json(['success' => 'Deleted Article.']);
  }
}
