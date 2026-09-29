<?php

namespace Agentoom\Compliance\Helpers;

use Agentoom\Core\Models\Configuration;

class ComplianceConfig
{
    /**
     * Configuration keys stored in the database.
     *
     * When a value exists in the Configuration model (set via the admin UI),
     * it takes precedence over the .env-based config value. This allows
     * superadmins to toggle compliance features without modifying .env files.
     */
    private const DB_KEY_ENABLED = 'eu_compliance_enabled';

    private const DB_KEY_FEATURES = [
        'audit_logging' => 'compliance_audit_logging',
        'emergency_stops' => 'compliance_emergency_stops',
        'auto_incidents' => 'compliance_auto_incidents',
        'auto_alerts' => 'compliance_auto_alerts',
        'governance_gate' => 'compliance_governance_gate',
        'ai_disclosure' => 'compliance_ai_disclosure',
        'risk_enforcement' => 'compliance_risk_enforcement',
    ];

    /**
     * All compliance-related configuration keys (for bulk operations).
     *
     * @return array<string>
     */
    public static function allDbKeys(): array
    {
        return array_merge(
            [self::DB_KEY_ENABLED],
            array_values(self::DB_KEY_FEATURES),
        );
    }

    /**
     * Check if the EU AI Act compliance system is enabled globally.
     * This is the master switch — when false, ALL features are bypassed.
     *
     * Reads from Configuration model (database) first, then falls back
     * to the .env-based config value.
     */
    public static function isEnabled(): bool
    {
        $dbValue = Configuration::getValue(self::DB_KEY_ENABLED);

        if ($dbValue !== null) {
            return (bool) $dbValue;
        }

        return (bool) config('agentoom-compliance.enabled', false);
    }

    /**
     * Check if a specific compliance feature is enabled.
     * Returns false immediately if the master switch is off.
     *
     * Reads from Configuration model (database) first, then falls back
     * to the .env-based config value.
     *
     * @param  string  $feature  One of: audit_logging, emergency_stops, auto_incidents,
     *                           auto_alerts, governance_gate, ai_disclosure, risk_enforcement
     */
    public static function isFeatureEnabled(string $feature): bool
    {
        if (! self::isEnabled()) {
            return false;
        }

        $dbKey = self::DB_KEY_FEATURES[$feature] ?? null;

        if ($dbKey !== null) {
            $dbValue = Configuration::getValue($dbKey);

            if ($dbValue !== null) {
                return (bool) $dbValue;
            }
        }

        return (bool) config("agentoom-compliance.features.{$feature}", true);
    }

    /**
     * Get all enabled feature flags.
     *
     * @return array<string, bool>
     */
    public static function enabledFeatures(): array
    {
        if (! self::isEnabled()) {
            return [];
        }

        $enabled = [];

        foreach (self::DB_KEY_FEATURES as $feature => $dbKey) {
            if (self::isFeatureEnabled($feature)) {
                $enabled[$feature] = true;
            }
        }

        return $enabled;
    }
}
