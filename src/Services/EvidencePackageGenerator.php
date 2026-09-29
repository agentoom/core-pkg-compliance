<?php

namespace Agentoom\Compliance\Services;

use Agentoom\Compliance\Models\AiAuditLog;
use Agentoom\Compliance\Models\AiComplianceAlert;
use Agentoom\Compliance\Models\AiEmergencyStop;
use Agentoom\Compliance\Models\AiEvaluation;
use Agentoom\Compliance\Models\AiIncident;
use Agentoom\Compliance\Models\AiPendingDecision;
use Agentoom\Compliance\Models\AiRiskAssessment;
use Agentoom\Compliance\Models\EvidencePackage;
use Illuminate\Support\Facades\Storage;

class EvidencePackageGenerator
{
    /**
     * Generate an evidence package for a given subject (Agent, Pipeline, Endpoint).
     */
    public function generate(EvidencePackage $package): EvidencePackage
    {
        $package->markGenerating();

        try {
            $data = $this->collectData($package->subject_type, $package->subject_id);

            if ($package->format === 'json') {
                $path = $this->storeJson($package, $data);
            } else {
                $path = $this->storeZip($package, $data);
            }

            $package->markReady($path);
        } catch (\Throwable $e) {
            $package->markFailed();
            throw $e;
        }

        return $package;
    }

    /**
     * Collect all governance data for a subject.
     *
     * @return array<string, mixed>
     */
    private function collectData(string $subjectType, int $subjectId): array
    {
        $riskAssessments = AiRiskAssessment::where('assessable_type', $subjectType)
            ->where('assessable_id', $subjectId)
            ->with('versions')
            ->get()
            ->toArray();

        $alerts = AiComplianceAlert::where('entity_type', $subjectType)
            ->where('entity_id', $subjectId)
            ->get()
            ->toArray();

        $incidents = AiIncident::where('affected_entity_type', $subjectType)
            ->where('affected_entity_id', $subjectId)
            ->get()
            ->toArray();

        $evaluations = AiEvaluation::where('entity_type', $subjectType)
            ->where('entity_id', $subjectId)
            ->get()
            ->toArray();

        $auditLogs = AiAuditLog::where('entity_type', $subjectType)
            ->where('entity_id', $subjectId)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get()
            ->toArray();

        $overrides = AiPendingDecision::where('entity_type', $subjectType)
            ->where('entity_id', $subjectId)
            ->whereNotNull('overridden_at')
            ->get()
            ->toArray();

        $emergencyStops = AiEmergencyStop::where('entity_type', $subjectType)
            ->where('entity_id', $subjectId)
            ->get()
            ->toArray();

        return [
            'generated_at' => now()->toIso8601String(),
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'risk_assessments' => $riskAssessments,
            'compliance_alerts' => $alerts,
            'incidents' => $incidents,
            'evaluations' => $evaluations,
            'audit_logs' => $auditLogs,
            'overrides' => $overrides,
            'emergency_stops' => $emergencyStops,
        ];
    }

    private function storeJson(EvidencePackage $package, array $data): string
    {
        $path = "evidence-packages/{$package->id}/evidence.json";
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        return $path;
    }

    private function storeZip(EvidencePackage $package, array $data): string
    {
        $dir = storage_path("app/evidence-packages/{$package->id}");
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $jsonPath = "{$dir}/evidence.json";
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

        $zipPath = "{$dir}/evidence.zip";
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($jsonPath, 'evidence.json');
            $zip->close();
        }

        @unlink($jsonPath);

        return "evidence-packages/{$package->id}/evidence.zip";
    }
}
