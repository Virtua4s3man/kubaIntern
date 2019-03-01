<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 12:27
 */

namespace App\Utils\ExportHelpers;

use App\Utils\ExportEntityHelper;

class ExportCategoryHelper extends ExportEntityHelper
{
    protected $headers = ['id', 'name', 'description' , 'creationDate', 'modificationDate'];
}
