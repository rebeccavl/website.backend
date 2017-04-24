<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response;
use App\Comment;
use App\Article;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;

class CommentsController extends Controller
{
  public function __construct()//aka a constructor but in php form.this ensures that only logged in users may comment.
  {
    $this->middleware('jwth.auth',['only'=>['store']]);
  }
    public function index($id)
    {
      $comments = Comment::where('comments.articleID','=',$id)
        ->join('users','comments.userID','=','users.id')
        ->select('comments.id', 'comments.created_at','comments.body','users.name')
        ->orderBy('id','desc')
        ->get();

        foreach ($comments as $key => $comment)
        {
          $comment->commentDate = Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans();
        }
      return Response::json($comments);
    }

    public function store(Request $request)
    {
      $rules = [//corresponds with the storeComment in the comments index.js
        'body' => 'required',
        'articleID' => 'required'
      ];

    $validator = Validator::make(Purifier::clean($request->all()),$rules);

    if($validator->fails())
    {
      return Response::json(["error"=>"Please fill out the comment section."]);
    }
    $user = Auth::user();//$user is being formatted here, so it can be referenced below

    $check = Article::find($request->input('articleID'));//verifies that the article actually exists that the comment is being posted to.
      if(empty($check))
      {
        return Response::json(['error'=>"This is not the article you are looking for."]);
      }

    $comment = new Comment;//references the storeComment in the Single index.js
    $comment->userID = $user->id;//references the ''$user' above.requesting the user id from the frontend
    $comment->articleID = $request->input('articleID');
    $comment->body = $request->body('body');
    $comment->save();//make sure to include save at the end

    return Response::json(['success'=> 'Your comment has been added.']);
  }

  public function destroy($id)
  {
    $comment = Comment::find($id);
    $comment->delete();

    return Response::json(['success'=>'Comment Successfully Deleted.']);
  }
}
