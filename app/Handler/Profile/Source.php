<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Source\CreateNew;
use App\Command\Profile\Source\DeleteAll;
use App\Command\Profile\Source\DeleteOne;
use App\Command\Profile\Source\GetOne;
use App\Command\Profile\Source\UpdateOne;
use App\Entity\Profile\Source as SourceEntity;
use App\Exception\AppException;
use App\Exception\Create;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Command as CommandFactory;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Source as SourceValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Source commands.
 */
class Source implements HandlerInterface {
    /**
     * Source Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Process Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $processRepository;
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $companyRepository;
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $settingRepository;
    /**
     * Identity Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $identityRepository;
    /**
     * Source Validator instance.
     *
     * @var \App\Validator\Profile\Source
     */
    private $validator;
    /**
     * File System instance.
     *
     * @var \League\Flysystem\Filesystem
     */
    private $fileSystem;
    /**
     * Command factory instance.
     *
     * @var \App\Factory\Command
     */
    private $commandFactory;
    /**
     * Command bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
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
     * Generates a file path based on source details.
     *
     * @param int    $companyId
     * @param int    $credentialId
     * @param int    $userId
     * @param int    $sourceId
     * @param string $fileExtension
     *
     * @return string
     */
    private function filePath(
        int $companyId,
        int $credentialId,
        int $userId,
        int $sourceId,
        string $fileExtension
    ) : string {
        return sprintf(
            '%s/%s/%s/%s.%s',
            $companyId,
            $credentialId,
            $userId,
            $sourceId,
            $fileExtension
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');
            $fileSystem        = $container->get('fileSystem');

            return new \App\Handler\Profile\Source(
                $repositoryFactory
                    ->create('Profile\Source'),
                $repositoryFactory
                    ->create('Profile\Process'),
                $repositoryFactory
                    ->create('Identity'),
                $repositoryFactory
                    ->create('Company\Setting'),
                $repositoryFactory
                    ->create('Company'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Source'),
                $fileSystem('source'),
                $container
                    ->get('commandFactory'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class Constructor.
     *
     * @param \App\Repository\RepositoryInterface $repository         The repository
     * @param \App\Repository\RepositoryInterface $processRepository  The process    repository
     * @param \App\Repository\RepositoryInterface $identityRepository The identity   repository
     * @param \App\Repository\RepositoryInterface $settingRepository  The setting    repository
     * @param \App\Repository\RepositoryInterface $companyRepository  The company    repository
     * @param \App\Validator\Profile\Source       $validator          The validator
     * @param \League\Flysystem\Filesystem        $fileSystem
     * @param \App\Factory\Command                $commandFactory     The command    factory
     * @param \League\Tactician\CommandBus        $commandBus         The command    bus        instance.
     * @param \App\Factory\Event                  $eventFactory       The event      factory
     * @param \League\Event\Emitter               $emitter            The emitter
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositoryInterface $processRepository,
        RepositoryInterface $identityRepository,
        RepositoryInterface $settingRepository,
        RepositoryInterface $companyRepository,
        SourceValidator $validator,
        Filesystem $fileSystem,
        CommandFactory $commandFactory,
        CommandBus $commandBus,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository         = $repository;
        $this->processRepository  = $processRepository;
        $this->identityRepository = $identityRepository;
        $this->settingRepository  = $settingRepository;
        $this->companyRepository  = $companyRepository;
        $this->validator          = $validator;
        $this->fileSystem         = $fileSystem;
        $this->commandFactory     = $commandFactory;
        $this->commandBus         = $commandBus;
        $this->eventFactory       = $eventFactory;
        $this->emitter            = $emitter;
    }

    /**
     * Creates a new source to a user ($command->userId).
     *
     * @param \App\Command\Profile\Source\CreateNew $command
     *
     * @throws \App\Exception\Validate\Profile\SourceException
     * @throws \App\Exception\Create\Profile\SourceException
     *
     * @return \App\Entity\Profile\Source
     */
    public function handleCreateNew(CreateNew $command) : SourceEntity {
        try {
            $this->validator->assertShortName($command->name, 'name');
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertCredential($command->credential, 'credential');
            $this->validator->assertId($command->user->id, 'userId');
            $this->validator->assertIpAddr($command->ipaddr, 'ipaddr');
            $this->validator->assertArray($command->tags, 'tags');
            foreach ($command->tags as $key => $value) {
                $this->validator->assertString($key, sprintf('tags.%s', $key));
            }

            if (isset($command->tags['phone'])) {
                $this->validator->assertPhone($command->tags['phone'], 'tags.phone');
            }

            if (isset($command->tags['email'])) {
                $this->validator->assertEmail($command->tags['email'], 'tags.email');
            }
        } catch (ValidationException $exception) {
            throw new Validate\Profile\SourceException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $company = $this->companyRepository->find($command->credential->companyId);

        // File
        $sendFile = false;
        if (isset($command->tags['contents'])) {
            if (! isset($command->tags['extension'])) {
                throw new Validate\Profile\SourceException('Inline file upload must contain an extension field', 400);
            }

            $fileExtension = $command->tags['extension'];

            if (strlen($command->tags['contents']) > 4194304) {
                throw new Validate\Profile\SourceException('Inline file upload cannot exceed 4 MB.', 400);
            }

            $fileContents = base64_decode($command->tags['contents'], true);
            if ($fileContents === false) {
                throw new Validate\Profile\SourceException('Invalid file uploaded, content must be base64 encoded.', 400);
            }

            unset($command->tags['contents']);
            $command->tags['file_size'] = strlen($fileContents);
            $command->tags['file_sha1'] = sha1($fileContents);

            $sendFile = true;
        }

        // OTP check
        $sendOTP = false;
        if (isset($command->tags['otp_check'])) {
            $this->validator->validateFlag($command->tags['otp_check']);

            if ((! isset($command->tags['email'])) && (! isset($command->tags['phone']))) {
                throw new Validate\Profile\SourceException('OTP Checks must have "phone" or "email" fields.', 400);
            }

            $command->tags['otp_code']     = random_int(100000, 999999);
            $command->tags['otp_verified'] = false;
            $command->tags['otp_attempts'] = 0;
            $sendOTP                       = true;
        }

        // CRA check
        $sendCRA = false;
        if (isset($command->tags['cra_check'])) {
            $this->validator->validateFlag($command->tags['cra_check'], 'tags.cra_check');

            // Reference code for tracking the CRA Result
            $command->tags['cra_reference'] = md5(
                sprintf(
                    'Veridu:idOS:%s',
                    microtime()
                )
            );
            $sendCRA = true;
        }

        $source = $this->repository->create(
            [
                'name'       => $command->name,
                'user_id'    => $command->user->id,
                'tags'       => $command->tags,
                'created_at' => time(),
                'ipaddr'     => $command->ipaddr
            ]
        );

        // if it is a social media Source
        if (isset($command->tags['profile_id'], $command->tags['access_token'])) {
            $keys = $this->settingRepository->getSourceTokens(
                $command->credential->companyId,
                $command->credential->public,
                $command->name
            );
            $key  = $keys->where('property', sprintf('%s.%s.key', $command->credential->public, $command->name));

            // main variables
            $appKey     = (! empty($key->first()->value)) ? $key->first()->value : 'Veridu';
            $profileId  = $command->tags['profile_id'];
            $sourceName = $command->name;

            try {
                $identity = $this->identityRepository->findOneBySourceNameAndProfileId(
                    $sourceName,
                    $profileId,
                    $appKey
                );
            } catch (NotFound $exception) {
                $identityCommand = $this->commandFactory->create('Identity\CreateNew');
                $identityCommand
                    ->setParameter('sourceName', $sourceName)
                    ->setParameter('profileId', $profileId)
                    ->setParameter('appKey', $appKey);

                $identity = $this->commandBus->handle($identityCommand);
            }
        }

        try {
            $source = $this->repository->save($source);
        } catch (\Exception $exception) {
            throw new Create\Profile\SourceException('Error while trying to create a source', 500, $exception);
        }

        $event = $this->eventFactory->create(
            'Profile\Source\Created',
            $source,
            $command->user,
            $command->ipaddr,
            $command->credential
        );

        try {
            $processEntity = $this->processRepository->create(
                [
                'name'      => 'idos:verification',
                'user_id'   => $command->user->id,
                'source_id' => $source->id,
                'event'     => (string) $event,
                ]
            );
            $processEntity = $this->processRepository->save($processEntity);
        } catch (\Exception $exception) {
            throw new Create\Profile\SourceException('Error while trying to create a process for the Source', 500, $exception);
        }

        $event->process = $processEntity;
        $this->emitter->emit($event);

        if ($sendFile) {
            $filePath = $this->filePath(
                $company->id,
                $command->credential->id,
                $command->user->id,
                $source->id,
                $fileExtension
            );
            $this->fileSystem->write(
                $filePath,
                $fileContents
            );

            $this->emitter->emit(
                $this->eventFactory->create(
                    'Profile\Source\File',
                    $source,
                    $command->user,
                    $command->credential,
                    $company,
                    $processEntity,
                    $filePath,
                    $fileContents,
                    $fileExtension,
                    $command->ipaddr
                )
            );
        }

        if ($sendOTP) {
            $this->emitter->emit(
                $this->eventFactory->create(
                    'Profile\Source\OTP',
                    $source,
                    $command->user,
                    $command->credential,
                    $company,
                    $processEntity,
                    $command->ipaddr
                )
            );
        }

        if ($sendCRA) {
            $this->emitter->emit(
                $this->eventFactory->create(
                    'Profile\Source\CRA',
                    $command->user,
                    $source,
                    $command->ipaddr,
                    $command->credential
                )
            );
        }

        return $source;
    }

    /**
     * Gets a source.
     *
     * @param \App\Command\Profile\Source\GetOne $command
     *
     * @throws \App\Exception\AppException
     * @throws \App\Exception\NotFound\Profile\SourceException
     * @throws \App\Exception\Validate\Profile\SourceException
     *
     * @return \App\Entity\Profile\Source
     */
    public function handleGetOne(GetOne $command) : SourceEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertCredential($command->credential, 'credential');
            $this->validator->assertId($command->sourceId, 'sourceId');
            $this->validator->assertBoolean($command->includePicture, 'includePicture');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\SourceException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $source = $this->repository->findOne($command->sourceId, $command->user->id);

        if ((property_exists($source->tags, 'file_sha1')) && ($command->includePicture)) {
            $filePath = $this->filePath(
                $command->credential->companyId,
                $command->credential->id,
                $command->user->id,
                $source->id,
                $source->tags->extension
            );

            if (! $this->fileSystem->has($filePath)) {
                throw new NotFound\Profile\SourceException('Picture could not be found');
            }

            $tags           = $source->tags;
            $tags->contents = base64_encode($this->fileSystem->read($filePath));
            $source->tags   = $tags;
        }

        return $source;
    }

    /**
     * Updates a source.
     *
     * @param \App\Command\Profile\Source\UpdateOne $command
     *
     * @throws \App\Exception\Validate\Profile\SourceException
     * @throws \App\Exception\NotAllowed\Profile\SourceException
     * @throws \App\Exception\AppException
     * @throws \App\Exception\Update\Profile\SourceException
     *
     * @return \App\Entity\Profile\Source
     */
    public function handleUpdateOne(UpdateOne $command) : SourceEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertCredential($command->credential, 'credential');
            $this->validator->assertId($command->user->id, 'userId');
            $this->validator->assertSource($command->source, 'source');
            $this->validator->assertId($command->source->id, 'sourceId');
            $this->validator->assertIpAddr($command->ipaddr, 'ipaddr');

            $this->validator->assertArray($command->tags, 'tags');
            foreach ($command->tags as $key => $value) {
                $this->validator->assertString($key, sprintf('tags.%s', $key));
            }
        } catch (ValidationException $exception) {
            throw new Validate\Profile\SourceException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $source     = $command->source;
        $serialized = $source->serialize();

        $tags        = json_decode($serialized['tags']);
        $commandTags = (object) $command->tags;

        if ((isset($tags->otp_voided))) {
            throw new NotAllowed\Profile\SourceException('Too many tries', 500);
        }

        if (isset($command->otpCode)) {
            try {
                $this->validator->assertOTPCode($command->otpCode, 'otpCode');
            } catch (ValidationException $exception) {
                throw new Validate\Profile\SourceException(
                    $exception->getFullMessage(),
                    400,
                    $exception
                );
            }
        }

        // OTP check must only work on valid sources (i.e. not voided and unverified)
        if ($tags
            && property_exists($tags, 'otp_check')
            && ((! property_exists($tags, 'otp_verified'))
            || (! $tags->otp_verified))
        ) {
            // code verification
            if (property_exists($tags, 'otp_code')
                && property_exists($commandTags, 'otp_code')
                && ($tags->otp_code === $commandTags->otp_code)
            ) {
                $tags->otp_verified = true;
            }

            // attempt verification
            if (! property_exists($tags, 'otp_attempts')) {
                $tags->otp_attempts = 0;
            }

            $tags->otp_attempts++;

            // after 3 failed attempts, the otp is voided (avoids brute-force validation)
            if (($tags->otp_attempts > 2)
                && ((! property_exists($tags, 'otp_verified'))
                || (property_exists($tags, 'otp_verified')
                && ! $tags->otp_verified))
            ) {
                $tags->otp_voided = true;
                $source->tags     = $tags;
                $source           = $this->repository->save($source);

                throw new AppException('Too many tries.', 403);
            }
        }

        foreach ($command->tags as $key => $value) {
            if (substr_compare($key, 'otp_', 0, 4) === 0) {
                continue;
            }

            $tags->$key = $value;
        }

        $source->tags      = $tags;
        $source->updatedAt = time();

        try {
            $source = $this->repository->save($source);
            $this->emitter->emit(
                $this->eventFactory->create(
                    'Profile\Source\Updated',
                    $command->user,
                    $source,
                    $command->ipaddr,
                    $command->credential
                )
            );
        } catch (\Exception $exception) {
            throw new Update\Profile\SourceException('Error while trying to update a source', 500, $exception);
        }

        return $source;
    }

    /**
     * Deletes a source ($command->sourceId) from a user.
     *
     * @param \App\Command\Profile\Source\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Profile\SourceException
     * @throws \App\Exception\NotFound\Profile\SourceException
     *
     * @see \App\Repository\DBSource::delete
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertId($command->user->id, 'userId');
            $this->validator->assertSource($command->source, 'source');
            $this->validator->assertId($command->source->id, 'sourceId');
            $this->validator->assertIpAddr($command->ipaddr, 'ipaddr');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\SourceException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $rowsAffected = $this->repository->delete($command->source->id);

        if (! $rowsAffected) {
            throw new NotFound\Profile\SourceException('No sources found for deletion', 404);
        }

        $this->emitter->emit(
            $this->eventFactory->create(
                'Profile\Source\Deleted',
                $command->user,
                $command->source,
                $command->ipaddr,
                $command->credential
            )
        );
    }

    /**
     * Deletes all sources from a user ($command->userId).
     *
     * @param \App\Command\Profile\Source\DeleteAll $command
     *
     * @throws \App\Exception\Validate\Profile\SourceException
     *
     * @see \App\Repository\DBSource::getAllByUserId
     * @see \App\Repository\DBSource::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertId($command->user->id, 'userId');
            $this->validator->assertIpAddr($command->ipaddr, 'ipaddr');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\SourceException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $sources = $this->repository->getByUserId($command->user->id);
        if ($sources->isEmpty()) {
            return 0;
        }

        $rowsAffected = $this->repository->deleteByUserId($command->user->id);
        if ($rowsAffected) {
            $this->emitter->emit(
                $this->eventFactory->create(
                    'Profile\Source\DeletedMulti',
                    $command->user,
                    $sources,
                    $command->ipaddr,
                    $command->credential
                )
            );

            return $rowsAffected;
        }

        return 0;
    }
}
