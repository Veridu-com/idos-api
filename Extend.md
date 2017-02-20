Extension manual
=================

# Creating a New Route

The route files are located in the `app/Route` directory. In order to create a new route you need to create a new file under this directory or its subdirectories. An example code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Company.
 *
 * Within the hierarchy a Company allows you to separate access to the services to specific individuals.
 * Each Company is allowed to have multiple Users, Keys, Widgets and even other Companies.
 * All Access Roles configured in a parent Company will have access to all data from children Companies created.
 * These users will NOT be visible to users who only have access to the child Company.
 *
 * @link docs/companies/overview.md
 * @see \App\Controller\Companies
 */
class Companies implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'companies:listAll',
            'companies:createNew',
            'companies:getOne',
            'companies:updateOne',
            'companies:deleteOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Companies::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Companies(
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Companies.
     *
     * Retrieves a complete list of all child companies that belong to the requesting company.
     *
     * @apiEndpoint GET /companies
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Companies::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies',
                'App\Controller\Companies:listAll'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT | Role::COMPANY_REVIEWER_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:listAll');
    }

    /**
     * Retrieve a single Company.
     *
     * Retrieves all public information about a single Company.
     *
     * @apiEndpoint GET /companies/{companySlug}
     * @apiGroup Company
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Companies::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('companies:getOne');
    }

    /**
     * Create a new Company.
     *
     * Creates a new child company for the {companySlug} company.
     *
     * @apiEndpoint POST /companies/{companySlug}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd Parent company
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Companies::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:createNew'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:createNew');
    }

    /**
     * Update a single Company.
     *
     * Updates the information for a single Company.
     *
     * @apiEndpoint PATCH /companies/{companySlug}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Companies::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->patch(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:updateOne'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:updateOne');
    }

    /**
     * Deletes a single Company.
     *
     * Deletes the requesting company or a child company that belongs to it.
     *
     * @apiEndpoint DELETE /companies/{companySlug}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Companies::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:deleteOne'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:deleteOne');
    }
}
```

Basically, you define the route names within the `getPublicNames` method, register the routes in the `register` method and define the registered routes in separated methods, as in the code above.

# Creating a New Controller

The controller files are located in the `app/Controller` directory. In order to create a new route you need to create a new file under this directory or its subdirectories. An example code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CompanyInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies and /companies/{companySlug}.
 */
