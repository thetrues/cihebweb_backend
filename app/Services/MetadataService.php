<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MetadataService
{
    protected array $metadata;
    protected array $attributeMap = [];
    protected array $stageDataElementMap = [];

    public function __construct()
    {
        // Load your metadata
        $this->metadata = $this->loadMetadata();
        $this->buildMaps();
    }

    /**
     * Load metadata from config or database
     */
    protected function loadMetadata(): array
    {
        // You can store this in config/dhis2_metadata.php or database
        return [
            'trackedEntityType' => 'H9a4SMFpH3N',
            'name' => 'Recruitment',
            'program' => 'IBuQKAzlpM8',
            'attributes' => [
                ['name' => 'Recruitment Reference Number', 'code' => 'reference_number', 'id' => 'PByXYzd3mm4'],
                ['name' => 'Recruitment Title', 'code' => 'title', 'id' => 'gcnPS35yD0e'],
                ['name' => 'Recruitment Location', 'code' => 'location', 'id' => 'ItLUyglz85G'],
                ['name' => 'Recruitment Posted By Id', 'code' => 'posted_by_id', 'id' => 'SOuXT8uHH8Y'],
                ['name' => 'Recruitment Posted By Name', 'code' => 'posted_by_name', 'id' => 'WEUQpC8zrxO'],
                ['name' => 'Recruitment Description', 'code' => 'description', 'id' => 'r1GGbx4TIFD'],
                ['name' => 'Recruitment End Date', 'code' => 'end_date', 'id' => 'M5qsRK5n6SX'],
                ['name' => 'Recruitment Start Date', 'code' => 'start_date', 'id' => 'PhvncaB8rdi'],
                ['name' => 'Recruitment Work Type', 'code' => 'work_type', 'id' => 'rClYIfLaFuc'],
                ['name' => 'Recruitment Job Type', 'code' => 'job_type', 'id' => 'EuQzJEkIPvb'],
                ['name' => 'Recruitment Region Id', 'code' => 'region_id', 'id' => 'vLYhbRD44KW'],
                ['name' => 'Recruitment Region Name', 'code' => 'region_name', 'id' => 'uIfTQKc14Sy'],
                ['name' => 'Recruitment Status', 'code' => 'status', 'id' => 'q2q4NURHygI'],
                ['name' => 'Current State', 'code' => 'current_state', 'id' => 'ObnE3GDg8zq'],
            ],
            'stages' => [
                'wFbwZvrdqTS' => [
                    'name' => 'descriptions',
                    'dataElements' => [
                        ['code' => 'name', 'name' => 'Name', 'id' => 'OIlHXyQGQom'],
                        ['code' => 'description', 'name' => 'Description', 'id' => 'y6calZGcGDg'],
                    ]
                ],
                'SP6mQgWvfSN' => [
                    'name' => 'applicants',
                    'dataElements' => [
                        ['code' => 'name', 'name' => 'Name', 'id' => 'OIlHXyQGQom'],
                        ['code' => 'phone_number', 'name' => 'Phone Number', 'id' => 'kIiHFHVJGa5'],
                        ['code' => 'email', 'name' => 'Email', 'id' => 'TjpEmLh0JIP'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Build lookup maps for quick access
     */
    protected function buildMaps(): void
    {
        // Build attribute map: id => metadata
        foreach ($this->metadata['attributes'] as $attribute) {
            $this->attributeMap[$attribute['id']] = $attribute;
        }

        // Build stage data element maps
        foreach ($this->metadata['stages'] as $stageId => $stage) {
            foreach ($stage['dataElements'] as $dataElement) {
                $this->stageDataElementMap[$stageId][$dataElement['id']] = $dataElement;
            }
        }
    }

    /**
     * Get attribute metadata by ID
     */
    public function getAttributeMetadata(string $id): ?array
    {
        return $this->attributeMap[$id] ?? null;
    }

    /**
     * Get data element metadata by stage and data element ID
     */
    public function getDataElementMetadata(string $stageId, string $dataElementId): ?array
    {
        return $this->stageDataElementMap[$stageId][$dataElementId] ?? null;
    }

    /**
     * Map a tracked entity to clean data
     */
    public function mapTrackedEntity(array $trackedEntity): array
    {
        return [
            'id' => $trackedEntity['trackedEntity'] ?? null,
            'org_unit' => $trackedEntity['orgUnit'] ?? null,
            'created_at' => $trackedEntity['createdAt'] ?? null,
            'updated_at' => $trackedEntity['updatedAt'] ?? null,
            'attributes' => $this->mapAttributes($trackedEntity['attributes'] ?? []),
            'enrollments' => $this->mapEnrollments($trackedEntity['enrollments'] ?? []),
        ];
    }

    /**
     * Map attributes to readable format
     */
    protected function mapAttributes(array $attributes): array
    {
        $mapped = [];

        foreach ($attributes as $attribute) {
            $attributeId = $attribute['attribute'];
            $metadata = $this->getAttributeMetadata($attributeId);

            $mapped[] = [
                'id' => $attributeId,
                'code' => $metadata['code'] ?? null,
                'name' => $metadata['name'] ?? null,
                'value' => $attribute['value'] ?? null,
                'stored_by' => $attribute['storedBy'] ?? null,
            ];
        }

        return $mapped;
    }

    /**
     * Map enrollments to readable format
     */
    protected function mapEnrollments(array $enrollments): array
    {
        $mapped = [];

        foreach ($enrollments as $enrollment) {
            $mapped[] = [
                'id' => $enrollment['enrollment'] ?? null,
                'enrolled_at' => $enrollment['enrolledAt'] ?? null,
                'stored_by' => $enrollment['storedBy'] ?? null,
                'events' => $this->mapEvents($enrollment['events'] ?? []),
            ];
        }

        return $mapped;
    }

    /**
     * Map events to readable format
     */
    protected function mapEvents(array $events): array
    {
        $mapped = [];

        foreach ($events as $event) {
            $programStageId = $event['programStage'] ?? null;
            $stageName = $this->metadata['stages'][$programStageId]['name'] ?? 'unknown';

            $mapped[] = [
                'id' => $event['event'] ?? null,
                'program_stage' => $programStageId,
                'stage_name' => $stageName,
                'occurred_at' => $event['occurredAt'] ?? null,
                'status' => $event['status'] ?? null,
                'data_values' => $this->mapDataValues($programStageId, $event['dataValues'] ?? []),
            ];
        }

        return $mapped;
    }

    /**
     * Map data values to readable format
     */
    protected function mapDataValues(?string $stageId, array $dataValues): array
    {
        $mapped = [];

        foreach ($dataValues as $dataValue) {
            $dataElementId = $dataValue['dataElement'] ?? null;
            $metadata = $this->getDataElementMetadata($stageId, $dataElementId);

            $mapped[] = [
                'data_element_id' => $dataElementId,
                'code' => $metadata['code'] ?? null,
                'name' => $metadata['name'] ?? null,
                'value' => $dataValue['value'] ?? null,
            ];
        }

        return $mapped;
    }

    /**
     * Map collection of tracked entities
     */
    public function mapTrackedEntities(array $trackedEntities): array
    {
        $mapped = [];

        foreach ($trackedEntities as $trackedEntity) {
            $mapped[] = $this->mapTrackedEntity($trackedEntity);
        }

        return $mapped;
    }
}