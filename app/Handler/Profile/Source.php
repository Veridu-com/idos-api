<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Source\CreateNew;
use App\Command\Profile\Source\DeleteAll;
use App\Command\Profile\Source\DeleteOne;
use App\Command\Profile\Source\UpdateOne;
use App\Entity\Profile\Source as SourceEntity;
use App\Exception\AppException;
use App\Exception\Create;
use App\Exception\NotAllowed;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\SourceInterface;
use App\Validator\Profile\Source as SourceValidator;
use Interop\Container\ContainerINterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Source commands.
 */
class Source implements HandlerInterface {
    /**
     * Source Repository instance.
     *
     * @var App\Repository\Profile\SourceInterface
     */
    private $repository;
    /**
     * Source Validator instance.
     *
     * @var App\Validator\Profile\Source
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
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
            return new \App\Handler\Profile\Source(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Source'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Source'),
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
     * @param App\Repository\SourceInterface $repository
     * @param App\Validator\Source           $validator
     * @param App\Factory\Event              $eventFactory
     * @param \League\Event\Emitter          $emitter
     *
     * @return void
     */
    public function __construct(
        SourceInterface $repository,
        SourceValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new source to a user ($command->userId).
     *
     * @param App\Command\Profile\Source\CreateNew $command
     *
     * @throws App\Exception\Validate\SourceException
     * @throws App\Exception\Create\SourceException
     *
     * @return App\Entity\Source
     */
    public function handleCreateNew(CreateNew $command) : SourceEntity {
        try {
            $this->validator->assertShortName($command->name);
            $this->validator->assertUser($command->user);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertId($command->user->id);
            $this->validator->assertIpAddr($command->ipaddr);
        } catch (ValidationException $e) {
            throw new Validate\Profile\SourceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        // OTP check
        $sendOTP = false;
        if ((isset($command->tags['otp_check']))
            && ($this->validator->validateFlag($command->tags['otp_check']))
        ) {
            $command->tags['otp_code']     = mt_rand(100000, 999999);
            $command->tags['otp_verified'] = false;
            $command->tags['otp_attempts'] = 0;
            $sendOTP                       = true;
        }

        // CRA check
        $sendCRA = false;
        if ((isset($command->tags['cra_check']))
            && ($this->validator->flagValue($command->tags['cra_check']))
        ) {
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

        try {
            $source = $this->repository->save($source);
        } catch (\Exception $e) {
            throw new Create\Profile\SourceException('Error while trying to create a setting', 500, $e);
        }

        $this->emitter->emit($this->eventFactory->create('Profile\\Source\\Created', $source, $command->user, $command->credential, $command->ipaddr));

        if ($sendOTP) {
            $this->emitter->emit($this->eventFactory->create('Profile\\Source\\OTP', $command->user, $source, $command->ipaddr));
        }

        if ($sendCRA) {
            $this->emitter->emit($this->eventFactory->create('Profile\\Source\\CRA', $command->user, $source, $command->ipaddr));
        }

        return $source;
    }

    /**
     * Updates a source.
     *
     * @param App\Command\Profile\Source\UpdateOne $command
     *
     * @throws App\Exception\Validate\SourceException
     * @throws App\Exception\NotAllowed\SourceException
     * @throws App\Exception\AppException
     * @throws App\Exception\Update\SourceException
     *
     * @return App\Entity\Source
     */
    public function handleUpdateOne(UpdateOne $command) : SourceEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertSource($command->source);
            $this->validator->assertId($command->source->id);
            $this->validator->assertIpAddr($command->ipaddr);
        } catch (ValidationException $e) {
            throw new Validate\Profile\SourceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $source     = $command->source;
        $serialized = $source->serialize();

        $tags = json_decode($serialized['tags']);

        if ((isset($tags->otp_voided))) {
            throw new NotAllowed\Profile\SourceException('Too many tries', 500, $e);
        }

        if (isset($command->otpCode)) {
            try {
                $this->validator->assertOTPCode($command->otpCode);
            } catch (ValidationException $e) {
                throw new Validate\Profile\SourceException(
                    $e->getFullMessage(),
                    400,
                    $e
                );
            }
        }

        // OTP check must only work on valid sources (i.e. not voided and unverified)
        if ($tags
            && property_exists($tags, 'otp_check')
            && (empty($tags->otp_verified))
        ) {
            // code verification
            if ((isset($tags->otp_code))
                && ($tags->otp_code === $command->otpCode)
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

        $source->tags      = $tags;
        $source->updatedAt = time();

        try {
            $source = $this->repository->save($source);
            $this->emitter->emit($this->eventFactory->create('Profile\\Source\\Updated', $command->user, $source, $command->ipaddr));
        } catch (\Exception $e) {
            throw new Update\Profile\SourceException('Error while trying to update a source', 500, $e);
        }

        return $source;
    }

    /**
     * Deletes a source ($command->sourceId) from a user.
     *
     * @param App\Command\Profile\Source\DeleteOne $command
     *
     * @throws App\Exception\Validate\SourceException
     * @throws App\Exception\NotFound\SourceException
     *
     * @see App\Repository\DBSource::delete
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertSource($command->source);
            $this->validator->assertId($command->source->id);
            $this->validator->assertIpAddr($command->ipaddr);
        } catch (ValidationException $e) {
            throw new Validate\Profile\SourceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $rowsAffected = $this->repository->delete($command->source->id);

        if (! $rowsAffected) {
            throw new NotFound\Profile\SourceException('No sources found for deletion', 404);
        }

        $this->emitter->emit($this->eventFactory->create('Profile\\Source\\Deleted', $command->user, $command->source, $command->ipaddr));
    }

    /**
     * Deletes all sources from a user ($command->userId).
     *
     * @param App\Command\Profile\Source\DeleteAll $command
     *
     * @throws App\Exception\Validate\SourceException
     *
     * @see App\Repository\DBSource::getAllByUserId
     * @see App\Repository\DBSource::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertIpAddr($command->ipaddr);
        } catch (ValidationException $e) {
            throw new Validate\Profile\SourceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $sources = $this->repository->getAllByUserId($command->user->id);
        $deleted = $this->repository->deleteByUserId($command->user->id);

        $this->emitter->emit($this->eventFactory->create('Profile\\Source\\DeletedMulti', $command->user, $sources, $command->ipaddr));

        return $deleted;
    }
}
