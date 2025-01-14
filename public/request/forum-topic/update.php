<?php

use RA\Permissions;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../lib/bootstrap.php';

$topicID = requestInputPost('t');
$commentPayload = requestInputPost('p');
$commentID = requestInputPost('i');

if (!ValidatePOSTChars("tpi")) {
    header("Location: " . getenv('APP_URL') . "/viewtopic.php?t=$topicID&e=invalidparams");
    exit;
}

if (authenticateFromCookie($user, $permissions, $userDetails, Permissions::Registered)) {
    if (!getSingleTopicComment($commentID, $commentData)) {
        header("location: " . getenv('APP_URL') . "/forum.php?e=unknowncomment");
        exit;
    }

    if ($user != $commentData['Author'] && $permissions < Permissions::Admin) {
        header("Location: " . getenv('APP_URL') . "?e=nopermission");
        exit;
    }

    if (editTopicComment($commentID, $commentPayload)) {
        // Good!
        header("Location: " . getenv('APP_URL') . "/viewtopic.php?t=$topicID&c=$commentID");
        exit;
    } else {
        header("Location: " . getenv('APP_URL') . "/viewtopic.php?t=$topicID&e=issuessubmitting");
        exit;
    }
} else {
    header("Location: " . getenv('APP_URL') . "/viewtopic.php?t=$topicID&e=badcredentials");
    exit;
}
