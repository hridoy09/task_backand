<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\Controlling;

class SupportTicketController extends Controller
{
    use Controlling;

    protected $model = SupportTicket::class;
    protected $listView = 'admin.support_tickets.list';
    protected $formView = 'admin.support_tickets.form';
    protected ?string $viewPermission = 'view-support-tickets';
    protected ?string $createPermission = 'save-support-tickets';
    protected ?string $updatePermission = 'save-support-tickets';

    public function __construct()
    {
        $departments = SupportDepartment::active()->get();
        viewShare('admin.support_tickets.form', compact('departments'));
    }

    /**
     * Generic list view
     */
    public function list()
    {
        return $this->data(__('Support Tickets'));
    }

    /**
     * Custom queries for list views
     */
    protected function listQuery($query)
    {
        return $query
            ->with(['department', 'user'])
            ->searching(['title'])
            ->latest();
    }

    public function byDepartment($id)
    {
        return $this->data(__('Support Tickets'), function($query) use ($id) {
            $query->where('department_id', $id);
        });
    }

    public function open()
    {
        return $this->data(__('Open Tickets'), 'open');
    }

    public function answered()
    {
        return $this->data(__('Answered Tickets'), 'answered');
    }

    public function closed()
    {
        return $this->data(__('Closed Tickets'), 'closed');
    }

    public function create()
    {
        return $this->dataCreate(__('Create Ticket'));
    }

    public function edit($id)
    {
        return $this->dataEdit(__('Ticket Details'), $id);
    }

    /**
     * Save or update a ticket
     */
    public function save(Request $request, $id = null)
    {
        $rules = [
            'department_id' => 'required|exists:support_departments,id',
            'title'         => 'required|string|max:255|unique:support_tickets,title,' . $id,
            'body'          => 'required|string',
            'status'        => 'required|in:0,1,2', // 2 = answered
        ];

        $ticket = $this->dataSave($id, $rules);

        return back()->withSuccess($id ? __('Ticket updated successfully') : __('Ticket created successfully'));
    }

    /**
     * Hooks to handle custom logic before/after save
     */
    protected function beforeDataSave($request, $model, $id)
    {
        $model->department_id = $request->department_id;
        $model->title         = $request->title;
        $model->slug          = Str::slug($request->title);
        $model->body          = $request->body;
        $model->status        = $request->status;
        $model->meta          = $request->meta ?? [];
        $model->added_by      = function_exists('admin') && admin() ? admin()->id : null;
    }

    protected function afterDataSave($request, $model, $id)
    {
        // Optional: handle attachments, replies, or other post-save actions here
    }

    /**
     * Ticket-specific actions
     */
    public function status($id)
    {
        goIfUserCan('save-support-tickets');

        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = $ticket->status ? 0 : 1;
        $ticket->save();

        return back()->withSuccess(__('Ticket status changed'));
    }

    public function delete($id)
    {
        goIfUserCan('delete-support-tickets');

        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return back()->withSuccess(__('Ticket deleted'));
    }

    public function reply(Request $request, $id)
    {
        goIfUserCan('save-support-tickets');

        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (! $file) {
                    continue;
                }

                $storedPath = upload_support_attachment($file);
                if ($storedPath) {
                    $attachments[] = $storedPath;
                }
            }
        }

        DB::transaction(function () use ($ticket, $request, $attachments) {
            $reply = new SupportTicketReply();
            $reply->ticket_id  = $ticket->id;
            $reply->is_admin   = true;
            $reply->admin_id   = function_exists('admin') && admin() ? admin()->id : null;
            $reply->message    = $request->message;
            $reply->attachments = $attachments ?: null;
            $reply->save();

            $ticket->status          = 2; // answered
            $ticket->last_replied_by = $reply->admin_id;
            $ticket->last_replied_at = now();
            $ticket->save();

            // mail the user
            $replyAuthor = admin()?->name ?? config('app.name') . ' Support';

            sendTemplatedNotification(
                $ticket->user->email,
                'SUPPORT_TICKET_REPLY',
                [
                    'user_name'     => $ticket->user->name,
                    'reply_author'  => $replyAuthor,
                    'reply_message' => $request->message,
                    'ticket_id'     => $ticket->id,
                    'ticket_url'    => route('user.support.list'),
                ]
            );

            sendTemplatedNotification(
                $ticket->user->email,
                'SUPPORT_TICKET_STATUS_CHANGED',
                [
                    'user_name' => $ticket->user->name,
                    'ticket_id' => $ticket->id,
                    'ticket_status' => __('Answered'),
                    'ticket_url' => route('user.support.list'),
                ]
            );
        });

        return back()->withSuccess(__('Reply added successfully'));
    }

    public function close($id)
    {
        goIfUserCan('save-support-tickets');

        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = 0;
        $ticket->save();

        if ($ticket->user?->email) {
            sendTemplatedNotification(
                $ticket->user->email,
                'SUPPORT_TICKET_STATUS_CHANGED',
                [
                    'user_name' => $ticket->user->name,
                    'ticket_id' => $ticket->id,
                    'ticket_status' => __('Closed'),
                    'ticket_url' => route('user.support.list'),
                ]
            );
        }

        return back()->withSuccess(__('Ticket closed'));
    }

    public function reopen($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = 1;
        $ticket->save();

        if ($ticket->user?->email) {
            sendTemplatedNotification(
                $ticket->user->email,
                'SUPPORT_TICKET_STATUS_CHANGED',
                [
                    'user_name' => $ticket->user->name,
                    'ticket_id' => $ticket->id,
                    'ticket_status' => __('Reopened'),
                    'ticket_url' => route('user.support.list'),
                ]
            );
        }

        return back()->withSuccess(__('Ticket reopened'));
    }

    public function setPriority(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'priority' => ['required', Rule::in([0,1,2,3])],
        ]);

        $ticket->priority = (int) $request->priority;
        $ticket->save();

        return back()->withSuccess(__('Priority updated'));
    }
}
