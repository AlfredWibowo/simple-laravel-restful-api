<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUpdateRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private function getFile(string $id)
    {
        $file = File::where('id', $id)->first();

        if (!$file) {
            $response = response()->json([
                'success' => false,
                'errors' => [
                    'message' => ['File not found']
                ]
            ], 404);

            throw new HttpResponseException($response);
        }

        return $file;
    }

    public function store(StoreFileRequest $request)
    {
        $data = $request->validated();

        $file = $data['file'];
        $filename = $file->getClientOriginalName();
        $mime_type = $file->getMimeType();
        $path = Storage::disk('public')->putFile('files', $file);

        $file = new File();

        $file->filename = $filename;
        $file->mime_type = $mime_type;
        $file->path = $path;

        $file->save();

        return response()->json([
            'success' => true,
            'data' => new FileResource($file)
        ], 201);
    }

    public function get(Request $request, int $id)
    {
        $file = $this->getFile($id);

        return response()->json([
            'success' => true,
            'data' => new FileResource($file)
        ], 200);
    }

    public function update(UpdateFileRequest $request, int $id)
    {
        $data =  $request->validated();

        $file = $this->getFile($id);
        Storage::disk('public')->delete($file->path);

        $newFile = $data['file'];
        $filename = $newFile->getClientOriginalName();
        $mime_type = $newFile->getMimeType();
        $path = Storage::disk('public')->putFile('files', $newFile);

        $file->filename = $filename;
        $file->mime_type = $mime_type;
        $file->path = $path;

        $file->save();

        return response()->json([
            'success' => true,
            'data' => new FileResource($file)
        ], 200);
    }

    public function destroy(Request $request, int $id)
    {
        $file = $this->getFile($id);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function list(Request $request)
    {
        $files = File::all();

        return response()->json([
            'success' => true,
            'data' => FileResource::collection($files)
        ], 200);
    }

    public function download(Request $request, int $id)
    {
        $file = $this->getFile($id);

        return Storage::disk('public')->download($file->path, $file->filename);
    }
}
