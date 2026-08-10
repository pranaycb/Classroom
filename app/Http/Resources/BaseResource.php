<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseResource extends ResourceCollection
{

    /**
     * Customize the pagination information for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array $paginated
     * @param  array $default
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
        foreach ($default['meta']['links'] as $i => $link) {

            if ($link['label'] === 'Next &raquo;') {

                $default['meta']['links'][$i]['label'] = '&raquo;';
            }

            if ($link['label'] === '&laquo; Previous') {

                $default['meta']['links'][$i]['label'] = '&laquo;';
            }
        }

        return $default;
    }
}
