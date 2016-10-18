<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Entity\Profile\Candidate;
use App\Entity\Profile\Feature;
use App\Entity\User;
use App\Factory\Command;
use App\Listener;
use App\Listener\AbstractListener;
use App\Repository\Profile\CandidateInterface;
use App\Repository\Profile\FeatureInterface;
use Illuminate\Support\Collection;
use League\Event\EventInterface;
use League\Tactician\CommandBus;

/**
 * Attribute Event Listener.
 */
class AttributeListener extends AbstractListener {
    /**
     * Candidate Repository instance.
     *
     * @var \App\Repository\Profile\Candidate
     */
    private $candidateRepository;
    /**
     * Feature Repository instance.
     *
     * @var \App\Repository\Profile\Feature
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
     * Formats a combined attribute output.
     *
     * @param string $name
     * @param array  $items
     *
     * @return string
     */
    private function formatCombination(string $name, array $items) : string {
        switch ($name) {
            case 'full-name':
                return implode(
                    ' ',
                    array_filter(
                        $items,
                        function ($item) {
                            return ! empty($item);
                        }
                    )
                );
            case 'gender':
                $value = strtolower($item[0]);
                if (! in_array(strtolower($value), ['male', 'female'])) {
                    return '';
                }

                return ucfirst($value);
            case 'birth-date':
                if ((! empty($items['birth-day']))
                    && (! empty($items['birth-month']))
                    && (! empty($items['birth-year']))
                ) {
                    return sprintf(
                        '%02d/%02d/%04d',
                        $items['birth-day'],
                        $items['birth-month'],
                        $items['birth-year']
                    );
                }

                if ((! empty($items['birth-day']))
                    && (! empty($items['birth-month']))
                ) {
                    return sprintf(
                        '%02d/%02d',
                        $items['birth-day'],
                        $items['birth-month']
                    );
                }

                if ((! empty($items['birth-month']))
                    && (! empty($items['birth-year']))
                ) {
                    return sprintf(
                        '%02d/%04d',
                        $items['birth-month'],
                        $items['birth-year']
                    );
                }

                if (! empty($items['birth-year'])) {
                    return sprintf(
                        '%04d',
                        $items['birth-year']
                    );
                }

                return '';
            case 'full-address':
                return ucwords(
                    strtolower(
                        implode(
                            ', ',
                            array_filter(
                                $items,
                                function ($element) {
                                    return ! empty($element);
                                }
                            )
                        )
                    )
                );
            case 'email':
                return strtolower($items[0]);
            case 'phone':
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
                $candidate          = $filteredCandidates[$feature->name]->whereStrict('value', $feature->value)->first();
                $probeCombination[] = $candidate->value;
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
     * @param \App\Entity\User $user
     * @param string           $name
     * @param string           $value
     *
     * @return void
     */
    private function createAttribute(User $user, string $name, string $value) {
        if (empty($value)) {
            return;
        }

        $command = $this->commandFactory->create('Profile\\Attribute\\CreateNew');
        $command
            ->setParameter('user', $user)
            ->setParameter('name', $name)
            ->setParameter('value', $value);
        $this->commandBus->handle($command);
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\CandidateInterface $candidateRepository
     * @param \App\Repository\FeatureInterface   $featureRepository
     * @param \League\Tactician\CommandBus       $commandBus
     * @param \App\Factory\Command               $commandFactory
     *
     * @return void
     */
    public function __construct(
        CandidateInterface $candidateRepository,
        FeatureInterface $featureRepository,
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
        $command = $this->commandFactory->create('Profile\\Attribute\\DeleteAll');
        $command
            ->setParameter('user', $event->user)
            ->setParameter('queryParams', []);
        $this->commandBus->handle($command);

        $compositions = [
            'full-name'    => [
                'first-name',
                'middle-name',
                'last-name'
            ],
            'gender'       => [
                'gender'
            ],
            'birth-date'   => [
                'birth-day',
                'birth-month',
                'birth-year'
            ],
            'full-address' => [
                'street-address',
                'postal-code',
                'city-name',
                'region-name',
                'country-name'
            ],
            'email'        => [
                'email'
            ],
            'phone'        => [
                'phone-country-code',
                'phone-number'
            ]
        ];

        foreach ($compositions as $composition => $attributes) {
            $candidates = $this->candidateRepository->getAllByUserIdAndAttributeNames(
                $event->user->id,
                [
                    'attribute' => implode(',', $attributes)
                ]
            );

            if ($candidates->isEmpty()) {
                continue;
            }

            if (count($attributes) == 1) {
                // single attribute
                $combination = [
                    $attributes[0] => $candidates
                        ->sortBy('support')
                        ->last()
                        ->value
                ];
                $this->createAttribute(
                    $event->user,
                    $attributes[0],
                    $candidates
                        ->sortBy('support')
                        ->last()
                        ->value
                );
                continue;
            }

            // attribute composition
            $features = $this->featureRepository->getByUserId(
                $event->user->id,
                [
                    'name' => implode(',', $attributes)
                ]
            );

            $combination = $this->bestCombination(
                $candidates,
                $features,
                $attributes
            );

            foreach ($attributes as $attribute) {
                if (empty($combination[$attribute])) {
                    continue;
                }

                $this->createAttribute($event->user, $attribute, $combination[$attribute]);
            }

            $this->createAttribute(
                $event->user,
                $composition,
                $this->formatCombination($composition, $combination)
            );
        }
    }
}
