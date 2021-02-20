<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\ReviewRepository;

/**
 * Class ReviewController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ReviewController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var ReviewRepository
     */
    protected $reviewRepository;

    /**
     * ReviewController constructor.
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(
        ReviewRepository $reviewRepository
    ){
        $this->_routes = request('_routes');
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index()
    {
        $reviews = $this->reviewRepository->orderBy('id', 'DESC')->paginate(10);

        return view($this->_routes['view'], compact('reviews'));
    }

}