<?php declare(strict_types=1);

namespace App\Service;

use App\Mappers\JobMapper;
use Exception;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class JobService
{
    /**
     * @param string $token
     * @param HttpClientInterface $client
     * @param JobMapper $jobMapper
     * @param CacheInterface $cache
     */
    public function __construct(private string $token, private HttpClientInterface $client, private JobMapper $jobMapper, private CacheInterface $cache)
    {
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetchJobs(int $page, int $limit): array
    {
        $cache_key = sprintf('jobs_page_%d_limit_%d', $page, $limit);

        try {
            return $this->cache->get($cache_key, function (ItemInterface $item) use ($page, $limit) {
                $item->expiresAfter(3600);

                $response = $this->client->request(
                    'GET',
                    'https://app.recruitis.io/api2/jobs',
                    [
                        'query' => [
                            'page' => $page,
                            'limit' => $limit,
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->token,
                        ],
                    ]
                );

                $data = $response->toArray();

                if ($data['meta']['code'] == "api.response.null")
                    return ['jobs' => [], 'total_jobs' => 0];

                $jobs = array_map([$this->jobMapper, 'map'], $data['payload']);

                $total_jobs = $data['meta']['entries_total'];

                return ['jobs' => $jobs, 'total_jobs' => $total_jobs];
            });

        } catch (Exception $e) {
            throw new Exception('Chyba při fetchování dat: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public
    function fetchJobDetail(int $id): ?array
    {
        $cacheKey = sprintf('job_detail_%d', $id);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);

            try {
                $response = $this->client->request(
                    'GET',
                    sprintf('https://app.recruitis.io/api2/jobs/%d', $id),
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->token,
                        ],
                    ]
                );

                $data = $response->toArray();

                if ($data['meta']['code'] === 'api.response.null') {
                    return null;
                }

                return $this->jobMapper->map($data['payload']);

            } catch (Exception $e) {
                throw new Exception('Chyba při fetchování dat: ' . $e->getMessage(), $e->getCode());
            }
        });
    }
}
