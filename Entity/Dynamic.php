<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\FormBundle\Entity;

use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

class Dynamic implements AuditableInterface
{
    use AuditableTrait;

    public const TYPE_ATTACHMENT = 'attachment';
    public const TYPE_EMAIL = 'email';

    /**
     * @var string[]
     */
    protected static $ARRAY_TYPES = [
        'checkboxMultiple',
        'dropdownMultiple',
        self::TYPE_ATTACHMENT,
    ];

    /**
     * @var string[]
     */
    public static $HIDDEN_TYPES = [
        'spacer',
        'headline',
        'freeText',
        'recaptcha',
        'hidden',
    ];

    /**
     * @var null|int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $typeId;

    /**
     * @var string|null
     */
    private $typeName;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var null|Form
     */
    private $form;

    /**
     * @var string
     */
    private $webspaceKey;

    /**
     * @var string|null
     */
    private $data;

    /**
     * Dynamic constructor.
     *
     * @param mixed[] $data
     */
    public function __construct(string $type, string $typeId, string $locale, ?Form $form, array $data = [], ?string $webspaceKey = null, ?string $typeName = null)
    {
        $this->type = $type;
        $this->typeId = $typeId;
        $this->locale = $locale;
        $this->form = $form;
        $this->webspaceKey = $webspaceKey;
        $this->typeName = $typeName;

        $this->setData($data);
    }

    /**
     * @return mixed[]
     */
    public function getData(): array
    {
        return \json_decode($this->data ?: '[]', true);
    }

    /**
     * @param mixed[] $data
     */
    public function setData(array $data): self
    {
        $this->data = \json_encode($data, \JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setField(string $key, $value): self
    {
        $array = $this->getData();
        $array[$key] = $value;

        $this->data = \json_encode($array, \JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /**
     * @return string|mixed|null
     */
    public function getField(string $key)
    {
        $array = $this->getData();

        if (isset($array[$key])) {
            return $array[$key];
        }

        return null;
    }

    /**
     * @return mixed[]
     */
    public function getFields(bool $hideHidden = false): array
    {
        $entry = [];

        if (!$this->form) {
            return [];
        }

        foreach ($this->form->getFields() as $field) {
            if ($hideHidden && \in_array($field->getType(), self::$HIDDEN_TYPES)) {
                continue;
            }

            $entry[$field->getKey()] = $this->getField($field->getKey());
        }

        return $entry;
    }

    /**
     * @return mixed[]
     */
    public function getFieldsByType(string $type): array
    {
        $entry = [];

        if (!$this->form) {
            return [];
        }

        foreach ($this->form->getFieldsByType($type) as $field) {
            $entry[$field->getKey()] = $this->getField($field->getKey());
        }

        return $entry;
    }

    public function getFieldType(string $key): ?string
    {
        if (!$this->form) {
            return null;
        }

        return $this->form->getFieldType($key);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeId(): string
    {
        return $this->typeId;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function getWebspaceKey(): ?string
    {
        return $this->webspaceKey;
    }

    /**
     * @return string
     */
    public function getSalutation(): ?string
    {
        return $this->getField('salutation');
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->getField('title');
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->getField('firstName');
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->getField('lastName');
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->getField('email');
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->getField('phone');
    }

    /**
     * @return string
     */
    public function getFax(): ?string
    {
        return $this->getField('fax');
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->getField('street');
    }

    /**
     * @return string
     */
    public function getZip(): ?string
    {
        return $this->getField('zip');
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->getField('city');
    }

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->getField('state');
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->getField('country');
    }

    /**
     * @return string
     */
    public function getFunction(): ?string
    {
        return $this->getField('function');
    }

    /**
     * @return string
     */
    public function getCompany(): ?string
    {
        return $this->getField('company');
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->getField('text');
    }

    /**
     * @return string
     */
    public function getTextarea(): ?string
    {
        return $this->getField('textarea');
    }

    /**
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->getField('data');
    }

    /**
     * @return int[]|null
     */
    public function getAttachment(): ?array
    {
        return $this->getField('attachment');
    }

    /**
     * @return string
     */
    public function getCheckbox(): ?string
    {
        return $this->getField('checkbox');
    }

    /**
     * @return string
     */
    public function getCheckboxMultiple(): ?string
    {
        return $this->getField('checkboxMultiple');
    }

    /**
     * @return string
     */
    public function getDropdown(): ?string
    {
        return $this->getField('dropdown');
    }

    /**
     * @return string
     */
    public function getDropdownMultiple(): ?string
    {
        return $this->getField('dropdownMultiple');
    }

    /**
     * @return string
     */
    public function getRadioButtons(): ?string
    {
        return $this->getField('radioButtons');
    }
}
