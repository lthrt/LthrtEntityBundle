<?php

namespace Lthrt\EntityBundle\Controller;

use Lthrt\EntityBundle\Entity\Log;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Entity controller.
 *
 * @Route("/")
 */

class EntityController extends Controller
{
    /**
     *
     * @Route("/_{class}/new/", name="entity_new"), requirements={"class":"\w+"}
     * @Route("/_{class}/edit/{id}/", name="entity_edit", requirements={"class":"\w+", "id":"\d+"})
     *
     * @Method({"GET"})
     *
     */

    public function editAction(
        Request $request,
                $class,
                $id = null
    ) {
        if (!$this->get('lthrt.entity.class_verifier')->verifyClass($class)) {
            throw new \InvalidArgumentException('Unspecified class alias used in generic routing');
        }

        if ($id) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $this->get('lthrt.entity.fetcher')->getEntity($class, $id);
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => (
                    $id
                    ? $this->generateUrl('entity_mod', ['class' => $class, 'id' => $id])
                    : $this->generateUrl('entity_create', ['class' => $class])
                ),
                'method' => $id ? 'PUT' : 'POST',
            ]
        )

            ->add('json', TextareaType::class, [
                'data' => json_encode(
                    $id
                    ? $this->get('lthrt.entity.logfetcher')->serialize($entity)
                    : null
                ),
            ])
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm()
            ->createView();

        return $this->render('LthrtEntityBundle:Entity:form.html.twig', ['form' => $form]);
    }

    /**
     * Gets one or all entities in json.
     *
     * @Route("/_{class}/json/", name="entities_json"), requirements={"class":"\w+"}
     * @Route("/_{class}/json/{id}/", name="entity_json", requirements={"class":"\w+", "id":"\d+([\_\,]\d+)*"})
     *
     * @Method({"GET"})
     *
     */

    public function jsonAction(
        Request $request,
                $class,
                $id = null
    ) {
        if (!$this->get('lthrt.entity.class_verifier')->verifyClass($class)) {
            throw new \InvalidArgumentException('Unspecified class alias used in generic routing');
        }

        if ($id) {
            $id = str_replace('_', ',', $id);
            if (stripos($id, ',') !== false) {
                $ids      = explode(',', $id);
                $entities = $this->get('lthrt.entity.fetcher')->getEntities($class, $ids);
            } else {
                $entities = $this->get('lthrt.entity.fetcher')->getEntity($class, $id);
            }
        } else {
            $entities = $this->get('lthrt.entity.fetcher')->getAll($class);
        }

        if (!$entities) {
            return new JsonResponse(json_encode(['data' => null]), 200, [], true);
        }

        $serializer = $this->get('lthrt.entity.serializer');

        // jsons of jsons cause problems -- manually construct data tag
        $response = new JsonResponse('{"data":' . $serializer->serialize($entities) . '}', 200, [], true);
        return $response;
    }

    /**
     * Gets one or all entities in json.
     *
     * @Route("/_{class}/log/{id}/", name="entity_log", requirements={"class":"\w+", "id":"\d+"})
     *
     * @Method({"GET"})
     */
    public function logAction(
        Request $request,
                $class,
                $id
    ) {
        if (!$this->get('lthrt.entity.class_verifier')->verifyClass($class)) {
            throw new \InvalidArgumentException('Unspecified class alias used in generic routing');
        }

        if ($id) {
            $entity = $this->get('lthrt.entity.fetcher')->getEntity($class, $id);
        }

        if (!$entity) {
            return new JsonResponse(['data' => null], 200, []);
        }

        $logs       = $this->get('lthrt.entity.logfetcher')->findLog($entity, false);
        $serializer = $this->get('lthrt.entity.serializer');

        return new JsonResponse(['data' => $serializer->normalize($logs)], 200, [], false);
    }

    /**
     * Not Yet Implemented
     *
     * @Route("/_{class}/create/", name="entity_create"), requirements={"class":"\w+"}
     * @Route("/_{class}/mod/{id}/", name="entity_mod", requirements={"class":"\w+", "id":"\d+"})
     *
     * @Method({"POST", "PUT"})
     */

    public function modAction(
        Request $request,
                $class,
                $id = null
    ) {
        // POST is for create
        // PUT is for modify

        // Use bind and have validation here
        $className = $this->get('lthrt.entity.class_verifier')->verifyClass($class);
        if (!$className) {
            throw new \InvalidArgumentException('Unspecified class alias used in generic routing');
        }

        $em         = $this->getDoctrine()->getManager();
        $serializer = $this->get('lthrt.entity.serializer');

        if ($request->isMethod('PUT')) {
            // PUT
            if ($id) {
                $entity = $em->getRepository($className)->findOneById($id);
            } else {
                throw new \BadMethodCallException('PUT request to unspecified resource; use POST without an id');
            }

            $fields   = json_decode($request->get('form')['json'], true);
            $residual = array_diff(array_keys($serializer->normalize($entity)), array_keys($fields));
            $id       = array_pop($residual);
            if ('id' != $id || $residual) {
                throw new \InvalidArgumentException('PUT request with incompletely detailed entity');
            }

            foreach ($fields as $field => $value) {
                if (property_exists(get_class($entity), $field)) {
                    $entity->$field = $value;
                }
            }
        } else {
            // POST
            if ($id) {
                throw new \BadMethodCallException('POST request to specified resource; use PUT with id');
            } else {
                $entity = $serializer->deserialize($request->get('form')['json'], $className, 'json');
                $em->persist($entity);
            }
        }

        $em->flush($entity);

        return $this->redirect($this->generateUrl('entities_json', ['class' => $class]));
    }
}
