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
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['company_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('company_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('company_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Credential Related Metrics
         */
        $this
            ->table('credential_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['credential_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('credential_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('credential_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Hook Related Metrics
         */
        $this
            ->table('hook_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('hook_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['hook_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('hook_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('hook_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Invitation Related Metrics
         */
        $this
            ->table('invitation_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('invitation_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['invitation_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('invitation_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('invitation_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Member Related Metrics
         */
        $this
            ->table('member_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('member_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['member_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('member_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('member_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Permission Related Metrics
         */
        $this
            ->table('permission_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('permission_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['permission_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('permission_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('permission_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Settings Related Metrics
         */
        $this
            ->table('setting_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('setting_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['setting_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('setting_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('setting_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Attribute Related Metrics
         */
        $this
            ->table('attribute_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('attribute_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['attribute_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('attribute_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('attribute_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Candidate Related Metrics
         */
        $this
            ->table('candidate_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('candidate_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['candidate_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('candidate_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('candidate_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Feature Related Metrics
         */
        $this
            ->table('feature_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('feature_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['feature_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('feature_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('feature_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Flag Related Metrics
         */
        $this
            ->table('flag_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('flag_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['flag_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('flag_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('flag_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Gate Related Metrics
         */
        $this
            ->table('gate_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('gate_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['gate_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('gate_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Process Related Metrics
         */
        $this
            ->table('process_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('process_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['process_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('process_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('process_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Raw Data Related Metrics
         */
        $this
            ->table('raw_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('raw_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['raw_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('raw_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('raw_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Reference Related Metrics
         */
        $this
            ->table('reference_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('reference_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['reference_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('reference_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('reference_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Review Related Metrics
         */
        $this
            ->table('review_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('review_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['review_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('review_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('review_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Score Related Metrics
         */
        $this
            ->table('score_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('score_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['score_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('score_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('score_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Source Related Metrics
         */
        $this
            ->table('source_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['source_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('source_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Tag Related Metrics
         */
        $this
            ->table('tag_metrics')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('tag_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addIndex(['tag_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('tag_metrics_hourly')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('tag_metrics_daily')
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['identity_id'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /**
         * Task Related Metrics
         */
        $this
            ->table('task_metrics')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('task_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addIndex(['task_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('task_metrics_hourly')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this
            ->table('task_metrics_daily')
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
