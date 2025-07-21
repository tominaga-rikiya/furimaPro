<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;


class ItemController extends Controller
{
    public function index(Request $request)
    {
      
        $search = $request->input('search');

       
        $items = Item::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->when(auth()->check(), function ($query) {
           
            return $query->where('user_id', '!=', auth()->id());
        })->get();
       
     
        $favorites = auth()->check() ? auth()->user()->favorites : collect();

   
        return view('item.index', compact('items', 'favorites','search'));
    }

    public function store(Request $request)
    {
      
        $path = $request->file('image')->store('images', 'public');

     
        $item = Item::create([
            'name' => $request->name,
            'image' => $path,
            'user_id' => Auth::id(),
            'category_ids' => $request->category_ids, 
        ]);

        return redirect()->route('item.index');
    }
    public function purchaseItem($id)
    {
     
        $item = Item::findOrFail($id);
        $item->is_sold = true;
        $item->save();
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }


        return redirect()->route('item.index')->with('success', '商品を購入しました！');
    }

    public function show($id)
    {
    
        $item = Item::findOrFail($id);

      
        $categories = $item->category_ids; 

        $isFavorite = auth()->check() && $item->favorites()->where('user_id', auth()->id())->exists();
        $comments = $item->comments()->with('user')->latest()->get();

        return view('item.show', compact('item', 'isFavorite', 'comments', 'categories'));
        
    }

    public function favorite($item_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        $item = Item::findOrFail($item_id);

    
        if (!auth()->user()->favorites()->where('item_id', $item->id)->exists()) {
            $item->favorites()->create(['user_id' => auth()->id()]);
        }
       
        return redirect()->route('item.index', ['tab' => 'mylist'])->with('success', 'いいねしました');
    }

    public function toggleFavorite($itemId)
{
    $item = Item::findOrFail($itemId);
    $user = auth()->user();


    if ($user->favorites()->where('item_id', $item->id)->exists()) {
       
        $user->favorites()->where('item_id', $item->id)->delete();
    } else {
   
        $user->favorites()->create(['item_id' => $item->id]);
    }

    return redirect()->route('item.show', $item->id);
}

    public function unfavorite($item_id)
    {
        $item = Item::findOrFail($item_id);
        $item->favorites()->where('user_id', auth()->id())->delete();

        return back()->with('success', 'いいねを解除しました');
    }

    public function comment(CommentRequest $request, $item_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        $comment = new Comment();
        $comment->item_id = $item_id;
        $comment->user_id = auth()->id();
        $comment->comment = $request->comment;
        $comment->save();

        return redirect()->route('item.show', $item_id);
    }
}

