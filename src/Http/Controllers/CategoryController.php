<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Seshra\Core\Repositories\CategoryRepository;
use Seshra\Core\Repositories\ProductRepository;

/**
 * Class CategoryController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CategoryController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * CategoryController constructor.
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ){
        $this->_routes = request('_routes');
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->orderByTranslation('name', 'asc');
        if ($request->has('search')){
            $categories = $categories->whereTranslationLike('name', $request->search.'%');
        }
        $categories = $categories->paginate(15);
        return view($this->_routes['view'], compact('categories'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function create()
    {
        $categories = $this->categoryRepository->getAllWithChildren();
        return view($this->_routes['view'], compact('categories'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_category' => 'required',
            'type' => 'required|in:0,1'
        ]);
        $this->categoryRepository->create($request->only('name','parent_category','type','banner','icon','meta_title','meta_description'));

        flash(translate('Category has been added successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function edit(Request $request, $id)
    {
        $lang = lang($request->lang);
        $category =$this->categoryRepository->findOrFail($id);
        $parents = $category->parents()->pluck('id')->toArray();
        $categories = $this->categoryRepository->getAllWithChildren();
        return view($this->_routes['view'], compact('category', 'categories','parents', 'lang'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function update(Request $request, $id)
    {
        $locale = $request->locale ?? app()->getLocale();
        $request->validate([
            $locale.'.name' => 'required',
            'parent_category' => 'required',
            'type' => 'required|in:0,1',
            'slug' => 'required'
        ]);
        $this->categoryRepository->modify($request->only($locale,'slug','parent_category','type','banner','icon','meta_title','meta_description'), $id);

        flash(translate('Category has been updated successfully'))->success();
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findOrFail($id);
        if($category->children->count() > 0) {
            flash(translate('You cannot delete root category without deleting its children.'))->warning();
        }
        else {
            // $this->productRepository->deleteWhere(['category_id' => $category->id]);
            $this->categoryRepository->delete($id);
            flash(translate('Category has been deleted successfully'))->success();
        }

        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @return int
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateFeatured(Request $request)
    {
        $category = $this->categoryRepository->findOrFail($request->id);
        $category->featured = $request->status;
        if($category->save()){
            return 1;
        }
        return 0;
    }
}
