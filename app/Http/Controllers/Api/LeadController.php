<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LeadCreated;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'service' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);

        $lead = Lead::create($data);

        $notifyTo = env('LEAD_NOTIFY_TO');
        if (is_string($notifyTo) && $notifyTo !== '') {
            $leadId = $lead->id;
            dispatch(function () use ($notifyTo, $lead, $leadId) {
                try {
                    Mail::to($notifyTo)->send(new LeadCreated($lead));
                } catch (\Throwable $e) {
                    Log::warning('Lead notification email failed', [
                        'lead_id' => $leadId,
                        'error' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
        }

        return response()->json([
            'ok' => true,
            'id' => $lead->id,
        ], 201);
    }
}
