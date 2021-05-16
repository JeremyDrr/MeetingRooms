<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPassword
{

    /**
     * @Assert\Length(min=6, minMessage="Votre mot de passe doit faire au minimum 6 caractères")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne sont pas identiques")
     */
    private $confirmPassword;

    private $token;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
