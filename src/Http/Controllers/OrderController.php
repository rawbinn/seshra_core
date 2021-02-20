<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\OrderRepository;

/**
 * Class OrderController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class OrderController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ){
        $this->_routes = request('_routes');
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index()
    {
        $orders = $this->orderRepository->orderBy('id', 'DESC')->paginate(10);

        return view($this->_routes['view'], compact('orders'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function show($id)
    {
        $order = $this->orderRepository->orderBy('id', 'DESC')->findOrFail(decrypt($id)) ;

        return view($this->_routes['view'], compact('order'));
    }

    public function download($id)
    {
        
    }

}