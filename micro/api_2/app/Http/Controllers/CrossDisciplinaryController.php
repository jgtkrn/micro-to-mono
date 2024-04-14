<?php

namespace App\Http\Controllers;

use App\Models\CrossDisciplinary;
use App\Models\CarePlan;
use App\Http\Resources\CrossDisciplinaryResource;
use Illuminate\Http\Request;
use App\Traits\RespondsWithHttpStatus;

class CrossDisciplinaryController extends Controller
{
    use RespondsWithHttpStatus;
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/cross-disciplinary",
     *     tags={"Cross Disciplinary"},
     *     summary="List of Cross Disciplinary", 
     *     operationId="getCrossDisciplinary",
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="case_id",
     *          description="case id",
     *          example=1
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          description="Page number",
     *          example=1
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          description="Page size (default 10)",
     *          example=10
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="Sort By (default: created_at), available options: id, created_at, updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="Sort Directions (default: asc), available options: asc, desc",
     *          example="asc"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",ref="#/components/schemas/CrossDisciplinary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if(
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([
            'case_id' => ['required', 'integer']
        ]);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);
        $crossDisciplinaries = CrossDisciplinary::where('case_id', $request->case_id)->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
        if(count($crossDisciplinaries) == 0) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with case_id $request->case_id",
                    'success' => false,                
                ],
            ], 404);
        }
        return CrossDisciplinaryResource::collection($crossDisciplinaries);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/cross-disciplinary",
     *     tags={"Cross Disciplinary"},
     *     operationId="createCrossDisciplinary",  
     *     summary="Create Cross Disciplinary",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id"},
     *                  @OA\Property(property="case_id", type="integer", example=1),
     *                  @OA\Property(property="role", type="string", example="yes" ),
     *                  @OA\Property(property="comments", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Cross Disciplinary created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/CrossDisciplinary")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Cross Disciplinary validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Cross Disciplinary",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update CrossDisciplinary")
     *              )
     *          )
     *     )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(
            
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $request->validate([
            'case_id' => ['required', 'integer'],
            'role' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'date' => ['nullable', 'date_format:Y-m-d']
        ]);
        $crossDisciplinary = CrossDisciplinary::create([
            'case_id' => $request->case_id,
            'role' => $request->role,
            'comments' => $request->comments,
            'name' => $request->name,
            'date' => $request->date
        ]);
        return new CrossDisciplinaryResource($crossDisciplinary);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/cross-disciplinary/{id}",
     *     operationId="getCrossDisciplinaryDetail",
     *     summary="Get Cross Disciplinary by id",    
     *     tags={"Cross Disciplinary"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the Cross Disciplinary",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Cross Disciplinary detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/CrossDisciplinary")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Cross Disciplinary not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Cross Disciplinary with id {id}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        if(
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if(!$currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id $id",
                    'success' => false,                
                ],
            ], 404);
        }
        return new CrossDisciplinaryResource($currentCrossDisciplinary);
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/cross-disciplinary/{id}",
     *     tags={"Cross Disciplinary"},
     *     summary="Update Cross Disciplinary by id",
     *     operationId="updateCrossDisciplinary",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update Cross Disciplinary by id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Cross Disciplinary information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property(property="case_id", type="integer", example=1),
     *                  @OA\Property(property="role", type="string", example="yes" ),
     *                  @OA\Property(property="comments", type="string", example="yes")
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Cross Disciplinary updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/CrossDisciplinary")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Cross Disciplinary not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Cross Disciplinary with id {id}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Cross Disciplinary validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Cross Disciplinary",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Cross Disciplinary")
     *              )
     *          )
     *     )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if(   
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $user = $request->user_id;
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if(!$currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id $id",
                    'success' => false,                
                ],
            ], 404);
        }
        $care_plan = CarePlan::where('case_id', $currentCrossDisciplinary->case_id)->first();
        if(!$care_plan){
            return $this->failure('Care plan does not exists.', 404);
        }
        if($care_plan->manager_id !== $user){
            return $this->failure('You are not the author.', 401);   
        }
        $request->validate([
            'case_id' => ['nullable', 'integer'],
            'role' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'date' => ['nullable', 'date_format:Y-m-d']
        ]);

        $crossDisciplinary = CrossDisciplinary::where('id', $id)->update([
            'case_id' => $request->case_id ? $request->case_id : $currentCrossDisciplinary->case_id,
            'role' => $request->role ? $request->role  : $currentCrossDisciplinary->role,
            'comments' => $request->comments ? $request->comments  : $currentCrossDisciplinary->comments,
            'name' => $request->name ? $request->name  : $currentCrossDisciplinary->name,
            'date' => $request->date ? $request->date  : $currentCrossDisciplinary->date
        ]);
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        return new CrossDisciplinaryResource($currentCrossDisciplinary);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/cross-disciplinary/{id}",
     *     tags={"Cross Disciplinary"},
     *     summary="Delete Cross Disciplinary by id",
     *     operationId="deleteCrossDisciplinary",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delete Cross Disciplinary by id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cross Disciplinary deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Cross Disciplinary not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Cross Disciplinary with id {id}")
     *              )
     *          )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        if(
            
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if(!$currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id $id",
                    'success' => false,                
                ],
            ], 404);
        }
        $currentCrossDisciplinary->delete();
        return response()->json([
            'data' => null,
            'message' => "Cross Disciplinary with id $id deleted successfully",
            'success' => true,
        ], 201);
    }
}
