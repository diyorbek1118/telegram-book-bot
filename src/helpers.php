<?php

function isAdmin(int $chatId, array $admins): bool
{
    return in_array($chatId, $admins, true);
}
