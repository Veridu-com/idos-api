<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Metrics RELATED TABLES.
 * These tables holds API metrics information.
 */
class Metrics extends AbstractMigration {
    public function change() {
        /**
         * Company Related Metrics
         */
        $this
            ->table('company_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('company_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('company_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Credential Related Metrics
         */
        $this
            ->table('credential_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('credential_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('credential_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Hook Related Metrics
         */
        $this
            ->table('hook_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('hook_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('hook_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Invitation Related Metrics
         */
        $this
            ->table('invitation_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('invitation_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('invitation_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Member Related Metrics
         */
        $this
            ->table('member_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('member_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('member_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Permission Related Metrics
         */
        $this
            ->table('permission_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('permission_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('permission_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Settings Related Metrics
         */
        $this
            ->table('setting_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('setting_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('setting_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Attribute Related Metrics
         */
        $this
            ->table('attribute_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('attribute_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('attribute_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Candidate Related Metrics
         */
        $this
            ->table('candidate_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('candidate_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('candidate_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Feature Related Metrics
         */
        $this
            ->table('feature_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('feature_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('feature_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Flag Related Metrics
         */
        $this
            ->table('flag_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('flag_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('flag_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Gate Related Metrics
         */
        $this
            ->table('gate_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Process Related Metrics
         */
        $this
            ->table('process_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('process_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('process_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Raw Data Related Metrics
         */
        $this
            ->table('raw_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('raw_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('raw_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Reference Related Metrics
         */
        $this
            ->table('reference_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('reference_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('reference_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Review Related Metrics
         */
        $this
            ->table('review_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('review_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('review_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Score Related Metrics
         */
        $this
            ->table('score_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('score_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('score_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Source Related Metrics
         */
        $this
            ->table('source_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Tag Related Metrics
         */
        $this
            ->table('tag_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('tag_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('tag_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Task Related Metrics
         */
        $this
            ->table('task_metrics')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('entity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addIndex(['entity_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('task_metrics_hourly')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('task_metrics_daily')
            ->addColumn('actor_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['actor_id'])
            ->addForeignKey('actor_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
