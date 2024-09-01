<?php declare(strict_types=1);

namespace App\Mappers;

class JobMapper
{
    /**
     * @param array $data
     * @return array
     */
    public function map(array $data): array
    {
        return [
            'id' => $data['job_id'] ?? '',
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'date_created' => $data['date_created'] ?? '',
        ];
    }
}
