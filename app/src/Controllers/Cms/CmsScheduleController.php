<?php 

namespace App\Controllers\Cms;


use App\Controllers\BaseController;
use App\Service\ScheduleService;

class CmsScheduleController extends BaseController
{
    private ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/schedule/index', ['title' => 'Schedule Management']);
    }
}
?>