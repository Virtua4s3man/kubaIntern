<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 12:27
 */

namespace App\Utils\ExportHelpers;

use App\Entity\ProductCategory;
use App\Utils\ExportEntityHelper;

class ExportProductHelper extends ExportEntityHelper
{
    protected function convert(&$value)
    {
        parent::convert($value);
        if ($value instanceof ProductCategory) {
            $value = $value->getName();
        }
    }
}
