<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function list()
    {
        $title = __('My Tickets');
        $tickets = SupportTicket::where('user_id', auth()->id())->paginate();
        return theme('user.support_tickets.list', compact('title', 'tickets'));
    }

    public function openTicket()
    {
        $title = __('Open New Ticket');

        $departments = SupportDepartment::active()->get();

        return theme('user.support_tickets.form', compact('title', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:support_departments,id',
            'priority'      => 'required|integer|in:0,1,2,3',
            'title'         => 'required|string|max:255',
            'body'          => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB
        ]);

        $ticket                = new SupportTicket();
        $ticket->user_id       = auth()->id();
        $ticket->department_id = $request->department_id;
        $ticket->priority      = $request->priority;
        $ticket->title         = $request->title;
        $ticket->body          = $request->body;
        $ticket->status        = 1;                        // open
        $ticket->save();

        // handle attachments
        if ($request->hasFile('attachments')) {
            $paths = [];
            foreach ($request->file('attachments') as $file) {
                if (! $file) {
                    continue;
                }

                $storedPath = upload_support_attachment($file);
                if ($storedPath) {
                    $paths[] = $storedPath;
                }
            }

            if (! empty($paths)) {
                $ticket->attachments = $paths;
                $ticket->save();
            }
        }

        sendTemplatedNotification(
            auth()->user()->email,
            'SUPPORT_TICKET_CREATED',
            [
                'ticket_id' => $ticket->id,
                'user_name' => auth()->user()->name,
                'ticket_subject' => $ticket->title,
                'ticket_priority' => ticket_priority_label((int) $ticket->priority),
                'ticket_url' => route('user.support.list'),
            ]
        );

        return redirect()->route('user.support.list')->with('success', __('Your ticket has been created successfully!'));
    }

}
