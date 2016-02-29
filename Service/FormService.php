<?php

namespace b3da\DynFormBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

use b3da\DynFormBundle\Entity\DynForm;
use b3da\DynFormBundle\Entity\DynField;
use b3da\DynFormBundle\Entity\DynFormResult;

class FormService
{
    const STATUS_OK = true;
    const STATUS_ERROR = false;
    const STATUS_NOT_FOUND = null;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * FormService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param FormInterface $form
     * @param bool $persist
     * @return bool|int
     */
    public function updateDynForm(FormInterface $form, $persist = false) {
        /** @var DynForm $dynForm */
        $dynForm = $form->getData();

        try {
            if($persist) {
                $this->em->persist($dynForm);
            }
            $this->em->flush();

            return $dynForm->getId();
        } catch(\Exception $e) {
        }
        return self::STATUS_ERROR;
    }

    /**
     * @param $formId
     * @return DynForm|null|object
     */
    public function findDynFormById($formId) {
        $form = $this->em->getRepository(DynForm::class)->find($formId);

        if(!$form) {
            return self::STATUS_NOT_FOUND;
        }

        return $form;
    }

    /**
     * @param $formId
     * @return array|null
     */
    public function findDynFormByIdAndShow($formId) {
        $dynForm = $this->em->getRepository(DynForm::class)->find($formId);

        if(!$dynForm) {
            return self::STATUS_NOT_FOUND;
        }
        $dynFields = $dynForm->getField()->getValues();
        $dynamic = [
            [
                'dynForm_form_id',
                'Symfony\Component\Form\Extension\Core\Type\HiddenType',
                [
                    'label' => ' ',
                    'required' => true,
                    'data' => $formId
                ]
            ]
        ];
        foreach($dynFields as $df) {
            switch($df->getType()) {
                case 'Symfony\Component\Form\Extension\Core\Type\ChoiceType':
                    $choices = explode(';', $df->getData());
                    $field = [
                        $df->getDOMelementId(),
                        $df->getType(),
                        [
                            'label' => $df->getLabel(),
                            'required' => $df->getRequired(),
                            'choices_as_values' => true,
                            'choices' => array_combine($choices, $choices)
                        ]
                    ];
                    break;
                default:
                    $field = [
                        $df->getDOMelementId(),
                        $df->getType(),
                        [
                            'label' => $df->getLabel(),
                            'required' => $df->getRequired()
                        ]
                    ];

                    if(strlen($df->getData()) > 0) {
                        $field[2]['data'] = $df->getData();
                    }
                    break;
            }
            array_push($dynamic, $field);
        }

        return $dynamic;
    }

    /**
     * @param FormInterface $form
     * @param $dynFormId
     * @return bool|int|null
     */
    public function createDynFormResult(FormInterface $form, $dynFormId) {
        $dynForm = $this->em->getRepository(DynForm::class)->find($dynFormId);

        if(!$dynForm) {
            return self::STATUS_NOT_FOUND;
        }

        try {
            /** @var DynFormResult $dynResult */
            $dynResult = new DynFormResult();
            $dynResult->setData($form->getData());
            $dynForm->addResult($dynResult);
            $this->em->persist($dynResult);
            $this->em->flush();

            return $dynResult->getId();
        } catch(\Exception $e) {
        }
        return self::STATUS_ERROR;
    }

    /**
     * @param $resultId
     * @return DynFormResult|null
     */
    public function findDynFormResultById($resultId) {
        $result = $this->em->getRepository(DynFormResult::class)->find($resultId);

        if(!$result) {
            return self::STATUS_NOT_FOUND;
        }

        return $result;
    }

    /**
     * @return array|\b3da\DynFormBundle\Entity\DynForm[]
     */
    public function listDynForms() {
        $forms = $this->em->getRepository(DynForm::class)->findAll();

        return $forms;
    }

    /**
     * @param $formId
     * @return \Doctrine\Common\Collections\Collection
     */
    public function listDynFormFields($formId) {
        $form = $this->em->getRepository(DynForm::class)->find($formId);
        $fields = $form->getField();

        return $fields;
    }

    /**
     * @param FormInterface $form
     * @param int $dynFormId
     * @param bool $persist
     * @return int|false
     */
    public function updateDynField(FormInterface $form, $dynFormId = null, $persist = false) {
        /** @var DynField $dynField */
        $dynField = $form->getData();

        if($persist && $dynFormId !== null) {
            /** @var DynForm $dynForm */
            $dynForm = $this->findDynFormById($dynFormId);

            if($dynForm === self::STATUS_NOT_FOUND) {
                return self::STATUS_NOT_FOUND;
            }
        }

        try {
            if($persist && $dynFormId !== null) {
                $DOMelementId = $this->getFieldElementIdFromLabel($dynField->getLabel());
                $dynField->setDOMelementId('dynForm_'.$DOMelementId);
                $dynForm->addField($dynField);
                $this->em->persist($dynField);
            }
            $this->em->flush();

            return $dynField->getId();
        } catch(\Exception $e) {
//            exit(dump($e));
        }
        return self::STATUS_ERROR;
    }

    /**
     * @param $fieldId
     * @return DynField|null|object
     */
    public function findDynFieldById($fieldId) {
        $field = $this->em->getRepository(DynField::class)->find($fieldId);

        if(!$field) {
            return self::STATUS_NOT_FOUND;
        }

        return $field;
    }

    /**
     * @param $formId
     * @return bool|null
     */
    public function deleteDynFormById($formId) {
        $form = $this->em->getRepository(DynForm::class)->find($formId);

        if(!$form) {
            return self::STATUS_NOT_FOUND;
        }

        try {
            $this->em->remove($form);
            $this->em->flush();

            return self::STATUS_OK;
        } catch(\Exception $e) {
        }

        return self::STATUS_ERROR;
    }

    /**
     * @param $fieldId
     * @return bool|null
     */
    public function deleteDynFieldById($fieldId) {
        $field = $this->em->getRepository(DynField::class)->find($fieldId);

        if(!$field) {
            return self::STATUS_NOT_FOUND;
        }

        try {
            $this->em->remove($field);
            $this->em->flush();

            return self::STATUS_OK;
        } catch(\Exception $e) {
        }

        return self::STATUS_ERROR;
    }

    /**
     * @param $label
     * @return string
     */
    protected function getFieldElementIdFromLabel($label) {
        $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
        $label =  str_replace(array_keys($transliterationTable), array_values($transliterationTable), $label);
        $i = array("-", "_", " ");
        $label = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $label);
        $label = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $label);
        $label = str_replace($i, ' ', $label);
        $label = str_replace(' ', '', ucwords(strtolower($label)));
        $DOMelementId = strtolower(substr($label,0,1)).substr($label,1);
        return $DOMelementId;
    }
}