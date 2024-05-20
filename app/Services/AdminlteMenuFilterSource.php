<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class AdminlteMenuFilterSource implements FilterInterface
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Transforms a menu item. Add the restricted property to a menu item
     * when situable.
     *
     * @param  array  $item  A menu item
     * @return array The transformed menu item
     */
    public function transform($item): array
    {
        if (! $this->isAllowed($item)) {
            $item['restricted'] = true;
        }
        return $item;
    }

    /**
     * Check if a menu item is allowed for the current user.
     *
     * @param array $item  A menu item
     * @return bool
     */
    protected function isAllowed(array $item): bool
    {
        if (empty($item['can'])) {
            return true;
        }
        $resources = session('user_codes');
        if (empty($resources)) {
            return false;
        }
//        $args = isset($item['model']) ? $item['model'] : [];
        if (is_array($item['can'])) {
            foreach ($item['can'] as $can) {
                if (in_array($can, $resources)) {
                    return true;
                }
            }
            return false;
        }
        if (is_string($item['can']) && in_array($item['can'], $resources)) {
            return true;
        }
        return false;
    }
}
