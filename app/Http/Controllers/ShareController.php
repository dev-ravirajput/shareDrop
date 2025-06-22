<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ShareController extends Controller
{
    public function createShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:file,text',
            'text' => 'required_if:type,text|max:5000',
            'password' => 'nullable|string|min:3',
            'expires' => 'required|in:1,24,168,720,0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $share = new Share();
        $share->share_id = Str::random(8);
        $share->type = $data['type'];
        $share->creator_ip = $request->ip();

        if ($data['type'] === 'text') {
            $share->content = $data['text'];
        }

        if (!empty($data['password'])) {
            $share->password = $data['password'];
        }

        if ($data['expires'] != 0) {
            $share->expires_at = now()->addHours((int) $data['expires']);
        }


        $share->save();

        return response()->json([
            'share_id' => $share->share_id,
            'url' => route('share.view', $share->share_id)
        ]);
    }

    public function viewShare($shareId)
    {
        $share = Share::where('share_id', $shareId)->firstOrFail();

        if ($share->isExpired()) {
            return view('share.expired');
        }

        if ($share->requiresPassword() && !session()->has('share_'.$shareId.'_authenticated')) {
            return view('share.password', compact('share'));
        }

        return view('share.view', compact('share'));
    }

    // app/Http/Controllers/ShareController.php
    public function checkPassword(Request $request, $shareId)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $share = Share::where('share_id', $shareId)->firstOrFail();
        
        if ($share->checkPassword($request->password)) {
            session(['share_'.$shareId.'_authenticated' => true]);
            return redirect()->route('share.view', $shareId);
        }

        return back()->with('error', 'Incorrect password');
    }
}