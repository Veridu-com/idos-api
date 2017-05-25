<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * ROOT TABLES.
 */
class DatabaseInit extends AbstractMigration {
    public function change() {
        // Root for a person's ID
        $identities = $this->table('identities');
        $identities
            ->addColumn('reference', 'text', ['null' => false])
            ->addColumn('public_key', 'text', ['null' => false])
            // private_key should be binary
            ->addColumn('private_key', 'binary', ['null' => false])
            ->addTimestamps()
            ->addIndex('reference', ['unique' => true])
            ->addIndex('public_key')
            ->create();

        // Company base info
        $companies = $this->table('companies');
        $companies
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('public_key', 'text', ['null' => false])
            // private_key should be binary
            ->addColumn('private_key', 'text', ['null' => false])
            ->addColumn('personal', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addTimestamps()
            ->addIndex('public_key')
            ->addIndex('slug', ['unique' => true])
            ->addForeignKey('parent_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $features = $this->table('roles');
        $features
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('rank', 'integer', ['null' => false])
            ->addColumn('bit', 'integer', ['null' => false])
            ->addColumn('created_at', 'timestamp', ['null' => false, 'timezone' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('name', ['unique' => true])
            ->create();

        // Roles for rights management
        $roleAccess = $this->table('role_access');
        $roleAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'integer', ['null' => false, 'default' => 0x00]) // values [ none=0x00, exec=0x01, w=0x02, r=0x04, r-exec=0x05, rw=0x06, rw-exec=0x07 ]
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('role')
            ->addIndex('resource')
            ->addIndex(['identity_id', 'role', 'resource'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        // Company members (FIXME Review this table)
        $members = $this->table('members');
        $members
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => false, 'default' => 'member'])
            ->addTimestamps()
            ->addIndex(['company_id', 'identity_id'], ['unique' => true])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $settings = $this->table('settings');
        $settings
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('section', 'text', ['null' => false])
            ->addColumn('property', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => false])
            ->addColumn('protected', 'boolean', ['null' => false, 'default' => false])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex(['company_id', 'section', 'property'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company Endpoint Permission
        $permissions = $this->table('permissions');
        $permissions
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('route_name', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex(['company_id', 'route_name'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company credentials for API access
        $credentials = $this->table('credentials');
        $credentials
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('public', 'text', ['null' => false])
            ->addColumn('private', 'text', ['null' => false])
            ->addColumn('production', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('special', 'boolean', ['null' => false, 'default' => false]) // can create dashboards (sso will behave differently)
            ->addTimestamps()
            ->addIndex('company_id')
            ->addIndex('slug')
            ->addIndex('public', ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Service list
        $handlers = $this->table('handlers');
        $handlers
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('role', 'text', ['null' => false, 'default' => 'none'])
            ->addColumn('auth_username', 'text', ['null' => false])
            ->addColumn('auth_password', 'text', ['null' => false])
            ->addColumn('public', 'text', ['null' => false])
            ->addColumn('private', 'text', ['null' => false])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => true])
            ->addTimestamps()
            ->addIndex('name', ['unique' => true])
            ->addIndex('public', ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Handler services registration
        $handlerServices = $this->table('handler_services');
        $handlerServices
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('handler_id', 'integer', ['null' => false])
            ->addColumn('url', 'text', ['null' => false])
            ->addColumn('listens', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('privacy', 'integer', ['null' => false, 'default' => 0x00]) // 0x00 => public, 0x01 => private
            ->addTimestamps()
            ->addIndex(
                [
                'handler_id'
                ]
            )
            ->addForeignKey('handler_id', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Services a company will offer to their users
        // within handlers' services, from table 'handler_services'
        $services = $this->table('services');
        $services
            ->addColumn('handler_service_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('listens', 'jsonb', ['null' => false, 'default' => '[]']) // Must be in array "listens" of the the related "handler_services"
            ->addTimestamps()
            ->addIndex(['handler_service_id', 'company_id'], ['unique' => true])
            ->addForeignKey('handler_service_id', 'handler_services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $categories = $this->table('categories');
        $categories
            ->addColumn('display_name', 'text', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('handler_id', 'integer', ['null' => false])
            ->addIndex('type')
            ->addIndex('name', ['unique' => true])
            ->addTimestamps()
            ->addForeignKey('handler_id', 'handlers', 'id', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->create();

        // Identity link to credentials
        $users = $this->table('users');
        $users
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('username', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'username'], ['unique' => true])
            ->addIndex('credential_id')
            ->addIndex('username')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        // Credential WebHooks
        $hooks = $this->table('hooks');
        $hooks
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('trigger', 'text', ['null' => false])
            ->addColumn('url', 'binary', ['null' => false])
            ->addColumn('subscribed', 'boolean', ['null' => false, 'default' => 'TRUE'])
            ->addTimestamps()
            ->addIndex('credential_id')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Hook errors
        $hookErrors = $this->table('hook_errors');
        $hookErrors
            ->addColumn('hook_id', 'integer', ['null' => false])
            ->addColumn('payload', 'binary', ['null' => false])
            ->addColumn('error', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('hook_id')
            ->addForeignKey('hook_id', 'hooks', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
                // Links a user to an identity
        $user_identities = $this->table('user_identities');
        $user_identities
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('user_id')
            ->addIndex(['identity_id', 'user_id'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Profile attribute candidates
        $candidates = $this->table('candidates');
        $candidates
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('attribute', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn('support', 'decimal', ['null' => false, 'default' => 0.0])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('creator')
            ->addIndex('attribute')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Profile attributes values
        $attributes = $this->table('attributes');
        $attributes
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex(['user_id', 'name'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            // ->addForeignKey('name', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->create();

        // Profile references values
        $references = $this->table('references');
        $references
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn('ipaddr', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('name', ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // User sources
        $sources = $this->table('sources');
        $sources
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('tags', 'jsonb', ['null' => true])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // FIXME Review this table
        $userAccess = $this->table('user_access');
        $userAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'boolean', ['null' => false, 'default' => true])
            ->addTimestamps()
            ->addIndex('identity_id')
            ->addIndex('user_id')
            ->addIndex('resource')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $flags = $this->table('flags');
        $flags
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('attribute', 'text')
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('creator')
            ->addIndex(['user_id', 'creator', 'slug'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            // ->addForeignKey('slug', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            // ->addForeignKey('attribute', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->create();

        $tags = $this->table('tags');
        $tags
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex(['user_id', 'slug'], ['unique' => true])
            ->addIndex(['user_id', 'identity_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $gates = $this->table('gates');
        $gates
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('confidence_level', 'text', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('creator')
            ->addIndex('name')
            ->addIndex(['user_id', 'creator', 'name'], ['unique' => true])
            ->addIndex(['user_id', 'creator', 'slug'], ['unique' => true])
            ->addForeignKey('name', 'categories', 'name', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $recommendations = $this->table('recommendations');
        $recommendations
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('result', 'text', ['null' => false])
            ->addColumn('passed', 'text', ['null' => false])
            ->addColumn('failed', 'text', ['null' => false])
            ->addTimestamps()
            ->addIndex('user_id', ['unique' => true])
            ->addIndex('creator')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $processes = $this->table('processes');
        $processes
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('source_id', 'integer', ['null' => true])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('event', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex(['user_id'])
            ->addIndex(['source_id'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $tasks = $this->table('tasks');
        $tasks
            ->addColumn('process_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('event', 'text', ['null' => false])
            ->addColumn('running', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('success', 'boolean', ['null' => true])
            ->addColumn('message', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('process_id')
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('process_id', 'processes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Profile reviews values
        $reviews = $this->table('reviews');
        $reviews
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('gate_id', 'integer', ['null' => true])
            ->addColumn('recommendation_id', 'integer', ['null' => true])
            ->addColumn('positive', 'boolean', ['null' => false])
            ->addColumn('description', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex(['user_id', 'gate_id'], ['unique' => true])
            ->addIndex(['user_id', 'recommendation_id'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('gate_id', 'gates', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('recommendation_id', 'recommendations', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Attribute scores
        $scores = $this->table('scores');
        $scores
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('attribute', 'text', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'decimal', ['null' => false, 'default' => 0.0])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('creator')
            ->addIndex(['user_id', 'creator', 'name'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
        // Features
        $features = $this->table('features');
        $features
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('source', 'text', ['null' => true])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('creator', 'integer', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addTimestamps()
            ->addIndex('user_id')
            ->addIndex('source')
            ->addIndex('creator')
            ->addIndex(['user_id', 'source', 'creator', 'id'])
            ->addIndex(['user_id', 'source', 'creator', 'name'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator', 'handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $addressLookup = $this->table('address_lookup');
        $addressLookup
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('reference', 'text', ['null' => true])
            ->addColumn('region', 'text', ['null' => false])
            ->addColumn('postcode', 'text', ['null' => false])
            ->addColumn('number', 'integer', ['null' => false])
            ->addColumn('street', 'text', ['null' => true])
            ->addColumn('city', 'text', ['null' => true])
            ->addColumn('state', 'text', ['null' => true])
            ->addColumn('country', 'text', ['null' => true])
            ->addTimestamps()
            ->addIndex('reference')
            ->addIndex('postcode')
            ->create();

        // This table has no Foreign Keys as it's used as logs for deleted companies/credentials/users too
        $logs = $this->table('logs');
        $logs
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('level', 'text', ['null' => false])
            ->addColumn('message', 'text', ['null' => false])
            ->addColumn('context', 'binary', ['null' => true])
            ->addTimestamps()
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addIndex('company_id')
            ->addIndex('credential_id')
            ->addIndex('user_id')
            ->create();

        // SUBSCRIPTIONS
        $subscriptions = $this->table('subscriptions');
        $subscriptions
            ->addColumn('category_name', 'text', ['null' => true])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['category_name', 'credential_id', 'identity_id'])
            ->addIndex(['category_name', 'credential_id'], ['unique' => true])
            ->addForeignKey('category_name', 'categories', 'name', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Invitations
        $invitations = $this->table('invitations');
        $invitations
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('email', 'text', ['null' => false])
            ->addColumn('role', 'text', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('member_id', 'integer', ['null' => true])
            ->addColumn('creator_id', 'integer', ['null' => true])
            ->addColumn('expires', 'date', ['null' => false])
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('voided', 'boolean', ['null' => false, 'default' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'creator_id'])
            ->addIndex(['company_id', 'member_id'], ['unique' => true])
            ->addIndex(['hash'], ['unique' => true])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('member_id', 'members', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Widgets
        $widgets = $this->table('widgets');
        $widgets
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('label', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('enabled', 'boolean', ['default' => true])
            ->addColumn('config', 'jsonb', ['null' => false])
            ->addColumn('creator_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['credential_id', 'creator_id'])
            ->addIndex(['hash'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('creator_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Metrics
        // @FIXME make the id bigserial
        // select * from categories;
        $this
            ->table('metrics')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addTimestamps()
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_hourly')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_daily')
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('endpoint', 'text', ['null' => false])
            ->addColumn('action', 'text', ['null' => false])
            ->addColumn('count', 'integer', ['null' => false])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['credential_public'])
            ->addIndex(['endpoint'])
            ->addIndex(['action'])
            ->create();

        $this
            ->table('metrics_user')
            ->addColumn('hash', 'text', ['null' => false])
            ->addColumn('credential_public', 'text', ['null' => false])
            ->addColumn('sources', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('data', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('gates', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addColumn('flags', 'jsonb', ['null' => false, 'default' => '[]'])
            ->addTimestamps()
            ->addIndex(['hash'], ['unique' => true])
            ->addIndex(['credential_public'])
            ->create();

    }
}
