<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AdminReplyMailable;
use App\Models\ContactMessage;
use App\Models\ContactReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // add gates/roles as needed
    }

    // GET /admin/contacts
    public function index(Request $request)
    {
        $filters = [
            'q'      => trim($request->input('q', '')),
            'status' => trim($request->input('status', '')),
            'star'   => (bool) $request->input('star', false),
        ];

        $messages = ContactMessage::query()
            ->filter($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('panel.pages.contacts.index', compact('messages','filters'));
    }

    // GET /admin/contacts/{id}
    public function show(ContactMessage $contact)
    {
        $contact->load('replies.admin');
        return view('panel.pages.contacts.show', compact('contact'));
    }

    // POST /admin/contacts/{id}/toggle-star
    public function toggleStar(ContactMessage $contact)
    {
        $contact->is_starred = !$contact->is_starred;
        $contact->save();
        return back()->with('status', 'Updated star');
    }

    // POST /admin/contacts/{id}/status
    public function setStatus(Request $request, ContactMessage $contact)
    {
        $request->validate(['status' => 'required|in:open,replied,closed']);
        $contact->status = $request->status;
        $contact->save();
        return back()->with('status', 'Status updated');
    }

    // POST /admin/contacts/{id}/reply
    public function reply(Request $request, ContactMessage $contact)
    {
        $data = $request->validate([
            'subject'   => ['required','string','max:190'],
            'body_html' => ['required','string','max:10000'],
        ]);

        // Save reply
        $reply = ContactReply::create([
            'contact_message_id' => $contact->id,
            'admin_id' => optional($request->user())->id,
            'to_email' => $contact->email,
            'subject'  => $data['subject'],
            'body'     => $data['body_html'],
        ]);

        // Send mail
        $payload = [
            'name'            => $contact->name,
            'subject'         => $data['subject'],
            'body_html'       => $data['body_html'],
            'reply_to_email'  => optional($request->user())->email ?? config('mail.from.address'),
            'reply_to_name'   => optional($request->user())->name ?? config('mail.from.name'),
            'greeting'        => 'Hello ' . ($contact->name ?? ''),
        ];

        // throttle or queue in real-world
        Mail::to($contact->email)->send(new AdminReplyMailable($payload));

        // update counters
        $contact->increment('reply_count');
        if (is_null($contact->first_replied_at)) {
            $contact->first_replied_at = now();
        }
        $contact->status = 'replied';
        $contact->save();

        return back()->with('status', 'Reply sent to ' . $contact->email);
    }

    // DELETE /admin/contacts/{id}
    public function destroy(ContactMessage $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('status', 'Conversation moved to trash.');
    }

    // POST /admin/contacts/{id}/restore
    public function restore($id)
    {
        $contact = ContactMessage::withTrashed()->findOrFail($id);
        $contact->restore();
        return back()->with('status', 'Conversation restored.');
    }
}

