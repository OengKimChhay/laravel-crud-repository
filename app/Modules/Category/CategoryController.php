<?php

namespace App\Modules\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Category\Request\{CategoryRequest, CategoryUpdateRequest, CategoryLoginRequest};
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{

    public function __construct(protected CategoryService $categoryService)
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return CategoryResource
     */
    public function index(Request $request)
    {
        $category = $this->categoryService->paginate($request->all());
        return CategoryResource::collection($category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return CategoryResource
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->createOne($request->all());
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return CategoryResource
     */
    public function show($id)
    {
        $category = $this->categoryService->getOneOrFail($id);
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return CategoryResource
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = $this->categoryService->getOneOrFail($id);
        $categoryUpdated = $this->categoryService->updateOne($category, $request->all());
        return new CategoryResource($categoryUpdated);
    }
}
