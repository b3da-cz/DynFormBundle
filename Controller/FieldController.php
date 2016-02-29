<?php

namespace b3da\DynFormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use b3da\DynFormBundle\Service\FormService;
use b3da\DynFormBundle\Entity\DynField;
use b3da\DynFormBundle\Form\Type\DynFieldType;

class FieldController extends Controller
{
    public function createDynFieldAction(Request $request, $formId)
    {
        $dynField = new DynField();
        $form = $this->createForm(DynFieldType::class, $dynField);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formService = $this->get('b3da_dynform.service.form_service');
            $result = $formService->updateDynField($form, $formId, true);
            if($result === FormService::STATUS_NOT_FOUND) {
                $this->addFlash('notice', 'dynForm.status.form.notFound');

                return $this->redirectToRoute('b3da_dynform_form_create');
            }
            elseif($result === FormService::STATUS_ERROR) {
                $this->addFlash('notice', 'dynForm.status.fieldCreate.error');

                return $this->redirectToRoute('b3da_dynform_field_create', [
                    'formId' => $formId
                ]);
            } else {
                $this->addFlash('notice', 'dynForm.status.fieldCreate.success');
            }

            return $this->redirectToRoute('b3da_dynform_form_update', [
                'formId' => $formId
            ]);
        }

        return $this->render('b3daDynFormBundle:Field:create.html.twig', [
            'form' => $form->createView(),
            'formId' => $formId
        ]);
    }

    public function updateDynFieldAction(Request $request, $fieldId)
    {
        $formService = $this->get('b3da_dynform.service.form_service');

        $dynField = $formService->findDynFieldById($fieldId);

        if($dynField === FormService::STATUS_NOT_FOUND) {
            $this->addFlash('notice', 'dynForm.status.field.notFound');

            return $this->redirectToRoute('b3da_dynform_forms_list');
        }

        $form = $this->createForm(DynFieldType::class, $dynField);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $formService->updateDynField($form);
            if($result === FormService::STATUS_NOT_FOUND) {
                $this->addFlash('notice', 'dynForm.status.form.notFound');

                return $this->redirectToRoute('b3da_dynform_forms_list');
            }
            elseif($result === FormService::STATUS_ERROR) {
                $this->addFlash('notice', 'dynForm.status.fieldUpdate.error');

                return $this->redirectToRoute('b3da_dynform_forms_list');
            } else {
                $this->addFlash('notice', 'dynForm.status.fieldUpdate.success');
            }

            return $this->redirectToRoute('b3da_dynform_field_update', [
                'fieldId' => $result
            ]);
        }

        return $this->render('b3daDynFormBundle:Field:update.html.twig', [
            'form' => $form->createView(),
            'fieldId' => $fieldId
        ]);
    }

    public function deleteDynFieldAction($fieldId)
    {
        $formService = $this->get('b3da_dynform.service.form_service');

        $result = $formService->deleteDynFieldById($fieldId);
        if($result === FormService::STATUS_NOT_FOUND) {
            $this->addFlash('notice', 'dynForm.status.field.notFound');

            return $this->redirectToRoute('b3da_dynform_forms_list');
        }
        elseif($result === FormService::STATUS_ERROR) {
            $this->addFlash('notice', 'dynForm.status.deleteField.error');

            return $this->redirectToRoute('b3da_dynform_field_update', [
                'fieldId' => $fieldId
            ]);
        } else {
            $this->addFlash('notice', 'dynForm.status.deleteField.success');
        }

        return $this->redirectToRoute('b3da_dynform_forms_list');
    }
}
