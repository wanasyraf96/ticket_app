<?php

namespace App\Http\Controllers;

use App\Events\TicketUpdateEvent;
use App\Models\Lookup;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\HelpersTrait;
use App\Traits\LookupTrait;
use App\Traits\UserRolesTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use LookupTrait, UserRolesTrait, HelpersTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ?/&|s=(priority=desc,created_at=desc)

        $queryOrderParams = [];
        $queryFilterParams = [];
        $availableOrderColumn = ['priority', 'status', 'updated_at', 'title'];
        $availableFilterColumn = ['priority', 'status'];
        $table = 'ticket_';
        $search = null;
        $searchId = null;


        if (request()->has('sorting')) {
            $orderParams = $this->extractQueryParams(request('sorting'));
            $encodedQueryOrderParams = $this->checkQueryNames($orderParams, $availableOrderColumn, 'sorting');
            $queryOrderParams = array_map(function ($sort) {
                $query = urldecode($sort);
                if ($query === 'true') return 'asc';
                if ($query === 'false') return 'desc';
            }, $encodedQueryOrderParams);
            $queryOrderParams;
        }

        if (request()->has('filter')) {
            $filterParams = $this->extractQueryParams(request('filter'));
            $encodedFilterParams = $this->checkQueryNames($filterParams, $availableFilterColumn, 'filtering');
            // decode
            $queryFilterParams = array_map(fn ($query) => base64_decode($query), ($encodedFilterParams));
        }

        if (request()->has('search') && gettype(request('search')) === 'string') {
            $search = '%' . urldecode(trim(request('search'))) . '%';
            $searchId = ltrim(request('search'), '#');
        }

        $tickets = Ticket::select(['id', 'title', 'description', 'created_at', 'updated_at', 'priority', 'status', 'creator', 'assignee'])->with([
            'user:id,name',
            'assignee:id,name'
        ])
            ->when(auth()->id(), fn ($query) => $query->where('creator', auth()->id()))
            ->when(
                $search,
                fn ($query) => $query
                    ->where('title', 'like', $search)
                    ->orWhere('id', $searchId)
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like',  $search);
                    })
            )

            // Filtering
            ->when(count($queryFilterParams) > 0, function ($query) use ($queryFilterParams, $table) {
                foreach ($queryFilterParams as $key => $value) {
                    $query->where($key, $value);
                }
            })
            // query order: order based on entries columns
            ->when(
                count($queryOrderParams) > 0,
                function ($query) use ($queryOrderParams) {
                    foreach ($queryOrderParams as $key => $value) {
                        if ($value === 'asc' || $value === 'desc')
                            $query->orderBy($key, $value);
                    }
                },
                fn ($query) => $query->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'asc')
            )
            // ->toSql();
            // return $tickets;
            ->paginate(request('per_page') ?? 10);

        $tickets->getCollection()->map(function ($ticket) {
            $ticket->created_at = Carbon::parse($ticket->created_at->format('Y-m-d H:i:s'))->diffForHumans();
            $ticket->updated_at = Carbon::parse($ticket->updated_at->format('Y-m-d H:i:s'))->diffForHumans();
            $ticket->priority = $this->getPriority($ticket->priority);
            $ticket->status = $this->getStatus($ticket->status);
            $ticket->link = "/ticket/" . str_pad($ticket->id, 7, 0, STR_PAD_LEFT);
            $ticket->human_readable_created_at = Carbon::parse($ticket->created_at)->diffForHumans();
            $ticket->human_readable_updated_at = Carbon::parse($ticket->updated_at)->diffForHumans();
        });
        return $tickets;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'string|sometimes',
            'description' => 'string|nullable',
        ], ['title.string' => 'title cannot be empty']);

        $ticket = new Ticket([
            'title' => $request->title,
            'description' => $request->description
        ]);
        $ticket = auth()->user()->tickets()->save($ticket);
        return $ticket;
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->created_at = Carbon::parse($ticket->created_at)->format('Y-m-d H:i:s');
        $ticket->updated_at = Carbon::parse($ticket->updated_at)->format('Y-m-d H:i:s');
        $ticket->priority = $this->getPriority($ticket->priority);
        $ticket->status = $this->getStatus($ticket->status);
        return $ticket->load(['user:id,name', 'assignee:id,name', 'assignor:id,name']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        if (auth()->id() !== $ticket->creator) return response(['error' => 'You do not have permission for this'], 403);
        $request->validate([
            'title' => 'string|sometimes',
            'description' => 'string|nullable',
        ], ['title.string' => 'title cannot be empty']);

        $ticket->update($request->all());
        $ticket->save();
        TicketUpdateEvent::dispatch($ticket);
        return response("updated", 202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->creator) return response(['error' => 'You do not have permission for this'], 403);

        $ticket->delete();
        return response()->noContent();
    }

    /**
     * Assign the tickets to a staff
     *
     * @param Ticket $ticket
     * @param User @user
     * @return \Http\Response
     */
    public function assignTicket(Ticket $ticket, User $user)
    {
        if (!$this->isStaff($user->id)) {
            return response(["error" => "only assignable to staff."], 422);
        }
        $ticket->assignor = auth()->id();
        $ticket->assignee = $user->id;
        $ticket->save();

        // dispatch event
        return $ticket;
    }


    /**
     * Update the ticket status
     *
     * @param Illuminate\Http\Request $request
     * @param Ticket $ticket
     * @return \Http\Response
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $lookup = $this->lookupElement('ticket_status');
        $priorityIdsString = implode(',', array_map(fn ($item) => trim($item['id']), $lookup));
        $request->validate([
            'status' => 'required|in:' . $priorityIdsString
        ]);
        $ticket->status = $request->status;
        $ticket->save();

        TicketUpdateEvent::dispatch($ticket);
        return response()->noContent();
    }

    /**
     * Update the ticket priority
     *
     * @param Illuminate\Http\Request $request
     * @param Ticket $ticket
     * @return \Http\Response
     */
    public function updatePriority(Request $request, Ticket $ticket)
    {

        $lookup = $this->lookupElement('ticket_priority');
        $priorityIdsString = implode(',', array_map(fn ($item) => trim($item['id']), $lookup));
        $request->validate([
            'priority' => 'required|in:' . $priorityIdsString
        ]);
        $ticket->priority = $request->priority;
        $ticket->save();

        TicketUpdateEvent::dispatch($ticket);
        return response()->noContent();
    }
}
