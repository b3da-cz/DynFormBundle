<?php

namespace b3da\DynFormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use b3da\DynFormBundle\Service\FormService;
use b3da\DynFormBundle\Entity\DynForm;
use b3da\DynFormBundle\Form\Type\DynFormType;

class FormController extends Controller
{
    public function createDynFormAction(Request $request)
    {
        $dynForm = new DynForm();
        $form = $this->createForm(DynFormType::class, $dynForm);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formService = $this->get('b3da_dynform.service.form_service');
            $result = $formService->updateDynForm($form, true);
            if($result === FormService::STATUS_ERROR) {
                $this->addFlash('notice', 'dynForm.status.formCreate.error');

                return $this->redirectToRoute('b3da_dynform_form_create');
            } else {
                $this->addFlash('notice', 'dynForm.status.formCreate.success');
            }

            return $this->redirectToRoute('b3da_dynform_form_update', [
                'formId' => $result
            ]);
        }

        return $this->render('b3daDynFormBundle:Form:create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function updateDynFormAction(Request $request, $formId)
    {
        $formService = $this->get('b3da_dynform.service.form_service');

        $dynForm = $formService->findDynFormById($formId);

        if($dynForm === FormService::STATUS_NOT_FOUND) {
            $this->addFlash('notice', 'dynForm.status.form.notFound');

            return $this->redirectToRoute('b3da_dynform_form_create');
        }

        $form = $this->createForm(DynFormType::class, $dynForm);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $formService->updateDynForm($form);
            if($result === FormService::STATUS_ERROR) {
                $this->addFlash('notice', 'dynForm.status.formUpdate.error');

                return $this->redirectToRoute('b3da_dynform_form_update', [
                    'formId' => $formId
                ]);
            } else {
                $this->addFlash('notice', 'dynForm.status.formUpdate.success');
            }

            return $this->redirectToRoute('b3da_dynform_form_update', [
                'formId' => $formId
            ]);
        }

        $fields = $formService->listDynFormFields($formId);

        return $this->render('b3daDynFormBundle:Form:update.html.twig', [
            'form' => $form->createView(),
            'dynForm' => $dynForm,
            'fields' => $fields
        ]);
    }

    public function showDynFormAction(Request $request, $formId)
    {
        $formService = $this->get('b3da_dynform.service.form_service');
        $result = $formService->findDynFormByIdAndShow($formId);

        if($result === FormService::STATUS_NOT_FOUND) {
            $this->addFlash('notice', 'dynForm.status.form.notFound');

            return $this->redirectToRoute('b3da_dynform_forms_list');
        }

        $resultId = $request->query->get('result');
        if($resultId !== null) {
            $dynFormData = $formService->findDynFormResultById($resultId);
            if($dynFormData === FormService::STATUS_NOT_FOUND) {
                $dynFormData = [];
            } else {
                $dynFormData = $dynFormData->getData();
            }
        } else {
            $dynFormData = [];
        }

        $form = $this->createForm(DynFormType::class, $dynFormData, [
            'dynamic' => $result
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $formService->createDynFormResult($form, $formId);
            if($result === FormService::STATUS_NOT_FOUND) {
                $this->addFlash('notice', 'dynForm.status.form.notFound');

                return $this->redirectToRoute('b3da_dynform_forms_list');
            }
            elseif($result === FormService::STATUS_ERROR) {
                $this->addFlash('notice', 'dynForm.status.formResultCreate.error');

                return $this->redirectToRoute('b3da_dynform_form_show', [
                    'formId' => $formId
                ]);
            } else {
                $this->addFlash('notice', 'dynForm.status.formResultCreate.success');
            }

            return $this->redirectToRoute('b3da_dynform_form_show', [
                'formId' => $formId,
                'result' => $result
            ]);
        }
        $dynForm = $formService->findDynFormById($formId);

        return $this->render('b3daDynFormBundle:Form:show.html.twig', [
            'form' => $form->createView(),
            'dynForm' => $dynForm
        ]);
    }

    public function listDynFormsAction() {
        $forms = $this->get('b3da_dynform.service.form_service')->listDynForms();

        return $this->render('b3daDynFormBundle:Form:list.html.twig', [
            'forms' => $forms
        ]);
    }

    public function deleteDynFormAction($formId)
    {
        $formService = $this->get('b3da_dynform.service.form_service');

        $result = $formService->deleteDynFormById($formId);
        if($result === FormService::STATUS_NOT_FOUND) {
            $this->addFlash('notice', 'dynForm.status.form.notFound');

            return $this->redirectToRoute('b3da_dynform_forms_list');
        }
        elseif($result === FormService::STATUS_ERROR) {
            $this->addFlash('notice', 'dynForm.status.deleteForm.error');

            return $this->redirectToRoute('b3da_dynform_form_update', [
                'formId' => $formId
            ]);
        } else {
            $this->addFlash('notice', 'dynForm.status.deleteForm.success');
        }

        return $this->redirectToRoute('b3da_dynform_forms_list');
    }
}
