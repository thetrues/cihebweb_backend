<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class Dhis2Service
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected MetadataService $metadataService;

    public function __construct(MetadataService $metadataService)
    {
        $this->baseUrl = config('dhis2.base_url');
        $this->username = config('dhis2.username');
        $this->password = config('dhis2.password');
        $this->metadataService = $metadataService;
    }

    /**
     * Get the HTTP client with basic auth
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Get tracked entities with metadata mapping
     */
    public function getTrackedEntities(): array
    {
        $query = [
            'orgUnit' => 'LA93mGekSOL',
            'orgUnitMode' => 'SELECTED',
            'trackedEntityType' => 'H9a4SMFpH3N',
            'page' => 1,
            'pageSize' => 400,
            'filter' => 'q2q4NURHygI:eq:OPEN',
            'fields' => 'createdAt,orgUnit,trackedEntity,updatedAt,enrollments[storedBy,enrolledAt,enrollment,events[event,programStage,occurredAt,status,dataValues[dataElement,value]]],attributes[storedBy,value,attribute,code]'
        ];

        $response = $this->client()
            ->get($this->baseUrl . '/api/tracker/trackedEntities.json', $query);

        if ($response->successful()) {
            $data = $response->json();
            
            // Return mapped data with metadata
            return [
                'raw' => $data,
                'mapped' => $this->metadataService->mapTrackedEntities($data['instances'] ?? []),
                'pager' => $data['pager'] ?? [],
            ];
        }

        return [
            'error' => true,
            'message' => $response->body(),
            'status' => $response->status(),
        ];
    }

    /**
     * Get a single tracked entity with metadata mapping
     */
    public function getTrackedEntity(string $trackedEntityId): array
    {
        $query = [
            'trackedEntity' => $trackedEntityId,
            'fields' => 'createdAt,orgUnit,trackedEntity,updatedAt,enrollments[storedBy,enrolledAt,enrollment,events[event,programStage,occurredAt,status,dataValues[dataElement,value]]],attributes[storedBy,value,attribute,code]'
        ];

        $response = $this->client()
            ->get($this->baseUrl . '/api/tracker/trackedEntities.json', $query);

        if ($response->successful()) {
            $data = $response->json();
            
            // Get the first instance (should be only one)
            $trackedEntity = $data['instances'][0] ?? null;
            
            return [
                'raw' => $data,
                'mapped' => $trackedEntity ? $this->metadataService->mapTrackedEntity($trackedEntity) : null,
            ];
        }

        return [
            'error' => true,
            'message' => $response->body(),
            'status' => $response->status(),
        ];
    }

    /**
     * Get tracked entities filtered by attribute value
     */
    public function getTrackedEntitiesByAttribute(string $attributeId, string $value): array
    {
        $query = [
            'orgUnit' => 'LA93mGekSOL',
            'orgUnitMode' => 'SELECTED',
            'trackedEntityType' => 'H9a4SMFpH3N',
            'filter' => "{$attributeId}:EQ:{$value}",
            'fields' => 'createdAt,orgUnit,trackedEntity,updatedAt,enrollments[storedBy,enrolledAt,enrollment,events[event,programStage,occurredAt,status,dataValues[dataElement,value]]],attributes[storedBy,value,attribute,code]'
        ];

        $response = $this->client()
            ->get($this->baseUrl . '/api/tracker/trackedEntities.json', $query);

        if ($response->successful()) {
            $data = $response->json();
            
            return [
                'raw' => $data,
                'mapped' => $this->metadataService->mapTrackedEntities($data['instances'] ?? []),
                'pager' => $data['pager'] ?? [],
            ];
        }

        return [
            'error' => true,
            'message' => $response->body(),
            'status' => $response->status(),
        ];
    }

    /**
     * Add event to an existing enrollment
     */
    public function addEventToEnrollment(array $eventData): Response
    {
        $payload = ['events' => [$eventData]];

     
        return $this->client()
            ->post($this->baseUrl . '/api/events', $payload);
    }

    /**
     * Check job status
     */
    public function getJobStatus(string $jobId): Response
    {
        return $this->client()
            ->get($this->baseUrl . '/api/tracker/jobs/' . $jobId);
    }

    /**
     * Get enrollment details
     */
    public function getEnrollment(string $enrollmentId): Response
    {
        return $this->client()
            ->get($this->baseUrl . '/api/tracker/enrollments/' . $enrollmentId);
    }

    /**
     * Get events for a specific enrollment and stage
     */
    public function getEvents(string $enrollmentId, ?string $programStageId = null): Response
    {
        $query = ['enrollment' => $enrollmentId];
        
        if ($programStageId) {
            $query['programStage'] = $programStageId;
        }

        return $this->client()
            ->get($this->baseUrl . '/api/tracker/events', $query);
    }
}