<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCapacitor;
use App\Http\Resources\UserResource;
/**
 * @OA\Tag(name="UserCapacitor")
 */
class UserCapacitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/user-capacitor/{id}",
     *     operationId="v1GetToken",
     *     tags={"UserCapacitor"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the user",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          required=false,
     *          name="user_token",
     *          description="token user capacitor, leave this empty if the token exist.",
     *          @OA\Schema(
     *              type="string",
     *              example="exampleuser"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Token data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserCapacitor")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Token not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {id}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $user_id = $id;
        $role = 'employee';
        $user = User::where('id', $id)->first();
        $user_token = $request->query('user_token');
        if($user){
            $role = $user->access_roles != null ? $user->access_roles->name : null;
            if($role == 'admin'){
                $role = 'hr';
            } else if($role == 'manager'){
                $role = 'manager';
            } else {
                $role = 'employee';
            }
        }
        $recent_token = UserCapacitor::where('user_id', $user_id)->first();
        if(!$recent_token){
            UserCapacitor::updateOrCreate(
                ['user_id' => $user_id],
                ['user_token' => $user_token]
            );
            $new_token = UserCapacitor::where('user_id', $user_id)->first();
            return response()->json([
                'data' => [
                    'company_token' => env('CAPACITOR_TOKEN'),
                    'user_token' => $new_token['user_token'],
                    'user_role' => $role
                ]
            ], 200);
        }
        return response()->json([
            'data' => [
                'company_token' => env('CAPACITOR_TOKEN'),
                'user_token' => $recent_token['user_token'],
                'user_role' => $role
            ]
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/user-api/v1/user-capacitor/{id}",
     *     operationId="UpdateToken",
     *     tags={"UserCapacitor"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of user",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          required=false,
     *          name="user_token",
     *          description="token user capacitor",
     *          @OA\Schema(
     *              type="string",
     *              example="exampleuser"
     *          )
     *     ),
     *     
     *     @OA\Response(
     *          response=200,
     *          description="token updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserCapacitor")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Token not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="404",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {id}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     
     *     @OA\Response(
     *          response=500,
     *          description="Failed",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create Token"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
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
        $user_id = $id;
        $user_token = $request->query('user_token');
        if(!$user_token || $user_token == null){
            return;
        }
        UserCapacitor::updateOrCreate(
            ['user_id' => $user_id],
            ['user_token' => $user_token]
        );
        return;
    }

    /**
     * @OA\Delete(
     *     path="/user-api/v1/user-capacitor/{id}",
     *     operationId="v1DeleteToken",
     *     tags={"UserCapacitor"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of user",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Token deleted",
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Token not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {id}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $token = UserCapacitor::where('user_id', $id)->delete();
        if(!$token){
            return response()->json(['data' => null], 204);
        }
        return response()->json(['data' => null], 204);
    }
}
