<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use App\Traits\FileSaver;
use Illuminate\Support\Facades\Log;

class CourseLessonController extends Controller
{
    use FileSaver;

    public function index(Course $course, CourseChapter $chapter, Request $request)
    {
        abort_unless($chapter->course_id === $course->id, 404);

        if ($request->wantsJson()) {
            $lessons = CourseLesson::where('course_id', $course->id)
                ->where('chapter_id', $chapter->id)
                ->orderBy('sort_order')->orderBy('id')->get();

            return response()->json([
                'data' => $lessons->map(function($l){
                    return [
                        'id' => $l->id,
                        'title' => $l->title,
                        'type' => $l->type,
                        'video_provider' => $l->video_provider,
                        'video_url' => $l->video_url,
                        'video_file_url' => $l->video_file_url,
                        'file_url' => $l->file_url,
                        'mime_type' => $l->mime_type,
                        'duration' => $l->duration_seconds,
                        'sort_order' => $l->sort_order,
                        'toils'  => $l->toils,
                        'rounds' => $l->rounds,
                        'notes'   => $l->notes,
                        'others'  => $l->others,
                        'status' => $l->status ? 'Active' : 'Inactive',
                        'status_bool' => (bool)$l->status,
                        'created_at' => $l->created_at->format('Y-m-d H:i'),
                        // Optional backfill to edit modal:
                        'content_text' => $l->content_text,
                    ];
                })
            ]);
        }
        abort(404);
    }

    public function store(Course $course, CourseChapter $chapter, Request $request)
    {
        abort_unless($chapter->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'type'  => ['required','in:video,file,text'],
            'video_provider' => ['nullable','in:youtube,vimeo,local'],
            'video_url' => ['nullable','url'],
            'video_file' => ['nullable','file','max:512000','mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska,video/x-msvideo'],
            'content_text' => ['nullable','string'],
            'duration_seconds' => ['nullable','integer','min:0'],
            'sort_order' => ['nullable','integer','min:1'],
            'status' => ['required','boolean'],
            'toils'  => ['nullable','integer','min:0'],
            'rounds' => ['nullable','string','max:255'],
            'notes'  => ['nullable','string'],
            'others' => ['nullable','string','max:255'],
            'file' => ['nullable','file','max:20480','mimes:pdf,jpg,jpeg,png,webp,gif,zip'],
        ]);

        if ($data['type']==='video' && !$request->hasFile('video_file') && empty($data['video_url'])) {
            return response()->json(['message' => 'Provide a video URL or upload a video file.'], 422);
        }

        $lesson = CourseLesson::create([
            'course_id' => $course->id,
            'chapter_id' => $chapter->id,
            'title' => $data['title'],
            'type' => $data['type'],
            'video_provider' => $data['type']==='video' ? ($data['video_provider'] ?? null) : null,
            'video_url'      => $data['type']==='video' ? ($data['video_url'] ?? null) : null,
            'content_text'   => $data['type']==='text'  ? ($data['content_text'] ?? null) : null,
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'sort_order' => $data['sort_order'] ?? 1,
            'status' => $data['status'],
            'toils'  => $data['toils'] ?? null,
            'rounds' => $data['rounds'] ?? null,
            'notes'  => $data['notes']  ?? null, 
            'others' => $data['others'] ?? null,
        ]);

        try {
            // Uploaded video
            if ($lesson->type==='video' && $request->hasFile('video_file')) {
                $vf = $request->file('video_file');
                if (!$vf->isValid()) {
                    return response()->json(['message' => 'Video upload invalid.'], 422);
                }
                $videoMime = $vf->getClientMimeType();
                $videoSize = $vf->getSize();

                $this->upload_file($vf, $lesson, 'video_file_path', 'lessons/videos');

                $lesson->update([
                    'video_provider' => 'local',
                    'video_url'      => null,
                    'video_mime'     => $videoMime,
                    'video_size'     => $videoSize,
                ]);
            }

            // Attachment for "file" lessons
            if ($lesson->type==='file' && $request->hasFile('file')) {
                $ff = $request->file('file');
                if (!$ff->isValid()) {
                    return response()->json(['message' => 'File upload invalid.'], 422);
                }
                $fileMime = $ff->getClientMimeType();
                $fileSize = $ff->getSize();

                $this->upload_file($ff, $lesson, 'file_path', 'lessons/files');

                $lesson->update([
                    'mime_type' => $fileMime,
                    'file_size' => $fileSize,
                ]);
            }
        } catch (\Throwable $e) {
            if ($lesson->video_file_path) @unlink(public_path($lesson->video_file_path));
            if ($lesson->file_path) @unlink(public_path($lesson->file_path));
            $lesson->delete();
            Log::error('Lesson upload failed: '.$e->getMessage());
            return response()->json(['message' => 'Upload failed: '.$e->getMessage()], 422);
        }

