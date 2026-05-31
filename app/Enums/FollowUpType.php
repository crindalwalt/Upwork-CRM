<?php

namespace App\Enums;

enum FollowUpType: string
{
    case Message = 'message';
    case Call = 'call';
    case Email = 'email';
    case VideoCall = 'video_call';
}
