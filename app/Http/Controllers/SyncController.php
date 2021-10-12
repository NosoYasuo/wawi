<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Channel;
use App\Watch;
use App\Room;
use App\Comment;
use Validator;

class SyncController extends Controller
{
    // ダッシュボード表示(index.blade.php)
    public function index()
    {
        $channels = Channel::orderBy('created_at', 'asc')->get();
        $watches = Watch::orderBy('created_at', 'asc')->get();

        return view('index', ['channels' => $channels, 'watches' => $watches]);
    }

    //chat表示(chat.blade.php)
    public function room($id)
    {
        $user1 = Auth::id();
        $user2 = $id;
        if ($user1 <= $user2) {
            $room = Room::firstOrCreate(['user1' => $user1, 'user2' => $user2]);
        }else{
            $room = Room::firstOrCreate(['user1' => $user2, 'user2' => $user1]);
        }

        return view('chat', ['room' => $room]);
    }

    //watchをstore
    public function store_watch(Request $request)
    {
        //バリデーション
        $validator = Validator::make($request->all(), [
            'watch' => 'required|string|min:11|max:11|unique:watches',
        ]);

        //バリデーション:エラー
        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }

        // Eloquentモデル
        $watches = new Watch;
        $watches->watch = $request->watch;
        $watches->user_id = Auth::id();
        $watches->save();
        return redirect('/');
    }

    //channelをstore
    public function store_channel(Request $request)
    {
        //バリデーション
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string|min:24|max:24|starts_with:UC|unique:channels',
        ]);

        //バリデーション:エラー
        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }

        // Eloquentモデル
        $channels = new Channel;
        $channels->channel = $request->channel;
        $channels->user_id = Auth::id();
        $channels->save();
        return redirect('/');
    }
}
