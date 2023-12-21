<?php

namespace App\Http\Controllers\Api_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My First API", version="0.1")
 */

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $category = category::where('name_category', 'like', '%' . trim($request->keyword) . '%')->paginate($request->pagesize);
        return response()->json([
            'type' => 'success',
            'data' =>  $category,
        ]);
    }

    public function store(Request $request)
    {
        $category = category::create([
            'name_category' => $request->input('name_category'),
        ]);
        return response()->json([
            'message' => 'Them danh muc thanh cong',
            'type' => 'success',
            'data' =>  $category,
        ]);
    }

    /**
     * Get Category
     * @OA\Get(
     *      path="/api/category/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=false,
     *          description="The event ID specific to this event",
     *          @OA\Schema(
     *              type="int"
     *          ),
     *     ),
     *      tags={"Category"},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     ),
     * )
     */
    public function show(category $category)
    {
        return $category;
    }

    public function update(Request $request)
    {
        $category = category::find($request->id);
        if(!category::where('id', $request->id)->first()) {
            return response()->json([
                'message' => 'loi',
                'type' => 'error',
            ]);
        }
        $category->name_category = $request->name_category;
        $category->save();
        $category = category::get();
        return response()->json([
            'message' => 'Sua danh muc thanh cong',
            'type' => 'success',
            'data' =>  $category,
        ]);
    }

    /**
     * Delete Category
     * @OA\Delete(
     *      path="/api/category/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=false,
     *          description="The event ID specific to this event",
     *          @OA\Schema(
     *              type="int"
     *          ),
     *     ),
     *      tags={"Category"},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     ),
     * )
     */
    public function destroy($id)
    {
        $category = category::find($id);
        $category->deleted = 1;
        $rs = $category->save();
        if ($rs) {
            return "200";
        } else {
            return "500";
        }
    }

    public function visible($id)
    {
        $category = category::find($id);
        $visible = 0;
        if ($category->visible == 0) {
            $visible = 1;
        }
        $category->visible = $visible;
        $rs = $category->save();
        if ($rs) {
            return "200";
        } else {
            return "500";
        }
    }
}
