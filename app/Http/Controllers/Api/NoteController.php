<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->notes()->with('tags');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($search) {
                      $tagQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by archived status
        if ($request->has('archived')) {
            if ($request->archived) {
                $query->archived();
            } else {
                $query->active();
            }
        } else {
            $query->active(); // Default to active notes
        }

        // Filter by pinned status
        if ($request->has('pinned') && $request->pinned) {
            $query->pinned();
        }

        // Filter by tag
        if ($request->has('tag_id') && !empty($request->tag_id)) {
            $query->whereHas('tags', function ($tagQuery) use ($request) {
                $tagQuery->where('id', $request->tag_id);
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $notes = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|string|max:7',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $note = $request->user()->notes()->create([
            'title' => $request->title,
            'content' => $request->content,
            'color' => $request->color ?? '#ffffff',
            'last_edited_at' => now(),
        ]);

        // Attach tags if provided
        if ($request->has('tag_ids')) {
            $note->tags()->attach($request->tag_ids);
        }

        $note->load('tags');

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully',
            'data' => $note
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $note = $request->user()->notes()->with('tags')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $note
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'color' => 'nullable|string|max:7',
            'is_archived' => 'sometimes|boolean',
            'is_pinned' => 'sometimes|boolean',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $note = $request->user()->notes()->findOrFail($id);
        
        $updateData = $request->only(['title', 'content', 'color', 'is_archived', 'is_pinned']);
        $updateData['last_edited_at'] = now();
        
        $note->update($updateData);

        // Sync tags if provided
        if ($request->has('tag_ids')) {
            $note->tags()->sync($request->tag_ids);
        }

        $note->load('tags');

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
            'data' => $note
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $note = $request->user()->notes()->findOrFail($id);
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully'
        ]);
    }

    /**
     * Toggle archive status of a note.
     */
    public function toggleArchive(Request $request, $id)
    {
        $note = $request->user()->notes()->findOrFail($id);
        $note->update([
            'is_archived' => !$note->is_archived,
            'last_edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $note->is_archived ? 'Note archived' : 'Note unarchived',
            'data' => $note
        ]);
    }

    /**
     * Toggle pin status of a note.
     */
    public function togglePin(Request $request, $id)
    {
        $note = $request->user()->notes()->findOrFail($id);
        $note->update([
            'is_pinned' => !$note->is_pinned,
            'last_edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $note->is_pinned ? 'Note pinned' : 'Note unpinned',
            'data' => $note
        ]);
    }
}
