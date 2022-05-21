<?php

Mock::api('/', 'Hello World', 200);

Mock::api('/array', [
    'message' => 'Hello World'
]);

Mock::api('/closure', function(){
    msleep(300);
    Mock::setCode(302);
    Mock::setHeader('Location: https://github.com');
    return [
        'message' => 'Hello World'
    ];
});

Mock::api('/search/*', [
    'list' => [
        ['title' => 'Hello', 'text' => 'Some text...'],
        ['title' => 'Hi', 'text' => 'Some text...'],
        ['title' => 'Bye', 'text' => 'Some text...'],
    ]
]);
