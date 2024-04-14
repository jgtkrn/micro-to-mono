<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     *  @OA\Tag(name="Community") 
     */

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/communities",
     *     tags={"Community"},
     *     summary="List of community",
     *     operationId="communityList",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Page size (default 10)",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by (default created_at). Option: id, name, url, created_at, updated_at",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (default desc). Option: asc, desc",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", description="Id of community", example="1"),
     *                     @OA\Property(property="name", type="string", description="Name of community", example="Google Maps"),
     *                     @OA\Property(property="url", type="string", description="Url of community", example="https://www.google.com/maps")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'integer',
            'sort_by' => [Rule::in(['id', 'name', 'url', 'created_at', 'updated_at'])],
            'sort_dir' => [Rule::in(['asc, desc'])]
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

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/communities",
     *     tags={"Community"},
     *     summary="Store new community",
     *     operationId="communityStore",
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required community information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"name, url"},
     *                 @OA\Property(property="name", type="string", example="Google Maps", description="Community Name"),
     *                 @OA\Property(property="url", type="string", example="https://www.google.com/maps", description="Url of community")
     *             )
     *     )
     * )
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required'
        ]);
        $community = Community::create($request->only('name', 'url'));
        return new CommunityResource($community);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/communities/{id}",
     *     tags={"Community"},
     *     summary="Community details by Id",
     *     operationId="communityDetails",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Community Id to be viewed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Id of community", example="1"),
     *                 @OA\Property(property="name", type="string", description="Name of community", example="Google Maps"),
     *                 @OA\Property(property="url", type="string", description="Url of community", example="https://www.google.com/maps")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Community not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $community = Community::where('id', $id)->first();

        if (!$community) {
            return $this->failure('Community not found', 404);
        }

        return new CommunityResource($community);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/communities/{id}",
     *     tags={"Community"},
     *     summary="Update community by Id",
     *     operationId="communityUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Community Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required community information (in string)",
     *         required=true,
     *              @OA\JsonContent(
     *                 required={"name, url"},
     *                 @OA\Property(property="name", type="string", example="Google Maps", description="Community Name"),
     *                 @OA\Property(property="url", type="string", example="https://www.google.com/maps", description="Url of community")
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Community not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required'
        ]);

        $community = Community::where('id', $id)->first();

        if (!$community) {
            return $this->failure('Community not found', 404);
        }

        $updated = $community->update([
            'name' => $request->name,
            'url' => $request->url
        ]);

        if (!$updated) {
            return $this->failure('Failed to update community', 500);
        }

        return new CommunityResource($community);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/communities/{id}",
     *     tags={"Community"},
     *     summary="Delete community by Id",
     *     operationId="communityDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Community Id to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Community not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $community = Community::where('id', $id)->first();

        if (!$community) {
            return $this->failure('Community not found', 404);
        }

        $community->delete();

        return response(null, 204);
    }
}
