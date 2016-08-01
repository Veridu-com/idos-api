<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

class DatabaseInit extends AbstractMigration {
    public function change() {

        /*
         *
         * ROOT TABLES
         *
         */

        // Root for a person's ID
        $identities = $this->table('identities');
        $identities
            ->addColumn('public_key', 'text', ['null' => false])
            ->addColumn('private_key', 'text', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            // TODO: Discover why no index on the "private_key"
            ->addIndex('public_key')
            ->create();

        // Company base info
        $companies = $this->table('companies');
        $companies
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('slug', 'text', ['null' => false])
            ->addColumn('public_key', 'text', ['null' => false])
            ->addColumn('private_key', 'text', ['null' => false])
            ->addColumn('personal', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('public_key')
            ->addIndex('slug', ['unique' => true])
            ->addForeignKey('parent_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * IDENTITY RELATED TABLES
         *
         */

        // Identity attributes values
        $attributes = $this->table('attributes');
        $attributes
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Identity flags
        $flags = $this->table('flags');
        $flags
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addIndex('name')
            ->addIndex(['identity_id', 'name'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Identity gates status
        $gates = $this->table('gates');
        $gates
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('pass', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addIndex('name')
            ->addIndex(['identity_id', 'name'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Identity features
        $features = $this->table('features');
        $features
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addIndex('name')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Roles
        $features = $this->table('roles');
        $features
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('created_at', 'timestamp', ['null' => false, 'timezone' => false, 'default'  => 'CURRENT_TIMESTAMP'])
            ->addIndex('name', ['unique' => true])
            ->create();

        // Roles for rights management
        $roleAccess = $this->table('role_access');
        $roleAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'text', ['null' => true, 'default' => null]) // enum ('r', 'w', 'rw', null)
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addIndex('role')
            ->addIndex('resource')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        $roleLogs = $this->table('role_logs');

        /*
         *
         * ATTRIBUTE RELATED TABLES
         *
         */

        // Attribute scores
        $scores = $this->table('scores');
        $scores
            ->addColumn('attribute_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'float', ['null' => false, 'default' => 0.0])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('attribute_id')
            ->addForeignKey('attribute_id', 'attributes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * COMPANY RELATED TABLES
         *
         */

        $applications = $this->table('applications');
        $applications
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('token', 'binary', ['null' => false])
            ->addColumn('secret', 'binary', ['null' => false])
            ->addColumn('version', 'text', ['null' => true])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => 'TRUE'])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $settings = $this->table('settings');
        $settings
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('section', 'text', ['null' => false])
            ->addColumn('property', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addIndex(['company_id', 'section', 'property'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $permissions = $this->table('permissions');
        $permissions
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('route_name', 'text', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addIndex(['company_id', 'route_name'], ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $companyWhiteList = $this->table('company_whitelist');
        $companyWhiteList
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('fqdn', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('value', 'binary', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addIndex(['company_id', 'value'], ['unique' => true])
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
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addIndex('slug')
            ->addIndex('public', ['unique' => true])
            ->addIndex('private', ['unique' => true])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * SERVICE RELATED TABLES
         *
         */

        // Service list
        $services = $this->table('services');
        $services
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('name', ['unique' => true])
            ->create();

        // Service handlers registration
        $serviceHandlers = $this->table('service_handlers');
        $serviceHandlers
            ->addColumn('service_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => true])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('source', 'text', ['null' => false])
            ->addColumn('location', 'text', ['null' => false])
            ->addIndex('service_id')
            ->addIndex('source')
            ->addForeignKey('service_id', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company's service handlers (configuration)
        $companyServiceHandlers = $this->table('company_service_handlers');
        $companyServiceHandlers
            ->addColumn('handler_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addForeignKey('handler_id', 'service_handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * PROCESS RELATED TABLES
         *
         */

        // Process list
        $processes = $this->table('processes');
        $processes
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('name', ['unique' => true])
            ->create();

        // Process handlers registrarion
        $processHandlers = $this->table('process_handlers');
        $processHandlers
            ->addColumn('process_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => true])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('step', 'text', ['null' => false])
            ->addColumn('sources', 'text', ['null' => true])
            ->addColumn('runlevel', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('location', 'text', ['null' => false])
            ->addIndex('step')
            ->addIndex('runlevel')
            ->addForeignKey('process_id', 'processes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Company's process handlers (configuration)
        $companyProcessHandlers = $this->table('company_process_handlers');
        $companyProcessHandlers
            ->addColumn('handler_id', 'integer', ['null' => false])
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addForeignKey('handler_id', 'process_handlers', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * CREDENTIAL RELATED TABLES
         *
         */

        // Identity link to credentials
        $users = $this->table('users');
        $users
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => true])
            ->addColumn('identity_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('username', 'binary', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex(['credential_id', 'username'], ['unique' => true])
            ->addIndex('credential_id')
            ->addIndex('identity_id')
            ->addIndex('username')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('role', 'roles', 'name', ['delete' => 'SET NULL', 'update' => 'SET NULL'])
            ->create();

        $hooks = $this->table('hooks');
        $hooks
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('trigger', 'text', ['null' => false])
            ->addColumn('url', 'binary', ['null' => false])
            ->addColumn('subscribed', 'boolean', ['null' => false, 'default' => 'TRUE'])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('credential_id')
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $members = $this->table('members');
        $members
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('role', 'text', ['null' => false, 'default' => 'member'])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('company_id')
            ->addIndex('user_id')
            ->addForeignKey('company_id', 'companies', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();


        $hookLogs = $this->table('hook_logs');

        $credentialWhiteList = $this->table('credential_whitelist');
        $credentialWhiteList
            ->addColumn('credential_id', 'integer', ['null' => false])
            ->addColumn('fqdn', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('value', 'binary', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('credential_id')
            ->addIndex(['credential_id', 'value'], ['unique' => true])
            ->addForeignKey('credential_id', 'credentials', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * HOOK RELATED TABLES
         *
         */

        // Hook errors
        $hookErrors = $this->table('hook_errors');
        $hookErrors
            ->addColumn('hook_id', 'integer', ['null' => false])
            ->addColumn('payload', 'binary', ['null' => false])
            ->addColumn('error', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('hook_id')
            ->addForeignKey('hook_id', 'hooks', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * USER RELATED TABLES
         *
         */

        // User sources
        $sources = $this->table('sources');
        $sources
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('valid', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('user_id')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        // Process ID control
        $controls = $this->table('controls');
        $controls
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('scrape', 'integer', ['null' => false, 'default' => -1])
            ->addColumn('map', 'integer', ['null' => false, 'default' => -1])
            ->addColumn('feature', 'integer', ['null' => false, 'default' => -1])
            ->addColumn('score', 'integer', ['null' => false, 'default' => -1])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->create();

        $userAccess = $this->table('user_access');
        $userAccess
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('resource', 'text', ['null' => true])
            ->addColumn('access', 'boolean', ['null' => false, 'default' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('identity_id')
            ->addIndex('user_id')
            ->addIndex('resource')
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * SOURCE RELATED TABLES
         *
         */

        $social = $this->table('social');
        $social
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('uuid', 'text', ['null' => true])
            ->addColumn('token', 'binary', ['null' => false])
            ->addColumn('secret', 'binary', ['null' => true])
            ->addColumn('refresh', 'binary', ['null' => true])
            ->addColumn('application_id', 'integer', ['null' => true])
            ->addColumn('sso', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addIndex('application_id')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('application_id', 'applications', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE'])
            ->create();

        $email = $this->table('email');
        $email
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('email', 'binary', ['null' => false])
            ->addColumn('code', 'text', ['null' => false])
            ->addColumn('verified', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('expires', 'timestamp', ['null' => false, 'timezone' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addIndex('email')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $sms = $this->table('sms');
        $sms
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('phone', 'binary', ['null' => false])
            ->addColumn('code', 'text', ['null' => false])
            ->addColumn('verified', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('expires', 'timestamp', ['null' => false, 'timezone' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addIndex('phone')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $spotafriend = $this->table('spotafriend');
        $spotafriend
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('provider', 'text', ['null' => false])
            ->addColumn('target', 'text', ['null' => false])
            ->addColumn('setup', 'text', ['null' => false])
            ->addColumn('verified', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('voided', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $tasks = $this->table('tasks');
        $tasks
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('running', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('success', 'boolean', ['null' => false, 'default' => 'FALSE'])
            ->addColumn('message', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $normalised = $this->table('normalised');
        $normalised
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addIndex('name')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $digested = $this->table('digested');
        $digested
            ->addColumn('source_id', 'integer', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'binary', ['null' => true])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addIndex('source_id')
            ->addIndex('name')
            ->addForeignKey('source_id', 'sources', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        /*
         *
         * SPECIAL TABLES
         *
         */

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
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
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
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn('ipaddr', 'text', ['null' => true])
            ->addIndex('company_id')
            ->addIndex('credential_id')
            ->addIndex('user_id')
            ->create();

        // System metrics
        $metrics = $this->table('metrics');
        $metrics
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('value', 'float', ['null' => false])
            ->addColumn(
                'created_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->addColumn(
                'updated_at',
                'timestamp',
                [
                    'null'     => false,
                    'timezone' => false,
                    'default'  => 'CURRENT_TIMESTAMP'
                ]
            )
            ->create();

        /*
         *
         * DATA SUPLIER TABLES
         *
         */

        $cityList = $this->table('city_list');
        $cityList
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('alternate_name', 'text', ['null' => true])
            ->addColumn('region', 'text', ['null' => true])
            ->addColumn('country', 'text', ['null' => true])
            ->addIndex('name')
            ->addIndex('region')
            ->addIndex('country')
            ->create();

        $countryList = $this->table(
            'country_list',
            [
                'id'          => false,
                'primary_key' => 'code'
            ]
        );
        $countryList
            ->addColumn('code', 'char', ['null' => false, 'limit' => 2])
            ->addColumn('name', 'text', ['null' => false])
            ->create();

        $knownNameList = $this->table(
            'known_name_list',
            [
                'id'          => false,
                'primary_key' => ['name', 'type']
            ]
        );
        $knownNameList
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('soundex', 'text', ['null' => false])
            ->addColumn('metaphone', 'text', ['null' => false])
            ->addColumn('dmetaphone1', 'text', ['null' => false])
            ->addColumn('dmetaphone2', 'text', ['null' => false])
            ->addIndex('soundex')
            ->addIndex('metaphone')
            ->addIndex('dmetaphone1')
            ->addIndex('dmetaphone2')
            ->create();

        $nameList = $this->table(
            'name_list',
            [
                'id'          => false,
                'primary_key' => ['country', 'name']
            ]
        );
        $nameList
            ->addColumn('country', 'text', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('gender', 'char', ['null' => false, 'limit' => 1])
            ->addColumn('soundex', 'text', ['null' => false])
            ->addColumn('metaphone', 'text', ['null' => false])
            ->addColumn('dmetaphone1', 'text', ['null' => false])
            ->addColumn('dmetaphone2', 'text', ['null' => false])
            ->addIndex('soundex')
            ->addIndex('metaphone')
            ->addIndex('dmetaphone1')
            ->addIndex('dmetaphone2')
            ->create();

    }
}
