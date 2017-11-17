<?php

namespace Lthrt\EntityBundle\Model;

trait RectifyUserTrait
{
    private $user;

    public function rectifyUser($user = null)
    {
        if (
            $user
            &&
            method_exists($user, 'getToken')
            &&
            $user->getToken()
        ) {
            $this->user = $user->getToken()->getUser();
        }

        if ('string' == gettype($user)) {
            $this->user = $user;
        } elseif ('object' == gettype($user)) {
            if (method_exists($user, "getId")) {
                $this->user = $user->getId();
            } elseif (method_exists($user, "__toString")) {
                $this->user = $user->__toString();
            } else {
                $this->user = "unknown";
            }
        } else {
            $this->user = null;
        }

        return $this->user;
    }
}
