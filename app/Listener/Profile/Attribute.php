<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Entity\Company\Credential;
use App\Entity\User;
use App\Factory\Command;
use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;
use League\Tactician\CommandBus;

/**
 * Attribute Event Listener.
 */
class Attribute extends AbstractListener {
    /**
     * Candidate Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $candidateRepository;
    /**
     * Feature Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $featureRepository;
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
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Listener\Profile\Attribute(
                $repositoryFactory
                    ->create('Profile\Candidate'),
                $repositoryFactory
                    ->create('Profile\Feature'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };
    }

    /**
     * Formats a combined attribute output.
     *
     * @param string $name
     * @param array  $items
     *
     * @return string
     */
    private function formatCombination(string $name, array $items) : string {
        switch ($name) {
            case 'profilePicture':
                return $items[0];
            case 'fullName':
                $composition = [];
                if (! empty($items['firstName'])) {
                    $composition[] = $items['firstName'];
                }

                if (! empty($items['middleName'])) {
                    $composition[] = $items['middleName'];
                }

                if (! empty($items['lastName'])) {
                    $composition[] = $items['lastName'];
                }

                return implode(' ', $composition);
            case 'gender':
                $value = strtolower($items[0]);
                if (! in_array(strtolower($value), ['male', 'female'])) {
                    return '';
                }

                return ucfirst($value);
            case 'birthDate':
                if ((! empty($items['birthDay']))
                    && (! empty($items['birthMonth']))
                    && (! empty($items['birthYear']))
                ) {
                    return sprintf(
                        '%02d/%02d/%04d',
                        $items['birthDay'],
                        $items['birthMonth'],
                        $items['birthYear']
                    );
                }

                if ((! empty($items['birthDay']))
                    && (! empty($items['birthMonth']))
                ) {
                    return sprintf(
                        '%02d/%02d',
                        $items['birthDay'],
                        $items['birthMonth']
                    );
                }

                if ((! empty($items['birthMonth']))
                    && (! empty($items['birthYear']))
                ) {
                    return sprintf(
                        '%02d/%04d',
                        $items['birthMonth'],
                        $items['birthYear']
                    );
                }

                if (! empty($items['birthYear'])) {
                    return sprintf(
                        '%04d',
                        $items['birthYear']
                    );
                }

                return '';
            case 'fullAddress':
                $address = [];
                if (! empty($items['streetAddress'])) {
                    $address[] = $items['streetAddress'];
                }

                if (! empty($items['postalCode'])) {
                    $address[] = $items['postalCode'];
                }

                if (! empty($items['cityName'])) {
                    $address[] = $items['cityName'];
                }

                if (! empty($items['regionName'])) {
                    $address[] = $items['regionName'];
                }

                if (! empty($items['countryName'])) {
                    $address[] = $items['countryName'];
                }

                return ucwords(strtolower(implode(', ', $address)));
            case 'email':
                return strtolower($items[0]);
            case 'phoneNumber':
                return implode('', $items);
        }

        return '';
    }

    /**
     * Returns the best candidate (or combination) for an attribute (or compound attribute).
     *
     * @param \Illuminate\Support\Collection $candidates
     * @param \Illuminate\Support\Collection $features
     * @param array                          $attributes
     *
     * @return array
     */
    private function bestCombination(
        Collection $candidates,
        Collection $features,
        array $attributes
    ) : array {
        $sourceList = $features
            ->pluck('source')
            ->unique()
            ->all();
        $filteredFeatures = [];
        foreach ($sourceList as $source) {
            $filteredFeatures[] = $features->whereStrict('source', $source);
        }

        $filteredCandidates = [];
        foreach ($attributes as $attribute) {
            $filteredCandidates[$attribute] = $candidates->whereStrict('attribute', $attribute);
        }

        $bestCombination = [];
        $bestScore       = 0;
        foreach ($filteredFeatures as $features) {
            $probeCombination = [];
            $probeScore       = 0;

            foreach ($features as $feature) {
                $candidate = $filteredCandidates[$feature->name]
                    ->where('value', $feature->value)
                    ->first();
                if (empty($candidate)) {
                    continue;
                }

                $probeCombination[$feature->name] = $candidate->value;
                $probeScore += $candidate->support;
            }

            if ($probeScore > $bestScore) {
                $bestScore       = $probeScore;
                $bestCombination = $probeCombination;
                continue;
            }

            if ($probeScore == $bestScore) {
                if (count($probeCombination) > count($bestCombination)) {
                    $bestCombination = $probeCombination;
                }
            }
        }

        return $bestCombination;
    }

