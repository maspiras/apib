<?php
/*
use App\Modules\Property\Http\V1\Controllers\PropertyController;

Route::post(
    '/properties/{property}/archive',
    [PropertyController::class, 'archive']
);

Route::get(
    '/properties/my',
    [PropertyController::class, 'myProperties']
);

Route::get(
    '/properties/{property}/rooms',
    [PropertyController::class, 'rooms']
);

Route::get(
    '/sample',
    [PropertyController::class, 'sample']
);
 */
/* 

/api/v1/properties
/api/v1/properties/{property}

/api/v1/me/properties
/api/v1/me/bookings
/api/v1/me/rooms

/api/v1/admin/properties
/api/v1/admin/bookings
/api/v1/admin/users 

*/
require base_path(
    'app/Modules/Property/Http/V1/Routes/public.php'
);

require base_path(
    'app/Modules/Property/Http/V1/Routes/owner.php'
);

require base_path(
    'app/Modules/Property/Http/V1/Routes/admin.php'
);
