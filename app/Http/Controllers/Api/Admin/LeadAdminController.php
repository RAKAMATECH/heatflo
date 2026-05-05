<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadAdminController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 200);
        if ($limit <= 0) {
            $limit = 200;
        }
        $limit = min($limit, 500);

        $leads = Lead::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get([
                'id',
                'created_at',
                'name',
                'email',
                'phone',
                'service',
                'location',
                'subject',
                'message',
                'source',
            ]);

        return response()->json($leads);
    }
}
