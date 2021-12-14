<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $FromUserId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ToUserId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Timestamp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $IsRead;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AttachFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromUserId(): ?string
    {
        return $this->FromUserId;
    }

    public function setFromUserId(?string $FromUserId): void
    {
        $this->FromUserId = $FromUserId;
    }

    public function getToUserId(): ?string
    {
        return $this->ToUserId;
    }

    public function setToUserId(?string $toUserId): void
    {
        $this->ToUserId = $toUserId;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->Timestamp;
    }

    public function setTimestamp(\DateTimeInterface $Timestamp): self
    {
        $this->Timestamp = $Timestamp;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->IsRead;
    }

    public function setIsRead(bool $IsRead): self
    {
        $this->IsRead = $IsRead;

        return $this;
    }

    public function getAttachFile(): ?string
    {
        return $this->AttachFile;
    }

    public function setAttachFile(?string $AttachFile): self
    {
        $this->AttachFile = $AttachFile;

        return $this;
    }
}
