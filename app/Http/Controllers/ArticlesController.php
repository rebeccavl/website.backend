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

  }
  //Show Single Article
  public function show($id)
  {

  }
//Delete a Single Function
  public function destroy($id)
  {

  }
}