        return response()->json(['message' => 'Lesson created']);
    }

    public function update(Course $course, CourseChapter $chapter, CourseLesson $lesson, Request $request)
    {
        abort_unless($chapter->course_id === $course->id && $lesson->chapter_id === $chapter->id, 404);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'type'  => ['required','in:video,file,text'],
            'video_provider' => ['nullable','in:youtube,vimeo,local'],
            'video_url' => ['nullable','url'],
            'video_file' => ['nullable','file','max:512000','mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska,video/x-msvideo'],
            'content_text' => ['nullable','string'],
            'duration_seconds' => ['nullable','integer','min:0'],
            'sort_order' => ['nullable','integer','min:1'],
            'status' => ['required','boolean'],
            'toils'  => ['nullable','integer','min:0'],
            'rounds' => ['nullable','string','max:255'],
            'notes'  => ['nullable','string'],
            'others' => ['nullable','string','max:255'],
            'file' => ['nullable','file','max:20480','mimes:pdf,jpg,jpeg,png,webp,gif,zip'],
        ]);

        if ($data['type']==='video' && !$request->hasFile('video_file') && empty($data['video_url']) && !$lesson->video_file_path) {
            return response()->json(['message' => 'Provide a video URL or upload a video file.'], 422);
        }

        $lesson->update([
            'title' => $data['title'],
            'type'  => $data['type'],
            'video_provider' => $data['type']==='video' ? ($data['video_provider'] ?? $lesson->video_provider) : null,
            'video_url'      => $data['type']==='video' ? ($data['video_url'] ?? null) : null,
            'content_text'   => $data['type']==='text'  ? ($data['content_text'] ?? null) : null,
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'sort_order' => $data['sort_order'] ?? $lesson->sort_order,
            'status' => $data['status'],
            'toils'  => $data['toils'] ?? $lesson->toils,
            'rounds' => $data['rounds'] ?? $lesson->rounds,
            'notes'  => array_key_exists('notes',$data)  ? $data['notes']  : $lesson->notes,   
            'others' => array_key_exists('others',$data) ? $data['others'] : $lesson->others,  
        ]);

        try {
            // Video
            if ($lesson->type==='video' && $request->hasFile('video_file')) {
                $vf = $request->file('video_file');
                if (!$vf->isValid()) {
                    return response()->json(['message' => 'Video upload invalid.'], 422);
                }
                $videoMime = $vf->getClientMimeType();
                $videoSize = $vf->getSize();

                $this->upload_file($vf, $lesson, 'video_file_path', 'lessons/videos');

                $lesson->update([
                    'video_provider' => 'local',
                    'video_url'      => null,
                    'video_mime'     => $videoMime,
                    'video_size'     => $videoSize,
                ]);
            }

            // File
            if ($lesson->type==='file' && $request->hasFile('file')) {
                $ff = $request->file('file');
                if (!$ff->isValid()) {
                    return response()->json(['message' => 'File upload invalid.'], 422);
                }
                $fileMime = $ff->getClientMimeType();
                $fileSize = $ff->getSize();

                $this->upload_file($ff, $lesson, 'file_path', 'lessons/files');

                $lesson->update([
                    'mime_type' => $fileMime,
                    'file_size' => $fileSize,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lesson upload failed: '.$e->getMessage());
            return response()->json(['message' => 'Upload failed: '.$e->getMessage()], 422);
        }

        return response()->json(['message' => 'Lesson updated']);
    }

    public function destroy(Course $course, CourseChapter $chapter, CourseLesson $lesson)
    {
        abort_unless($chapter->course_id === $course->id && $lesson->chapter_id === $chapter->id, 404);

        if ($lesson->video_file_path) {
            $abs = public_path($lesson->video_file_path);
            if (is_file($abs)) @unlink($abs);
        }
        if ($lesson->file_path) {
            $abs = public_path($lesson->file_path);
            if (is_file($abs)) @unlink($abs);
        }
        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted']);
    }
}
