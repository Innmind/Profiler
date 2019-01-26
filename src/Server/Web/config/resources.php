<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web;

use Innmind\Rest\Server\{
    Definition\Directory,
    Definition\HttpResource,
    Definition\Gateway,
    Definition\Identity,
    Definition\Property,
    Definition\Access,
    Definition\AllowedLink,
    Definition\Type\StringType,
    Definition\Type\BoolType,
    Definition\Type\PointInTimeType,
    Action,
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Immutable\Set;

return function(TimeContinuumInterface $clock): Directory {
    return Directory::of(
        'api',
        Set::of(
            Directory::class,
            Directory::of(
                'section',
                Set::of(Directory::class),
                new HttpResource(
                    'request_response',
                    new Gateway('request_response'),
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
                            'response',
                            new StringType,
                            new Access(Access::UPDATE)
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create(), Action::update()),
                    Set::of(
                        AllowedLink::class,
                        new AllowedLink('section-of', 'api.profile')
                    )
                ),
                new HttpResource(
                    'exception',
                    new Gateway('exception'),
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
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create()),
                    Set::of(
                        AllowedLink::class,
                        new AllowedLink('section-of', 'api.profile')
                    )
                ),
                new HttpResource(
                    'app_graph',
                    new Gateway('app_graph'),
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
                        )
                    ),
                    Set::of(Action::class, Action::get(), Action::create()),
                    Set::of(
                        AllowedLink::class,
                        new AllowedLink('section-of', 'api.profile')
                    )
                )
            )
        ),
        new HttpResource(
            'profile',
            new Gateway('profile'),
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
                )
            ),
            Set::of(Action::class, Action::get(), Action::create(), Action::update())
        )
    );
};
