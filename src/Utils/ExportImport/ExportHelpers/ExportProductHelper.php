<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 12:27
 */

namespace App\Utils\ExportImport\ExportHelpers;

use App\Entity\ProductCategory;
use App\Utils\ExportImport\AbstractExportEntityHelper;

class ExportProductHelper extends AbstractExportEntityHelper
{
    protected $headers = [ 'name', 'description', 'creationDate', 'modificationDate', 'category'];

    protected function convert(&$value)
    {
        parent::convert($value);
        if ($value instanceof ProductCategory) {
            $value = $value->getName();
        }
    }
}