class Companies implements ControllerInterface {
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $repository;
    /**
     * Command Bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
    /**
     * Command Factory instance.
     *
     * @var \App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param \App\Repository\CompanyInterface $repository
     * @param \League\Tactician\CommandBus     $commandBus
     * @param \App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * List all child Companies that belongs to the Acting Company.
     *
     * @apiEndpointParam query string after 2016-01-01|1070-01-01 Initial Company creation date (lower bound)
     * @apiEndpointParam query string before 2016-01-31|2016-12-31 Final Company creation date (upper bound)
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/company/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity  = $request->getAttribute('identity');
        $companies = $identity->company();

        $body = [
            'data'    => $companies->toArray(),
            'updated' => (
                $companies->isEmpty() ? time() : max($companies->max('updatedAt'), $companies->max('createdAt'))
            )
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves the Target Company, a child of the Acting Company.
     *
     * @apiEndpointResponse 200 schema/company/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $body = [
            'data'    => $targetCompany->toArray(),
            'updated' => $targetCompany->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new child Company for the Acting Company.
     *
     * @apiEndpointRequiredParam body string name NewCo. Company name
     * @apiEndpointResponse 201 schema/company/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('parentId', $company->id);
        $company = $this->commandBus->handle($command);

        $body = [
            'data' => $company->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body)
            ->setParameter('statusCode', 201);

        return $this->commandBus->handle($command);
    }

    /**
     * Updates the Target Company, a child of the Acting Company.
     *
     * @apiEndpointRequiredParam body string name NewName New Company name
     * @apiEndpointResponse 200 schema/company/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('company', $targetCompany);
        $targetCompany = $this->commandBus->handle($command);

        $body = [
            'data'    => $targetCompany->toArray(),
            'updated' => time()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes the Target Company, a child of the Acting Company.
     *
     * @apiEndpointResponse 200 schema/company/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\DeleteOne');
        $command->setParameter('company', $targetCompany);
        $command->setParameter('identity', $identity);

        $this->commandBus->handle($command);
        $body = [
            'status' => true
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
```

Each registered route (see [Creating a New Route](#creating-a-new-route)) will call a specific method defined in the controller. For example: the `companies:listAll` route will execute the `listAll` method of the companies controller (`App\Controller\Companies`).

No business logic should be written in the controller. Instead, a new command should be created (see [Creating Commands](#creating-commands)) and sent through the command bus (see [Command Bus](https://laravel.com/docs/5.0/bus)) to be processed by a specific handler (see [Creating a New Handler](#creating-a-new-handler)).

# Creating a New Entity

Basically, an entity represents a tuple in the database. The entity files are located in the `app/Entity` directory. In order to create a new entity you need to create a new file under this directory or its subdirectories. An example code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SlugMutator;

/**
 * Companies Entity.
 *
 * @apiEntity schema/company/companyEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $public_key
 * @property string $private_key
 * @property int    $created_at
 * @property int    $updated_at
 */
class Company extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public_key', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['private_key'];
}
```

The attribute `visible` in the entity class is an array of the visible attributes of the entity. More specifically, a call to the `toArray` method of the entity object will return an array containing the visible attributes.

The attribute `dates` in the entity class is an array of entity attributes that should be treated as dates.

The attribute `secure` in the entity class is an array of entity attributes that should be kept encrypted in the database.

The attribute `json` in the entity class is an array of entity attributes that should be treated as JSON data.

# Creating a New Repository

The repository files are located in the `app/Repository` directory. In order to create a new repository you need to create at least two new files under this directory or its subdirectories.

The first file is the repository interface. This interface will define which methods the repositories for each strategy must implement. An example code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Member;
use App\Entity\Identity;
use Illuminate\Support\Collection;

/**
 * Company Repository Interface.
 */
interface CompanyInterface extends RepositoryInterface {
    /**
     * Determines if a company is related to another.
     *
     * @param \App\Entity\Company $parent The parent
     * @param \App\Entity\Company $child  The child
     *
     * @return bool
     */
    public function isParent(Company $parent, Company $child) : bool;

    /**
     * Gets the children recursively, by company identifier.
     *
     * @param int $companyId The company identifier
     *
     * @throws \App\Exception\AppException
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChildrenById(int $companyId) : Collection;

    /**
     * Returns a company based on its public key.
     *
     * @param string $pubKey
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Company
     */
    public function findByPubKey(string $pubKey) : Company;

    /**
     * Returns a company based on its slug.
     *
     * @param string $slug
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Company
     */
    public function findBySlug(string $slug) : Company;

    /**
     * Return companies based on its parent id.
     *
     * @param int $parentId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByParentId(int $parentId) : Collection;

    /**
     * Creates a new member for a company.
     *
     * @param \App\Entity\Company  $company  The company
     * @param \App\Entity\Identity $identity The identity
     * @param string               $role     The role
     */
    public function createNewMember(Company $company, Identity $identity, string $role) : Member;

    /**
     * Delete companies based on its parent id.
     *
     * @param int $parentId
     *
     * @return int
     */
    public function deleteByParentId(int $parentId) : int;
}
```

The example above is from the companies repository. For this specific one, the database strategy is used, so a class named `App\Repository\DBCompany` must also be created an define the methods from the interface:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Member;
use App\Entity\Identity;
use App\Entity\Role;
use App\Exception\AppException;
use Illuminate\Support\Collection;

/**
 * Database-based Company Repository Implementation.
 */
