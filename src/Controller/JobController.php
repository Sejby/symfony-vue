<?php

namespace App\Controller;

use App\Service\JobService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{

    /**
     * @param JobService $jobService
     */
    public function __construct(private readonly JobService $jobService)
    {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: 'jobs')]
    public function index(Request $request): Response
    {
        $limit = 10;
        $page = max(1, $request->query->getInt('page', 1));

        $jobs_data = $this->jobService->fetchJobs($page, $limit);
        $jobs = $jobs_data['jobs'];

        $total_jobs = $jobs_data['total_jobs'];

        $total_pages = (int)ceil($total_jobs / $limit);

        return $this->render('jobs/jobs.html.twig', [
            'controller_name' => 'JobController',
            'jobs' => $jobs,
            'total_jobs' => $total_jobs,
            'current_page' => $page,
            'total_pages' => $total_pages,
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/job/{id}', name: 'job_detail', requirements: ['id' => '\d+'])]
    public function jobDetail(int $id, Request $request): Response
    {
        $job = $this->jobService->fetchJobDetail($id);

        $page = $request->query->getInt('page', 1);

        return $this->render('jobs/job_detail.html.twig', [
            'job' => $job,
            'page' => $page,
        ]);
    }
}
