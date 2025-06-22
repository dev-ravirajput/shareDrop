<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\ShareFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function uploadFiles(Request $request, $shareId)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240' // 10MB max
        ]);

        $share = Share::where('share_id', $shareId)->firstOrFail();

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('shares/'.$shareId, 'public');

            $uploadedFiles[] = $share->files()->create([
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
        }

        return response()->json($uploadedFiles);
    }

    public function downloadFile($shareId, $fileId)
    {
        $share = Share::where('share_id', $shareId)->firstOrFail();
        
        if ($share->isExpired()) {
            abort(404);
        }

        if ($share->requiresPassword() && !session()->has('share_'.$shareId.'_authenticated')) {
            abort(403);
        }

        $file = $share->files()->findOrFail($fileId);

        return Storage::disk('public')->download($file->storage_path, $file->original_name);
    }
}