class DBCompany extends AbstractSQLDBRepository implements CompanyInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'companies';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company';

    /**
     * {@inheritdoc}
     */
    public function isParent(Company $parent, Company $child) : bool {
        if ($child->parentId === null) {
            return false;
        }

        if ($child->parentId === $parent->id) {
            return true;
        }

        return $this->isParent(
            $this->find($child->parentId),
            $child
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : Company {
        return $this->findOneBy(['public_key' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug) : Company {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function getByParentId(int $parentId) : Collection {
        return $this->findBy(['parent_id' => $parentId]);
    }

    /**
     * {@inheritdoc}
     */
    public function saveNewCompany(Company $company, Identity $owner) : Company {
        $company = parent::save($company);
        $this->createNewMember($company, $owner, Role::COMPANY_OWNER);

        return $company;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenById(int $companyId) : Collection {
        $children = $this->getByParentId($companyId);

        if ($children->isEmpty()) {
            return new Collection();
        }

        foreach ($children as $child) {
            $children = $children->merge($this->getChildrenById($child->id));
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function createNewMember(Company $company, Identity $identity, string $role) : Member {
        $query = $this->query('members', Member::class);
        $id    = $query->insertGetId(
            [
                'company_id'  => $company->id,
                'identity_id' => $identity->id,
                'role'        => $role
            ]
        );
        if ($id) {
            $member = $this->entityFactory->create(
                'Company\Member',
                [
                    'role'     => $role,
                    'company'  => $company->id,
                    'identity' => $identity->id,
                ]
            );
            $member->relations['company']  = $company;
            $member->relations['identity'] = $identity;

            return $member;
        }

        throw new AppException(sprintf('Error creating Company Member on %s', get_class($this)), 500);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = 'id') : int {
        return $this->deleteBy([$key => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId(int $parentId) : int {
        return $this->deleteBy(['parent_id' => $parentId]);
    }
}
```

Note that the `App\Repository\DBCompany` class inherits the class `App\Repository\AbstractSQLDBRepository` (so it uses the SQL database) and implements the interface `App\Repository\CompanyInterface`.

You may also create a repository using the database strategy but that uses a NoSQL database. In this case, you would inherit the class `App\Repository\AbstractNoSQLDBRepository`. Take a look in the class `App\Repository\Profile\DBRaw` and the interface `App\Repository\Profile\RawInterface` if you want an example of how to do that.

# Creating Commands

The commands are used by the command bus and processed by the handlers. Anytime you want to execute a predefined task (like create or delete a company) you may send a specific command to the command bus. This command will be processed by a handler, which implements the business logic needed to execute this task.

The command files are located in the `app/Command` directory. Each command is a separate file/class and these files/classes are grouped into directories/namespaces according to its scope. For example, we have several commands that execute tasks for companies:

* `App\Command\Company\CreateNew` is a command for creating a new company;
* `App\Command\Company\Setup` is a command for setting up a company;
* `App\Command\Company\UpdateOne` is a command for updating a company;
* `App\Command\Company\DeleteOne` is a command for deleting a company.

All these commands are in the `app/Command/Company` directory (or in the `App\Command\Company` namespace). An example of a command class code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Company Name.
     *
     * @var string
     */
    public $name;
    /**
     * Company's Parent Id.
     *
     * @var int
     */
    public $parentId;
    /**
     * Identity creating the company.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
```

In the command class, all the attributes should be filled by who is sending the command and are acessible in the handler that will process the command.

# Creating a New Handler

For more information about handlers, please read [Creating Commands](#creating-commands).

The handler files are located in the `app/Handler` directory. In order to create a new handler you need to create a new file under this directory or its subdirectories. An example code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteOne;
use App\Command\Company\Setup;
use App\Command\Company\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Entity\HandlerService;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\CompanyInterface;
use App\Repository\HandlerServiceInterface;
use App\Repository\ServiceInterface;
use App\Validator\Company as CompanyValidator;
use Defuse\Crypto\Key;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Company commands.
 */
class Company implements HandlerInterface {
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $repository;
    /**
     * Service Repository instance.
     *
     * @var \App\Repository\Service
     */
    private $serviceRepository;
    /**
     * Handler Service Repository instance.
     *
     * @var \App\Repository\HandlerService
     */
    private $handlerServiceRepository;
    /**
     * Company Validator instance.
     *
     * @var \App\Validator\Company
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Company(
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('repositoryFactory')
                    ->create('Service'),
                $container
                    ->get('repositoryFactory')
                    ->create('HandlerService'),
                $container
                    ->get('validatorFactory')
                    ->create('Company'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\CompanyInterface        $repository
     * @param \App\Repository\ServiceInterface        $serviceRepository
     * @param \App\Repository\HandlerServiceInterface $handlerServiceRepository
     * @param \App\Validator\Company                  $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        ServiceInterface $serviceRepository,
        HandlerServiceInterface $handlerServiceRepository,
        CompanyValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository                 = $repository;
        $this->serviceRepository          = $serviceRepository;
        $this->handlerServiceRepository   = $handlerServiceRepository;
        $this->validator                  = $validator;
        $this->eventFactory               = $eventFactory;
        $this->emitter                    = $emitter;
    }

    /**
     * Creates a new child Company ($command->parentId).
     *
     * @param \App\Command\Company\CreateNew $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Create\CompanyException
     *
     * @return \App\Entity\Company
     */
    public function handleCreateNew(CreateNew $command) : CompanyEntity {
        try {
            $this->validator->assertLongString($command->name);
            $this->validator->assertNullableId($command->parentId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $company = $this->repository->create(
            [
                'name'       => $command->name,
                'parent_id'  => $command->parentId,
                'created_at' => time()
            ]
        );

        $company->public_key  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $company = $this->repository->saveNewCompany($company, $command->identity);
            $event   = $this->eventFactory->create('Company\\Created', $company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\CompanyException('Error while trying to create a company', 500, $e);
        }

        return $company;
    }

    /**
     * ize a Company.
     *
     * @param \App\Command\Company\Setup $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Create\CompanyException
     *
     * @return \Illuminate\Support\Collection of handlers
     */
    public function handleSetup(Setup $command) : Collection {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $company = $this->repository->find($command->companyId);

            if ($company->parentId) {
                $handlerServices = $this->handlerServiceRepository->getByServiceCompanyId($company->parentId);
            } else {
                $handlerServices = $this->handlerServiceRepository->getAll();
            }

            // populate company services
            foreach ($handlerServices as $handlerService) {
                if ($handlerService->privacy === HandlerService::PRIVACY_PRIVATE) {
                    continue;
                }

                $service = $this->serviceRepository->create(
                    [
                    'company_id'         => $command->companyId,
                    'handler_service_id' => $handlerService->id,
                    'listens'            => $handlerService->listens
                    ]
                );
                $this->serviceRepository->upsert($service);
            }

            $event = $this->eventFactory->create('Company\\Setup', $company, $command->identity);
            $this->emitter->emit($event);

            return $handlerServices;
        } catch (\Exception $e) {
            throw new Create\CompanyException('Error while trying to create a company', 500, $e);
        }
    }

    /**
     * Updates a Company.
     *
     * @param \App\Command\Company\UpdateOne $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Update\CompanyException
     *
     * @return \App\Entity\Company
     */
    public function handleUpdateOne(UpdateOne $command) : CompanyEntity {
        try {
            $this->validator->assertId($command->company->id);
            $this->validator->assertMediumString($command->name);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $company            = $command->company;
        $company->name      = $command->name;
        $company->updatedAt = time();

        try {
            $company = $this->repository->save($company);
            $event   = $this->eventFactory->create('Company\\Updated', $company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\CompanyException('Error while trying to update a company', 500, $e);
        }

        return $company;
    }

    /**
     * Deletes a Company.
     *
     * @param \App\Command\Company\DeleteOne $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\NotFound\CompanyException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->company->id);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $rowsAffected = $this->repository->delete($command->company->id);

        if (! $rowsAffected) {
            throw new NotFound\CompanyException('No companies found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Deleted', $command->company, $command->identity);
        $this->emitter->emit($event);
    }
}
```

In the handler class, there are several methods implementing the business logic to execute commands. For example, the method `handleCreateNew` will be responsible for processing the `App\Command\Company\CreateNew` command and receives the command object as an argument.

# Creating Events

Events are represented by classes under the `App\Event` namespace. At any point in the application, an event may be *emitted* and may carry some data with it. You can detect whenever an event of specific type is emitted by implementing a event listener (see [Listening to Events](#listening-to-events)).

The event files are located in the `app/Event` directory. Each event is a separate file/class and these files/classes are grouped into directories/namespaces according to its scope. For example, we have several events for companies:

* `App\Event\Company\Created` is an event emitted after a company is created;
* `App\Event\Company\Setup` is an event emitted after a company has been setup;
* `App\Event\Company\Updated` is an event emitted after a company is updated;
* `App\Event\Company\Deleted` is an event emitted after a company is deleted.

All these events are in the `app/Event/Company` directory (or in the `App\Event\Company` namespace). An example of an event class code could be:

```
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company;

use App\Entity\Company;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company  $company
     * @param \App\Entity\Identity $identity
     *
     * @return void
     */
    public function __construct(Company $company, Identity $identity) {
        $this->company  = $company;
        $this->identity = $identity;
    }
}
```

Note that event may carry some data within its attributes. This data will be available to the event listener that listens to this event type.

# Listening to Events

The event listener files are located in the `app/Listener` directory.
