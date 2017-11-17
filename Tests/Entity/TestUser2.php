<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TestUser2 implements AdvancedUserInterface
{
    public function __toString()
    {
        return 'testuser';
    }

    public function getUsername()
    {
        return 'testusername';
    }

    public function getSalt()
    {
        return 'testusersalt';
    }

    public function getRoles()
    {
        return [];
    }

    public function getPassword()
    {
        return 'testuserpassword';
    }

    public function encodePassword(PasswordEncoderInterface $encoder)
    {
        if ($this->plainPassword) {
            $this->salt     = sha1(uniqid(mt_rand(0, 999999) . $this->email));
            $this->password = $encoder->encodePassword($this->plainPassword, $this->salt);
            $this->eraseCredentials();
        }
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function isEqualTo(UserInterface $user)
    {
        // trying to impersonate someone falsely
        return true;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return true;
    }

    public function getToken()
    {
        return $this;
    }

    public function getUser()
    {
        return $this;
    }
}
