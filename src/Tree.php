<?php

namespace Seshra\Core;

use Illuminate\Support\Facades\Request;

/**
 * Class Tree
 * @package Seshra\Core
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Tree {

    /**
     * Contains tree item
     *
     * @var array
     */
	public $items = [];

    /**
     * Contains acl roles
     *
     * @var array
     */
	public $roles = [];

    /**
     * Contains current item route
     *
     * @var string
     */
	public $current;

    /**
     * Contains current item key
     *
     * @var string
     */
	public $currentKey;

    /**
     * Create a new instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->current = Request::url();
	}

	/**
	 * Shortcut method for create a Config with a callback.
	 * This will allow you to do things like fire an event on creation.
	 *
	 * @param  callable  $callback Callback to use after the Config creation
	 * @return object
	 */
	public static function create($callback = null)
	{
		$tree = new Tree();

		if ($callback) {
			$callback($tree);
		}

		return $tree;
	}

	/**
	 * Add a Config item to the item stack
	 *
	 * @param  string  $item
	 * @return void
	 */
	public function add($item, $type = '')
	{
        $item['children'] = [];

		if ($type == 'menu') {
            $item['url'] = route($item['route'], $item['params'] ?? []);

			if (strpos($this->current, $item['url']) !== false) {
                $this->currentKey = $item['key'];
			}
		} elseif ($type == 'acl') {
			$item['name'] = translate($item['name']);

			$this->roles[$item['route']] = $item['key'];
		}

		$children = str_replace('.', '.children.', $item['key']);

		core()->array_set($this->items, $children, $item);
	}

	/**
	 * Method to find the active links
	 *
	 * @param  array  $item
	 * @return string|void
	 */
	public function getActive($item)
	{
		$url = trim($item['url'], '/');

		if ((strpos($this->current, $url) !== false) || (strpos($this->currentKey, $item['key']) === 0)) {
			return 'active';
		}
	}
}
