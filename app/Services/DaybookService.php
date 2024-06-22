<?php
namespace App\Services;


use App\Repositories\DaybookRepository;

class DaybookService
{
    protected DaybookRepository $daybookRepository;
    public function __construct(DaybookRepository $daybookRepository)
    {
        $this->daybookRepository = $daybookRepository;
    }

    public function initProcess($data)
    {
        
    }
}