    /**
     * Creates a new attribute.
     *
     * @param \App\Entity\User               $user
     * @param \App\Entity\Company\Credential $credential
     * @param string                         $name
     * @param string                         $value
     *
     * @return void
     */
    private function createAttribute(User $user, Credential $credential, string $name, string $value) {
        if (empty($value)) {
            return;
        }

        $command = $this->commandFactory->create('Profile\Attribute\UpsertOne');
        $command
            ->setParameter('user', $user)
            ->setParameter('credential', $credential)
            ->setParameter('name', $name)
            ->setParameter('value', $value);
        $this->commandBus->handle($command);
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $candidateRepository
     * @param \App\Repository\RepositoryInterface $featureRepository
     * @param \League\Tactician\CommandBus        $commandBus
     * @param \App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $candidateRepository,
        RepositoryInterface $featureRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->candidateRepository = $candidateRepository;
        $this->featureRepository   = $featureRepository;
        $this->commandBus          = $commandBus;
        $this->commandFactory      = $commandFactory;
    }

    /**
     * Handles events that trigger attribute filtering.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $command = $this->commandFactory->create('Profile\Attribute\DeleteAll');
        $command
            ->setParameter('user', $event->user)
            ->setParameter('credential', $event->credential)
            ->setParameter('queryParams', []);
        $this->commandBus->handle($command);

        $compositions = [
            'profilePicture' => [
                'profilePicture'
            ],
            'fullName' => [
                'firstName',
                'middleName',
                'lastName'
            ],
            'gender' => [
                'gender'
            ],
            'birthDate' => [
                'birthDay',
                'birthMonth',
                'birthYear'
            ],
            'fullAddress' => [
                'streetAddress',
                'postalCode',
                'cityName',
                'regionName',
                'countryName'
            ],
            'email' => [
                'email'
            ],
            'phoneNumber' => [
                'phoneCountryCode',
                'phoneNumber'
            ]
        ];

        $attributesToCreate = [];

        foreach ($compositions as $composition => $attributes) {
            $candidates = $this->candidateRepository->getAllByUserIdAndAttributeNames(
                $event->user->id,
                $attributes
            );

            if ($candidates->isEmpty()) {
                continue;
            }

            if (count($attributes) == 1) {
                // single attribute
                $attributesToCreate[] = [
                    'user_id' => $event->user->id,
                    'name'    => $attributes[0],
                    'value'   => $candidates->sortBy('support')->last()->value
                ];
                continue;
            }

            // attribute composition
            $features = $this->featureRepository->getByUserIdAndNames(
                $event->user->id,
                $attributes
            );

            $combination = $this->bestCombination(
                $candidates,
                $features,
                $attributes
            );

            foreach ($combination as $attribute => $value) {
                if (empty($value)) {
                    continue;
                }

                $attributesToCreate[] = [
                    'user_id' => $event->user->id,
                    'name'    => $attribute,
                    'value'   => $value
                ];
            }

            $attributesToCreate[] = [
                'user_id' => $event->user->id,
                'name'    => $composition,
                'value'   => $this->formatCombination($composition, $combination)
            ];
        }

        $command = $this->commandFactory->create('Profile\Attribute\UpsertBulk');
        $command
            ->setParameter('user', $event->user)
            ->setParameter('credential', $event->credential)
            ->setParameter('attributes', $attributesToCreate);
        $this->commandBus->handle($command);
    }
}
