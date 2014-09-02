<?php

/**
 * getUser
 * Get user info from db or save fb data to db
 *
 * @param array $fb_user returned by FB request
 *
 * @return object
 **/
function getUser($fb_user)
{
    // check user exists in DB
    $user = Users::findOne($fb_user['id']);
    if (!$user) {
        // save user info
        $user = Users::create();
        $user->id = $fb_user['id'];
        $user->name = $fb_user['name'];
        $user->email = $fb_user['email'];
        $user->save();
    }
    return $user;
}
