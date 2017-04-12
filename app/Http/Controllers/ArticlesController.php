<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Response;

class ArticlesController extends Controller
{
  //List of Articles
  public function index()
  {
    $articles = Article::orderBy("id","desc")->take(3)->get();

    return Response::json($articles);
  }
  //Stores Our Articles
  public function store(Request $request)
  {
    $article = new Article;
    $article->title = $request->input('title');
    $article->body = $request->input('body');

    $image = $request->file('image');
    $imageName= $image->getClientOriginalName();
    $image->move('storage/',$imageName);
    $article->image = $request->root()."/storage/".$imageName;
    $article->save();

    return Response::json(["success" => "you beastmoded it"]);

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

//Delete a Single Function
  public function destroy($id)
  {
    $article = Article::find($id);

    $article->delete();

    return Response::json(['success' => 'Deleted Article.']);
  }
}
