<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CreateReferralRequest;
use App\Http\Requests\v2\Elders\ReferralIndexRequest;
use App\Http\Requests\v2\Elders\UpdateReferralRequest;
use App\Http\Resources\v2\Elders\ReferralResource;
use App\Models\v2\Elders\Referral;

class ReferralController extends Controller
{
    public function index(ReferralIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['label', 'code', 'created_at', 'updated_at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $referrals = Referral::orderBy($sortBy, $sortDir)->paginate($perPage);

        return ReferralResource::collection($referrals);
    }

    public function store(CreateReferralRequest $request)
    {
        $referral = Referral::create($request->validated());

        return new ReferralResource($referral);
    }

    public function show($referralId)
    {
        $referral = Referral::find($referralId);
        if (! $referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$referralId}",
                    'errors' => [],
                ],
            ], 404);
        }

        return new ReferralResource($referral);
    }

    public function update(UpdateReferralRequest $request, $referralId)
    {
        $referral = Referral::find($referralId);
        if (! $referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$referralId}",
                    'errors' => [],
                ],
            ], 404);
        }

        $referral->label = $request->has('label') ? $request->label : $referral->label;
        $referral->code = $request->has('code') ? $request->code : $referral->code;
        $referral->bzn_code = $request->has('bzn_code') ? $request->bzn_code : $referral->bzn_code;
        $referral->cga_code = $request->has('cga_code') ? $request->cga_code : $referral->cga_code;
        $referral->save();

        return new ReferralResource($referral);
    }

    public function destroy($referralId)
    {
        $referral = Referral::find($referralId);
        if (! $referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$referralId}",
                    'errors' => [],
                ],
            ], 404);
        }

        $referral->delete();

        return response()->json(null, 204);
    }
}
