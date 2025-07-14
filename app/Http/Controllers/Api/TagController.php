<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tags = $request->user()->tags()->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if tag already exists for this user
        $existingTag = $request->user()->tags()
            ->where('name', $request->name)
            ->first();

        if ($existingTag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag already exists',
            ], 409);
        }

        $tag = $request->user()->tags()->create([
            'name' => $request->name,
            'color' => $request->color ?? '#3b82f6',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tag created successfully',
            'data' => $tag
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $tag = $request->user()->tags()->with('notes')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $tag = $request->user()->tags()->findOrFail($id);

        // Check if name is being changed and if it conflicts with existing tag
        if ($request->has('name') && $request->name !== $tag->name) {
            $existingTag = $request->user()->tags()
                ->where('name', $request->name)
                ->where('id', '!=', $id)
                ->first();

            if ($existingTag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag name already exists',
                ], 409);
            }
        }

        $tag->update($request->only(['name', 'color']));

        return response()->json([
            'success' => true,
            'message' => 'Tag updated successfully',
            'data' => $tag
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $tag = $request->user()->tags()->findOrFail($id);
        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully'
        ]);
    }
}
