<?php
/**
 * This file is part of the prooph/standard-projections.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\StandardProjections;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Projection\ProjectionOptions;

class MessageNameStreamProjectionRunner
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var ProjectionOptions|null
     */
    private $projectionOptions;

    public function __construct(EventStore $eventStore, ProjectionOptions $projectionOptions = null)
    {
        $this->eventStore = $eventStore;
        $this->projectionOptions = $projectionOptions;
    }

    public function __invoke(bool $keepRunning = true): void
    {
        $this->eventStore
            ->createProjection('$by_message_name', $this->projectionOptions)
            ->fromAll()
            ->whenAny(function ($state, Message $event): void {
                $messageName = $event->messageName();

                $this->linkTo('$mn-' . $messageName, $event);
            })
            ->run($keepRunning);
    }
}
