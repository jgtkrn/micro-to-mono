<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CommunityIndexRequest;
use App\Http\Requests\v2\Assessments\CommunityStoreRequest;
use App\Http\Resources\v2\Assessments\CommunityResource;
use App\Models\v2\Assessments\Community;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    use RespondsWithHttpStatus;

    public function index(CommunityIndexRequest $request)
    {
        $request->validate([
            'per_page' => 'integer',
            'sort_by' => [Rule::in(['id', 'name', 'url', 'created_at', 'updated_at'])],
            'sort_dir' => [Rule::in(['asc, desc'])],
        ]);

        $per_page = $request->query('per_page', 10);
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $communities = Community::query();

        if ($search) {
            $communities->where('name', 'like', '%' . $search . '%')
                ->orWhere('url', 'like', '%' . $search . '%');
        }

        return CommunityResource::collection($communities
            ->orderBy($sortBy, $sortDir)
            ->paginate($per_page)
            ->appends($request->except(['page'])));
    }

    public function store(CommunityStoreRequest $request)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required',
        ]);
        $community = Community::create($request->only('name', 'url'));

        return new CommunityResource($community);
    }

    public function show($id)
    {
        $community = Community::where('id', $id)->first();

        if (! $community) {
            return $this->failure('Community not found', 404);
        }

        return new CommunityResource($community);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required',
        ]);

        $community = Community::where('id', $id)->first();

        if (! $community) {
            return $this->failure('Community not found', 404);
        }

        $updated = $community->update([
            'name' => $request->name,
            'url' => $request->url,
        ]);

        if (! $updated) {
            return $this->failure('Failed to update community', 500);
        }

        return new CommunityResource($community);
    }

    public function destroy($id)
    {
        $community = Community::where('id', $id)->first();

        if (! $community) {
            return $this->failure('Community not found', 404);
        }

        $community->delete();

        return response(null, 204);
    }
}
