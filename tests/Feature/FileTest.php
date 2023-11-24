<?php

namespace Tests\Feature;

use App\Models\File;
use Database\Seeders\FileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class FileTest extends TestCase
{
    public function testStoreSuccess()
    {
        $image = UploadedFile::fake()->image('test.png');
        $filename = $image->getClientOriginalName();
        $mime_type = $image->getMimeType();

        $body = [
            'file' => $image
        ];

        $this->post('/api/files', $body)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'filename' => $filename,
                    'mime_type' => $mime_type,
                    'path' => 'files/' . $filename
                ]
            ]);
    }

    public function testStoreFailed()
    {
        $body = [
            'file' => ''
        ];

        $this->post('/api/files', $body)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'file' => [
                        'The file field is required.'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $this->get('/api/files/' . $file->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'filename' => $file->filename,
                    'mime_type' => $file->mime_type,
                    'path' => $file->path
                ]
            ]);
    }

    public function testGetFailed()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $this->get('/api/files/' . $file->id + 1)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['File not found']
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $image = UploadedFile::fake()->image('test.png');
        $filename = $image->getClientOriginalName();
        $mime_type = $image->getMimeType();

        $body = [
            'file' => $image
        ];

        $this->put('/api/files/' . $file->id, $body)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'filename' => $filename,
                    'mime_type' => $mime_type,
                    'path' => 'files/' . $filename
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $image = UploadedFile::fake()->image('test.png');
        $filename = $image->getClientOriginalName();
        $mime_type = $image->getMimeType();

        $body = [
            'file' => ''
        ];

        $this->put('/api/files/' . $file->id, $body)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'file' => ['The file field is required.']
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $this->delete('/api/files/' . $file->id)
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function testDeleteFailed()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $this->delete('/api/files/' . $file->id + 1)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['File not found']
                ],
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([FileSeeder::class]);

        $file = File::query()->limit(1)->first();

        $this->get('/api/files')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'filename' => $file->filename,
                        'mime_type' => $file->mime_type,
                        'path' => $file->path
                    ]
                ]
            ]);
    }

    public function testDeleteFile()
    {
        // Storage::disk('public')->delete('files/eXCYy46P1pMErHVN4QGnNdWRJ5REfmqccUwdj8fy.png');
        unlink(storage_path('app/public/files/MuDkcAqi4PXVerdNorUbjc9sut3xZxxHIx8IjnUS.png'));
        self::assertTrue(true);
    }
}
