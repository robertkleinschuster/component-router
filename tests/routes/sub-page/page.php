<?php

declare(strict_types=1);

return page(
    view: fn() => 'sub page',
    meta: meta(
        title: 'sub page title',
        description: 'sub page description'
    ),
    model: (object)[
        'heading' => 'sub page heading'
    ]
);
