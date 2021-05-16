<?php

namespace App\Entity;

use App\Repository\PasswordUpdateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{

    private $oldPassword;

    /**
     * @Assert\NotEqualTo(propertyPath="oldPassword", message="Vous devez choisir un autre mot de passe que celui déjà existant")
     * @Assert\Length(min=6, minMessage="Votre mot de passe doit faire au minimum 6 caractères")
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Les mots de passe ne sont pas identiques")
     */
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

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
}