<?php

namespace App\Http\Controllers;

use App\Actions\Uploads;
use App\Events\NewMessage;
use App\Http\Resources\Msg as MessageResource;
use App\Models\Launchpad;
use App\Models\Msg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MsgsController extends Controller
{
    /**
     * Get messages for a specific launchpad
     */
    public function index(Request $request, Launchpad $launchpad)
    {
        $messages = $launchpad->msgs()
            ->with('user.nfts')
            ->latest()
            ->paginate(50);
        return MessageResource::collection($messages);
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'launchpad_id' => 'required|exists:launchpads,id',
            'message' => 'required_without:image_path|string|max:300',
            'image_path' => 'nullable',
            'image_upload' => 'required|boolean',
            'image_uri' => ['nullable', 'required_if:image_upload,true', 'string'],
        ]);
        $msg = new Msg();
        $msg->user_id = $request->user()->id;
        $msg->launchpad_id = $validated['launchpad_id'];
        $msg->message = $validated['message'] ?? null;
        $msg->save();
        // Handle image upload if present
        if ($request->image_upload) {
            $upload = app(Uploads::class)->upload($request, $msg, 'image');
            $msg->image = $upload->url;
            $msg->save();
        }
        // Broadcast the new message
        $msg->load('user');
        NewMessage::dispatch($msg);
        return back();
    }

    /**
     * Update message status (for moderation)
     */
    public function updateStatus(Request $request, Msg $msg)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,hidden,blocked,review'
        ]);
        $msg->update(['status' => $validated['status']]);
        return new MessageResource($msg);
    }

    /**
     * Delete a message
     */
    public function destroy(Msg $msg)
    {
        Gate::authorize('delete', $msg);
        $msg->delete();
        return back();
    }
}
