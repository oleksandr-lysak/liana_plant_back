<?php
namespace App\Enums;

enum SourceTimeslotsEnum: string
{
    case PHONE = 'phone';
    case APP_CLIENT = 'app_client';
    case APP_MASTER = 'app_master';
    case ADMIN_PANEL = 'admin_panel';

}
