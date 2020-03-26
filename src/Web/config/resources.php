<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Domain\Entity;
use Innmind\Rest\Server\{
    Definition\Directory,
    Definition\HttpResource,
    Definition\Gateway,
    Definition\Identity,
    Definition\Property,
    Definition\Access,
    Definition\Type\StringType,
    Definition\Type\BoolType,
    Definition\Type\SetType,
    Definition\Type\PointInTimeType,
    Action,
};
use Innmind\TimeContinuum\Clock;
use Innmind\Immutable\Set;

return function(Clock $clock): Directory {
    return Directory::of(
        'api',
        Set::of(
            Directory::class,
            Directory::of(
                'section',
                Set::of(
                    Directory::class,
                    Directory::of(
                        'remote',
                        Set::of(Directory::class),
                        new HttpResource(
                            'http',
                            new Gateway(Entity\Remote\Http::class),
                            new Identity('uuid'),
                            Set::of(
                                Property::class,
                                Property::required(
                                    'uuid',
                                    new StringType,
                                    new Access(Access::READ)
                                ),
                                Property::required(
                                    'profile',
                                    new StringType,
                                    new Access(Access::CREATE)
                                ),
                                Property::required(
                                    'request',
                                    new StringType,
                                    new Access(Access::CREATE, Access::UPDATE)
                                ),
                                Property::required(
                                    'response',
                                    new StringType,
                                    new Access(Access::CREATE, Access::UPDATE)
                                )
                            )
                        ),
                        new HttpResource(
                            'processes',
                            new Gateway(Entity\Remote\Processes::class),
                            new Identity('uuid'),
                            Set::of(
                                Property::class,
                                Property::required(
                                    'uuid',
                                    new StringType,
                                    new Access(Access::READ)
                                ),
                                Property::required(
                                    'processes',
                                    new SetType('string', new StringType),
                                    new Access(Access::CREATE)
                                ),
                                Property::required(
                                    'profile',
                                    new StringType,
                                    new Access(Access::CREATE)
                                )
                            ),
                            Set::of(Action::class, Action::get(), Action::create())
                        )
                    )
                ),
                new HttpResource(
                    'http',
                    new Gateway(Entity\Http::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'request',
                            new StringType,
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'response',
                            new StringType,
                            new Access(Access::UPDATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create(), Action::update())
                ),
                new HttpResource(
                    'exception',
                    new Gateway(Entity\Exception::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'graph',
                            new StringType,
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create())
                ),
                new HttpResource(
                    'app_graph',
                    new Gateway(Entity\AppGraph::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'graph',
                            new StringType,
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create())
                ),
                new HttpResource(
                    'call_graph',
                    new Gateway(Entity\CallGraph::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'graph',
                            new StringType,
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create())
                ),
                new HttpResource(
                    'environment',
                    new Gateway(Entity\Environment::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'pairs',
                            new SetType('string', new StringType),
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create())
                ),
                new HttpResource(
                    'processes',
                    new Gateway(Entity\Processes::class),
                    new Identity('uuid'),
                    Set::of(
                        Property::class,
                        Property::required(
                            'uuid',
                            new StringType,
                            new Access(Access::READ)
                        ),
                        Property::required(
                            'processes',
                            new SetType('string', new StringType),
                            new Access(Access::CREATE)
                        ),
                        Property::required(
                            'profile',
                            new StringType,
                            new Access(Access::CREATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create())
                )
            )
        ),
        new HttpResource(
            'profile',
            new Gateway(Entity\Profile::class),
            new Identity('uuid'),
            Set::of(
                Property::class,
                Property::required(
                    'uuid',
                    new StringType,
                    new Access(Access::READ)
                ),
                Property::required(
                    'name',
                    new StringType,
                    new Access(Access::CREATE)
                ),
                Property::required(
                    'started_at',
                    new PointInTimeType($clock),
                    new Access(Access::CREATE)
                ),
                Property::required(
                    'success',
                    new BoolType,
                    new Access(Access::UPDATE)
                ),
                Property::required(
                    'exit',
                    new StringType,
                    new Access(Access::UPDATE)
                )
            ),
            Set::of(Action::class, Action::get(), Action::create(), Action::update())
        )
    );
};
