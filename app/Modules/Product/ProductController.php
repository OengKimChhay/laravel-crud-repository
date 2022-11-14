<?php

namespace App\Modules\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Generator;
use App\Helper\FileUpload\FileUploadService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected array $allowFileTypes = ['jpg', 'png'];

    public function __construct(
        protected ProductService $productService,
        protected FileUploadService $fileUploadService
    ){}


    /**
     * Display a listing of the resource.
     * @return ProductResource
     */
    public function index(Request $request)
    {
        $products = $this->productService->paginate($request->all());
        return new ProductResource($products);
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return ProductResource
     */
    public function store(ProductRequest $request)
    {
        $image = $this->fileUploadService->createAndUploadImage($request->image,'resize', $this->allowFileTypes);
        $data = [
            'name' => $request->name,
            'code' => Generator::productCodeGenerator('XT'),
            'image' => $image['path']
        ];
        $product = $this->productService->createOne($data);
        return new ProductResource($product);
    }


    /**
     * Display the specified resource.
     * @param  int  $id
     * @return ProductResource
     */
    public function show($id)
    {
        $product = $this->productService->getOneOrFail($id);
        return new ProductResource($product);
    }


    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ProductResource
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            // delete old image
            $product = $this->productService->getOneOrFail($id);
            $this->fileUploadService->delete($product->image);

            $newImage = $this->fileUploadService->uploadFile($request->image, $this->allowFileTypes);
            $data = [
                'name' => $request->name,
                'code' => Generator::productCodeGenerator('XT'),
                'image' => $newImage['path']
            ];

            $updateProduct = $this->productService->updateOne($product, $data);
            return new ProductResource($updateProduct);
        });
    }

    /**
     * get the specified resource from trash.
     * @param string|int $id
     */
    public function trash(string|int $id)
    {
        $product = $this->productService->getOneOnlyTrash($id);
        return new ProductResource($product);
    }


    /**
     * get many data from trash.
     * @param \Illuminate\Http\Request  $request
     * @return ProductResource
     */
    public function trashMany(Request $request)
    {
        $product = $this->productService->paginateOnlyTrash($request->all());
        return new ProductResource($product);
    }


    /**
     * Remove item.
     * @param  int  $id
     * @return ProductResource
     */
    public function destroy($id)
    {
        $product = $this->productService->deleteOne($id);
        return new ProductResource($product);
    }


    /**
     * Remove many data.
     * @return bool
     */
    public function destroyMany(Request $request)
    {
        // collect all ids
        $products = $this->productService->getManyById($request->items ?? []);
        $ids = $products->pluck('id')->toArray();
        return $this->productService->deleteMany($ids);
    }


    /**
     * force delete the specified item.
     * @param  string|int  $id
     * @return bool
     */
    public function forceDestroy($id)
    {
        // delete old image
        $product = $this->productService->getOneOrFail($id);
        $this->fileUploadService->delete($product->image);

        return $this->productService->forceDeleteOne($id);
    }


    /**
     * force remove many data.
     * @return bool
     */
    public function forceDestroyMany(Request $request)
    {
        // delete all old image
        $products = $this->productService->getManyById($request->items ?? []);
        $paths = $products->pluck('image')->toArray();
        $this->fileUploadService->deleteMultiFile($paths);

        // collect all ids
        $ids = $products->pluck('id')->toArray();
        return $this->productService->forceDeleteMany($ids);
    }


    /**
     * restore one data from trash.
     * @param string|int $id
     * @return bool
     */
    public function restoreOne(string|int $id)
    {
        return $this->productService->restoreOne($id);
    }


    /**
     * restore many data from trash.
     * @param array $ids
     * @return bool
     */
    public function restoreMany(Request $request)
    {
        return $this->productService->restoreMany($request->items ?? []);
    }
}
