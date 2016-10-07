<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Panel address
    |--------------------------------------------------------------------------
    |
    | The URL where the panel is going to live.
    |
    */

    'url' => '/panel',

    /*
    |--------------------------------------------------------------------------
    | Panel name
    |--------------------------------------------------------------------------
    |
    | The panel title.
    |
    */

    'title'         => 'Panel',
    'title_prepend' => '',

    /*
    |--------------------------------------------------------------------------
    | Pagination items
    |--------------------------------------------------------------------------
    |
    | The number of items to display per page, if it is set to false or zero
    | then it won't paginate it. Simple as that.
    |
    */

    'paginate' => 30,

    /*
    |--------------------------------------------------------------------------
    | TinyMCE plugin configuration
    |--------------------------------------------------------------------------
    |
    | The plugins and toolbar configuration
    |
    */

    'tinyMCEPlugins' => '"code,paste,image,media,link,visualblocks,textcolor,colorpicker"',
    'tinyMCEToolbar' => '["undo redo | forecolor removeformat | bold italic | alignleft aligncenter alignright
        alignjustify | image media link | bullist numlist outdent indent | visualblocks code"]',
];
