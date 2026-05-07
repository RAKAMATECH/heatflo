<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LeadCreated;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                    $mailable = new LeadCreated($lead);
                    $relayUrl = config('services.mail_relay.url');
                    $relaySecret = config('services.mail_relay.secret');

                    if ($relayUrl && $relaySecret) {
                        $response = Http::withToken($relaySecret)
                            ->timeout((int) config('services.mail_relay.timeout', 5))
                            ->acceptJson()
                            ->asJson()
                            ->post($relayUrl, [
                                'to' => $notifyTo,
                                'subject' => $mailable->envelope()->subject,
                                'html' => $mailable->render(),
                            ]);

                        if (! $response->successful()) {
                            throw new \RuntimeException('relay returned HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 200));
                        }
                    } else {
                        Mail::to($notifyTo)->send($mailable);
                    }
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
