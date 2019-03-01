<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 12:27
 */

namespace App\Utils\ExportImport\ExportHelpers;

use App\Utils\ExportImport\AbstractExportEntityHelper;

class ExportCategoryHelper extends AbstractExportEntityHelper
{
    protected $headers = ['name', 'description' , 'creationDate', 'modificationDate'];
}
